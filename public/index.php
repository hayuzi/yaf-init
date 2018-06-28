<?php
/**
 * 框架的官方文档
 * http://www.laruence.com/manual/index.html
 * http://php.net/manual/zh/book.yaf.php
 *
 * github
 * https://github.com/laruence/yaf
 */

date_default_timezone_set('Asia/Shanghai');

define('APPLICATION_PATH', dirname(__DIR__));
define('PHP_FPM_ENV', TRUE);

$iniPath = APPLICATION_PATH . "/conf/application.ini";
$config = parse_ini_file($iniPath, true);
$errLevel = isset($config['common']['errorLevel']) ? $config['common']['errorLevel'] : 0;
error_reporting($errLevel);

require APPLICATION_PATH . '/vendor/autoload.php';

$application = new \Yaf\Application( $iniPath);
$application->bootstrap()->run();

