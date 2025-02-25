<?php

namespace App\Helpers;



class TextFormaterHelper
{

    public static function getPrice($value): string
    {
        return number_format($value, 0, '.', ' ') . ' ₽';
    }

    public static function toCamelCase($string)
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $string))));
    }

    public static function toSnakeCase($string)
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }

    public static function visualFormatNumber($number, $with_rubles = false, $stringify = false)
    {
        $words = [];
        $formatted_number = intval(preg_replace("/[^,.0-9]/", '', $number));
        if (!$formatted_number) {
            return $number;
        }

        $words['num'] = $formatted_number;
        $words['text'] = self::num2str($formatted_number);

        if ($with_rubles) {
            $words['ruble'] = self::rubleTermination($formatted_number);
        }
        if ($stringify) {

            $words['text'] = '(' . $words['text'] . ')';

            return implode(' ', $words);
        }

        return $words;
    }

    public static function getNumberFromString(string $string): int
    {
        return intval(preg_replace("/[^,.0-9]/", '', $string));
    }

    public static function rubleTermination($num)
    {

        //Оставляем две последние цифры от $num
        $number = substr($num, -2);

        //Если 2 последние цифры входят в диапазон от 11 до 14
        //Тогда подставляем окончание "ЕЙ"
        if ($number > 4 and $number < 21) {
            $term = "ей";
        } else {

            $number = substr($number, -1);

            if ($number == 0) {
                $term = "ей";
            }
            if ($number == 1) {
                $term = "ь";
            }
            if ($number > 1) {
                $term = "я";
            }
        }

        return 'рубл' . $term;
    }

    public static function num2str($num)
    {
        $nul = 'ноль';
        $ten = array(
            array('', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'),
            array('', 'одна', 'две', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'),
        );
        $a20 = array('десять', 'одиннадцать', 'двенадцать', 'тринадцать', 'четырнадцать', 'пятнадцать', 'шестнадцать', 'семнадцать', 'восемнадцать', 'девятнадцать');
        $tens = array(2 => 'двадцать', 'тридцать', 'сорок', 'пятьдесят', 'шестьдесят', 'семьдесят', 'восемьдесят', 'девяносто');
        $hundred = array('', 'сто', 'двести', 'триста', 'четыреста', 'пятьсот', 'шестьсот', 'семьсот', 'восемьсот', 'девятьсот');
        $unit = array( // Units
            array('', '', '',     1),
            array('', '', '', 0),
            array('тысяча', 'тысячи', 'тысяч', 1),
            array('миллион', 'миллиона', 'миллионов', 0),
            array('миллиард', 'милиарда', 'миллиардов', 0),
        );
        //
        list($rub, $kop) = explode('.', sprintf("%015.2f", floatval($num)));
        $out = array();
        if (intval($rub) > 0) {
            foreach (str_split($rub, 3) as $uk => $v) { // by 3 symbols
                if (!intval($v)) continue;
                $uk = sizeof($unit) - $uk - 1; // unit key
                $gender = $unit[$uk][3];
                list($i1, $i2, $i3) = array_map('intval', str_split($v, 1));
                // mega-logic
                $out[] = $hundred[$i1]; # 1xx-9xx
                if ($i2 > 1) $out[] = $tens[$i2] . ' ' . $ten[$gender][$i3]; # 20-99
                else $out[] = $i2 > 0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
                // units without rub & kop
                if ($uk > 1) $out[] = self::morph($v, $unit[$uk][0], $unit[$uk][1], $unit[$uk][2]);
            } //foreach
        } else $out[] = $nul;
        $out[] = self::morph(intval($rub), $unit[1][0], $unit[1][1], $unit[1][2]); // rub
        return trim(preg_replace('/ {2,}/', ' ', join(' ', $out)));
    }

    public static function morph($n, $f1, $f2, $f5)
    {
        $n = abs(intval($n)) % 100;
        if ($n > 10 && $n < 20) return $f5;
        $n = $n % 10;
        if ($n > 1 && $n < 5) return $f2;
        if ($n == 1) return $f1;
        return $f5;
    }


    public static function visualFormatDeadline($number)
    {
        $words = [];
        $formatted_number = intval(preg_replace("/[^,.0-9]/", '', $number));
        if (!$formatted_number) {
            return $number;
        }

        $words[] = $formatted_number;
        $words[] = '(' .  self::num2str($formatted_number) . ')';
        $words[] = self::deadlineWorkWordTermination($formatted_number);
        $words[] = self::deadlineDayTermination($formatted_number);

        return implode(' ', $words);
    }

    public static function deadlineWorkWordTermination($num)
    {

        //Оставляем две последние цифры от $num
        $number = substr($num, -2);

        //Если 2 последние цифры входят в диапазон от 11 до 14
        //Тогда подставляем окончание "ЕВ"
        if ($number > 4 and $number < 21) {
            $term = "х";
        } else {

            $number = substr($number, -1);

            if ($number == 0) {
                $term = "х";
            }
            if ($number == 1) {
                $term = "й";
            }
            if ($number > 1) {
                $term = "х";
            }
        }

        return 'рабочи' . $term;
    }

    public static function deadlineDayTermination($num)
    {
        //Оставляем две последние цифры от $num
        $number = substr($num, -2);

        //Если 2 последние цифры входят в диапазон от 11 до 14
        //Тогда подставляем окончание "ЕВ"
        if ($number > 4 and $number < 21) {
            $term = "ней";
        } else {

            $number = substr($number, -1);

            if ($number == 0) {
                $term = "ней";
            }
            if ($number == 1) {
                $term = "ень";
            }
            if ($number > 1) {
                $term = "ня";
            }
        }

        return 'д' . $term;
    }
}
