<?php

namespace Libelulasoft\AsyncAwait;

use yii\base\Component;
use Amp\Parallel\Worker;
use Yii;
use yii\base\InvalidConfigException;

class BaseAsync extends Component implements AsyncInterface
{

    protected $promises = [];

    public $loader;

    public function init()
    {
        parent::init();
        if (empty($this->loader)) {
            throw new InvalidConfigException('Required loader');
        }
        Worker\factory(new Worker\BootstrapWorkerFactory($this->loader));
    }

    /**
     * {@inheritdoc}
     */
    public function remove(string $key)
    {
        Yii::debug("Removing {$key} from promises", static::class);
        unset($this->promises[$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        Yii::debug('Reset the promises', static::class);
        $this->promises = [];
    }
}
