<?php


namespace Izifir\Core;


use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Loader;
use Izifir\Core\Helpers\Iblock;

class Collection
{
    /**
     * Код свойства привязки коллекции к товару
     */
    const PROPERTY_GOOD_CODE = 'COLLECTION';

    /**
     * Код свойства с минимальной ценой
     */
    const PROPERTY_MIN_PRICE = 'MIN_PRICE';

    /**
     * Код свойства с кол-ом товаров в коллекции
     */
    const PROPERTY_GOODS_CNT = 'GOODS_CNT';

    /**
     * Метод обновляет информацию о коллекции по ID товара
     * @param int $productId
     * @throws \Bitrix\Main\LoaderException
     */
    public static function updateByElement(int $productId)
    {
        if ($productId) {
            Loader::includeModule('iblock');
            $propertyCollection = \CIBlockElement::GetProperty(
                Iblock::getIblockIdByCode('eshop_catalog'),
                $productId,
                'sort',
                'asc',
                ['CODE' => self::PROPERTY_GOOD_CODE]
            )->Fetch();

            if ($propertyCollection['VALUE'])
                self::update($propertyCollection['VALUE']);
        }
    }

    /**
     * Метод обновляет информацию о коллекции
     * @param int $collectionId
     * @throws \Bitrix\Main\LoaderException
     */
    public static function update(int $collectionId)
    {
        Loader::includeModule('iblock');
        $baseCatalogGroup = \CCatalogGroup::GetBaseGroup();
        $elementFilter = [
            'IBLOCK_ID' => Iblock::getIblockIdByCode('eshop_catalog'),
            'PROPERTY_' . self::PROPERTY_GOOD_CODE => $collectionId,
            'ACTIVE' => 'Y'
        ];
        $cnt = \CIBlockElement::GetList([], $elementFilter, [], false, ['ID']);
        $element = \CIBlockElement::GetList(
            ["PRICE_{$baseCatalogGroup['ID']}" => 'ASC'],
            $elementFilter,
            false,
            ['nTopCount' => 1],
            ['ID', 'IBLOCK_ID', "PRICE_{$baseCatalogGroup['ID']}"]
        )->Fetch();

        \CIBlockElement::SetPropertyValuesEx(
            $collectionId,
            Iblock::getIblockIdByCode('eshop_collections'),
            [
                self::PROPERTY_GOODS_CNT => $cnt,
                self::PROPERTY_MIN_PRICE => $element["PRICE_{$baseCatalogGroup['ID']}"]
            ]
        );
    }
}
