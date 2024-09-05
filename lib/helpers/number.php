<?php


namespace Izifir\Core\Helpers;

use Bitrix\Main\Grid\Declension;

class Number
{
    /**
     * @param int $number
     * @param array $forms
     * @return string
     */
    public static function pluralForm(int $number, array $forms): string
    {
        $declension = new Declension($forms[0], $forms[1], $forms['2']);
        return $declension->get($number);
    }
}
