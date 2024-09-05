<?php


namespace Izifir\Core;


use Bitrix\Main\Data\Cache;
use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Loader;
use Izifir\Core\Helpers\Iblock;

class City
{
    /**
     * @var array
     */
    static protected array $_defaultCity = [];

    public static function getDefaultCity($useCache = true)
    {
        if (!self::$_defaultCity) {
            $cacheTime = $useCache ? 2678400 : 0;
            $cache = Cache::createInstance();
            if ($cache->initCache($cacheTime, md5('default_city'), 'izifir')) {
                $vars = $cache->getVars();
                self::$_defaultCity = $vars['defaultCity'];
            } elseif ($cache->startDataCache()) {
                Loader::includeModule('iblock');
                self::$_defaultCity = self::getCity([])->GetNext();
                $cache->endDataCache(['defaultCity' => self::$_defaultCity]);
            }
        }
        return self::$_defaultCity;
    }

    /**
     * @param array $filter
     * @return \CIBlockResult|int
     */
    public static function getCity(array $filter)
    {
        $filter['IBLOCK_ID'] = Iblock::getIblockIdByCode('dictionaries_city');
        return \CIBlockElement::GetList(
            [],
            $filter,
            false,
            false,
            ['ID', 'IBLOCK_ID', 'NAME', 'PROPERTY_LOCATION_ID', 'PROPERTY_PHONE', 'PROPERTY_EMAIL', 'PROPERTY_ADDRESS', 'PROPERTY_INSTAGRAM_LINK', 'PROPERTY_VK_LINK', 'PROPERTY_WHATSAPP']
        );
    }
}
