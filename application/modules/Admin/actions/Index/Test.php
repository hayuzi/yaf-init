<?php
/**
 * Created by PhpStorm.
 * User: yuzi
 * Date: 2019/7/25
 * Time: 15:06
 */

use AppCore\BaseClass\AbstractActionBase;


class TestAction extends AbstractActionBase
{

    public function execute()
    {
        parent::execute(); // TODO: Change the autogenerated stub
        echo "54312\n";
        $this->getRequest();
    }

}