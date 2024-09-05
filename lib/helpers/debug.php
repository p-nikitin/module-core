<?php


namespace Izifir\Core\Helpers;


class Debug
{
    /**
     * Выводит массив в виде дерева
     *
     * @param mixed - Массив или объект, который надо обойти
     * @param boolean - Раскрыть дерево элементов по-умолчанию или нет?
     *
     * @return void
     */
    public static function dump($in, $opened = true)
    {
        if ($opened)
            $opened = ' open';
        if (is_object($in) or is_array($in)) {
            echo '<div>';
            echo '<details' . $opened . '>';
            echo '<summary>';
            echo (is_object($in)) ? 'Object {' . count((array)$in) . '}' : 'Array [' . count($in) . ']';
            echo '</summary>';
            self::dump_rec($in, $opened);
            echo '</details>';
            echo '</div>';
        }
    }

    protected static function dump_rec($in, $opened, $margin = 10)
    {
        if (!is_object($in) && !is_array($in))
            return;

        foreach ($in as $key => $value) {
            if (is_object($value) or is_array($value)) {
                echo '<details style="margin-left:' . $margin . 'px" ' . $opened . '>';
                echo '<summary>';
                echo (is_object($value)) ? $key . ' {' . count((array)$value) . '}' : $key . ' [' . count($value) . ']';
                echo '</summary>';
                self::dump_rec($value, $opened, $margin + 10);
                echo '</details>';
            } else {
                switch (gettype($value)) {
                    case 'string':
                        $bgc = 'red';
                        break;
                    case 'integer':
                        $bgc = 'green';
                        break;
                }
                echo '<div style="margin-left:' . $margin . 'px">' . $key . ' : <span style="color:' . $bgc . '">' . $value . '</span> (' . gettype($value) . ')</div>';
            }
        }
    }
}
