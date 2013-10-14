<?php namespace Orchestra\Html\Table;

use Closure;
use Orchestra\Html\AbstractableEnvironment;

class Environment extends AbstractableEnvironment {
	
	/**
	 * {@inheritdoc}
	 */
	public function make(Closure $callback)
	{
		return new TableBuilder($this->app, $callback);
	}
}
