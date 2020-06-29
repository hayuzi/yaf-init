<?php

namespace AppCore\Common;


use AppCore\LogLib\Logger;
use Yaf\Registry;

class HttpClient
{

    const  API_SERVER = 'http://localhost';//'http://cron.wx.jaeapp.com';

    /**
     * @var array
     */
    private $_responses = array();


    public function __construct()
    {
    }


    /**
     * @param $request
     * @return mixed
     */
    protected static function _initPreRequestId($request)
    {
        $requestId = EnvInfo::getRequestId();

        if (!isset($request['data'])) {
            $request['data'] = [
                'preRequestId' => $requestId,
            ];
        } elseif (is_array($request['data'])) {
            $request['data']['preRequestId'] = $requestId;
        }

        return $request;
    }


    /**
     * @return mixed
     */
    public static function getInternalApiServer()
    {
        $config = Registry::get("config");
        return $config->hostname->internal;
    }

    /**
     * @param $data
     * @param $delay
     * @return mixed
     */
    public static function callback($data, $delay)
    {
        usleep($delay);
        return $data;
    }


    /**
     * @param $ch
     * @param $request
     * @param int $timeout
     * @param $headers
     */
    public static function PackageGetRequest(&$ch, $request, $timeout = 10, $headers = [])
    {
        $request        = self::_initPreRequestId($request);
        $path           = http_build_query($request['data']);
        $url            = isset($request['host']) ? $request['host'] : self::getInternalApiServer();
        $request['url'] .= '?' . $path;

        curl_setopt($ch, CURLOPT_URL, $url . $request['url']);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_NOSIGNAL, true);
        if ($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
    }


    /**
     * @param $ch
     * @param $request
     * @param int $timeout
     * @param $headers
     */
    public static function PackagePostRequest(&$ch, $request, $timeout = 10, $headers = [])
    {
        $request = self::_initPreRequestId($request);
        $url     = isset($request['host']) ? $request['host'] : self::getInternalApiServer();

        curl_setopt($ch, CURLOPT_URL, $url . $request['url']);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_NOSIGNAL, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request['data']);
        if ($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
    }


    /**
     * @param $instance
     */
    public function processMultiResult($instance)
    {
        $this->_responses[$instance->id] = $instance->response;
    }


    /**
     * @return array
     */
    public function getResponse()
    {
        ksort($this->_responses);
        return $this->_responses;
    }


    /**
     * @param $request
     * @param int $retry
     * @param int $timeout
     * @param array $headers
     * @return bool|mixed
     */
    public static function Call($request, $retry = 1, $timeout = 10, $headers = [])
    {
        $ch    = curl_init();
        $start = Util::milliTimeInt();
        switch ($request['method']) {
            case 'get':
            case 'GET':
                self::PackageGetRequest($ch, $request, $timeout, $headers);
                break;
            case 'post':
            case 'POST':
                self::PackagePostRequest($ch, $request, $timeout, $headers);
                break;
            default:
                break;
        }

        $retryTimes = 0;
        $response   = false;
        while ($retryTimes < $retry) {
            $response = curl_exec($ch);
            $status   = curl_getinfo($ch);


            $cost = Util::milliTimeInt() - $start;
            Logger::getInstance()->netrcd($request, $response, $cost);
            curl_close($ch);

            if (intval($status["http_code"]) != 200) {
                $response = false;
            } else {
                break;
            }

            sleep(pow(2, $retryTimes));
            ++$retryTimes;
        }

        return $response;
    }


    /**
     * @param $requests
     * @param int $delay
     * @return array
     */
    public static function MultiCall($requests, $delay = 0)
    {

        $queue = curl_multi_init();
        $map   = array();

        foreach ($requests as $reqId => $request) {
            if (false == isset($request['data']) || false == is_array($request['data'])) {
                $request['data'] = array();
            }
            $ch = curl_init();
            switch ($request['method']) {
                case 'get':
                case 'GET':
                    self::PackageGetRequest($ch, $request);
                    break;
                case 'post':
                case 'POST':
                    self::PackagePostRequest($ch, $request);
                    break;
                default:
                    break;
            }
            // self::PackageGetRequest( $ch, $request );
            curl_multi_add_handle($queue, $ch);
            $map[(string)$ch] = $request['key'];
        }

        $responses = array();
        $start     = Util::milliTimeInt();

        do {
            while (($code = curl_multi_exec($queue, $active)) == CURLM_CALL_MULTI_PERFORM) ;

            if ($code != CURLM_OK) {
                break;
            }

            // a request was just completed -- find out which one
            while ($done = curl_multi_info_read($queue)) {

                // get the info and content returned on the request
                $info    = curl_getinfo($done['handle']);
                $error   = curl_error($done['handle']);
                $results = curl_multi_getcontent($done['handle']);

                if (empty($error)) {
                    $responses[$map[(string)$done['handle']]] = json_decode($results, true);
                } else {
                    $responses[$map[(string)$done['handle']]] = compact('info', 'error', 'results');
                }
                // remove the curl handle that just completed
                curl_multi_remove_handle($queue, $done['handle']);
                curl_close($done['handle']);
            }

            // Block for data in / output; error handling is done by curl_multi_exec
            if ($active > 0) {
                curl_multi_select($queue, 0.5);
            }
        } while ($active);

        curl_multi_close($queue);

        $cost = Util::milliTimeInt() - $start;
        Logger::getInstance()->netrcd($requests, $responses, $cost);
        // ksort( $responses );

        return $responses;
    }


    /**
     * raw格式消息发送
     * @param $url
     * @param $data_string
     * @return mixed
     */
    public static function rawCurl($url, $data_string)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
}