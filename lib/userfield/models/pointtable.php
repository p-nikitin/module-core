<?php


namespace Izifir\Core\UserField\Models;


use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity;

class PointTable extends DataManager
{
    public static function getTableName()
    {
        return 'sf_prop_picture_dots';
    }

    public static function getMap()
    {
        return [
            'ID' => new Entity\IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true
            ]),
            'PICTURE_ID' => new Entity\IntegerField('PICTURE_ID', [
                'required' => true
            ]),
            'ELEMENT_ID' => new Entity\IntegerField('ELEMENT_ID', []),
            'TOP' => new Entity\StringField('TOP', []),
            'LEFT' => new Entity\StringField('LEFT', [])
        ];
    }

    public static function addForPicture(int $pictureId, array $points)
    {
        self::clearForPicture($pictureId);
        foreach ($points as $point) {
            self::add([
                'PICTURE_ID' => $pictureId,
                'ELEMENT_ID' => $point['elementId'],
                'TOP' => $point['top'],
                'LEFT' => $point['left']
            ]);
        }
    }

    public static function clearForPicture(int $pictureId)
    {
        $points = self::getList(['filter' => ['PICTURE_ID' => $pictureId], 'select' => ['ID']])->fetchAll();
        foreach ($points as $point) {
            self::delete($point['ID']);
        }
    }

    public static function getForPicture(int $pictureId): array
    {
        return self::getList(['filter' => ['PICTURE_ID' => $pictureId]])->fetchAll();
    }
}
