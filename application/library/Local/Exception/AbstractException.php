<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2017/12/12
 * Time: 23:48
 */

class Local_Exception_AbstractException extends Yaf_Exception
{

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }



}
