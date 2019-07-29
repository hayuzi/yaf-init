<?php
/**
 * Created by PhpStorm.
 * User: yuzi
 * Date: 2019/7/25
 * Time: 15:30
 */

namespace AppCore\BaseClass;


class BaseErrorCode
{

    const SERVER_ERROR                  = 500;
    const SERVER_ERROR_MSG              = '系统错误';
    const VIRTUAL_SESSION_NOT_START     = 500001;
    const VIRTUAL_SESSION_NOT_START_MSG = '会话机制未开启';
    const REQUEST_METHOD_ERROR          = 403003;
    const REQUEST_METHOD_ERROR_MSG      = '请求方法错误';


}