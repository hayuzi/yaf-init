<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2017/12/12
 * Time: 23:25
 */


define('APPLICATION_PATH', dirname(__FILE__) . '/../');

$application = new \Yaf\Application( APPLICATION_PATH . "/conf/application.ini");


// 添加自动加载本地类的代码 2017-12-23
$loader = \Yaf\Loader::getInstance();
$loader->registerLocalNamespace(array("Local"));


$application->getDispatcher()->dispatch(new \Yaf\Request\Simple());

// 命令行的格式如下(在项目根目录)
// php ./public/cli.php  "request_uri=/admin/passport/index"