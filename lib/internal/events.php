<?php


namespace Izifir\Core\Internal;


use Bitrix\Main\Loader;
use Izifir\Core\Events\Manager;

class Events
{
    public static function onPageStart()
    {
        // Загружаем модуль для доступности на сайте
        Loader::includeModule('izifir.core');
        Manager::registerEvents();
    }

    public static function onGlobalMenuBuild(&$globalMenuItems, &$moduleMenuItems)
    {
        /*$globalMenuItems['global_stratosfera_settings'] = [
            'menu_id' => 'stratosfera_settings',
            'text' => 'Стратосфера',
            'sort' => '500',
            'items_id' => 'global_stratosfera_settings',
            'help_section' => 'stratosfera_settings',
            'items' => []
        ];*/
    }
}
