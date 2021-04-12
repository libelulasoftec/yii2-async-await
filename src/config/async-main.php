<?php

return [
  'id' => 'app-async',
  'basePath' => dirname(__DIR__),
  'bootstrap' => ['log'],
  'controllerNamespace' => 'frontend\controllers',
  'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
  'aliases' => [
    '@bower' => '@vendor/bower-asset',
    '@npm'   => '@vendor/npm-asset',
  ],
  'components' => [
    'log' => [
      'traceLevel' => YII_DEBUG ? 3 : 0,
      'targets' => [
        [
          'class' => 'yii\log\FileTarget',
          'levels' => ['error', 'warning'],
        ],
      ],
    ],
    'cache' => [
      'class' => 'yii\caching\FileCache',
    ],
    // Config your database 
    'db' => [
      'class' => 'yii\db\Connection',
      'dsn' => 'mysql:host=localhost;dbname=yii2advanced',
      'username' => 'root',
      'password' => '',
      'charset' => 'utf8',
    ],
  ],
  'params' => [],
];
