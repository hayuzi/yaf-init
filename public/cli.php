<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2017/12/12
 * Time: 23:25
 */


define('APPLICATION_PATH', dirname(__FILE__) . '/../');

$application = new Yaf_Application( APPLICATION_PATH . "/conf/application.ini");


// 添加自动加载本地类的代码 2017-12-23
$loader = Yaf_Loader::getInstance();
$loader->registerLocalNamespace(array("Local"));

$application->getDispatcher()->dispatch(new Yaf_Request_Simple());