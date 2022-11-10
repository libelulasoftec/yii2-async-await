<?php

namespace Libelulasoft\AsyncAwait;

interface AsyncInterface
{

    /**
     * Delete your task or promise
     */
    public function remove(string $key);

    /**
     * Execute all promises and return the functions results 
     */
    public function run();

    /**
     * Reset all promises
     */
    public function flush();
}
