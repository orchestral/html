<?php namespace Orchestra\Html\Table;

use Closure;
use Orchestra\Html\AbstractableEnvironment;

class Environment extends AbstractableEnvironment {
	
	/**
	 * Create a new Builder instance
	 *
	 * @access public	
	 * @param  Closure $callback
	 * @return \Orchestra\Html\Table\TableBuilder
	 */
	public function make(Closure $callback)
	{
		return new TableBuilder($this->app, $callback);
	}
}
