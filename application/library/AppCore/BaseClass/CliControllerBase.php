<?php
/**
 * Created by PhpStorm.
 * User: hayuzi
 * Date: 2019/2/28
 * Time: 下午10:46
 */

namespace AppCore\BaseClass;

use Yaf\Exception;

/**
 * 脚本控制器需要继承该基础类
 *
 * Class CliControllerBase
 * @package AppCore\BaseClass
 */
class CliControllerBase extends ControllerBase
{

    /**
     * 控制台脚本运行的控制器初始化方法
     * @throws Exception
     */
    public function init()
    {
        if (php_sapi_name() !== 'cli') {
            throw new Exception('无访问权限', 403);
        }
    }

}