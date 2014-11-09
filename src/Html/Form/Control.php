<?php namespace Orchestra\Html\Form;

use Closure;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Illuminate\Support\Fluent;
use Orchestra\Html\HtmlBuilder;
use Illuminate\Contracts\Config\Repository as Config;
use Orchestra\Contracts\Html\Form\Control as ControlContract;

class Control implements ControlContract
{
    /**
     * Config instance.
     *
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * Html builder instance.
     *
     * @var \Orchestra\Html\HtmlBuilder
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
    protected $template = [];

    /**
     * Create a new Field instance.
     *
     * @param  \Illuminate\Contracts\Config\Repository   $config
     * @param  \Orchestra\Html\HtmlBuilder   $html
     * @param  \Illuminate\Http\Request   $request
     */
    public function __construct(Config $config, HtmlBuilder $html, Request $request)
    {
        $this->config  = $config;
        $this->html    = $html;
        $this->request = $request;
    }

    /**
     * Set template.
     *
     * @param  array   $template
     * @return $this
     */
    public function setTemplate(array $template = [])
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
        $config = $this->config;

        return function ($row, $control, $templates = []) use ($config, $type) {
            $templates = array_merge(
                $config->get('orchestra/html::form.templates', []),
                $templates
            );

            $data = $this->buildFieldByType($type, $row, $control);

            return $this->render($templates, $data);
        };
    }

    /**
     * Build field by type.
     *
     * @param  string   $type
     * @param  mixed   $row
     * @param  \Illuminate\Support\Fluent   $control
     * @return \Illuminate\Support\Fluent
     */
    public function buildFieldByType($type, $row, Fluent $control)
    {
        $data     = $this->buildFluentData($type, $row, $control);
        $template = $this->template;
        $html     = $this->html;
        $method   = $data->get('method');

        if (in_array($method, ['checkboxes', 'select'])) {
            $data->options($this->getOptionList($row, $control));
        } elseif (in_array($method, ['checkbox', 'radio'])) {
            $data->checked($control->get('checked'));
        }

        $data->attributes($html->decorate($control->attributes, $template[$method]));

        return $data;
    }

    /**
     * Build data.
     *
     * @param  string   $type
     * @param  mixed   $row
     * @param  \Illuminate\Support\Fluent   $control
     * @return \Illuminate\Support\Fluent
     */
    public function buildFluentData($type, $row, Fluent $control)
    {
        // set the name of the control
        $name  = $control->get('name');
        $value = $this->resolveFieldValue($name, $row, $control);

        $data = new Field([
            'method'     => '',
            'type'       => '',
            'options'    => [],
            'checked'    => false,
            'attributes' => [],
            'name'       => $name,
            'value'      => $value,
        ]);

        return $this->resolveFieldType($type, $data);
    }

    /**
     * Get options from control.
     *
     * @param  mixed    $row
     * @param  \Illuminate\Support\Fluent   $control
     * @return array
     */
    protected function getOptionList($row, Fluent $control)
    {
        // set the value of options, if it's callable run it first
        $options = $control->get('options');

        if ($options instanceof Closure) {
            $options = call_user_func($options, $row, $control);
        }

        return $options;
    }

    /**
     * Render the field.
     *
     * @param  array   $templates
     * @param  \Illuminate\Support\Fluent   $data
     * @return string
     * @throws \InvalidArgumentException
     */
    public function render($templates, Fluent $data)
    {
        $method = $data->get('method');

        if (! isset($templates[$method])) {
            throw new InvalidArgumentException("Form template for [{$method}] is not available.");
        }

        return call_user_func($templates[$method], $data);
    }

    /**
     * Resolve method name and type.
     *
     * @param  string   $value
     * @param  \Illuminate\Support\Fluent   $data
     * @return \Illuminate\Support\Fluent
     */
    protected function resolveFieldType($value, Fluent $data)
    {
        $filterable = [
            'button',
            'checkbox',
            'checkboxes',
            'file',
            'password',
            'radio',
            'select',
            'textarea',
        ];

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
     * @param  string   $name
     * @param  mixed   $row
     * @param  \Illuminate\Support\Fluent   $control
     * @return mixed
     */
    protected function resolveFieldValue($name, $row, Fluent $control)
    {
        // Set the value from old input, followed by row value.
        $value = $this->request->old($name);
        $model = data_get($row, $name);

        if (! is_null($model) && is_null($value)) {
            $value = $model;
        }

        if (is_null($control->get('value'))) {
            return $value;
        }

        $value = $control->get('value');

        // If the value is set from the closure, we should use it instead of
        // value retrieved from attached data. Should also check if it's a
        // closure, when this happen run it.
        if ($value instanceof Closure) {
            $value = call_user_func($value, $row, $control);
        }

        return $value;
    }
}
