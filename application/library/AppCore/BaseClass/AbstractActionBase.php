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
     * @return mixed|void
     */
    final public function execute()
    {
        try {
            $this->_initRequest();
            $this->_preExec();
            $this->_exec();
            $this->_postExec();
        } catch (AppCoreException $e) {

        } catch (\Exception $e) {

        }
    }

    /**
     * @throws AppCoreException
     */
    public function _initRequest()
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


    public function _preExec()
    {
        // ...
    }

    public function _postExec()
    {
        // ...
    }

}