<?php


namespace Izifir\Core\Events;


use Bitrix\Main\EventManager;

class Manager
{
    public static function registerEvents()
    {
        $eventManager = EventManager::getInstance();

        $eventManager->addEventHandler(
            'iblock',
            'OnAfterIBlockElementUpdate',
            ['\Izifir\Core\Events\Iblock', 'afterElementUpdate']
        );
        $eventManager->addEventHandler(
            'iblock',
            'OnBeforeIBlockElementUpdate',
            ['\Izifir\Core\Events\Iblock', 'beforeElementUpdate']
        );
        $eventManager->addEventHandler(
            'iblock',
            'OnAfterIBlockElementAdd',
            ['\Izifir\Core\Events\Iblock', 'afterElementAdd']
        );
        $eventManager->addEventHandler(
            'iblock',
            'OnBeforeIBlockElementDelete',
            ['\Izifir\Core\Events\Iblock', 'beforeElementDelete']
        );
        $eventManager->addEventHandler(
            'iblock',
            'OnAfterIBlockElementDelete',
            ['\Izifir\Core\Events\Iblock', 'afterElementDelete']
        );
        $eventManager->addEventHandler(
            'catalog',
            'OnProductAdd',
            ['\Izifir\Core\Events\Product', 'productAdd']
        );
        $eventManager->addEventHandler(
            'catalog',
            'OnPriceAdd',
            ['\Izifir\Core\Events\Product', 'priceAdd']
        );
        $eventManager->addEventHandler(
            'iblock',
            'OnIBlockPropertyBuildList',
            ['\Izifir\Core\UserField\DotsFileType', 'GetUserTypeDescription']
        );
        $eventManager->addEventHandler(
            'main',
            'OnBeforeUserUpdate',
            ['\Izifir\Core\Events\User', 'beforeUpdate']
        );
        $eventManager->addEventHandler(
            'main',
            'OnAfterUserUpdate',
            ['\Izifir\Core\Events\User', 'afterUpdate']
        );
        $eventManager->addEventHandler(
            'main',
            'OnBeforeProlog',
            ['\Izifir\Core\Events\Main', 'beforeProlog']
        );
        $eventManager->addEventHandler(
            'main',
            'OnEpilog',
            ['\Izifir\Core\Events\Main', 'onEpilog']
        );
    }
}
