boofw/phpole
=============

一些常用的工具类

```
依赖关系

如无说明，均可独立使用

下面列出有依赖关系的类，括号中为所依赖的类

PDbService [PService, PDAO]
```

Index
--------

```
MVC模式
 - PMVC
 - PController
 - PAuthController

Utils
 - PArray
 - PCfg
 - PHttp
 - PUtil

Database
 - PDAO

WebService
 - PService
 - PDbService
 - PApiClient
```

Installation
--------------

* php5.3 以上环境

```
composer install

require 'vendor/autoload.php';
```

* php5.2 及以下环境

```
composer install

require 'vendor/boofw/phpole/PAutoload.php';

PAutoload::importDir(array(your_library_dirs)); // optional

PAutoload::importMap(array(your_class_maps)); // optional
```

Configuration
---------------

* 所有配置项列表

```
PMVC::$approot 应用controller与view的根目录
PMVC::$route 路由正则

PService::$apiroot 应用service的根目录

PDAO::$cfg 数据库连接相关配置
PDAO::$daoroot 自定义DAO目录
```

* 使用`PCfg`加载配置使用`PCfg::init(<config data>)`或`PCfg::init(<config file path>)`

配置实例

```
array(
		'libcfg'=>array(
				'PMVC'=>array(
						'approot'=>__DIR__.'/../app',
						'route'=>array(
								'/^\/(\d+)([\/\?]{1}.*)?$/'=>'c=feed&a=view&id=$1&v=$2',
								'/^\/u\/(\d+)([\/\?]{1}.*)?$/'=>'c=user&a=view&id=$1&v=$2',
								'/^\/([\w]+)\/(\d+)([\/\?]{1}.*)?$/'=>'c=$1&a=view&id=$2&v=$3',
								'/^\/([\w]+)\/([\w]+)\/(\d+)([\/\?]{1}.*)?$/'=>'c=$1&a=$2&id=$3&v=$4',
						),
				),
				'PDAO'=>array(
						'daoroot'=>'',
						'cfg'=>array(
								'db1'=>array(
										'dsn'=>'sqlite:'.__DIR__.'/test.sq3',
								),
								'db2'=>array(
										'dsn'=>'mysql:host=localhost;port=3306;dbname=test',
										'username'=>'root',
										'password'=>'',
										'prefix'=>'tbl_',
										'opts'=>array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'')
								),
						),
				),
				'PService'=>array(
						'apiroot'=>__DIR__.'/../api',
				),
		),
);
``` 

Usage
----------

* PDAO

```
PDAO::$cfg = $cfg; // or use PCfg::init($cfg);

$r = PDAO::init('user')->all(array(), array(), 10);

$r = D('user')->all(array(), array(), 10); // require 'vendor/boofw/phpole/functions.php'
```

* PService

```
PService::$apiroot = __DIR__.'/service'; // or use PCfg::init($cfg);

$r = PService::init('sync', 1)->get(3);
$r = PService::init('base.user', 1)->get(3);

$r = S('base.user')->get(3); // require 'vendor/boofw/phpole/functions.php'
```

* PDbService

```
$r = PDbService::init('db:base.user', 1)->get(3);

$r = S('db:base.user')->get(3); // require 'vendor/boofw/phpole/functions.php'
```

* functions

可选组件，使用后可更方便地是有`DAO`与`Service`

可自己参照修改

```
require '<PATH>functions.php';
```
