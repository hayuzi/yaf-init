<?php
/**
 * Created by PhpStorm.
 * User: hayuzi
 * Date: 2019/7/24
 * Time: 10:28 PM
 */

/**
 * 主处理程序中必须加入信号触发机制
 * 注意：在子进程执行中如果注册了信号处理器的话，同样需要声明ticks触发
 * 参考 https://www.php.net/manual/zh/function.pcntl-signal.php 中的更新说明
 */
declare(ticks = 1);

namespace AppCore\Concurrency\Src;

use Utils\TimeUtil;

abstract class AbstractReactor
{

    /**
     * 最大子进程数量
     */
    const MAX_CHILD_PROCESS_NUM = 10;

    /**
     * @var int 当前进程是否是主进程
     */
    protected $isParent = true;

    /**
     * @var int 父进程的pid
     */
    protected $pPid;

    /**
     * @var callable 主进程在子进程为空的状态下是否需要自动退出
     */
    protected $pAutoExit;

    /**
     * @var bool 主进程是否被通知要退出
     */
    protected $pExitSigned = false;

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
     * @var int 任务拉取时间间隔
     */
    protected $taskPullGap = 0;

    /**
     * @var int 最后一次任务拉取时间
     */
    protected $lastTaskPull = 0;


    /**
     * AbstractReactor constructor.
     * @param bool $pAutoExit       子进程执行完成都关闭的时候父级别进程是否自动退出（）
     * @param int $maxTaskQueueNum  任务队列最大容量, 超出数量的任务不会推到队列
     * @param int $taskPullGap      拉取任务的时间间隔单位为毫秒
     */
    public function __construct($pAutoExit = false, $maxTaskQueueNum = 10, $taskPullGap = 1000)
    {
        $this->pAutoExit   = $pAutoExit;
        $this->taskQueue   = new TaskQueue($maxTaskQueueNum);
        $this->taskPullGap = $taskPullGap;
        $this->pPid        = posix_getpid();
        // echo "pPid: {$this->pPid} \n";
    }


    /**
     * 执行入口
     */
    final public function run()
    {

        while (true) {
            // 如果是父亲进程，执行fork或者监管
            if ($this->isParent) {

                // 拉取待处理任务
                if (TimeUtil::milliTimeInt() - $this->lastTaskPull >= $this->taskPullGap) {
                    $this->pullTask();
                    $this->lastTaskPull = TimeUtil::milliTimeInt();
                }

                // 进程未有结束信号/有待处理任务/并且进程未达到上限制的时候
                if (
                    !$this->pExitSigned
                    && !$this->taskQueue->isEmpty()
                    && count($this->child) < self::MAX_CHILD_PROCESS_NUM
                ) {
                    $task = $this->taskQueue->pop();
                    $pid  = pcntl_fork();
                    // 父进程和子进程都会执行下面代码
                    if ($pid == -1) {
                        die("could not fork\n");
                    } else if ($pid) {
                        // 父进程会得到子进程号，所以这里是父进程执行的逻辑
                        $this->isParent = true;
                        $this->addChild($pid);
                    } else {
                        // 子进程得到的$pid为0, 所以这里是子进程执行的逻辑。
                        $this->isParent = false;
                        $this->callback($task);
                        // 子进程任务结束之后，直接跳出循环，走后续逻辑结束进程
                        break;
                    }
                } else {
                    // 主进程监控子进程的执行状态
                    foreach ($this->child as $key => $pid) {
                        $res = pcntl_waitpid($pid, $status, WNOHANG);
                        // If the process has already exited
                        if ($res == -1 || $res > 0)
                            unset($this->child[$key]);
                    }
                    usleep($this->taskPullGap * 1000); // 按照拉取任务的频率休眠

                    // 任务执行完成之后，跳出循环区间，结束进程
                    if (
                        ($this->pAutoExit || $this->pExitSigned)
                        && count($this->child) == 0) {
                        break;
                    }

                    // 主进程接收信号 10， 安全中断所有子进程
                    pcntl_signal(SIGUSR1, [$this, 'sigHandler']);
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
        $this->child[$pid] = $pid;
    }


    /**
     * 获取执行任务的方法
     *  该方法需要将要执行的task任务参数传递进来，参数格式为callback方法的参数数组.
     *  由于目前封装的task队列有数量上限，所以该方法中需要使用者自己处理 task入队列的方法
     *  但是请不要阻塞队列
     */
    abstract public function pullTask();


    /**
     * task任务的具体执行处理方法
     *  该方法需要尽量单纯，处理task事务即可
     * @param array $task
     * @return mixed
     */
    abstract public function callback($task);


    /**
     * @param $signo
     */
    function sigHandler($signo)
    {
        switch ($signo) {
            case SIGUSR1:
                // 处理SIGUSR1信号
                if ($this->isParent) {
                    // 主进程接收，则通知所有子进程
                    $this->pExitSigned = true;
                    foreach ($this->child as $key => $pid) {
                         echo "custom kill pid {$pid}\n";
                        posix_kill($pid, SIGUSR1);
                    }
                } else {
                     echo "child " . posix_getpid() . " exit\n";
                    exit(); // 子进程直接结束
                }
                break;
            default:
                // 处理所有其他信号
        }
    }

}