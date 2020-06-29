<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2017/12/12
 * Time: 23:48
 */

namespace AppCore\BaseClass\Exception;

use AppCore\LogLib\Logger;
use Yaf\Exception;

class AppCoreException extends Exception
{

    /**
     * AppCoreException constructor.
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct($message = "", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->writeLog();

    }

    /**
     * 写错误日志
     */
    protected function writeLog()
    {
        Logger::getInstance()->addNotice('ExceptionCode', self::getCode());
        Logger::getInstance()->error(parent::getMessage(), self::getCode(), [
            'file'  => parent::getFile(),
            'line'  => parent::getLine(),
            'trace' => parent::getTraceAsString(),
        ]);
    }


}
