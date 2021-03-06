<?php

namespace sukung34\kit\components;

use DateTime;
use DateTimeZone;
use Exception;
use Yii;

class DateHelper extends Html
{

    public static function createDateTime($time = null, $timezone = null)
    {
        if (!isset($timezone)) {
            $timezone = Yii::$app->formatter->timeZone;
        }
        return new DateTime($time, new DateTimeZone($timezone));
    }

    public static function getDayOptions()
    {
        $ret = [];
        for ($i = 1; $i <= 31; $i++) {
            $ret[$i] = $i;
        }
        return $ret;
    }

    public static function getFiscalYear($month = null, $year = null)
    {
        $year = isset($year) ? $year : date('Y');
        $month = isset($month) ? $month : date('n');

        if ($month >= 10) {
            return intval($year) + 1;
        }
        return intval($year);
    }

    public static function getFiscalIterator($fiscal_year = null)
    {
        $ret = [];
        $fiscal_year = isset($fiscal_year) ? $fiscal_year : static::getFiscalYear();
        for ($i = 0; $i < 12; $i++) {
            $m = ($i < 3) ? 10 + $i : $i - 2;
            $ret[] = [
                'id' => [
                    'fiscal_year' => $fiscal_year,
                    'db_month' => $m,
                    'db_year' => $m >= 10 ? $fiscal_year - 1 : $fiscal_year,
                ],
            ];
        }
        return $ret;
    }

    public static function convertThai2Iso($date)
    {

        if ($date) {
            try {
                list($d, $m, $y) = explode('/', $date);
            } catch (Exception $e) {
                return;
            }
            return implode('-', [
                ($y - 543),
                $m,
                $d,
            ]);
        }
        return;
    }

    public static function convertIso2Thai($date)
    {
        if ($date) {
            list($y, $m, $d) = explode('-', $date);
            return implode('/', [
                $d,
                $m,
                ($y + 543),
            ]);
        }
        return;
    }

    public static function inRange($start, $end)
    {
        $date = date('Y-m-d');
        if (isset($start) && $date < $start) {
            return false;
        }
        if (isset($end) && $date > $end) {
            return false;
        }
        return true;
    }

    public static function getMonthOptions($code = null)
    {
        $ret = [
            1 => '??????????????????',
            2 => '??????????????????????????????',
            3 => '??????????????????',
            4 => '??????????????????',
            5 => '?????????????????????',
            6 => '????????????????????????',
            7 => '?????????????????????',
            8 => '?????????????????????',
            9 => '?????????????????????',
            10 => '??????????????????',
            11 => '???????????????????????????',
            12 => '?????????????????????',
        ];
        return isset($code) ? $ret[$code] : $ret;
    }

    public static function getMonthShortOptions($code = null)
    {
        $ret = [
            1 => '???.???.',
            2 => '???.???.',
            3 => '??????.???.',
            4 => '??????.???.',
            5 => '???.???.',
            6 => '??????.???.',
            7 => '???.???.',
            8 => '???.???.',
            9 => '???.???.',
            10 => '???.???.',
            11 => '???.???.',
            12 => '???.???.',
        ];
        return isset($code) ? $ret[$code] : $ret;
    }

    public static function getYearOptions($from = null, $to = null)
    {
        $from = isset($from) ? $from : date('Y');
        $to = isset($to) ? $to : date('n') >= 10 ? date('Y') + 1 : date('Y');
        if ($from <= $to) {
            $tmp = $from;
            $from = $to;
            $to = $tmp;
        }
        $ret = [];
        for ($y = $from; $y >= $to; $y--) {
            $ret[$y] = $y + 543;
        }
        return $ret;
    }

    public static function getYearRangeOptions($from, $to)
    {
        $ret = [];
        if ($from < $to) {
            for ($i = $from; $i <= $to; $i++) {
                $ret[$i] = $i + 543;
            }
        } else {
            for ($i = $from; $i >= $to; $i--) {
                $ret[$i] = $i + 543;
            }
        }
        return $ret;
    }

}
