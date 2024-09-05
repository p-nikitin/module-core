<?php


namespace Izifir\Core;


use Bitrix\Main\Diag\Debug;

class App
{
    const SESSION_LOCATION_NAME = 'stratosfera_location';
    const SESSION_FAVORITE_NAME = 'stratosfera_favorite';
    const SESSION_SORTING_NAME = 'catalog_product_sorting';
    const NO_PHOTO = '/local/templates/.default/assets/images/nophoto.png';
    const NO_PHOTO_COLLECTION = '/local/templates/.default/assets/images/nophoto_coll.png';
    const NO_PHOTO_WEBP = '/local/templates/.default/assets/images/nophoto.webp';
    const NO_PHOTO_COLLECTION_WEBP = '/local/templates/.default/assets/images/nophoto_coll.webp';
    const NO_PHOTO_CATALOG_HORIZONTAL = '/local/templates/.default/assets/images/horizon_nophoto.png';
    const NO_PHOTO_CATALOG_HORIZONTAL_WEBP = '/local/templates/.default/assets/images/horizon_nophoto.webp';
    const NO_PHOTO_CATALOG_VERTICAL = '/local/templates/.default/assets/images/vertical_nophoto.png';
    const NO_PHOTO_CATALOG_VERTICAL_WEBP = '/local/templates/.default/assets/images/vertical_nophoto.webp';
    const SMSRU_API_KEY = 'ED51D04A-7581-3637-51D9-E0126185817A';

    public static function isMainPage()
    {
        return \CSite::InDir('/index.php');
    }

    public static function showPageTitle()
    {
        global $APPLICATION;
        $APPLICATION->AddBufferContent(['Izifir\Core\App', 'getPageTitle']);
    }

    /**
     * @return string
     */
    public static function getPageTitle(): string
    {
        global $APPLICATION;
        $hidePageTitle = $APPLICATION->GetProperty('HIDE_PAGE_TITLE', 'N');
        $showTitle = $hidePageTitle !== 'Y';

        $title = '';
        if ($showTitle) {
            $title = '<div class="section-head__title">';
            $title .= '<h1 class="h1">';
            $title .= $APPLICATION->GetTitle(false);
            $title .= '</h1>';
            $title .= $APPLICATION->GetViewContent('TITLE_PROJECTS_BUTTON');
            $title .= '</div>';
        }
        return $title;
    }
}
