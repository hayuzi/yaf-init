<?php
/**
 * Created by PhpStorm.
 * User: yuzi
 * Date: 2018/8/31
 * Time: 上午11:13
 */

namespace DbLib;

use Medoo\Medoo;
use Utils\StringFmt;

/**
 * https://medoo.in/
 * https://github.com/catfan/Medoo
 * 使用Medoo数据操作类
 *
 * Class AbstractModelBase
 * @package CommonLib
 */
abstract class AbstractModelBase
{

    /**
     * @var Medoo
     */
    protected static $database = null;


    /**
     * @var string 数据表名，优先使用$tableName
     */
    protected static $tableName = '';

    /**
     * @var string 数据表的主键字段，默认是 id
     */
    protected $primaryKeyField = 'id';


    /**
     * AbstractModelBase constructor.
     * @param null $tableName
     * @param string $db
     * @param null $options
     */
    public function __construct($tableName = null, $db = 'default_db', $options = null)
    {
        if ($tableName) {
            static::$tableName = $tableName;
        }
        self::$database = Connection::getInstance($db, $options);
    }


    /**
     * @return Medoo
     */
    public function getDatabase()
    {
        return self::$database;
    }


    public function beginTransaction()
    {
        self::$database->pdo->beginTransaction();
    }


    public function commit()
    {
        self::$database->pdo->commit();
    }


    public function rollback()
    {
        self::$database->pdo->rollBack();
    }


    /**
     * 获取数据表名字
     * @return string
     */
    public static function getTableName()
    {
        if (!static::$tableName) {
            $classNameParse = explode("\\",__CLASS__);
            $modelName = StringFmt::camel2snake(array_pop($classNameParse));
            $tableName = str_replace('_model', '', $modelName);
            static::$tableName = $tableName;
        }
        return static::$tableName;
    }


    /**
     * @param string $tableName 表名
     * @return bool
     */
    public function setTableName($tableName)
    {
        $tableName = trim($tableName);
        if (!$tableName) {
            return false;
        }
        static::$tableName = $tableName;
        return true;
    }


    /**
     * @param $columns
     * @param null $where
     * @param null $join
     * @return array|bool
     */
    public function select($columns, $where = null, $join = null)
    {
        if ($join) {
            return self::$database->select(static::getTableName(), $join, $columns, $where);
        } else {
            return self::$database->select(static::getTableName(), $columns, $where);
        }
    }


    /**
     * @param $data
     * @return bool|\PDOStatement
     */
    public function insert($data)
    {
        if (key_exists(0, $data)) {
            foreach ($data as $k => $v) {
                if (empty($v['created_at'])) {
                    $data[$k]['created_at'] = time();
                }
                $data[$k]['updated_at'] = time();
            }
        } else {
            if (empty($data['created_at'])) {
                $data['created_at'] = time();
            }
            $data['updated_at'] = time();
        }
        return self::$database->insert(static::getTableName(), $data);
    }


    /**
     * @return int|mixed|string
     */
    public function getLastInsertId()
    {
        // 此处封装ID为int类型
        $id = self::$database->id();
        if (is_numeric($id)) {
            $id = (int)$id;
        }
        return $id;
    }


    /**
     * @param $data
     * @param null $where
     * @return bool|\PDOStatement
     */
    public function update($data, $where = null)
    {
        if (empty($data['updated_at'])) {
            $data['updated_at'] = time();
        }
        return self::$database->update(static::getTableName(), $data, $where);
    }


    /**
     * @param $where
     * @return bool|\PDOStatement
     */
    public function delete($where)
    {
        return self::$database->delete(static::getTableName(), $where);
    }


    /**
     * @param $columns
     * @param null $where
     * @return bool|\PDOStatement
     */
    public function replace($columns, $where = null)
    {
        return self::$database->replace(static::getTableName(), $columns, $where);
    }


    /**
     * 获取单条数据
     *
     * @param $columns
     * @param null $where
     * @param null $join
     * @return array|bool|mixed
     */
    public function get($columns, $where = null, $join = null)
    {
        if ($join) {
            return self::$database->get(static::getTableName(), $join, $columns, $where);
        } else {
            return self::$database->get(static::getTableName(), $columns, $where);
        }
    }


    /**
     * @param $where
     * @param null $join
     * @return bool
     */
    public function has($where, $join = null)
    {
        if ($join) {
            return self::$database->has(static::getTableName(), $join, $where);
        } else {
            return self::$database->has(static::getTableName(), $where);
        }

    }


    /**
     * @param $columns
     * @param null $where
     * @param null $join
     * @return mixed
     */
    public function count($columns, $where = null, $join = null)
    {
        if ($join) {
            return self::$database->count(static::getTableName(), $join, $columns, $where);
        } else {
            return self::$database->count(static::getTableName(), $columns, $where);
        }
    }


    /**
     * @param $columns
     * @param null $where
     * @param null $join
     * @return mixed
     */
    public function max($columns, $where = null, $join = null)
    {
        if ($join) {
            return self::$database->max(static::getTableName(), $join, $columns, $where);
        } else {
            return self::$database->max(static::getTableName(), $columns, $where);
        }
    }


    /**
     * @param $columns
     * @param null $where
     * @param null $join
     * @return mixed
     */
    public function min($columns, $where = null, $join = null)
    {
        if ($join) {
            return self::$database->min(static::getTableName(), $join, $columns, $where);
        } else {
            return self::$database->min(static::getTableName(), $columns, $where);
        }
    }


    /**
     * @param $columns
     * @param null $where
     * @param null $join
     * @return mixed
     */
    public function avg($columns, $where = null, $join = null)
    {
        if ($join) {
            return self::$database->avg(static::getTableName(), $join, $columns, $where);
        } else {
            return self::$database->avg(static::getTableName(), $columns, $where);
        }
    }


    /**
     * @param $columns
     * @param null $where
     * @param null $join
     * @return mixed
     */
    public function sum($columns, $where = null, $join = null)
    {
        if ($join) {
            return self::$database->sum(static::getTableName(), $join, $columns, $where);
        } else {
            return self::$database->sum(static::getTableName(), $columns, $where);
        }
    }


    /**
     * 事务处理
     *
     * @param $actions callable
     * @return bool
     * @throws \Exception
     */
    public function action($actions)
    {
        return self::$database->action($actions);
    }


    /**
     * 获取分页列表
     *
     * @param $columns
     * @param null $where
     * @param null $join
     * @param array $pageOptions
     * @return array
     */
    public function getPaginatedList($columns, $where = null, $join = null, $pageOptions = [])
    {
        $result = [
            'list' => [],
        ];

        if ($pageOptions) {
            $where['LIMIT'] = [
                ($pageOptions['page_num'] - 1) * $pageOptions['page_limit'],
                $pageOptions['page_limit'],
            ];
        }

        if ($join) {
            $list = self::$database->select(static::getTableName(), $join, $columns, $where);
        } else {
            $list = self::$database->select(static::getTableName(), $columns, $where);
        }

        $result['list'] = $list ?: [];

        if ($pageOptions && $pageOptions['need_pagination']) {
            unset($where['LIMIT']);
            $columns = [static::getTableName() . '.' . $this->primaryKeyField];
            if ($join) {
                $count = self::$database->count(static::getTableName(), $join, $columns, $where);
            } else {
                $count = self::$database->count(static::getTableName(), $columns, $where);
            }
            $result['total_page'] = ceil($count / $pageOptions['page_limit']);
            $result['total_cnt']  = (int)$count;
            $result['page_limit'] = $pageOptions['page_limit'];
        }

        return $result;
    }


    /**
     * 过滤字段
     * @param $fields
     * @param $params
     * @return array
     */
    public function filterParams($fields, $params)
    {
        $data = [];
        foreach ($fields as $field) {
            if (isset($params[$field])) {
                $data[$field] = $params[$field];
            }
        }
        return $data;
    }


    /**
     * @param $querySql
     * @param array $map
     * @return bool|\PDOStatement
     */
    public function query($querySql, $map = [])
    {
        return self::$database->query($querySql, $map);
    }


    /**
     * @return array
     */
    public function getSqlArr()
    {
        return self::$database->log();
    }

}
