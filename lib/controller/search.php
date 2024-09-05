<?php


namespace Izifir\Core\Controller;

use Bitrix\Catalog\MeasureTable;
use Bitrix\Main;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Loader;
use Izifir\Core\App;
use Izifir\Core\Helpers\Iblock;
use Izifir\Core\Helpers\Product;

class Search extends Controller
{
    public function configureActions()
    {
        return [
            'search' => ['prefilters' => []]
        ];
    }

    public function searchAction()
    {
        return [
            'sections' => $this->searchSections(),
            'items' => $this->searchItems()
        ];
    }

    /**
     * @return array
     * @throws Main\LoaderException
     */
    protected function searchSections(): array
    {
        global $APPLICATION;
        Loader::includeModule('iblock');
        $sections = [];
        $sectionsIdList = $APPLICATION->IncludeComponent(
            'izifir:section.search',
            '',
            array(
                'IBLOCK_ID' => Iblock::getIblockIdByCode('eshop_catalog')
            )
        );

        if (!empty($sectionsIdList)) {
            $sectionIterator = \CIBlockSection::GetList(
                ['ID' => $sectionsIdList],
                ['ID' => $sectionsIdList],
                false,
                ['ID', 'IBLOCK_ID', 'NAME', 'SECTION_PAGE_URL'],
                ['nTopCount' => 3]
            );
            while ($section = $sectionIterator->GetNext()) {
                $sections[] = $section;
            }
        }

        return $sections;
    }

    protected function searchItems()
    {
        global $APPLICATION;
        Loader::includeModule('iblock');
        Loader::includeModule('catalog');
        $items = [];
        $elementIdList = $APPLICATION->IncludeComponent(
            "bitrix:search.page",
            ".default",
            array(
                "RESTART" => 'N',
                "NO_WORD_LOGIC" => 'N',
                "USE_LANGUAGE_GUESS" => 'Y',
                "CHECK_DATES" => 'N',
                "arrFILTER" => ["iblock_eshop"],
                "arrFILTER_iblock_eshop" => [Iblock::getIblockIdByCode('eshop_catalog')],
                "USE_TITLE_RANK" => 'N',
                "DEFAULT_SORT" => "rank",
                "FILTER_NAME" => "",
                "SHOW_WHERE" => "N",
                "arrWHERE" => [],
                "SHOW_WHEN" => "N",
                "PAGE_RESULT_COUNT" => 3,
                "DISPLAY_TOP_PAGER" => "N",
                "DISPLAY_BOTTOM_PAGER" => "N",
                "PAGER_TITLE" => "",
                "PAGER_SHOW_ALWAYS" => "N",
                "PAGER_TEMPLATE" => "N",
            )
        );

        if (!empty($elementIdList)) {
            $baseCatalogGroup = \CCatalogGroup::GetBaseGroup();

            $elementIterator = \CIBlockElement::GetList(
                ['ID' => $elementIdList],
                ['ID' => $elementIdList],
                false,
                ['nTopCount' => 3],
                ['ID', 'IBLOCK_ID', 'NAME', 'DETAIL_PAGE_URL', 'PREVIEW_PICTURE', 'CATALOG_MEASURE']
            );

            while ($element = $elementIterator->GetNext()) {
                if (!empty($element['~PREVIEW_PICTURE'])) {
                    $element['PREVIEW_PICTURE'] = \CFile::GetFileArray($element['~PREVIEW_PICTURE']);
                    $cropPicture = \CFile::ResizeImageGet(
                        $element['PREVIEW_PICTURE'],
                        ['width' => 0, 'heigth' => 0],
                        BX_RESIZE_IMAGE_EXACT
                    );

                    if ($cropPicture) {
                        $element['PREVIEW_PICTURE']['SRC'] = $cropPicture['src'];
                    }
                } else {
                    $element['PREVIEW_PICTURE'] = [
                        'SRC' => App::NO_PHOTO
                    ];
                }

                $element['OPTIMAL_PRICE'] = Product::getPrice($element['ID']);

                if (!empty($element['CATALOG_MEASURE'])) {
                    $element['MEASURE_ITEM'] = MeasureTable::getRow(['filter' => ['ID' => $element['CATALOG_MEASURE']]]);
                }

                if (!empty($element['MEASURE_ITEM'])) {
                    $element['FORMATTED_PRICE'] = $element['OPTIMAL_PRICE']['PRINT_PRICE'] . ' / ' . htmlspecialcharsback($element['MEASURE_ITEM']['SYMBOL']);
                }

                $items[] = $element;
            }
        }

        return $items;
    }
}
