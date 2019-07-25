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
     * 定义获取任务的方法
     *
     * @return bool
     */
    public function pullTask()
    {
        if ($this->taskPushNum >= 10) {
            return false;
        }
        for ($i = 0; $i < 10; $i++) {
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
            sleep(1);
            $i++;
            echo "- pPid: {$this->pPid}  - pid: " . posix_getpid() . " task i_{$i}：" . json_encode($task, JSON_UNESCAPED_UNICODE) . " \n";
            if ($i > 20) {
                break;
            }
            // --------------------------------------

        }
    }

}