<?php
/**
 * Created by PhpStorm.
 * User: hayuzi
 * Date: 2019/3/25
 * Time: 下午10:48
 */

namespace AppCore\BaseClass;


class ResultBase
{

    const DATA_NAME = 'data';
    const CODE_NAME = 'code';
    const MSG_NAME  = 'message';

    protected $code = 200;
    protected $msg  = '';
    protected $data;


    /**
     * @param int $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }


    /**
     * @param string $msg
     */
    public function setMsg($msg)
    {
        $this->msg = $msg;
    }


    /**
     * @param $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }


    /**
     * @return array
     */
    private function getResult()
    {
        return [
            self::CODE_NAME => $this->code,
            self::MSG_NAME  => $this->msg,
            self::DATA_NAME => $this->data,
            'timestamp'     => time(),
        ];
    }


    /**
     * [__toString 结果当做字符串]
     * @return string [description]
     */
    public function __toString()
    {
        return $this->toJson();
    }


    /**
     * [__toString 结果当做字符串]
     * @return string [description]
     */
    public function toJson()
    {
        $result = $this->getResult();
        if (is_array($result[self::DATA_NAME]) && empty($result[self::DATA_NAME])) {
            $result[self::DATA_NAME] = new \stdClass();
        }
        return json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }




}