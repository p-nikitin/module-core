<?php


namespace Izifir\Core\Helpers;


use Bitrix\Iblock\IblockTable;
use Bitrix\Main\Data\Cache;
use Bitrix\Main\Loader;

class Iblock
{
    public static function getIblockIdByCode(string $iblockCode)
    {
        Loader::includeModule('iblock');

        $cache = Cache::createInstance();
        $iblockId = null;
        if ($cache->initCache(2678400, md5($iblockCode), 'izifir/iblock')) {
            $vars = $cache->getVars();
            $iblockId = $vars['iblockId'];
        } elseif ($cache->startDataCache()) {
            $iblock = IblockTable::getList([
                'filter' => ['CODE' => $iblockCode],
                'select' => ['ID']
            ])->fetch();
            if ($iblock) {
                $iblockId = $iblock['ID'];
                $cache->endDataCache(['iblockId' => $iblockId]);
            } else {
                $cache->abortDataCache();
            }
        }
        return $iblockId;
    }
}
