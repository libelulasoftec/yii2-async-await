<?php

/**
 * This is a example configuration for you app
 */

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../vendor/yiisoft/yii2/Yii.php';
// Example bootstrap from avanced template 
require __DIR__ . '/../../common/config/bootstrap.php';

$config = require __DIR__ . '/async-main.php';

new yii\console\Application($config);
