<?php namespace Orchestra\Html\Form;

use InvalidArgumentException;
use Illuminate\Container\Container;
use Illuminate\Support\Fluent;

class Field {
	
	/**
	 * Application instance.
	 *
	 * @var Illuminate\Container\Container
	 */
	protected $app = null;

	/**
	 * Configuration.
	 *
	 * @var  array
	 */
	protected $config = array();

	/**
	 * Create a new Field instance.
	 * 
	 * @param \Illuminate\Container\Container   $app
	 * @param array                             $config
	 */
	public function __construct(Container $app, array $config = array())
	{
		$this->app    = $app;
		$this->config = $config;
	}

	/**
	 * Generate Field.
	 *
	 * @param  string   $type
	 * @return \Closure
	 */
	public function generate($type)
	{
		$me = $this;
		$config = $this->app['config'];

		return function ($row, $control, $templates = array()) use ($config, $me, $type) 
		{
			$templates = array_merge(
				$config->get('orchestra/html::form.templates', array()),
				$templates
			);
			
			$data = $me->buildFluentData($row, $control);
			$me->buildFieldByType($type, $row, $control, $data);

			return $me->render($templates, $data);
		};
	}

	/**
	 * Build field by type.
	 */
	public function buildFieldByType($type, Fluent $row, Fluent $control, Fluent $data)
	{
		$config = $this->config;

		// prep control type information
		$type    = ($type === 'input:password' ? 'password' : $type);
		$methods = explode(':', $type);
		$html    = $this->app['html'];

		if (in_array($type, array('select', 'input:select')))
		{
			$data->method('select')
				->attributes($html->decorate($control->attributes, $config['select']))
				->options($this->getOptionList($row, $control));
		}
		elseif (in_array($type, array('checkbox', 'input:checkbox')))
		{
			$data->method('checkbox')
				->checked($control->checked);
		}
		elseif (in_array($type, array('radio', 'input:radio')))
		{
			$data->method('radio')
				->checked($control->checked);
		}
		elseif (in_array($type, array('textarea', 'input:textarea')))
		{
			$data->method('textarea')
				->attributes($html->decorate($control->attributes, $config['textarea']));
		}
		elseif (in_array($type, array('password', 'input:password'))) 
		{
			$data->method('password')
				->attributes($html->decorate($control->attributes, $config['password']));
		}
		elseif (in_array($type, array('file', 'input:file'))) 
		{
			$data->method('file')
				->attributes($html->decorate($control->attributes, $config['file']));
		}
		elseif (isset($methods[0]) and $methods[0] === 'input') 
		{
			$methods[1] = $methods[1] ?: 'text';
			$data->method('input')
				->type($methods[1])
				->attributes($html->decorate($control->attributes, $config['input']));
		}
		else
		{
			$data->method('input')
				->type('text')
				->attributes($html->decorate($control->attributes, $config['input']));	
		}
	}

	/**
	 * Build data.
	 *
	 * @param  \Illuminate\Support\Fluent   $row
	 * @param  \Illuminate\Support\Fluent   $control
	 * @return \Illuminate\Support\Fluent
	 */
	public function buildFluentData($row, $control)
	{
		// set the name of the control
		$name = $control->name;
		
		// set the value from old input, follow by row value.
		$value = $this->app['request']->old($name);

		if (! is_null($row->{$name}) and is_null($value)) $value = $row->{$name};

		// if the value is set from the closure, we should use it instead of 
		// value retrieved from attached data
		if ( ! is_null($control->value)) $value = $control->value;

		// should also check if it's a closure, when this happen run it.
		if ($value instanceof Closure) $value = $value($row, $control);

		return new Fluent(array(
			'method'     => '',
			'type'       => '',
			'options'    => array(),
			'checked'    => false,
			'attributes' => array(),
			'name'       => $name,
			'value'      => $value,
		));
	}

	/**
	 * Get options from control.
	 *
	 * @param  \Illuminate\Support\Fluent   $row
	 * @param  \Illuminate\Support\Fluent   $control
	 * @return array
	 */
	protected function getOptionList(Fluent $row, Fluent $control)
	{
		// set the value of options, if it's callable run it first
		$options = $control->options;
		
		if ( ! ($options instanceof Closure)) return $options;

		return call_user_func($options, $row, $control);
	}

	/**
	 * Render the field.
	 * 
	 * @param  array                        $templates
	 * @param  Illuminate\Support\Fluent    $data
	 * @return string
	 */
	public function render($templates, $data)
	{
		if ( ! isset($templates[$data->method]))
		{
			throw new InvalidArgumentException(
				"Form template for [{$data->method}] is not available."
			);
		}

		return call_user_func($templates[$data->method], $data);
	}
}
