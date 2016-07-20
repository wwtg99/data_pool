<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/7/20
 * Time: 11:14
 */

namespace Wwtg99\DataPool\Utils;


class FieldFormatter
{

    /**
     * @param string $datetime
     * @param string $format
     * @return string
     */
    public static function formatDateTimeField($datetime = null, $format = 'Y-m-d H:i:s')
    {
        if (is_null($datetime)) {
            return $datetime;
        }
        if ($datetime) {
            $dt = new \DateTime($datetime);
        } else {
            $dt = new \DateTime();
        }
        return $dt->format($format);
    }

    /**
     * @param array $data
     * @param string|array $dateFields
     * @param string $dateFormat
     * @return array
     */
    public static function formatDateTime(array &$data, $dateFields = '/^\w+_at$/', $dateFormat = 'Y-m-d H:i:s')
    {
        $re = array_walk_recursive($data, function(&$item, $key) use ($dateFields, $dateFormat) {
            if (is_null($item)) {
                return;
            }
            //format date
            if (is_array($dateFields)) {
                if (in_array($key, $dateFields)) {
                    $item = self::formatDateTimeField($item, $dateFormat);
                }
            } else {
                if (preg_match($dateFields, $key)) {
                    $item = self::formatDateTimeField($item, $dateFormat);
                }
            }
        });
        return $data;
    }

    /**
     * @param $number
     * @param int $precision
     * @param int $mode
     * @return float
     */
    public static function formatNumberField($number, $precision = 0, $mode = PHP_ROUND_HALF_UP)
    {
        if (is_numeric($number)) {
            return round($number, $precision, $mode);
        }
        return $number;
    }

    /**
     * @param array $data
     * @param string|array $numFields
     * @param int $precision
     * @param int $mode
     * @return array
     */
    public static function formatNumber(array &$data, $numFields = '/^num$/', $precision = 0, $mode = PHP_ROUND_HALF_UP)
    {
        $re = array_walk_recursive($data, function(&$item, $key) use ($numFields, $precision, $mode) {
            if (is_null($item)) {
                return;
            }
            //format date
            if (is_array($numFields)) {
                if (in_array($key, $numFields)) {
                    $item = self::formatNumberField($item, $precision, $mode);
                }
            } else {
                if (preg_match($numFields, $key)) {
                    $item = self::formatNumberField($item, $precision, $mode);
                }
            }
        });
        return $data;
    }

    /**
     * @param array $data
     * @param array $setting
     * @return array
     */
    public static function formatFields(&$data, $setting)
    {
        $re = array_walk_recursive($data, function(&$item, $key, $settings) {
            if (is_null($item)) {
                return;
            }
            //format date
            if (isset($settings['format_datetime'])) {
                $dt = $settings['format_datetime'];
                $dateFields = isset($dt['fields']) ? $dt['fields'] : '/^\w+_at$/';
                $dateFormat = isset($dt['format']) ? $dt['format'] : 'Y-m-d H:i:s';
                if (is_array($dateFields)) {
                    if (in_array($key, $dateFields)) {
                        $item = self::formatDateTimeField($item, $dateFormat);
                    }
                } else {
                    if (preg_match($dateFields, $key)) {
                        $item = self::formatDateTimeField($item, $dateFormat);
                    }
                }
            }
            //format number
            if (isset($settings['format_number'])) {
                $nt = $settings['format_number'];
                $numFields = isset($nt['fields']) ? $nt['fields'] : '/^num$/';
                $precision = isset($nt['precision']) ? intval($nt['precision']) : 0;
                $mode = isset($nt['mode']) ? intval($nt['mode']) : PHP_ROUND_HALF_UP;
                if (is_array($numFields)) {
                    if (in_array($key, $numFields)) {
                        $item = self::formatNumberField($item, $precision, $mode);
                    }
                } else {
                    if (preg_match($numFields, $key)) {
                        $item = self::formatNumberField($item, $precision, $mode);
                    }
                }
            }
        }, $setting);
        return $data;
    }

}