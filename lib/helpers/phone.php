<?php


namespace Izifir\Core\Helpers;


use Bitrix\Main\PhoneNumber\Parser;

class Phone
{
    public static function format($phoneNumber)
    {
        return preg_replace('/[^+0-9]/', '', $phoneNumber);
    }

    public static function getParser($phoneNumber)
    {
        return Parser::getInstance()->parse($phoneNumber);
    }

    public static function validate($phoneNumber)
    {
        $phoneNumber = self::format($phoneNumber);
        return Parser::getInstance()->parse($phoneNumber)->isValid();
    }
}
