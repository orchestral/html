<?php namespace Orchestra\Html\Form;

use Closure;
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
			
			$data = $me->buildFieldByType($type, $row, $control);

			return $me->render($templates, $data);
		};
	}

	/**
	 * Build field by type.
	 *
	 * @param  string                       $type
	 * @param  \Illuminate\Support\Fluent   $row
	 * @param  \Illuminate\Support\Fluent   $control
	 * @return \Illuminate\Support\Fluent
	 */
	public function buildFieldByType($type, Fluent $row, Fluent $control)
	{
		$data   = $this->buildFluentData($type, $row, $control);
		$config = $this->config;
		$html   = $this->app['html'];

		if ($data->method === 'select')
		{
			$data->options($this->getOptionList($row, $control));
		}
		elseif (in_array($data->method, array('checkbox', 'radio')))
		{
			$data->checked($control->checked);
		}

		$data->attributes($html->decorate($control->attributes, $config[$data->method]));

		return $data;
	}

	/**
	 * Build data.
	 *
	 * @param  string                       $type
	 * @param  \Illuminate\Support\Fluent   $row
	 * @param  \Illuminate\Support\Fluent   $control
	 * @return \Illuminate\Support\Fluent
	 */
	public function buildFluentData($type, Fluent $row, Fluent $control)
	{
		// set the name of the control
		$name = $control->name;

		// set the value from old input, follow by row value.
		$value = $this->app['request']->old($name);

		if ( ! is_null($row->{$name}) and is_null($value)) $value = $row->{$name};

		// if the value is set from the closure, we should use it instead of 
		// value retrieved from attached data
		if ( ! is_null($control->value)) $value = $control->value;

		// should also check if it's a closure, when this happen run it.
		if ($value instanceof Closure) $value = $value($row, $control);

		$data = new Fluent(array(
			'method'     => '',
			'type'       => '',
			'options'    => array(),
			'checked'    => false,
			'attributes' => array(),
			'name'       => $name,
			'value'      => $value,
		));

		return $this->resolveFieldType($type, $data);
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

	/**
	 * Resolve method name and type.
	 * 
	 * @param  string                       $value
	 * @param  \Illuminate\Support\Fluent   $data
	 * @return \Illuminate\Support\Fluent
	 */
	protected function resolveFieldType($value, $data)
	{
		$filterable = array('select', 'checkbox', 'radio', 'textarea', 'password', 'file');

		if (preg_match('/^(input):([a-zA-Z]+)$/', $value, $matches))
		{
			$value = $matches[2];
		}
		else
		{
			$value = 'text';
		}

		if (in_array($value, $filterable))
		{
			$data->method($value);
		}
		else
		{
			$data->method('input')->type($value);
		}

		return $data;
	}
}
