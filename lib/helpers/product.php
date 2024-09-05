<?php


namespace Izifir\Core\Helpers;


use Bitrix\Main\Data\Cache;
use Bitrix\Main\Diag\Debug;

class Product
{
    const OLD_PRICE_PROPERTY_CODE = 'OLD_PRICE';

    public static function getPrice(int $productId)
    {
        global $USER;
        $cache = Cache::createInstance();
        $optimalPrice = false;
        if ($cache->initCache(86400, md5($productId), 'izifir')) {
            $vars = $cache->getVars();
            $optimalPrice = $vars['optimalPrice'];
        } elseif ($cache->startDataCache()) {
            $element = \CIBlockElement::GetList(
                [],
                ['ID' => $productId],
                false,
                false,
                ['ID', 'IBLOCK_ID', 'PROPERTY_' . self::OLD_PRICE_PROPERTY_CODE]
            )->GetNext();
            $optimalPrice = \CCatalogProduct::GetOptimalPrice(
                $productId,
                1,
                $USER->GetUserGroupArray(),
                'N',
                [],
                SITE_ID,
                false
            );
            $optimalPrice['PRINT_PRICE'] = \CCurrencyLang::CurrencyFormat($optimalPrice['DISCOUNT_PRICE'], $optimalPrice['RESULT_PRICE']['CURRENCY']);
            if ($element['PROPERTY_' . self::OLD_PRICE_PROPERTY_CODE . '_VALUE']) {
                $optimalPrice['OLD_PRICE'] = $element['PROPERTY_' . self::OLD_PRICE_PROPERTY_CODE . '_VALUE'];
                $optimalPrice['PRINT_OLD_PRICE'] = \CCurrencyLang::CurrencyFormat($element['PROPERTY_' . self::OLD_PRICE_PROPERTY_CODE . '_VALUE'], $optimalPrice['RESULT_PRICE']['CURRENCY']);
            }
            $cache->endDataCache(['optimalPrice' => $optimalPrice]);
        }

        return $optimalPrice;
    }
}
