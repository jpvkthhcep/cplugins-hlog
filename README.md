# cplugins-hlog
Hyperf 日志组件

## 安装
项目根目录执行:
```
composer require cplugins/hlog
```

## 配置
config/autoload/logger.php 目录下

用以下代码覆盖原文件的代码
```
<?php

declare(strict_types=1);

use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

// config/autoload/logger.php
// $appEnv = env('APP_ENV', 'dev');
$formatter = [
    'class' => \Monolog\Formatter\LineFormatter::class,
    'constructor' => [
        'format' => "%datetime% %channel% %level_name% %message%\n",
        'allowInlineLineBreaks' => true,
        'includeStacktraces' => true,
    ],
];

return [
    'default' => [
        'handlers' => [
            [
                'class' => RotatingFileHandler::class,
                'constructor' => [
                    'filename' => BASE_PATH . '/runtime/logs/hyperf.log',
                    // 'level' => Logger::INFO,
                ],
                'formatter' => $formatter
            ],
        ],
    ],
];
```

## 使用 
```
$log = new LogHelper();
<!-- 该方法支持第二个参数, 传入exception对象-->
$log->info("提示信息");

<!-- 该方法支持第二个参数, 传入exception对象-->
$log->debug("调试信息");

<!-- 该方法支持第二个参数, 传入exception对象-->
$log->error("错误信息");

<!-- 该方法支持第二个参数, 传入exception对象-->
$log->warn("警告信息");
```

## 输出
```
格式： 时间 info 模块 类名 方法名 描述 code message trace
内容： 
2021-01-25 17:41:49  ERROR /workspace/php-project/ms-user-service App\Command\HyperfTest logTest 异常错误   
2021-01-25 17:41:49  INFO /workspace/php-project/ms-user-service App\Command\HyperfTest logTest 一般消息   
2021-01-25 17:41:49  WARNING /workspace/php-project/ms-user-service App\Command\HyperfTest logTest 警告消息   
2021-01-25 17:41:49  DEBUG /workspace/php-project/ms-user-service App\Command\HyperfTest logTest 调试消息   
```

## 优化
vendor/hyperf/rpc-client/src/AbstractServiceClient.php __request 方法下面
```
if (array_key_exists('error', $response)) 判断下面追加以下代码

try {
    $error = $response["error"];
    $log = new LogHelper();
    $log->error("异常捕获", new UrgentException($error["code"], $error["message"]));
} catch (\Exception $e) {

}

```


