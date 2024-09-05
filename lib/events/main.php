<?php


namespace Izifir\Core\Events;


use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\Web\Json;
use Izifir\Core\App;
use Izifir\Core\Models\FavoriteTable;

class Main
{
    public static function beforeProlog()
    {
        global $USER;
        $_SESSION[App::SESSION_FAVORITE_NAME] = [];
        $favorites = FavoriteTable::getList([
            'filter' => [
                'USER_ID' => $USER->GetID()
            ],
            'select' => ['ELEMENT_ID']
        ])->fetchAll();
        foreach ($favorites as $favorite) {
            $_SESSION[App::SESSION_FAVORITE_NAME][$favorite['ELEMENT_ID']] = $favorite['ELEMENT_ID'];
        }
    }

    public static function onEpilog()
    {
        if ($_SESSION[App::SESSION_FAVORITE_NAME]) {
            $favoriteItems = Json::encode($_SESSION[App::SESSION_FAVORITE_NAME]);
            $script = "
                <script>
                    var favoriteButtons = document.querySelectorAll('.js-favorite-add');
                    var favoriteItems = {$favoriteItems};
                    
                    if (favoriteButtons.length) {
                        [].forEach.call(favoriteButtons, function(button) {
                            var id = button.dataset.id;
                            if (favoriteItems.indexOf(id) >= 0) {
                                button.classList.add('is-added');
                            }
                        })
                    }
                </script>
            ";
            Asset::getInstance()->addString($script);
        }
    }
}
