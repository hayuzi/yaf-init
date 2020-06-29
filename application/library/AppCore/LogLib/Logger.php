<?php
/**
 * Created by PhpStorm.
 * User: yuzi
 * Date: 2020/6/21
 * Time: 21:30
 */

namespace AppCore\LogLib;

use AppCore\Common\EnvInfo;
use Yaf\Registry;

class Logger
{


    /**
     * log params data
     * @var array
     */
    private $_logParams = array();


    /**
     * common params for error/debug/netrcd log
     * @var array
     */
    private static $_logCommonParams = array();

    /**
     * file log type
     * @var integer
     */
    static private $_fileLog = 3;

    /**
     * Path to the log file
     * @var string
     */
    static private $_logFilePath = '/tmp/api.log.';

    private static $arrInstance = array();

    public static $currentInstance;


    /**
     * Logger constructor.
     * @param string $logPath
     */
    public function __construct($logPath = '/tmp/api.log.')
    {
        self::$_logFilePath = $logPath;
    }


    /**
     * 获取指定App的log对象，默认为当前App
     *
     * @param null $app
     * @return Logger
     */
    public static function getInstance($app = null)
    {
        if (empty($app)) {
            $app = 'appApi';
        }
        if (empty(self::$arrInstance[$app])) {
            $config                  = Registry::get("config");
            self::$arrInstance[$app] = new Logger($config->log->path);
        }
        return self::$arrInstance[$app];
    }

    /**
     * [addNotice 添加日志字段]
     * @param string $logKey 日志字段
     * @param array/string  $logValue   打印值
     */
    public function addNotice($logKey, $logValue)
    {
        $this->_logParams[$logKey] = $logValue;
    }


    /**
     * [notice 打印一般日志]
     */
    public function notice()
    {
        $this->general('access', 'access', 0, $this->_logParams);
    }

    /**
     * [error 打印错误日志]
     *
     * @param string $message
     * @param int $code
     * @param array $data
     */
    public function error($message, $code = 0, array $data = [])
    {
        $this->general('error', $message, $code, $data);
    }

    /**
     * [netrcd 打印网络日志]
     * @param $request
     * @param $response
     * @param $cost
     */
    public function netrcd($request, $response, $cost)
    {
        $logData = array(
            'request'  => $request,
            'response' => $response,
            'cost'     => $cost,
        );

        $this->general('netrcd', 'net record', 0, $logData);
    }


    /**
     * [trace 打印调试日志]
     * @param string $message
     * @param array $data
     */
    public function trace($message, array $data = [])
    {
        $this->general('debug', $message, 0, $data);
    }


    /**
     * [tempLog 打印临时调试日志]
     * @param string $message
     * @param array $data
     */
    public function tempLog($message, array $data = [])
    {
        $this->general('temp', $message, 0, $data);
    }

    /**
     * [general 根据业务打印日志]
     *
     * @param $logType
     * @param $message
     * @param $code
     * @param $data
     */
    public function general($logType, $message, $code = 0, $data = [])
    {
        $params         = [
                'time' => date('Y-m-d H:i:s'),
                'type' => $logType,
                'code' => $code,
                'msg'  => $message,
            ] + self::getLogCommonParams();
        $params['data'] = $data;

        $logContent = json_encode($params, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;
        error_log($logContent, self::$_fileLog, self::$_logFilePath . $logType . '.' . date('Ymd'));
    }


    /**
     * [DB Query SQL 日志]
     * @param string $queryName 查询名称
     * @param string $querySql 查询SQL
     * @param int $affectedRow 影响的行数
     */
    public function dbQuery($queryName, $querySql, $affectedRow = 0)
    {
        $data = [
            'query_name'   => $queryName,
            'query_sql'    => $querySql,
            'affected_row' => $affectedRow,
        ];
        $this->general('dbquery', $queryName, 0, $data);
    }


    /**
     * @return array
     */
    private static function getLogCommonParams()
    {
        if (!self::$_logCommonParams) {
            self::$_logCommonParams['request_id'] = EnvInfo::getRequestId();
            self::$_logCommonParams['uri']        = EnvInfo::getRequestUri();
            self::$_logCommonParams['host_name']  = EnvInfo::getHostName();
            self::$_logCommonParams['host_ip']    = EnvInfo::getHostIp();
            self::$_logCommonParams['project']    = 'api';
        }
        return self::$_logCommonParams;
    }

}