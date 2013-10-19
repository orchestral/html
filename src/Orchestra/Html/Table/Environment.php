<?php namespace Orchestra\Html\Table;

use Closure;

class Environment extends \Orchestra\Html\Abstractable\Environment
{
    /**
     * {@inheritdoc}
     */
    public function make(Closure $callback)
    {
        return new TableBuilder($this->app, $callback);
    }
}
