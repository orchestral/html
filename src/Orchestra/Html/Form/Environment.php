<?php namespace Orchestra\Html\Form;

use Closure;

class Environment extends \Orchestra\Html\Abstractable\Environment
{
    /**
     * {@inheritdoc}
     */
    public function make(Closure $callback)
    {
        return new FormBuilder($this->app, $callback);
    }
}
