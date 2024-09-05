<?php


namespace Izifir\Core\UserField;



use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Web\Json;
use Izifir\Core\UserField\Models\PointTable;

class DotsFileType
{
    public const
        USER_TYPE_ID = 'dotsfile',
        RENDER_COMPONENT = 'izifir:dots.file.edit';

    /**
     * @return array
     */
    public static function GetUserTypeDescription(): array
    {
        return [
            'PROPERTY_TYPE' => PropertyTable::TYPE_FILE,
            'DESCRIPTION' => 'Картинка с точками',
            'BASE_TYPE' => \CUserTypeManager::BASE_TYPE_FILE,
            'USER_TYPE' => self::USER_TYPE_ID,
            'GetPropertyFieldHtml' => [__CLASS__, 'GetPropertyFieldHtml'],
            'GetSettingsHTML' => [__CLASS__, "GetSettingsHTML"],
            "ConvertToDB" => [__CLASS__, "ConvertToDB"],
            "ConvertFromDB" => [__CLASS__, "ConvertFromDB"],
        ];
    }

    public static function GetPropertyFieldHtml($property, $value, $htmlControlName)
    {
        global $APPLICATION;
        if (!empty($value['VALUE']))
            $value['DESCRIPTION'] = PointTable::getForPicture($value['VALUE']);

        ob_start();
        $APPLICATION->IncludeComponent(
            self::RENDER_COMPONENT,
            '',
            [
                'PROPERTY' => $property,
                'VALUE' => $value,
                'HTML_CONTROL_NAME' => $htmlControlName
            ]
        );
        return ob_get_clean();
    }

    public static function GetSettingsHTML($arProperty, $strHTMLControlName, &$arPropertyFields)
    {
        $arPropertyFields = [
            "HIDE" => ["ROW_COUNT", "COL_COUNT"],
            "SHOW" => ["MULTIPLE_CNT"]
        ];

        return '';
    }

    public static function ConvertToDB($property, $value)
    {
        if (!empty($_REQUEST['Point'][$value['DESCRIPTION']])) {
            $pictureId = preg_replace('/[^0-9]/', '', $value['DESCRIPTION']);
            PointTable::addForPicture($pictureId, $_REQUEST['Point'][$value['DESCRIPTION']]);
        }
        $value['DESCRIPTION'] = '';

        return $value;
    }

    public static function ConvertFromDB($arProperty, $value)
    {
        if (!empty($value['VALUE']))
            $value['DESCRIPTION'] = PointTable::getForPicture($value['VALUE']);
        return $value;
    }
}
