<?php
/**
 * Created by PhpStorm.
 * User: yuzi
 * Date: 2018/8/31
 * Time: 上午10:38
 */

namespace AppCore\DbLib;

use PDO;
use Medoo\Medoo;
use Yaf\Registry;

class Connection
{

    /**
     * @var array 数据库集合
     */
    protected static $databases = [];

    /**
     * constructor.
     */
    protected function __construct()
    {

    }

    /**
     * 获取Medoo实例
     *  https://medoo.in 需要composer安装 composer require catfan/medoo
     *  https://github.com/catfan/Medoo
     * @param string $db
     * @param null $options
     * @return Medoo|mixed
     */
    public static function getInstance($db = 'default_db', $options = null)
    {
        if (!isset(self::$databases[$db])) {
            $config = Registry::get('config');
            if (is_null($options)) {
                $options    = [
                    'database_type'     => 'mysql',
                    'database_name'     => $config->database->params->dbname,
                    'server'            => $config->database->params->host,
                    'username'          => $config->database->params->user,
                    'password'          => $config->database->params->password,
                    //
                    'charset'           => $config->database->params->charset,
                    'port'              => $config->database->params->prot,
                    'prefix'            => '',      // [optional] Table prefix
                    'logging'           => true,   // [optional] Enable logging (Logging is disabled by default for better performance)
                    'option'            => [
                        PDO::ATTR_EMULATE_PREPARES      => false,   // 禁用本地预处理模拟
                        PDO::ATTR_STRINGIFY_FETCHES     => false,   // 数据不转字符串
                    ],
                ];
            }
            self::$databases[$db] = new Medoo($options);
        }
        return self::$databases[$db];
    }

}