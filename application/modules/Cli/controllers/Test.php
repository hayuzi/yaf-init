<?php
/**
 * Created by PhpStorm.
 * User: yuzi
 * Date: 2019/7/24
 * Time: 11:13
 */

use AppCore\BaseClass\CliControllerBase;

/**
 * 测试控制器
 * Class TestController
 */
class TestController extends CliControllerBase
{

    /**
     * 测试脚本
     * @return bool
     */
    public function testAction()
    {
        $name = $this->getRequest()->getParam('name');
        echo "testAction name: {$name}\n";
        return false;
    }


    /**
     * 多进程并发测试，使用 pcntl
     */
    public function concurrencyAction()
    {

        $data =

        $pid = pcntl_fork();
        // 父进程和子进程都会执行下面代码
        if ($pid == -1) {
            die('could not fork');
        } else if ($pid) {
            //父进程会得到子进程号，所以这里是父进程执行的逻辑
            while (true) {

            }

            posix_kill($pid, SIGKILL);
            // pcntl_wait($status); // 等待子进程中断，防止子进程成为僵尸进程
        } else {
            //子进程得到的$pid为0, 所以这里是子进程执行的逻辑。
            $running = true;


        }

    }

}