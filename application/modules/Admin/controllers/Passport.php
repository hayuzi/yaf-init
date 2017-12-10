<?php
/**
 * @name PassportController
 * @author lenovo-pc\lenovo
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class PassportController extends Yaf_Controller_Abstract {

    /**
     * 默认动作
     * Yaf支持直接把Yaf_Request_Abstract::getParam()得到的同名参数作为Action的形参
     * 对于如下的例子, 当访问http://yourhost/sample/index/index/index/name/lenovo-pc\lenovo 的时候, 你就会发现不同
     */
    public function indexAction($name = "TestTheModules") {
        //1. fetch query
        $get = $this->getRequest()->getQuery("get", "default value");

        //2. fetch model
        $model = new SampleModel();

        echo  '12212312';

        //3. assign
        $this->getView()->assign("content", 'hjehhehhda');
        $this->getView()->assign("name", $name);

        //4. render by Yaf, 如果这里返回FALSE, Yaf将不会调用自动视图引擎Render模板
        return TRUE;
    }


    public function loginAction(){
        echo 'hahahahhaa';
        return false;
    }

}
