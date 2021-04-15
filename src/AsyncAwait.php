<?php

namespace taguz91\AsyncAwait;

use Closure;
use Amp\Parallel\Worker;
use Amp\Promise;
use InvalidArgumentException;
use Opis\Closure\SerializableClosure;
use yii\base\Component;

/**
 * Support for async await, using amphp. To use it, just config in your application components. 
 * 
 * Usage example: 
 * ```
 * Yii::$app->asyncAwait->add('sendUserMessage', function (string $message, string $number) {
 *   if ($number === '') return 'Number is required.';
 *   return \common\models\Phone::sendMessage($message, $number);
 * }, $message, $number);
 * ```
 */
class AsyncAwait extends Component
{

  private $promises = [];

  public $loader;
  
  public function init() {
    if (empty($this->loader)) {
      throw new InvalidArgumentException('Required loader');
    }
    Worker\factory(new Worker\BootstrapWorkerFactory($this->loader));
    parent::init();
  }

  /**
   * Add a promise
   * 
   * @param string $key - Unique identifiquer for a function
   * @param Closure $closure - Your clouse with args 
   * @param ...$args - All params for you closure
   * @throws PromiseExistException - When the key exist
   */
  public function add(
    string $key,
    Closure $closure,
    ...$args
  ) {
    if (isset($this->promises[$key])) {
      throw new PromiseExistException('Promise ' . $key . '  exist, change the {$key}');
    }
    $this->promises[$key] = Worker\enqueueCallable(new SerializableClosure($closure), ...$args);
    return $this;
  }

  /**
   * Delete a function in your actually promises
   */
  public function remove(string $key)
  {
    unset($this->promises[$key]);
  }

  /**
   * Execute all promises and return the functions results 
   * Reset all promises 
   */
  public function run(): array
  {
    $res = Promise\wait(Promise\all($this->promises));
    $this->flush();
    return $res;
  }

  /**
   * Reset all promises
   */
  public function flush()
  {
    $this->promises = [];
  }
}
