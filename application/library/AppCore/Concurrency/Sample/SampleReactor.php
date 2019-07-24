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

}