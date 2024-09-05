<?php
/**
 *
 */

use Bitrix\Main\ModuleManager;
use Bitrix\Main\EventManager;

class izifir_core extends CModule
{
    public $MODULE_ID = 'izifir.core';

    public function __construct()
    {
        $arVersion = [];
        include (dirname(__FILE__) . '/version.php');

        $this->MODULE_VERSION = $arVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arVersion['VERSION_DATE'];

        $this->MODULE_NAME = 'IZISite';
        $this->MODULE_DESCRIPTION = 'Инструменты для разработки сайта';

        $this->PARTNER_NAME = 'Стратосфера';
        $this->PARTNER_URI = 'https://izifir.ru';
    }

    public function InstallEvents()
    {
        $eventManager = EventManager::getInstance();
        $eventManager->registerEventHandler('main', 'OnPageStart', $this->MODULE_ID, '\izifir\Core\Internal\Events', 'onPageStart');
        $eventManager->registerEventHandler('main', 'OnBuildGlobalMenu', $this->MODULE_ID, '\izifir\Core\Internal\Events', 'onGlobalMenuBuild');
    }

    public function UnInstallEvents()
    {
        $eventManager = EventManager::getInstance();
        $eventManager->unRegisterEventHandler('main', 'OnPageStart', $this->MODULE_ID, '\izifir\Core\Internal\Events', 'onPageStart');
        $eventManager->unRegisterEventHandler('main', 'OnBuildGlobalMenu', $this->MODULE_ID, '\izifir\Core\Internal\Events', 'onGlobalMenuBuild');
    }

    public function InstallFiles()
    {
        CopyDirFiles(dirname(__FILE__) . '/admin', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin', true, true);
        CopyDirFiles(dirname(__FILE__) . '/themes', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/themes', true, true);
    }

    public function UnInstallFiles()
    {
        DeleteDirFiles(dirname(__FILE__) . '/admin', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin');
        DeleteDirFiles(dirname(__FILE__) . '/themes', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/themes');
    }

    public function DoInstall()
    {
        $this->InstallEvents();
        $this->InstallFiles();
        ModuleManager::registerModule($this->MODULE_ID);
    }

    public function DoUninstall()
    {
        $this->UnInstallEvents();
        $this->UnInstallFiles();
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }
}
