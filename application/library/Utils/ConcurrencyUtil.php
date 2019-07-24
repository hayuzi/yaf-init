<?php
/**
 * Created by PhpStorm.
 * User: yuzi
 * Date: 2019/7/24
 * Time: 18:25
 */

namespace Utils;


class ConcurrencyUtil
{

    /**
     * @var int 最大子进程数量
     */
    protected $maxChildNum = 10;

    /**
     * @var array 子进程的集合
     */
    protected $child = [];

    /**
     * @var callable 子进程需要处理的任务方法
     */
    protected $callback;


    public function __construct($maxChildNum = 10)
    {
        $this->maxChildNum = $maxChildNum;
    }


    public function run()
    {

    }


}