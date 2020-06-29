<?php
/**
 * Created by PhpStorm.
 * User: yuzi
 * Date: 2019/7/24
 * Time: 11:13
 */

use AppCore\BaseClass\CliControllerBase;
use AppCore\Concurrency\Sample\SampleReactor;

/**
 * 测试控制器
 * Class TestController
 */
class TestController extends CliControllerBase
{

    /**
     * 测试脚本
     * @return bool
     */
    public function testAction()
    {
        $name = $this->getRequest()->getParam('name');
        echo "testAction name: {$name}\n";
        return false;
    }


    /**
     * 多进程并发测试，使用 pcntl
     */
    public function conAction()
    {
        $reactor = new SampleReactor(false);
        $reactor->run();
        return false;
    }

}