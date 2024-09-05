<?php


namespace Izifir\Core\UserField;

use Bitrix\Main\Entity;

class UserFieldEnumTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'b_user_field_enum';
    }

    public static function getMap()
    {
        return [
            'ID' => new Entity\IntegerField('ID', [
                'primary' => true,
                'autoincrement' => true
            ]),
            'USER_FIELD_ID' => new Entity\IntegerField('USER_FIELD_ID'),
            'VALUE' => new Entity\StringField('VALUE'),
            'DEF' => new Entity\StringField('DEF'),
            'SORT' => new Entity\StringField('SORT'),
            'XML_ID' => new Entity\StringField('XML_ID'),
        ];
    }

}
