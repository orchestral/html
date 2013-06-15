<?php namespace Orchestra\Html\Form;

use Closure;
use InvalidArgumentException;
use Illuminate\Support\Fluent;

class Grid {

	/**
	 * Application instance.
	 *
	 * @var \Illuminate\Foundation\Application
	 */
	protected $app = null;

	/**
	 * Enable CSRF token.
	 *
	 * @var boolean
	 */
	public $token = false;

	/**
	 * Hidden fields.
	 *
	 * @var array
	 */
	protected $hiddens = array();

	/**
	 * List of row in array.
	 *
	 * @var array
	 */
	protected $row = null;

	/**
	 * All the fieldsets.
	 *
	 * @var array
	 */
	protected $fieldsets = array();

	/**
	 * Form HTML attributes.
	 *
	 * @var array
	 */
	protected $attributes = array();

	/**
	 * Set submit button message.
	 *
	 * @var string
	 */
	public $submit = null;

	/**
	 * Set the no record message.
	 *
	 * @var string
	 */
	public $format = null;

	/**
	 * Selected view path for form layout.
	 *
	 * @var array
	 */
	protected $view = null;

	/**
	 * Create a new Grid instance.
	 *
	 * @access public
	 * @param  array    $config
	 * @return void
	 */
	public function __construct($app)
	{
		$this->app = $app;
		$config = $app['config']->get('orchestra/html::form', array());

		foreach ($config as $key => $value)
		{
			if ( ! property_exists($this, $key)) continue;

			$this->{$key} = $value;
		}

		$this->row = array();
	}

	/**
	 * Set fieldset layout (view)
	 *
	 * <code>
	 *		// use default horizontal layout
	 *		$fieldset->layout('horizontal');
	 *
	 * 		// use default vertical layout
	 * 		$fieldset->layout('vertical');
	 *
	 *		// define fieldset using custom view
	 *		$fieldset->layout('path.to.view');
	 * </code>
	 *
	 * @access public
	 * @param  string   $name
	 * @return void
	 */
	public function layout($name)
	{
		switch ($name)
		{
			case 'horizontal' :
				# passthru;
			case 'vertical' :
				$this->view = "orchestra/html::form.{$name}";
				break;
			default :
				$this->view = $name;
				break;
		}
	}

	/**
	 * Attach rows data instead of assigning a model.
	 *
	 * <code>
	 *		// assign a data
	 * 		$table->with(DB::table('users')->get());
	 * </code>
	 *
	 * @access public
	 * @param  array    $rows
	 * @return void
	 */
	public function with($row = null)
	{
		if (is_null($row)) return $this->row;

		$this->row = $row;
	}

	/**
	 * Attach rows data instead of assigning a model.
	 *
	 * @access public
	 * @param  array    $rows
	 * @return void
	 * @see    self::with()
	 */
	public function row($row = null)
	{
		return $this->with($row);
	}

	/**
	 * Add or append fieldset HTML attributes.
	 *
	 * @access public
	 * @param  mixed    $key
	 * @param  mixed    $value
	 * @return void
	 */
	public function attributes($key = null, $value = null)
	{
		switch (true)
		{
			case is_null($key) :
				return $this->attributes;
				break;

			case is_array($key) :
				$this->attributes = array_merge($this->attributes, $key);
				break;

			default :
				$this->attributes[$key] = $value;
				break;
		}
	}

	/**
	 * Create a new Fieldset instance.
	 *
	 * @access public
	 * @param  string   $name
	 * @param  \Closure $callback
	 * @return \Orchestra\Html\Form\Fieldset
	 */
	public function fieldset($name, Closure $callback = null)
	{
		return $this->fieldsets[] = new Fieldset($this->app, $name, $callback);
	}

	/**
	 * Add hidden field.
	 *
	 * @access public
	 * @param  string   $name
	 * @param  \Closure $callback
	 * @return void
	 */
	public function hidden($name, $callback = null)
	{
		$value = null;
		
		if (isset($this->row) and isset($this->row->{$name})) 
		{
			$value = $this->row->{$name};
		}

		$field = new Fluent(array(
			'name'       => $name,
			'value'      => $value ?: '',
			'attributes' => array(),
		));

		if ($callback instanceof Closure) call_user_func($callback, $field);

		$this->hiddens[$name] = $this->app['form']->hidden($name, $field->value, $field->attributes);
	}

	/**
	 * Magic Method for calling the methods.
	 */
	public function __call($method, array $arguments = array())
	{
		unset($arguments);

		if ( ! in_array($method, array('fieldsets', 'view', 'hiddens')))
		{
			throw new InvalidArgumentException("Unable to use __get for [{$method}].");
		}

		return $this->$method;
	}

	/**
	 * Magic Method for handling dynamic data access.
	 */
	public function __get($key)
	{
		if ( ! in_array($key, array('attributes', 'row', 'view', 'hiddens')))
		{
			throw new InvalidArgumentException("Unable to use __get for [{$key}].");
		}

		return $this->{$key};
	}

	/**
	 * Magic Method for handling the dynamic setting of data.
	 */
	public function __set($key, $arguments)
	{
		if ( ! in_array($key, array('attributes')))
		{
			throw new InvalidArgumentException("Unable to set [{$key}].");
		}
		elseif ( ! is_array($arguments))
		{
			throw new InvalidArgumentException("Require values to be an array.");
		}

		$this->attributes($arguments, null);
	}

	/**
	 * Magic Method for checking dynamically-set data.
	 */
	public function __isset($key)
	{
		if ( ! in_array($key, array('attributes', 'row', 'view', 'hiddens')))
		{
			throw new InvalidArgumentException("Unable to use __isset for [{$key}].");
		}

		return isset($this->{$key});
	}
}
