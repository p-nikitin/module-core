<?php


namespace Izifir\Core\Helpers;

use Izifir\Core\Helpers\Phone;

class User
{
    public static function getPhone()
    {
        global $USER;
        $phoneNumber = '';
        if ($USER->IsAuthorized()) {
            $user = \CUser::GetList(
                $by = 'ID',
                $order = 'SORT',
                ['ID' => $USER->GetID()],
                [
                    'FIELDS' => ['ID', 'LOGIN', 'PHONE_NUMBER', 'PERSONAL_PHONE']
                ]
            )->Fetch();
            if ($user) {
                if (Phone::validate($user['LOGIN'])) {
                    $phoneNumber = Phone::format($user['LOGIN']);
                } elseif (Phone::validate($user['PERSONAL_PHONE'])) {
                    $phoneNumber = Phone::format($user['PERSONAL_PHONE']);
                } elseif (Phone::validate($user['PHONE_NUMBER'])) {
                    $phoneNumber = Phone::format($user['PHONE_NUMBER']);
                }
            }
        }
        return $phoneNumber;
    }
}

