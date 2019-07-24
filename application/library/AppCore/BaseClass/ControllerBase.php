<?php
/**
 * Created by PhpStorm.
 * User: hayuzi
 * Date: 2019/2/28
 * Time: 下午10:46
 */

namespace AppCore\BaseClass;

use Yaf\Controller_Abstract;

class ControllerBase extends Controller_Abstract
{

    /**
     * 初始化方法： yaf 禁止了 __construct被重写, 但是可以使用 init() 方法来初始化
     */
    public function init()
    {
        // 此处写自己的初始化处理逻辑
    }

}