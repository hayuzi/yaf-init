<?php
/**
 * Created by PhpStorm.
 * User: yuzi
 * Date: 2019/7/25
 * Time: 10:11
 */

namespace AppCore\Common;


class Util
{

    /**
     * 获取微秒时间时间戳
     * @return float
     */
    public static function microTimeFloat()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }


    /**
     * 获取毫秒时间时间戳
     * @return int
     */
    public static function milliTimeInt()
    {
        return round(self::microTimeFloat() * 1000);
    }


    /**
     * 驼峰转蛇形
     * @param $str
     * @return string
     */
    public static function camel2snake($str)
    {
        return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $str), "_"));
    }


    /**
     * 蛇形转大驼峰
     * @param $str
     * @return string
     */
    public static function snake2ucFirstCamel($str)
    {
        return ucfirst(
            preg_replace_callback('/_([a-zA-Z])/', function ($match) {
                return strtoupper($match[1]);
            }, $str)
        );
    }


    /**
     * 蛇形转小驼峰
     * @param $str
     * @return string
     */
    public static function snake2camel($str)
    {
        return preg_replace_callback(
            '/_([a-zA-Z])/',
            function ($match) {
                return strtoupper($match[1]);
            },
            $str
        );
    }


    /**
     * 敏感数据日志脱敏
     *
     * @param $data
     * @param array $fields
     * @return mixed
     */
    public static function convertToSafeLogData(array $data, array $fields = ['pwd', 'password'])
    {
        foreach ($fields as $v) {
            if (isset($data[$v])) {
                $data[$v] = '***';
            }
        }
        return $data;
    }


    /**
     * 将所有key转换为驼峰形式
     * @param array $data
     * @return array
     */
    public static function recursiveKeys2Camel(array $data = [])
    {
        $temp = [];
        foreach ($data as $k => $v) {
            $kCamel = self::snake2Camel($k);
            if (is_array($v)) {
                $tempV = self::recursiveKeys2Camel($v);
            } else {
                $tempV = $v;
            }
            $temp[$kCamel] = $tempV;
        }
        return $temp;
    }


    /**
     * 将所有驼峰形式的key转为蛇形
     * @param array $data
     * @return array
     */
    public static function recursiveKeys2Snake(array $data = [])
    {
        $temp = [];
        foreach ($data as $k => $v) {
            $kCamel = self::camel2snake($k);
            if (is_array($v)) {
                $tempV = self::recursiveKeys2Camel($v);
            } else {
                $tempV = $v;
            }
            $temp[$kCamel] = $tempV;
        }
        return $temp;
    }



}