<?php

namespace App;

class Util
{
    public static function slugify($text): string
    {
        $text = trim((string)$text);
        if ($text === '') {
            return '';
        }
        $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        $text = strtolower($text);
        $text = preg_replace('~[^a-z0-9]+~', '-', $text);
        $text = trim($text, '-');
        if ($text === '') {
            $text = substr(md5((string)mt_rand()), 0, 8);
        }
        return $text;
    }

    public static function parseRssDate($dateStr): string
    {
        $ts = strtotime($dateStr);
        if ($ts === false) {
            $ts = time();
        }
        return date('Y-m-d H:i:s', $ts);
    }

    public static function h($str): string
    {
        return htmlspecialchars((string)$str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
