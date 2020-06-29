<?php
/**
 * Created by PhpStorm.
 * User: hayuzi
 * Date: 2019/2/28
 * Time: 下午10:46
 */

namespace AppCore\BaseClass;

use AppCore\DbLib\AbstractModelBase;
use AppCore\LogLib\Logger;
use AppCustom\AppException\Exception;

class ModelBase extends AbstractModelBase
{

    // 字段类型
    const FIELD_TYPE_DEFAULT    = 0;    //默认类型，全部字段
    const FIELD_TYPE_MAIN       = 1;    //主要字段类型，比较通用的字段（如，id，name，status，created_at 等尽量控制在5、6个字段以内）

    // 输出字段类型配置
    public static $tableFields  = [];   //表所有字段
    public static $fieldList    = [];   //所有类型对应的字段列表（[type => list]）


    /**
     * @var array 临时查询条件
     */
    protected $tmpWhere = [];

    /**
     * @var array 临时更新数据
     */
    protected $tmpUpdateData = [];


    /**
     * 根据字段类型获取对应字段列表
     *   默认取主要字段列表，主要字段列表不存在则使用表字段列表
     * @param int $fieldType 字段类型，默认为 1=主要字段
     * @return array
     */
    public static function getFieldsByType($fieldType = 1)
    {
        if (!is_numeric($fieldType)) {
            $fieldType = static::FIELD_TYPE_MAIN;
        }

        if (array_key_exists($fieldType, static::$fieldList)) {
            //存在指定类型对应的字段列表，直接使用
            $fields = static::$fieldList[$fieldType];
            //如果是默认类型（fieldType = 0），则使用表的字段列表（可以减少配置）
            if (!$fields && $fieldType == static::FIELD_TYPE_DEFAULT) {
                $fields = static::$tableFields;
            }
        } else if ($fieldType != static::FIELD_TYPE_MAIN && array_key_exists(static::FIELD_TYPE_MAIN, static::$fieldList)) {
            //没找到指定类型对应的字段列表，但存在主要字段列表则使用主要字段列表
            $fields = static::$fieldList[static::FIELD_TYPE_MAIN];
        } else {
            //没找到指定类型对应的字段列表，使用默认的表字段列表
            $fields = static::$tableFields;
        }

        return $fields;
    }

    /**
     * 根据字段类型获取对应字段列表
     *   默认取主要字段列表，主要字段列表不存在则使用表字段列表
     * @param int $fieldType 字段类型，默认为 1=主要字段
     * @return array
     */
    public function getFieldByType($fieldType = 1)
    {
        return self::getFieldsByType($fieldType);
    }


    /**
     * 检查SQL执行是否正确
     *
     * @param $errorMsg
     * @param $errorNo
     * @param array $logData
     * @return bool
     * @throws Exception
     */
    public function checkExecError($errorMsg, $errorNo, $logData = [])
    {
        list($sqlErrCode, $driErrCode, $driErrMsg)  = self::$database->error();
        if ($sqlErrCode == '00000' || !$driErrMsg) {
            list($sqlErrCode, $driErrCode, $driErrMsg) = self::$database->pdo->errorInfo();
        }

        //SQL执行错误
        if ($driErrCode && $sqlErrCode && $sqlErrCode != '00000') {
            $sqlArr = self::$database->log();
            $errSql = array_pop($sqlArr);
            $logData['errorSql']    = $errSql;
            $logData['errorMsg']    = "sqlErrCode: $sqlErrCode, dirErrCode: $driErrCode, dirErrMsg: $driErrMsg";
            Logger::getInstance()->error($errorMsg, $errorNo, $logData);
            throw new Exception($errorMsg, $errorNo);
        }

        return true;
    }


    /**
     * 检测数据是否为空（主要是在info接口使用）
     * @param array|null|false $data 需要检测的数据
     * @param $errorMsg
     * @param $errorNo
     * @param array $logData
     * @return bool
     * @throws Exception
     */
    public function checkEmpty($data, $errorMsg, $errorNo, $logData = [])
    {
        if (empty($data) || !is_array($data)) {
            $sqlArr                     = self::$database->log();
            if ($sqlArr) {
                $execSql                = array_pop($sqlArr);
                $logData['executeSql']  = $execSql;
            }
            Logger::getInstance()->error($errorMsg, $errorNo, $logData);
            throw new Exception($errorMsg, $errorNo);
        }

        return true;
    }



    /**
     * 临时查询条件初始化
     * @param array $where
     * @return $this
     */
    public function whereInit(array $where = [])
    {
        $this->tmpWhere = $where;
        return $this;
    }


    /**
     * 给where条件追加参数
     *
     * @param $field
     * @param $value
     * @param string $op
     * @return $this
     */
    public function whereAppend($field, $value, $op = '=')
    {
        if (empty($value)) {
            return $this;
        }
        switch ($op) {
            case '=':
                $this->tmpWhere[$field] = $value;
                break;
            default:
                $whereField = $field . '[' . $op . ']';
                $this->tmpWhere[$whereField] = $value;
        }
        return $this;
    }


    /**
     * 临时查询条件完成返回
     * @return array
     */
    public function whereReturn()
    {
        return $this->tmpWhere;
    }


    /**
     * @param array $data
     * @return $this
     */
    public function updateDataInit(array $data = [])
    {
        $this->tmpUpdateData = $data;
        return $this;
    }


    /**
     * 给where条件追加参数
     *
     * @param $field
     * @param $value
     * @param $filter string  empty/null
     * @return $this
     */
    public function updateDataAppend($field, $value, $filter = 'empty') {
        switch ($filter) {
            case 'empty':
                if (empty($value)) {
                    return $this;
                }
                break;
            case 'null':
                if (is_null($value)) {
                    return $this;
                }
                break;
            default:
                return $this;
        }
        $this->tmpUpdateData[$field] = $value;
        return $this;
    }


    /**
     * 完成数据组装并返回
     *
     * @return array
     */
    public function updateDataReturn()
    {
        return $this->tmpUpdateData;
    }



}