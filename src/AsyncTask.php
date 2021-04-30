<?php

namespace taguz91\AsyncAwait;

use Amp\Loop;
use Amp\Parallel\Worker\DefaultPool;
use Amp\Parallel\Worker\Task;
use Yii;
use yii\base\InvalidConfigException;

/**
 * Support for async tasks, using amphp. To use it, just config in your application components. 
 * 
 * Usage example: 
 * ```
 * ```
 */
class AsyncTask extends BaseAsync
{
    public $maxPoolSize = DefaultPool::DEFAULT_MAX_SIZE;

    public function init()
    {
        parent::init();
        if (!is_int($this->maxPoolSize) || $this->maxPoolSize > 64) {
            throw new InvalidConfigException('The {maxPoolSize} need to be int and less that 64');
        }
    }

    /**
     * Add a promise
     * 
     * @param string $key - Unique identifiquer for a function
     * @param Task $task - Your task
     * @throws TaskExistException - When the key exist
     */
    public function add(
        string $key,
        Task $task
    ) {
        if (isset($this->promises[$key])) {
            throw new TaskExistException('Task ' . $key . '  exist, change the {$key}');
        }
        $this->promises[$key] = $task;
        return $this;
    }


    /**
     * {@inheritdoc} 
     */
    public function run(): array
    {
        $results = [];
        $tasks = $this->tasks;

        if (empty($tasks)) return [];

        Yii::beginProfile('Runtask', self::class);

        $maxPoolSize = $this->maxPoolSize;

        Loop::run(function () use (&$results, $tasks, $maxPoolSize) {
            $pool = new DefaultPool($maxPoolSize);

            $coroutines = [];
            foreach ($tasks as $key => $task) {
                $coroutines[$key] = \Amp\call(function () use ($pool, $task) {
                    $result = yield $pool->enqueue($task);
                    return $result;
                });
            }

            $results = yield \Amp\Promise\all($coroutines);

            return yield $pool->shutdown();
        });

        $this->tasks = [];

        Yii::endProfile('Runtask', self::class);

        $this->flush();
        return $results;
    }
}
