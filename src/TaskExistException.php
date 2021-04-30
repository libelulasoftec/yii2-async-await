<?php

namespace taguz91\AsyncAwait;

use Throwable;
use yii\base\Exception;

class TaskExistException extends Exception
{

    public function __construct(
        string $message,
        int $code = 2001,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
