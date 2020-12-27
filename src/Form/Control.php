<?php

namespace Orchestra\Html\Form;

use Closure;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Fluent;
use InvalidArgumentException;
use Orchestra\Contracts\Html\Form\Control as ControlContract;
use Orchestra\Contracts\Html\Form\Field as FieldContract;
use Orchestra\Contracts\Html\Form\Template;
use Orchestra\Html\Concerns\Decorate;

class Control implements ControlContract
{
    use Decorate;

    /**
     * Container instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $app;

    /**
     * Html builder instance.
     *
     * @var \Orchestra\Html\HtmlBuilder
     */
    protected $html;

    /**
     * Presenter instance.
     *
     * @var \Orchestra\Contracts\Html\Form\Template|null
     */
    protected $presenter;

    /**
     * Request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Fieldset templates configuration.
     *
     * @var  array
     */
    protected $templates = [];

    /**
     * Create a new Field instance.
     */
    public function __construct(Container $app, Request $request)
    {
        $this->app = $app;
        $this->request = $request;
    }

    /**
     * Set presenter instance.
     *
     * @return $this
     */
    public function setPresenter(Template $presenter)
    {
        $this->presenter = $presenter;

        return $this;
    }

    /**
     * Get presenter instance.
     *
     * @return \Orchestra\Contracts\Html\Form\Template
     */
    public function getPresenter(): ?Template
    {
        return $this->presenter;
    }

    /**
     * Set template.
     *
     * @return $this
     */
    public function setTemplates(array $templates = [])
    {
        $this->templates = $templates;

        return $this;
    }

    /**
     * Get template.
     */
    public function getTemplates(): array
    {
        return $this->templates;
    }

    /**
     * Generate Field.
     */
    public function generate(string $type): callable
    {
        return function ($row, $control, $templates = []) use ($type) {
            $data = $this->buildFieldByType($type, $row, $control);

            return $this->render($templates, $data);
        };
    }

    /**
     * Build field by type.
     *
     * @param  mixed  $row
     *
     * @return \Illuminate\Support\Fluent
     */
    public function buildFieldByType(string $type, $row, Fluent $control): FieldContract
    {
        $templates = $this->templates;

        $data = $this->buildFluentData($type, $row, $control);
        $method = $data->get('method');

        $data->options($this->getOptionList($row, $control));
        $data->checked($control->get('checked'));

        $data->attributes($this->decorate($control->get('attributes'), $templates[$method] ?? []));

        return $data;
    }

    /**
     * Build data.
     *
     * @param  mixed  $row
     */
    public function buildFluentData(string $type, $row, Fluent $control): FieldContract
    {
        // set the name of the control
        $name = $control->get('name');
        $value = $this->resolveFieldValue($name, $row, $control);

        $data = new Field([
            'method' => '',
            'type' => '',
            'options' => [],
            'checked' => false,
            'attributes' => [],
            'name' => $name,
            'value' => $value,
        ]);

        return $this->resolveFieldType($type, $data);
    }

    /**
     * Get options from control.
     *
     * @param  mixed  $row
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable
     */
    protected function getOptionList($row, Fluent $control)
    {
        // set the value of options, if it's callable run it first
        $options = $control->get('options');

        if ($options instanceof Closure) {
            $options = $options($row, $control);
        }

        return $options;
    }

    /**
     * Render the field.
     *
     * @throws \InvalidArgumentException
     */
    public function render(array $templates, Fluent $field): string
    {
        $method = $field->get('method');
        $template = $templates[$method] ?? [$this->presenter, $method];

        if (! \is_callable($template)) {
            throw new InvalidArgumentException("Form template for [{$method}] is not available.");
        }

        return $template($field);
    }

    /**
     * Resolve method name and type.
     */
    protected function resolveFieldType(string $value, Fluent $data): FieldContract
    {
        if (\preg_match('/^(input):([a-zA-Z]+)$/', $value, $matches)) {
            $value = $matches[2];
        }

        $filterable = \in_array($value, \array_keys($this->templates))
            || (! \is_null($this->presenter) && \method_exists($this->presenter, $value));

        if ((bool) $filterable) {
            $data->method($value);
        } else {
            $data->method('input')->type($value ?: 'text');
        }

        return $data;
    }

    /**
     * Resolve field value.
     *
     * @param  mixed  $row
     *
     * @return mixed
     */
    protected function resolveFieldValue(string $name, $row, Fluent $control)
    {
        // Set the value from old input, followed by row value.
        $value = $control->get('value');
        $model = \data_get($row, $name, $this->request->old($name));

        if (! \is_null($model)) {
            return $model;
        }

        // If the value is set from the closure, we should use it instead of
        // value retrieved from attached data. Should also check if it's a
        // closure, when this happen run it.
        if ($value instanceof Closure) {
            $value = $value($row, $control);
        }

        return $value;
    }
}
