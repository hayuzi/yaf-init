<?php
/**
 * @name ErrorController
 * @desc 错误控制器, 在发生未捕获的异常时刻被调用
 * @see http://www.php.net/manual/en/yaf-dispatcher.catchexception.php
 * @author lenovo-pc\lenovo
 */
class ErrorController extends Yaf_Controller_Abstract {



    /**
     * 自定义异常处理逻辑
     * @param $exception mixed 从2.1开始, errorAction支持直接通过参数获取异常
     * @author yuzi
     * @datetime 2017-12-12 23:39
     */
	public function errorAction($exception) {
		//1. assign to view engine
		// $this->getView()->assign("exception", $exception);
		//5. render by Yaf

        // 自定义异常处理方式
        try {
            throw $exception;
        } catch (Yaf_Exception_LoadFailed $e) {
            //加载失败
            // 此处可以自定义逻辑 ( 暂时只是复用, 后期可以按照自己定义的规则处理 )
            $this->getView()->assign("exception", $e);
        } catch (Yaf_Exception $e) {
            //其他错误
            $this->getView()->assign("exception", $e);
        }
	}
}
