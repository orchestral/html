<?php namespace Orchestra\Html\Table;

use Closure;
use Orchestra\Html\AbstractableEnvironment;

class Environment extends AbstractableEnvironment {
	
	/**
	 * Create a new Builder instance
	 *
	 * @access public	
	 * @param  Closure $callback
	 * @return Orchestra\Html\Table\TableBuilder
	 */
	public function make(Closure $callback)
	{
		return new TableBuilder($this->app, $callback);
	}

	/**
	 * Create a new builder instance of a named builder.
	 *
	 * @access public	
	 * @param  string   $name
	 * @param  Closure  $callback
	 * @return Orchestra\Html\Table\TableBuilder
	 */
	public function of($name, Closure $callback = null)
	{
		if ( ! isset($this->names[$name]))
		{
			$this->names[$name] = $this->make($callback);
			$this->names[$name]->name = $name;
		}

		return $this->names[$name];
	}
}
