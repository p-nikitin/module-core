<?php

namespace Izifir\Core\Helpers;

use Bitrix\Main\Type\DateTime as BxDate;

class Date
{
    /**
     * Разбивает дату на день, месяц и год
     * @param BxDate $date
     * @return array
     */
    public static function explodeDate(BxDate $date): array
    {
        $time = $date->getTimestamp();
        return [
            'DAY' => FormatDate('d', $time),
            'MONTH' => FormatDate('F', $time),
            'YEAR' => FormatDate('Y', $time)
        ];
    }
}
