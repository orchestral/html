<?php namespace Orchestra\Html;

use Closure,
	InvalidArgumentException;

abstract class AbstractableEnvironment {

	/**
	 * Environment instances.
	 *
	 * @var array
	 */
	protected $names = array();

	/**
	 * Construct a new environment.
	 */
	public function __construct()
	{
		$this->names = array();
	}

	/**
	 * Create a new Builder instance
	 *
	 * @access public	
	 * @param  Closure $callback
	 * @return Orchestra\Html\AbstractableBuilder
	 */
	abstract public function make(Closure $callback);

	/**
	 * Create a new builder instance of a named builder.
	 *
	 * @access public	
	 * @param  string   $name
	 * @param  Closure  $callback
	 * @return Orchestra\Html\AbstractableBuilder
	 */
	abstract public function of($name, Closure $callback = null);
}
