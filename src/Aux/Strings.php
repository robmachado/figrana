<?php

namespace Figrana\Aux;

class Strings
{
    /**
     * Masquerade a string with a pattern
     * @param string $pattern
     * @param string $str
     * @return string
     */
    public static function mask(string $pattern, string $str):string
    {
        $replacements = substr_count($pattern, "#", 0, strlen($pattern));
        $str = preg_replace("/[^0-9]/", "", $str);
        $str = str_pad($str, $replacements, '0', STR_PAD_LEFT);
        $str = substr($str, 0, $replacements);
        for ($i = 0; $i < strlen($str); $i++) {
            $pattern[strpos($pattern, "#")] = $str[$i];
        }
        return $pattern;
    }
}