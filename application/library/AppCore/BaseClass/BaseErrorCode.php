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

    const SERVER_ERROR                  = 100500;
    const SERVER_ERROR_MSG              = '系统错误';
    const VIRTUAL_SESSION_NOT_START     = 100001;
    const VIRTUAL_SESSION_NOT_START_MSG = '会话机制未开启';
    const REQUEST_METHOD_ERROR          = 100403;
    const REQUEST_METHOD_ERROR_MSG      = '请求方法错误';

    // MySQL
    const MYSQL_COMMON_ERROR     = 110000;
    const MYSQL_COMMON_ERROR_MSG = 'MySQL异常';

    // Redis
    const REDIS_SERVER_ERROR     = 110010;
    const REDIS_SERVER_ERROR_MSG = 'redis server error';
    const REDIS_AUTH_ERROR       = 110011;
    const REDIS_AUTH_ERROR_MSG   = 'redis auth error';


}