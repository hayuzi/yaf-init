<?php
/**
 * Created by PhpStorm.
 * User: yuzi
 * Date: 2019/7/24
 * Time: 11:13
 */

use AppCore\BaseClass\CliControllerBase;

/**
 * 测试控制器
 * Class TestController
 */
class TestController extends CliControllerBase
{

    public function testAction()
    {
        $name = $this->getRequest()->getParam('name');
        echo "testAction name: {$name}\n";
        return false;
    }

}