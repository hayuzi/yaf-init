<?php
/**
 * Created by PhpStorm.
 * User: hayuzi
 * Date: 2019/7/24
 * Time: 10:29 PM
 */

namespace AppCore\Concurrency\Src;


class TaskQueue
{

    /**
     * @var \SplQueue 待处理任务队列
     */
    protected $taskQueue;

    /**
     * @var int 队列最大数量上限
     */
    protected $maxTaskNum;

    /**
     * TaskQueue constructor.
     * @param int $maxTaskNum
     */
    public function __construct($maxTaskNum = 10)
    {
        $this->taskQueue  = new \SplQueue();
        $this->maxTaskNum = $maxTaskNum;
    }


    /**
     * @param $task
     * @return bool
     */
    public function push($task)
    {
        if ($this->taskQueue->count() >= $this->maxTaskNum) {
            return false;
        }
        $this->taskQueue->push($task);
        return true;
    }

    /**
     * @return mixed
     */
    public function pop()
    {
        return $this->taskQueue->pop();
    }


    /**
     * @return mixed
     */
    public function isEmpty()
    {
        return $this->taskQueue->isEmpty();
    }

}