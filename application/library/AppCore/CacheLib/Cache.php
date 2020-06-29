<?php
/**
 * Created by PhpStorm.
 * User: yuzi
 * Date: 2020/6/21
 * Time: 21:27
 */

namespace AppCore\CacheLib;


use AppCore\BaseClass\Exception\AppCoreException;
use AppCore\BaseClass\BaseErrorCode;
use Yaf\Registry;

class Cache
{

    /**
     * node name
     */
    protected static $node = 'default';

    /**
     * Static instance of self
     *
     * @var Cache
     */
    protected static $instance;

    /**
     * Redis instance
     *
     * @var \Redis
     */
    protected $redis;


    /**
     * Cache constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        if (!$this->redis) {
            $this->connect();
        }
        self::$instance[static::$node] = $this;
    }

    /**
     * @throws \Exception
     */
    protected function connect()
    {
        $config      = Registry::get("config");
        $this->redis = new \Redis();
        if (isset($config->redis->{static::$node})) {
            $config = $config->redis->{static::$node}->config;
        } else {
            $config = $config->redis->config;
        }
        $res = $this->redis->connect($config->host, $config->port);

        if (false == $res) {
            // TODO Logger
            throw new AppCoreException(
                BaseErrorCode::REDIS_SERVER_ERROR_MSG,
                BaseErrorCode::REDIS_SERVER_ERROR
            );
        }

        if ($config->isauth) {
            $res = $this->redis->auth($config->auth);
            if (false == $res) {
                // TODO Logger
                throw new AppCoreException(
                    BaseErrorCode::REDIS_AUTH_ERROR_MSG,
                    BaseErrorCode::REDIS_AUTH_ERROR
                );
            }
        }
        $dbIndex = is_numeric($config->dbIndex) ? $config->dbIndex : 0;
        $this->redis->select($dbIndex);
    }

    /**
     * @return \Redis
     */
    public function getRedis()
    {
        return $this->redis;
    }


    /**
     * Close connection
     */
    public function __destruct()
    {
        if ($this->redis) {
            $this->redis->close();
        }
    }


    /**
     * 获取基础对象
     * @return self
     */
    public static function getInstance()
    {
        if (empty(self::$instance[static::$node])) {
            self::$instance[static::$node] = new self();
        }
        return self::$instance[static::$node];
    }

}