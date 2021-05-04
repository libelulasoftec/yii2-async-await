Async await
===========
Async await for php yii2 integration, this uses amphp and support callbacks and Task 

[![Latest Stable Version](https://poser.pugx.org/taguz91/yii2-async-await/v)](//packagist.org/packages/taguz91/yii2-async-await) 
[![Total Downloads](https://poser.pugx.org/taguz91/yii2-async-await/downloads)](//packagist.org/packages/taguz91/yii2-async-await) 
[![Latest Unstable Version](https://poser.pugx.org/taguz91/yii2-async-await/v/unstable)](//packagist.org/packages/taguz91/yii2-async-await) 
[![License](https://poser.pugx.org/taguz91/yii2-async-await/license)](//packagist.org/packages/taguz91/yii2-async-await)

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer require --prefer-dist taguz91/yii2-async-await
```

or add

```
"taguz91/yii2-async-await": "~1.0.0"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by: 

Need to configure the bootstrap configuration app, because the async run in another context.

The following examples was tested in advanced template.

For example: 
```php
return [
    'id' => 'app-async',
    'basePath' => dirname(__DIR__),
    // Your controllers, you can change this to backend\controllers
    'controllerNamespace' => 'frontend\controllers',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    // Required components for async functions 
    'components' => [
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
```

Also need to create the entry script, for autoload the dependecies and start Yii2 app. 

For example: 
```php
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');
// Autoload for composer an yii2 
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../vendor/yiisoft/yii2/Yii.php';
require __DIR__ . '/../../common/config/bootstrap.php';


// Your custom configuration for async 
$config = require __DIR__ . '/async-main.php';
// You can change the console application to web application 
// You dont have acces to vars or configuration in parent context
new yii\console\Application($config);
```

Adding to web app, in components section you need to add this configuration:  

```php 
[
    ...,
    'components' => [
        // If you want to use callbacks 
        'asyncAwait' => [
            'class' => \taguz91\AsyncAwait\AsyncAwait::class,
            // Your own entry script, see the above examples
            'loader' => __DIR__ . '/async.php'
        ],
        // If you want to use classes, this is more faster 
        'asyncTask' => [
            'class' => \taguz91\AsyncAwait\AsyncTask::class,
            // Your own entry script, see the above examples
            'loader' => __DIR__ . '/async.php'
        ],
    ]
]
```

Code example for callbacks usage:

```php 

use common\models\User;

// Adding you async function 
Yii::$app->asyncAwait->add('sendUserEmail', function (string $idUser, string $sender) {
    $user = User::findOne($idUser);
    // Return any serializable data, is prefer return a basic array response 
    return \common\models\Email::sendUser($user, $sender);
}, $idUser, $sender);

Yii::$app->asyncAwait->add('sendUserMessage', function (string $message, string $number) {
    if ($number === '') return 'Number is required.';
    return \common\models\Phone::sendMessage($message, $number);
}, $message, $number);

// Execute your asynct functions 
$responses = Yii::$app->asyncAwait->run();
// Getting especific response
$emailResponse = $responses['sendUserEmail'];

```

Code example for Tasks:

```php
// This class is autoloadable 
namespace common\tasks;

use Amp\Parallel\Worker\Environment;
use Amp\Parallel\Worker\Task;
use yii\helpers\VarDumper;

class PrintTask implements Task
{
    private $text;

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    /**
     * {@inheritdoc}
     */
    public function run(Environment $environment)
    {
        return [
            'response' => "FUTURE PROMISE WORKER {$this->text}",
            'enviroment' => VarDumper::dumpAsString($environment),
        ];
    }
}


/** @var \taguz91\AsyncAwait\AsyncTask */
$async = Yii::$app->asyncTask;

$async->add('1', new PrintTask('THIS IS MY LARGE TEXT'));
$response = $async->run();
```