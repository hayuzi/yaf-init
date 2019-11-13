<?php
/**
 * Created by PhpStorm.
 * User: hayuzi
 * Date: 2019/7/24
 * Time: 11:28 PM
 */

namespace AppCore\Concurrency\Sample;

use AppCore\Concurrency\Src\AbstractReactor;

class SampleReactor extends AbstractReactor
{


    private $taskPushNum = 0;


    /**
     * 定义父进程获取任务的方法
     *      在这个方法里，将需要处理的任务推送到队列中
     *      $this->taskQueue->push($task);
     * @return bool
     */
    public function pullTask()
    {
        if ($this->taskPushNum >= 2) {
            return false;
        }
        for ($i = 0; $i < 2; $i++) {
            $task = [$i, 'test'];
            $this->taskQueue->push($task);
            $this->taskPushNum++;
        }
        return true;
    }


    /**
     * 子进程任务执行方法示例
     *
     *
     * @param array $task
     * @return mixed|void
     */
    public function callback($task)
    {
        // 必须同时在子进程中做信号触发，这样在接收到固定信息的时候可以管控子进程
        declare(ticks = 1);
        // 安装信号处理器
        pcntl_signal(SIGUSR1, [$this, 'sigHandler']);
        $i = 1;
        while (true) {
            // --------------------------------------
            // 如果是类似循环的常驻脚本，在此处处理业务程序.
            if ($this->pExitSigned) {
                break; // 在循环开始时候，处理安全退出逻辑，这样的话下面的程序会处理完成再停止
            }
            sleep(1);
            $i++;
            echo "- pPid: {$this->pPid}  - pid: " . posix_getpid() . " task i_{$i}：" . json_encode($task, JSON_UNESCAPED_UNICODE) . " \n";
            sleep(1);
            echo "- test running 1 \n";
            sleep(1);
            echo "- test running 2 \n";
            sleep(1);
            echo "- test running 3 \n";
            if ($i > 20) {
                break;
            }
            // --------------------------------------

        }
    }

}