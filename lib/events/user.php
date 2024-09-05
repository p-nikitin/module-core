<?php


namespace Izifir\Core\Events;


use Bitrix\Main\Context;
use Bitrix\Main\Diag\Debug;
use Bitrix\Sender\ContactTable;
use Izifir\Core\Helpers\Phone;

class User
{
    public static function beforeUpdate(&$fields): bool
    {
        if (!empty($fields['PERSONAL_PHONE'])) {
            $phoneParser = Phone::getParser($fields['PERSONAL_PHONE']);
            if ($phoneParser->isValid()) {
                $fields['PERSONAL_PHONE'] = $phoneParser->getCountryCode() . $phoneParser->getNationalNumber();
                $fields['PHONE_NUMBER'] = $phoneParser->getCountryCode() . $phoneParser->getNationalNumber();
            } else {
                global $APPLICATION;
                $APPLICATION->ThrowException('Введите корректный номер телефона в поле "Телефон"');
                return false;
            }
        }
        return true;
    }
    public static function afterUpdate(&$fields)
    {
        global $USER_FIELD_MANAGER;
        $request = Context::getCurrent()->getRequest();
        $needSubscribe = $request->getPost('NEED_SUBSCRIBE');
        $oldEmail = $request->getPost('OLD_EMAIL');
        if (!empty($fields['EMAIL']) && $needSubscribe) {

            // Если был изменен e-mail и пользователь был подписан на рассылку то обновим e-mail
            if ($oldEmail && ($oldEmail != $fields['EMAIL'])) {
                $subscriber = ContactTable::getRow([
                    'filter' => ['CODE' => $oldEmail]
                ]);
                ContactTable::update($subscriber['ID'], [
                    'CODE' => $fields['EMAIL']
                ]);
            } else {
                $subscriber = ContactTable::getRow([
                    'filter' => ['CODE' => $fields['EMAIL']]
                ]);
            }

            // Если пользователя нужно подписать на рассылку и он еще не был подписан
            if ($needSubscribe == 'Y' && !$subscriber) {
                $addResult = ContactTable::add([
                    'CODE' => $fields['EMAIL']
                ]);
            }

            // Если пользователя нужно подписать на рассылку и он ранее (возможно отписан) был подписан
            if ($needSubscribe == 'Y' && $subscriber) {
                ContactTable::update($subscriber['ID'], [
                    'IS_UNSUB' => 'N'
                ]);
            }

            // Если пользователя нужно отписать от рассылки и он был подписан
            if ($needSubscribe == 'N' && $subscriber) {
                ContactTable::update($subscriber['ID'], [
                    'IS_UNSUB' => 'Y'
                ]);
            }

            // Обновим свойство подписки у пользователя
            $uf = $USER_FIELD_MANAGER->GetUserFields('USER', $fields['ID']);
            if ($uf['UF_SUBSCRIBE']) {
                $userFieldEnum = new \CUserFieldEnum;
                $enumList = $userFieldEnum->GetList(
                    [],
                    [
                        'USER_FIELD_ID' => $uf['UF_SUBSCRIBE']['ID'],
                        'XML_ID' => $needSubscribe
                    ]
                )->Fetch();
                (new \CUser())->Update($fields['ID'], ['UF_SUBSCRIBE' => $enumList['ID']]);
            }
        }
    }
}

