<?php


namespace Izifir\Core\Events;

use Bitrix\Catalog\PriceTable;
use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Loader;
use Izifir\Core\Collection;
use Izifir\Core\Helpers\Iblock as HIblock;
use Izifir\Core\UserField\Models\PointTable;

class Iblock
{
    public static function afterElementUpdate(&$fields)
    {
        if ($fields['IBLOCK_ID'] == HIblock::getIblockIdByCode('eshop_catalog')) {
            Collection::updateByElement($fields['ID']);
        }
    }

    public static function beforeElementUpdate(&$fields)
    {
        if (!empty($_REQUEST['PROP_del'])) {
            foreach ($_REQUEST['PROP_del'] as $propId => $prop) {
                foreach ($prop as $id => $v) {
                    if ($v['VALUE'] == 'Y') {
                        $imgId = intval(str_replace('dots-file-', '', $fields['PROPERTY_VALUES'][$propId][$id]['DESCRIPTION']));
                        if ($imgId > 0) {
                            \CFile::Delete($imgId);
                            PointTable::clearForPicture($imgId);
                            unset($_REQUEST['Point'][$fields['PROPERTY_VALUES'][$propId][$id]['DESCRIPTION']]);
                        }
                        $fields['PROPERTY_VALUES'][$propId][$id]['VALUE']['del'] = 'Y';
                    }
                }
            }
        }
    }

    public static function afterElementAdd(&$fields)
    {
        if ($fields['IBLOCK_ID'] == HIblock::getIblockIdByCode('eshop_catalog')) {
            Collection::updateByElement($fields['ID']);
        }
    }

    public static function beforeElementDelete($id)
    {
        Loader::includeModule('iblock');
        $element = \CIBlockElement::GetList(
            [],
            ['ID' => $id],
            false,
            false,
            ['ID', 'IBLOCK_ID', 'PROPERTY_' . Collection::PROPERTY_GOOD_CODE]
        )->Fetch();
        // Если удаляется товар и у него есть привязка к коллекции, то сохраним ID коллекции,
        // чтобы можно было обновить информацию о ней после успешного удаления товара
        if ($element['IBLOCK_ID'] == HIblock::getIblockIdByCode('eshop_catalog') && !empty($element['PROPERTY_' . Collection::PROPERTY_GOOD_CODE . '_VALUE'])) {
            $GLOBALS['COLLECTION_FOR_UPDATE'] = $element['PROPERTY_' . Collection::PROPERTY_GOOD_CODE . '_VALUE'];
        }
    }

    public static function afterElementDelete($fields)
    {
        // Если у удаляемого товара была коллекция, то нужно обновить информацию о ней
        // Наличие коллекции определяется на событии OnBeforeIBlockElementDelete
        // в методе self::beforeElementDelete
        if (!empty($GLOBALS['COLLECTION_FOR_UPDATE'])) {
            Collection::update($GLOBALS['COLLECTION_FOR_UPDATE']);
            unset($GLOBALS['COLLECTION_FOR_UPDATE']);
        }
    }
}
