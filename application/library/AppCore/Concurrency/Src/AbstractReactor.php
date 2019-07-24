<?php
/**
 * Created by PhpStorm.
 * User: hayuzi
 * Date: 2019/7/24
 * Time: 10:28 PM
 */

namespace AppCore\Concurrency\Src;

abstract class AbstractReactor
{

    /**
     * 最大子进程数量
     */
    const MAX_CHILD_PROCESS_NUM = 10;

    /**
     * @var array 子进程的集合
     */
    protected $child = [];


    /**
     * @var callable 子进程的任务执行方法
     */
    protected $cCallback;

    /**
     * @var TaskQueue 待处理的任务 (待处理的任务不空则处理子任务 )
     */
    protected $taskQueue;

    /**
     * @var callable 主进程在子进程为空的状态下是否需要自动退出
     */
    protected $pAutoExit;


    /**
     * AbstractReactor constructor.
     * @param $cCallback
     * @param int $maxTaskQueueNum
     * @param bool $pAutoExit
     */
    public function __construct($cCallback, $maxTaskQueueNum = 10, $pAutoExit = true)
    {
        $this->taskQueue = new TaskQueue($maxTaskQueueNum);
        $this->pAutoExit = $pAutoExit;
        $this->cCallback = $cCallback;
    }


    /**
     * 执行入口
     */
    final public function run()
    {
        $isParentProcess = true;

        while (true) {
            $this->pullTask();
            // 如果是父亲进程，执行fork或者监管
            if ($isParentProcess) {
                // 有任务分发并且进程未达到上限制的时候
                if (!$this->taskQueue->isEmpty() && $this->child < self::MAX_CHILD_PROCESS_NUM) {
                    $pid = pcntl_fork();
                    // 父进程和子进程都会执行下面代码
                    if ($pid == -1) {
                        die('could not fork');
                    } else if ($pid) {
                        // 父进程会得到子进程号，所以这里是父进程执行的逻辑
                        $isParentProcess = true;
                        $this->addChild($pid);
                        echo "this is parent";
                        // posix_kill($pid, SIGKILL);
                        // pcntl_wait($status); // 等待子进程中断，防止子进程成为僵尸进程
                    } else {
                        // 子进程得到的$pid为0, 所以这里是子进程执行的逻辑。
                        $task = $this->taskQueue->pop();
                        call_user_func_array($this->cCallback, $task);
                        // 子进程执行完结自动结束
                        break;  // 跳出循环区间，结束进程
                    }
                } else {
                    // 维持主进程，监控子进程的执行, 每隔一秒检测一次
                    sleep(1);
                    if ($this->pAutoExit && count($this->child) == 0) {
                        break; // 跳出循环区间，结束进程
                    }

                }
            }
        }
    }


    /**
     * 子进程写入全局记录
     * @param $pid
     */
    protected function addChild($pid)
    {
        $this->child[$pid] = [
            'pid'     => $pid,
            'startAt' => time(),
        ];
    }


    /**
     * 获取执行任务的方法
     *  该方法需要将要执行的task任务参数传递进来，参数格式为callback方法的参数数组.
     */
    abstract public function pullTask();


}