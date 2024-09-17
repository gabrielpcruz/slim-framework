<?php

namespace SlimFramework\Utils;

class StringUtil
{
    /**
     * @param $string
     * @return string
     */
    public static function toMoneyDatabase($string): string
    {
        $string = str_replace('.', '', $string);
        return str_replace(',', '.', $string);
    }
}
