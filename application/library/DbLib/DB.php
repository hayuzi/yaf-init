<?php
/**
 * Created by PhpStorm.
 * User: yuzi
 * Date: 2018/9/3
 * Time: 上午11:27
 */

namespace DbLib;

class DB
{

    /**
     * @var array
     */
    private static $modelProjects = [];


    /**
     * @param $tableName
     * @param string $db
     * @param null $options
     * @return ModelCommon
     */
    public static function table($tableName, $db = 'default_db', $options = null)
    {
        if (empty($tableName) || empty($db)) {
            return null;
        }

        if (!isset(self::$modelProjects[$db][$tableName])) {
            self::$modelProjects[$db][$tableName] = new ModelCommon($tableName, $db, $options);
        }

        return self::$modelProjects[$db][$tableName];
    }

}