<?php
/**
 * Created by PhpStorm.
 * User: hayuzi
 * Date: 2019/3/25
 * Time: 下午10:48
 */

namespace AppCore\BaseClass;


use AppCore\BaseClass\Exception\AppCoreException;
use Yaf\Action_Abstract;

abstract class AbstractActionBase extends Action_Abstract
{

    /**
     * @var bool 接口强制POST请求
     */
    public $forcePost = false;

    /**
     * @var bool 是否需要登陆
     */
    public $needLogin = false;

    /**
     * @var string 虚拟sessionId
     */
    public $sessionId = '';


    /**
     * @return mixed|void
     */
    final public function execute()
    {
        try {
            $this->_checkRequest();
            $this->_preExec();
            $this->_exec();
            $this->_postExec();
        } catch (AppCoreException $e) {
            // TODO Logger
            $this->_renderJson([], $e->getCode(), $e->getMessage());
        } catch (\Exception $e) {
            // TODO Logger
            $this->_renderJson([], BaseErrorCode::SERVER_ERROR, BaseErrorCode::SERVER_ERROR_MSG);
        }
    }

    /**
     * 可以继承重写这个类来统一处理请求数据
     * @throws AppCoreException
     */
    public function _checkRequest()
    {
        if ($this->forcePost && !$this->getRequest()->isPost()) {
            throw new AppCoreException(
                BaseErrorCode::REQUEST_METHOD_ERROR_MSG,
                BaseErrorCode::REQUEST_METHOD_ERROR
            );
        }
    }

    /**
     * 这个是封装的执行方法，必须要继承的类
     * @return mixed
     */
    abstract public function _exec();

    /**
     * 前置切面
     */
    public function _preExec()
    {
        // ...
    }

    /**
     * 后置切面
     */
    public function _postExec()
    {
        // ...
    }


    /**
     * 渲染成功的json数据
     * @param $retData
     */
    public function _renderSuccessJson($retData)
    {
        if (!$retData) {
            $retData = ['result' => 'success',];
        }
        $this->_renderJson($retData);
    }


    /**
     * @param $retData
     * @param int $code
     * @param string $msg
     */
    public function _renderJson($retData, $code = BaseConst::QUERY_OK_CODE, $msg = BaseConst::QUERY_OK_MSG)
    {
        $result = new ResultBase();
        $result->setCode($code);
        $result->setMsg($msg);
        $result->setData($retData);

        if (!$this->getRequest()->isCli()) {
            $this->getResponse()->setHeader('Content-Type', 'application/json;charset=utf-8');
            $this->getResponse()->setHeader('Server', 'apache/1.8.0');
            $this->getResponse()->setHeader('X-Powered-By', 'PHP');
            // TODO Logger

        }
        $this->getResponse()->setBody($result->toJson());
    }

    /**
     * 渲染行结果
     * @param $rowData
     */
    public function _renderRow($rowData)
    {
        if (!$this->getRequest()->isCli()) {
            $this->getResponse()->setHeader('Content-Type', 'application/html;charset=utf-8');
            $this->getResponse()->setHeader('Server', 'apache/1.8.0');
            $this->getResponse()->setHeader('X-Powered-By', 'PHP');
            // TODO Logger
        }
        $this->getResponse()->setBody($rowData);
    }

}