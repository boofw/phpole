polev/phpole
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

require 'vendor/polev/utils/PAutoload.php';

PAutoload::importDir(array(your_library_dirs)); // optional

PAutoload::importMap(array(your_class_maps)); // optional
```

Configuration
---------------

* PCfg

```
PCfg::init(<config data>);
or
PCfg::init(<config file path>);
```

lib config

```
PMVC::$approot 应用controller与view的根目录
PMVC::$route 路由正则

PService::$apiroot 应用service的根目录

PDAO::$cfg 数据库连接相关配置
PDAO::$daoroot 自定义DAO目录
``` 

* PDAO

```
PDAO::$cfg = array(
		'default'=>array(
				'dsn'=>'sqlite:'.__DIR__.'/test.sq3',
		),
		'mysql'=>array(
				'dsn'=>'mysql:host=localhost;dbname=test',
				'username'=>'root',
				'password'=>'',
				'prefix'=>'',
				'opts'=>array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''),
		),
);

$r = PDAO::init('user')->getAll(array(), array(), 10);
```

* PService

```
PService::$apiroot = __DIR__.'/service';

$r = PService::init('sync', 1)->get(3);
$r = PService::init('base.user', 1)->get(3);
```

* PDbService

```
PService::$apiroot = __DIR__.'/service';

$r = PDbService::init('db:base.user', 1)->get(3);
```

* functions

可选组件，使用后可更方便地是有`DAO`与`Service`

可自己参照修改

```
require '<PATH>functions.php';
```
