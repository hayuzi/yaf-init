<?php
/**
 * Created by PhpStorm.
 * User: hayuzi
 * Date: 2019/2/28
 * Time: 下午11:25
 */

namespace Utils;


class StringFmt
{

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
     * 蛇形转驼峰
     * @param $str
     * @return string
     */
    public static function snake2camel($str)
    {
        return ucfirst(
            preg_replace_callback('/_([a-zA-Z])/', function ($match) {
                return strtoupper($match[1]);
            }, $str)
        );
    }

}