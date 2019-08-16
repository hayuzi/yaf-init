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
use Yaf\Registry;

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
     * 不使用 Yaf\Session, 借助redis实现分布式登陆管理
     * @var string 虚拟sessionId
     */
    protected $sessionId;

    /**
     * 是否开启session
     */
    protected $sessionStart = true;


    /**
     * @return mixed|void
     */
    final public function execute()
    {
        try {
            $this->_checkRequest();
            $this->_init();
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
     * ==============================================
     *
     * 以下区域是请求处理相关方法
     *
     * ==============================================
     */


    /**
     * 可以继承重写这个方法来统一处理请求数据
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
     * 从COOKIE中获取sessionId
     *
     * @return string
     * @throws AppCoreException
     */
    public function _getSessionId()
    {
        if (!$this->sessionStart) {
            throw new AppCoreException(
                BaseErrorCode::VIRTUAL_SESSION_NOT_START_MSG,
                BaseErrorCode::VIRTUAL_SESSION_NOT_START
            );
        }
        if (is_null($this->sessionId)) {
            $this->sessionId =
                isset($_COOKIE[BaseConst::DEFAULT_SESSION_KEY]) ?
                    $_COOKIE[BaseConst::DEFAULT_SESSION_KEY] :
                    '';
        }
        return $this->sessionId;
    }


    /**
     * ==============================================
     *
     * 以下区域是执行处理的相关方法
     *
     * ==============================================
     */


    /**
     * 这个是封装的执行方法，必须要继承的类
     * @return mixed
     */
    abstract public function _exec();


    public function _init()
    {
        if ($this->sessionStart && empty($_COOKIE[BaseConst::DEFAULT_SESSION_KEY])) {
            // 初始化SESSIONID
            $sessionId = session_create_id('app_core');
            setcookie(
                BaseConst::DEFAULT_SESSION_KEY,
                $sessionId,
                time() + BaseConst::THIRTY_DAYS_SECONDS,
                '/',
                Registry::get('config')->cookieDomain
            );
        }
    }


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
     * ==============================================
     *
     * 以下区域是结果渲染相关方法
     *
     * ==============================================
     */


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