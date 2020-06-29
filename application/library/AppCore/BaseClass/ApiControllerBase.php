<?php
/**
 * Created by PhpStorm.
 * User: hayuzi
 * Date: 2019/2/28
 * Time: 下午10:46
 */

namespace AppCore\BaseClass;

use Yaf\Controller_Abstract;
use Yaf\Dispatcher;
use Yaf\Registry;

class ControllerBase extends Controller_Abstract
{

    /**
     * 初始化方法： yaf 禁止了 __construct被重写, 但是可以使用 init() 方法来初始化
     */
    public function init()
    {
        // 此处写自己的初始化处理逻辑
        Dispatcher::getInstance()->disableView();
        if (defined('PHP_FPM_ENV')) {
            $config = Registry::get("config");
            $this->getResponse()->setHeader('Access-Control-Allow-Headers', $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'] ?? 'Content-Type');
            if (false == empty($_SERVER['HTTP_ORIGIN']) && preg_match($config->hostname->h5->accessRegex, $_SERVER['HTTP_ORIGIN'])) {
                $this->getResponse()->setHeader('Access-Control-Allow-Origin', $_SERVER['HTTP_ORIGIN']);
                $this->getResponse()->setHeader('Access-Control-Allow-Credentials', 'true');
            } elseif (isset($config->dynamic_access_control) && $config->dynamic_access_control == 'open') {
                $this->getResponse()->setHeader('Access-Control-Allow-Origin', isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*');
                $this->getResponse()->setHeader('Access-Control-Allow-Credentials', 'true');
            }
        }
    }


}