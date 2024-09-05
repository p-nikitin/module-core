<?php


namespace Izifir\Core\Events;


use Bitrix\Main\Diag\Debug;
use Izifir\Core\Collection;

class Product
{
    public static function productAdd($id, $fields)
    {

    }

    public static function priceAdd($id, $fields)
    {
        Collection::updateByElement($fields['PRODUCT_ID']);
    }
}
