<?php
/**
 * Created by PhpStorm.
 * User: yuzi
 * Date: 2019/7/24
 * Time: 16:35
 */

namespace AppCore\Routers;

use Yaf\Route_Interface;

/**
 * Class CliRoute
 * @package AppCore\Routers
 */
class CliRoute implements Route_Interface
{

    /**
     * 设置相应的路由参数
     *
     * @param \Yaf\Request_Abstract $request （ 参数里不可加类型限制 ）
     * @return bool
     */
    public function route($request)
    {
        // Implement route() method.
        // 处理相应的路由参数
        if (!isset($_SERVER['argv'][1])) {
            return false;
        }
        $route = explode('/', trim($_SERVER['argv'][1], '/'));
        if (count($route)>2) {
            $request->setModuleName($route[0]);
            $request->setControllerName($route[1]);
            $request->setActionName($route[2]);
        } else {
            if (count($route)<2) {
                die('('.implode('/', $route).')route error, exit.');
            }
            $request->setControllerName($route[0]);
            $request->setActionName($route[1]);
        }

        // 直接将参数也注入到request中
        if (count($_SERVER['argv']) > 2) {
            $params = $this->parseCliParams(array_slice($_SERVER['argv'], 2));
            foreach ($params as $k => $v) {
                $request->setParam($k, $v);
            }
        }
        return true;
    }


    /**
     * 组装信息
     *
     * @param array $info
     * @param array|null $query
     * @return bool
     */
    public function assemble(array $info, array $query = null)
    {
        // Implement assemble() method.
        return false;
    }


    /**
     * 解析请求参数
     * @param $params
     * @return array
     */
    public function parseCliParams($params)
    {
        $data = [];
        foreach ($params as $item) {
            list($name, $value) = explode('=', $item);
            $field = str_replace('--', '', $name);
            if ($field) {
                $data[$field] = $value;
            }
        }
        return $data;
    }

}
