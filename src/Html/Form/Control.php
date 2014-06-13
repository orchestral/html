<?php namespace Orchestra\Html\Form;

use Closure;
use InvalidArgumentException;
use Illuminate\Config\Repository;
use Illuminate\Html\HtmlBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Fluent;

class Control
{
    /**
     * Config instance.
     *
     * @var \Illuminate\Config\Repository
     */
    protected $config;

    /**
     * Html builder instance.
     *
     * @var \Illuminate\Html\HtmlBuilder
     */
    protected $html;

    /**
     * Request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Template configuration.
     *
     * @var  array
     */
    protected $template = array();

    /**
     * Create a new Field instance.
     *
     * @param  \Illuminate\Config\Repository    $config
     * @param  \Orchestra\Html\HtmlBuilder      $html
     * @param  \Illuminate\Http\Request         $request
     */
    public function __construct(Repository $config, HtmlBuilder $html, Request $request)
    {
        $this->config  = $config;
        $this->html    = $html;
        $this->request = $request;
    }

    /**
     * Set template.
     *
     * @param  array   $template
     * @return Field
     */
    public function setTemplate(array $template = array())
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get template.
     *
     * @return array
     */
    public function getTemplate()
    {
        return $this->template;
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
        $config = $this->config;

        return function ($row, $control, $templates = array()) use ($config, $me, $type) {
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
     * @param  mixed                        $row
     * @param  \Illuminate\Support\Fluent   $control
     * @return \Illuminate\Support\Fluent
     */
    public function buildFieldByType($type, $row, Fluent $control)
    {
        $data     = $this->buildFluentData($type, $row, $control);
        $template = $this->template;
        $html     = $this->html;

        if ($data->method === 'select') {
            $data->options($this->getOptionList($row, $control));
        } elseif (in_array($data->method, array('checkbox', 'radio'))) {
            $data->checked($control->checked);
        }

        $data->attributes($html->decorate($control->attributes, $template[$data->method]));

        return $data;
    }

    /**
     * Build data.
     *
     * @param  string                       $type
     * @param  mixed                        $row
     * @param  \Illuminate\Support\Fluent   $control
     * @return \Illuminate\Support\Fluent
     */
    public function buildFluentData($type, $row, Fluent $control)
    {
        // set the name of the control
        $name  = $control->name;
        $value = $this->resolveFieldValue($name, $row, $control);

        $data = new Field(array(
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
     * @param  mixed                        $row
     * @param  \Illuminate\Support\Fluent   $control
     * @return array
     */
    protected function getOptionList($row, Fluent $control)
    {
        // set the value of options, if it's callable run it first
        $options = $control->options;

        if ($options instanceof Closure) {
            $options = call_user_func($options, $row, $control);
        }

        return $options;
    }

    /**
     * Render the field.
     *
     * @param  array                        $templates
     * @param  \Illuminate\Support\Fluent   $data
     * @return string
     */
    public function render($templates, Fluent $data)
    {
        if (! isset($templates[$data->method])) {
            throw new InvalidArgumentException("Form template for [{$data->method}] is not available.");
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
    protected function resolveFieldType($value, Fluent $data)
    {
        $filterable = array('button', 'checkbox', 'file', 'password', 'radio', 'select', 'textarea');

        if (preg_match('/^(input):([a-zA-Z]+)$/', $value, $matches)) {
            $value = $matches[2];
        } elseif (! in_array($value, $filterable)) {
            $value = 'text';
        }

        if (in_array($value, $filterable)) {
            $data->method($value);
        } else {
            $data->method('input')->type($value);
        }

        return $data;
    }

    /**
     * Resolve field value.
     *
     * @param  string                       $name
     * @param  mixed                        $row
     * @param  \Illuminate\Support\Fluent   $control
     * @return mixed
     */
    protected function resolveFieldValue($name, $row, Fluent $control)
    {
        // set the value from old input, follow by row value.
        $value = $this->request->old($name);

        if (!is_null($row->{$name}) && is_null($value)) {
            $value = $row->{$name};
        }

        // if the value is set from the closure, we should use it instead of
        // value retrieved from attached data
        if (!is_null($control->value)) {
            $value = $control->value;
        }

        // should also check if it's a closure, when this happen run it.
        if ($value instanceof Closure) {
            $value = $value($row, $control);
        }

        return $value;
    }
}
