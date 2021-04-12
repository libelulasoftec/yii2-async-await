<?php

namespace taguz91\AsyncAwait;

use Throwable;
use yii\base\Exception;

class PromiseExistException extends Exception
{

  public function __construct(
    string $message,
    int $code = 2000,
    Throwable $previous = null
  ) {
    parent::__construct($message, $code, $previous);
  }
}
