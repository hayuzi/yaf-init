<?php

use AppCore\BaseClass\BaseErrorCode;
use AppCore\BaseClass\Exception\AppCoreException;
use AppCore\BaseClass\ResultBase;

/**
 * @name ErrorController
 * @desc 错误控制器, 在发生未捕获的异常时刻被调用
 * @see http://www.php.net/manual/en/yaf-dispatcher.catchexception.php
 * @author lenovo-pc\lenovo
 */
class ErrorController extends \Yaf\Controller_Abstract
{

    //从2.1开始, errorAction支持直接通过参数获取异常
    public function errorAction($exception)
    {
        if ($exception instanceof AppCoreException) {
            $this->renderJson([], $exception->getCode(), $exception->getMessage());
        } elseif ($exception instanceof Yaf\Exception\LoadFailed\Module) {
            $this->renderJson([], $exception->getCode(), BaseErrorCode::SERVER_ERROR_MSG . ' 1');
        } elseif ($exception instanceof Yaf\Exception\LoadFailed\Controller) {
            $this->renderJson([], $exception->getCode(), BaseErrorCode::SERVER_ERROR_MSG . ' 2');
        } elseif ($exception instanceof Yaf\Exception\LoadFailed\Action) {
            $this->renderJson([], $exception->getCode(), BaseErrorCode::SERVER_ERROR_MSG . ' 3');
        } elseif ($exception instanceof Exception) {
            $this->renderJson([], $exception->getCode(), $exception->getMessage());
        }
    }


    /**
     * 渲染结果
     * @param $data
     * @param int $code
     * @param string $msg
     */
    private function renderJson($data, $code = BaseErrorCode::SERVER_ERROR, $msg = BaseErrorCode::SERVER_ERROR_MSG)
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: HEAD, POST, GET, OPTIONS, DELETE');
        header('Access-Control-Allow-Credentials: true');
        header('Content-Type: application/json;charset=utf-8');
        $result = new ResultBase();
        $result->setCode($code);
        $result->setMsg($msg);
        $result->setData($data);
        $this->getResponse()->setBody($result->toJson());
    }
}
