<?php
/**
 * Created by PhpStorm.
 * User: hayuzi
 * Date: 2019/3/25
 * Time: 下午10:48
 */

namespace AppCore\BaseClass;

use AppCore\BaseClass\Exception\AppCoreException;
use AppCore\Common\Util;
use AppCore\LogLib\Logger;
use Yaf\Action_Abstract;
use Yaf\Registry;

abstract class AbstractActionBase extends Action_Abstract
{

    /**
     * @var bool 接口强制POST请求
     */
    protected $forcePost = false;

    /**
     * @var bool 是否需要登陆
     */
    protected $needLogin = false;

    /**
     * 不使用 Yaf\Session, 借助redis实现分布式登陆管理
     * @var string 虚拟sessionId
     */
    protected $sessionId;

    /**
     * 是否开启session
     */
    protected $sessionStart = false;


    /**
     * @var array
     */
    protected $requestData = [];


    /**
     * @var array
     */
    protected $logSecretFields = [
        'pwd',
        'password',
    ];


    /**
     * @var int
     */
    protected $actStart = 0;


    /**
     * @return mixed|void
     */
    final public function execute()
    {
        try {
            $this->actStart = Util::milliTimeInt();
            $this->_intRequestParams();
            $this->_checkRequest();
            $this->_init();
            $this->_preExec();
            $this->_exec();
            $this->_postExec();
        } catch (AppCoreException $e) {
            Logger::getInstance()->error($e->getMessage(), $e->getCode());
            $this->_renderJson([], $e->getCode(), $e->getMessage());
        } catch (\Exception $e) {
            Logger::getInstance()->error($e->getMessage(), $e->getCode());
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
     * 初始化请求数据
     */
    public function _intRequestParams()
    {
        // 兼容application/json类型的row请求
        $rowParams = [];
        if (isset($_SERVER['HTTP_CONTENT_TYPE']) && $_SERVER['HTTP_CONTENT_TYPE'] == 'application/json') {
            $row = file_get_contents('php://input');
            if (!empty($row)) {
                $tmpData = json_decode($row, true);
                if ($tmpData) {
                    $rowParams = $tmpData;
                }
            }
        };
        // get与post组合
        $query             = $this->getRequest()->getQuery();
        $post              = $this->getRequest()->getPost();
        $params            = $this->getRequest()->getParams();
        $this->requestData = array_merge($query, $post, $params, $rowParams);
    }


    /**
     * 获取全部请求数据
     * @param array $fields
     * @return array
     */
    public function getRequestParams(array $fields = [])
    {
        if (empty($fields)) {
            return $this->requestData;
        }
        $params = [];
        foreach ($fields as $k => $v) {
            if (is_string($k)) {
                switch ($v) {
                    case 'int':
                        $params[$k] = (int)($this->requestData[$k] ?? 0);
                        break;
                    case 'float':
                        $params[$k] = (float)($this->requestData[$k] ?? 0);
                        break;
                    case 'string':
                        $params[$k] = $this->requestData[$k] ?? '';
                        break;
                    case 'array':
                        $params[$k] = (array)($this->requestData[$k] ?? []);
                        break;
                }
            } else {
                $params[$v] = $this->requestData[$v] ?? '';
            }
        }
        return $params;
    }


    /**
     * 获取单个请求数据
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function getRequestParam($key, $default = null)
    {
        return $this->requestData[$key] ?? $default;
    }


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
            $sessionId = session_create_id('app');
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
    public function _renderSuccessJson($retData = [])
    {
        if (!$retData) {
            $retData = ['result' => 'success',];
        }
        $retData = Util::recursiveKeys2Camel($retData);
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

            $serverArr = [
                'HTTP_REFERER'         => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
                'HTTP_USER_AGENT'      => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
                'HTTP_X_REAL_IP'       => isset($_SERVER['HTTP_X_REAL_IP']) ? $_SERVER['HTTP_X_REAL_IP'] : '',
                'HTTP_CLIENT_IP'       => isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : '',
                'HTTP_X_FORWARDED_FOR' => isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : '',
                'REMOTE_ADDR'          => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '',
            ];
            Logger::getInstance()->addNotice('server', $serverArr);
            Logger::getInstance()->addNotice('get', Util::convertToSafeLogData($_GET, $this->logSecretFields));
            Logger::getInstance()->addNotice('post', Util::convertToSafeLogData($_POST, $this->logSecretFields));
            Logger::getInstance()->addNotice('cost', Util::milliTimeInt() - $this->actStart);
            Logger::getInstance()->addNotice('errno', $code);
            Logger::getInstance()->notice();
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