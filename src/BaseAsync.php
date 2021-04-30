<?php

namespace taguz91\AsyncAwait;

use InvalidArgumentException;
use yii\base\Component;
use Amp\Parallel\Worker;

class BaseAsync extends Component implements AsyncInterface
{

    protected $promises = [];

    public $loader;

    public function init()
    {
        if (empty($this->loader)) {
            throw new InvalidArgumentException('Required loader');
        }
        Worker\factory(new Worker\BootstrapWorkerFactory($this->loader));
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function remove(string $key)
    {
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
        $this->promises = [];
    }
}
