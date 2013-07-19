<?php namespace Orchestra\Html\Form;

use Closure;
use Orchestra\Html\AbstractableEnvironment;

class Environment extends AbstractableEnvironment {
	
	/**
	 * Create a new Builder instance.
	 * 
	 * @param  \Closure $callback
	 * @return \Orchestra\Html\Form\FormBuilder
	 */
	public function make(Closure $callback)
	{
		return new FormBuilder($this->app, $callback);
	}
}
