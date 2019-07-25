<?php
/**
 * Created by PhpStorm.
 * User: yuzi
 * Date: 2019/7/25
 * Time: 10:11
 */

namespace Utils;


class TimeUtil
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

}