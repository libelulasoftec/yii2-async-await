<?php

namespace taguz91\AsyncAwait;

use Closure;
use Amp\Parallel\Worker;
use Amp\Promise;
use Opis\Closure\SerializableClosure;

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
class AsyncAwait extends BaseAsync
{
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
   * Execute all promises and return the functions results 
   * Reset all promises 
   */
  public function run(): array
  {
    $res = Promise\wait(Promise\all($this->promises));
    $this->flush();
    return $res;
  }
}
