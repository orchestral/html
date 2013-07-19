<?php namespace Orchestra\Html;

use Closure;
use InvalidArgumentException;

abstract class AbstractableEnvironment {

	/**
	 * Application instance.
	 *
	 * @var \Illuminate\Foundation\Application
	 */
	protected $app = null;

	/**
	 * Environment instances.
	 *
	 * @var array
	 */
	protected $names = array();

	/**
	 * Construct a new environment.
	 *
	 * @param  \Illuminate\Foundation\Application   $app
	 * @return void
	 */
	public function __construct($app)
	{
		$this->app = $app;
	}

	/**
	 * Create a new Builder instance.
	 *
	 * @param  \Closure $callback
	 * @return \Orchestra\Html\AbstractableBuilder
	 */
	abstract public function make(Closure $callback);

	/**
	 * Create a new builder instance of a named builder.
	 *
	 * @param  string   $name
	 * @param  \Closure $callback
	 * @return \Orchestra\Html\AbstractableBuilder
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
