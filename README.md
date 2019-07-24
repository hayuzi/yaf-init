yaf-init
==

> yaf的初始化框架，便于调用

### 1. 基础目录结构
```
- application
    |- controllers              默认模块控制器
    |- library                  自动加载的类库
    |- models                   默认的model类目录
    |- modules                  模块目录
    |   |- 
    |- plugins                  插件目录
    |- views                    模板文件
    |- Bootstrap.php            框架默认启动初始化文件         
- conf
    |- application.ini          配置文件
    |- application.ini.sample   配置文件示例（将项目拉下来后拷贝修改即可）
- public
    |- cli.php                  控制台脚本执行入口
    |- index.php                web执行入口
- vendor                        composer类库目录


```



### 2. 封装扩展的内容
#### 2.1 封装了基础 model 类
Model类采用 [Medoo](https://github.com/catfan/Medoo), 该类是基于PDO的封装, 但是非ORM形式，因而处理关联数据可能稍微有些不便。

#### 2.2 控制器封装
TODO

#### 2.3 路由与请求
TODO

