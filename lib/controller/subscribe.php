<?php


namespace Izifir\Core\Controller;

use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Engine\Controller;
use Bitrix\Sender\ContactTable;

class Subscribe extends Controller
{
    public function configureActions()
    {
        return [
            'add' => ['PREFILTERS' => []]
        ];
    }

    public function addAction($email)
    {
        $result = [
            'status' => 'error',
            'message' => 'Произошла ошибка. Повторите попытку позже',
        ];
        $subscriber = ContactTable::getRow(['filter' => ['CODE' => $email]]);
        if (!$subscriber) {
            $addResult = ContactTable::add([
                'CODE' => $email
            ]);
            if ($addResult->isSuccess()) {
                $result['status'] = 'success';
                $result['message'] = 'Вы успешно подписаны на рассылку';
            } else {
                $result['message'] = implode('. ', $addResult->getErrorMessages());
            }
        } else {
            $result['message'] = 'Вы уже были подписаны на рассылку.';
        }

        return $result;
    }
}
