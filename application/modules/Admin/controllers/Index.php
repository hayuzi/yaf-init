<?php
/**
 * @name IndexController
 * @author lenovo-pc\lenovo
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class IndexController extends \Yaf\Controller_Abstract {


    /**
     * 使用actions映射的方式获取action
     * @var array
     */
    public $actions = [
        'test' => 'modules/Admin/actions/Index/Test.php',
    ];

}
