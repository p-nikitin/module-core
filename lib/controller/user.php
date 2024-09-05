<?php


namespace Izifir\Core\Controller;

use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Engine\ActionFilter\Csrf;
use Bitrix\Main\Engine\ActionFilter\PostDecode;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Security\Random;
use Bitrix\Main\UserTable;
use Izifir\Core\App;
use Izifir\Core\Helpers\Phone;
use Izifir\Core\Smsru;

class User extends Controller
{
    /**
     * @return array
     */
    public function configureActions(): array
    {
        return [
            'sendCode' => ['prefilters' => [new Csrf(), new PostDecode()]],
            'checkCode' => ['prefilters' => [new Csrf(), new PostDecode()]],
        ];
    }

    /**
     * @param $phoneNumber
     * @return string[]
     */
    public function sendCodeAction($phoneNumber): array
    {
        $phoneParser = Phone::getParser($phoneNumber);
        $result = [
            'status' => 'error',
            'message' => 'Ошибка отправки кода',
        ];
        if ($phoneParser->isValid()) {
            $clearPhoneNumber = $phoneParser->getCountryCode() . $phoneParser->getNationalNumber();
            $user = $this->getUserByPhoneNumber($clearPhoneNumber);
            $obUser = new \CUser();
            if (!$user) {
                $password = Random::getString(10);
                $userId = $obUser->Add([
                    'LOGIN' => $clearPhoneNumber,
                    'PASSWORD' => $password,
                    'CONFIRM_PASSWORD' => $password,
                    'ACTIVE' => 'Y'
                ]);
                if ($userId > 0) {
                    $user = ['ID' => $userId];
                } else {
                    $result['message'] = $obUser->LAST_ERROR;
                }
            }

            if ($user['ID']) {
                $checkWord = Random::getStringByAlphabet(4, Random::ALPHABET_NUM);
                $setCheckWord = $obUser->Update($user['ID'], [
                    'CONFIRM_CODE' => $checkWord
                ]);

                if ($setCheckWord) {
                    $smsData = new \stdClass();
                    $smsData->to = $clearPhoneNumber;
                    $smsData->text = "Ваш код {$checkWord}. Никому его не сообщайте.";
                    //$smsData->test = 1;
                    $smsSender = new Smsru(App::SMSRU_API_KEY);
                    $smsResult = $smsSender->send($smsData);
                    if ($smsResult->status == 'OK') {
                        $result['status'] = 'success';
                        $result['message'] = 'Код успешно отправлен';
                        //$result['code'] = $checkWord;
                    }
                } else {
                    $result['message'] = 'Ошибка создания кода';
                }
            }
        } else {
            $result['message'] = 'Неверный формат';
        }
        return $result;
    }

    public function checkCodeAction($phoneNumber, $code)
    {
        $phoneParser = Phone::getParser($phoneNumber);
        $result = [
            'status' => 'error',
            'message' => 'Ошибка отправки кода',
        ];
        if ($phoneParser->isValid()) {
            $clearPhoneNumber = $phoneParser->getCountryCode() . $phoneParser->getNationalNumber();
            $user = $this->getUserByPhoneNumber($clearPhoneNumber);
            if ($user) {
                if ($user['CONFIRM_CODE'] == $code) {
                    if ($this->authorize($user['ID'])) {
                        $result['status'] = 'success';
                        $result['message'] = 'Пользователь успешно авторизован';
                    } else {
                        $result['message'] = 'Ошибка авторизации';
                    }
                } else {
                    $result['message'] = 'Неверный код';
                }
            } else {
                $result['message'] = 'Пользователь не найден';
            }
        }
        return $result;
    }

    /**
     * @param $phoneNumber
     * @return array|false
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    protected function getUserByPhoneNumber($phoneNumber)
    {
        $user = UserTable::getList([
            'filter' => [
                [
                    'LOGIC' => 'OR',
                    ['LOGIN' => $phoneNumber],
                    ['PERSONAL_PHONE' => $phoneNumber],
                ]
            ],
            'select' => ['ID', 'CONFIRM_CODE']
        ]);
        return $user->fetch();
    }

    /**
     * @param $userId
     * @return bool
     */
    protected function authorize($userId): bool
    {
        global $USER;
        return $USER->Authorize($userId);
    }
}
