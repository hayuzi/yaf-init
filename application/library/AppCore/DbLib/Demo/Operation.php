<?php
/**
 * Created by PhpStorm.
 * User: yuzi
 * Date: 2018/9/4
 * Time: 上午10:23
 */

namespace AppCore\DbLib\Demo;

use AppCore\DbLib\DB;
use AppCore\BaseClass\Exception\AppCoreException;
use AppCore\BaseClass\BaseErrorCode;

class Operation
{

    /**
     * @return array|bool|mixed
     */
    public function fetchDemo()
    {
        $model  = new ModelDemo();
        $info   = $model->get('*', ['id' => 1]);
        return $info;
    }


    /**
     * @return mixed
     */
    public function fetchDemoTwo()
    {
        $info = DB::table('demo')->get('*', ['id' => 1]);
        return $info;
    }


    /**
     * @return int|mixed|string
     */
    public function transactionDemo()
    {
        // 确保是同一个数据库链接实例，并且都是InnoDb引擎
        try {
            $model  = new ModelDemo();
            $model->getDatabase()->pdo->beginTransaction();

            $res    = $model->insert(['test' => 'test']);
            if (!$res->rowCount()) {
                throw new AppCoreException(
                    BaseErrorCode::MYSQL_COMMON_ERROR_MSG,
                    BaseErrorCode::MYSQL_COMMON_ERROR
                );
            }
            $id     = $model->getLastInsertId();
            $model->getDatabase()->pdo->commit();

            return $id;
        } catch (\Exception $e) {
            $model->getDatabase()->pdo->rollBack();
        }
    }


}