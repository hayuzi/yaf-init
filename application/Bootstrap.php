<?php
/**
 * @name Bootstrap
 * @author lenovo-pc\lenovo
 * @desc 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * @see http://www.php.net/manual/en/class.yaf-bootstrap-abstract.php
 * 这些方法, 都接受一个参数:\Yaf\Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */
class Bootstrap extends \Yaf\Bootstrap_Abstract{

    public function _initConfig() {
		//把配置保存起来
		$arrConfig = \Yaf\Application::app()->getConfig();
		\Yaf\Registry::set('config', $arrConfig);
	}

	public function _initPlugin(\Yaf\Dispatcher $dispatcher) {
		//注册一个插件
		$objSamplePlugin = new SamplePlugin();
		$dispatcher->registerPlugin($objSamplePlugin);
	}

	public function _initRoute(\Yaf\Dispatcher $dispatcher) {
		//在这里注册自己的路由协议,默认使用简单路由
	}
	
	public function _initView(\Yaf\Dispatcher $dispatcher){
		//在这里注册自己的view控制器，例如smarty,firekylin
	}


	// 注册 doctrine2的 ORM ( 但是引入ORM 由于依赖过多 会降低性能 )
	public function _initDoctrineOrm(){
        $config = \Yaf\Registry::get('config');

        // 指定entity文件存放位置
        $entityFielPaths = [
            APPLICATION_PATH . 'library/entity'
        ];

        // 指定开发模式
        $isDevMode = $config['database']['isDevMode'];

        // 设置数据库连接参数
        $dbParams = [
            'driver'    => $config['database']['params']['driver'],
            'host'      => $config['database']['params']['host'],
            'port'      => $config['database']['params']['port'],
            'user'      => $config['database']['params']['user'],
            'password'  => $config['database']['params']['password'],
            'dbname'    => $config['database']['params']['dbname'],
        ];

        $config = Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration($entityFielPaths, $isDevMode);
        $entityManager = Doctrine\ORM\EntityManager::create($dbParams, $config);

        \Yaf\Registry::set('entityManager', $entityManager);

        // 在其他位置使用 Registry::get获取注册的 orm
        // $em = \Yaf\Registry::get('entityManager');
    }



}
