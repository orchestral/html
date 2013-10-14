<?php namespace Orchestra\Html\Form;

use Closure;
use Orchestra\Html\AbstractableEnvironment;

class Environment extends AbstractableEnvironment {
	
	/**
	 * {@inheritdoc}
	 */
	public function make(Closure $callback)
	{
		return new FormBuilder($this->app, $callback);
	}
}
