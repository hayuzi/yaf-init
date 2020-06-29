<?php
/**
 * Created by PhpStorm.
 * User: yuzi
 * Date: 2018/7/27
 * Time: 上午11:37
 */

namespace AppCore\Common;


class EnvInfo
{

    /**
     * 唯一请求ID
     * @var string
     */
    protected static $requestId = '';

    /**
     * @var string
     */
    protected static $hostName  = '';

    /**
     * @var bool
     */
    protected static $isCli = null;

    /**
     * @return mixed
     */
    public static function getHostName()
    {
        if (!self::$hostName) {
            if (!empty($_SERVER['HOSTNAME'])) {
                $hostName       = $_SERVER['HOSTNAME'];
            } elseif ($hostName = gethostname()) {
                //get hostname
            } elseif ($hostName = php_uname('n')) {
                //get hostname
            }
            self::$hostName     = $hostName ?: 'unknown_hostname';
        }
        return self::$hostName;
    }

    /**
     * @return mixed
     */
    public static function getHostIp()
    {
        return isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '';
    }


    /**
     * 获取唯一请求ID
     * @return string
     */
    public static function getRequestId()
    {
        if (!self::$requestId) {
            $requestId = '';
            if (!empty($_REQUEST['request_id'])) {
                $requestId = $_REQUEST['request_id'];
            } elseif (!empty($_SERVER['X_REQUEST_ID'])) {
                $requestId = $_SERVER['X_REQUEST_ID'];
            }

            self::$requestId = $requestId ?: md5(uniqid('request_id', true) . mt_rand(10000,99999));
        }

        return self::$requestId;
    }


    /**
     * @return mixed
     */
    public static function getRequestUri()
    {
        if (self::isCliType()) {
            return isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : '/';
        } else {
            return explode('?', $_SERVER['REQUEST_URI'])[0];
        }
    }


    /**
     * @return mixed
     */
    public static function getRequestMethod()
    {
        return isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'cli';
    }

    /**
     * @return bool
     */
    public static function isCliType()
    {
        if (is_null(self::$isCli)) {
            self::$isCli = stripos(php_sapi_name(), 'cli') !== false ? true : false;
        }
        return self::$isCli;
    }


}