<?php


namespace Izifir\Core\Models;

use Bitrix\Main\Entity;
use Bitrix\Main\Type\DateTime;

class FavoriteTable extends Entity\DataManager
{
    public static function getTableName(): string
    {
        return 'sf_favorite';
    }

    public static function getMap(): array
    {
        return [
            'ID' => new Entity\IntegerField('ID', [
                'autocomplete' => true,
                'primary' => true
            ]),
            'USER_ID' => new Entity\IntegerField('USER_ID', [
                'required' => true
            ]),
            'ELEMENT_ID' => new Entity\IntegerField('ELEMENT_ID', [
                'required' => true
            ]),
            'DATE_INSERT' => new Entity\DatetimeField('DATE_INSERT', [
                'default_value' => new DateTime()
            ])
        ];
    }
}
