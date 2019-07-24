<?php
/**
 * 框架的官方文档
 * http://www.laruence.com/manual/index.html
 * http://php.net/manual/zh/book.yaf.php
 *
 * github
 * https://github.com/laruence/yaf
 */


define('APPLICATION_PATH', dirname(__DIR__));

$application = new \Yaf\Application( APPLICATION_PATH . "/conf/application.ini");

// composer自动加载
require APPLICATION_PATH . '/vendor/autoload.php';


/**
 * 命令行路由分发（ 请不要使用，注释下有替代方案 ）
 *      \Yaf\Request\Simple()不传递参数的时候, 会默认去寻找一个字符串参数，并解析，格式如下
 *              php ./public/cli.php  "request_uri=/admin/passport/index"
 *      如果我们想要额外解析参数，或者解析自定义的路由方式，我们需要获取到命令行执行的参数然后去带参数实例话请求类
 *      目前这个方法直接分发路由，不会走Bootstrap.php, 所以我们在Bootstrap中加载的插件都不可用
 *      即便使用 $app->execute("callback") 的方式也不太方便。
 * 替代方案如下：
 *      改造index.php入口的路由
 *      在Bootstrap.php中注册一个解析cli参数的路由，从$_SERVER中获取参数来解析
 */
$application->getDispatcher()->dispatch(new \Yaf\Request\Simple());
