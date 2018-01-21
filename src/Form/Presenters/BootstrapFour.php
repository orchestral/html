<?php

namespace Orchestra\Html\Form\Presenters;

use Illuminate\Support\Traits\Macroable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Arrayable;
use Orchestra\Contracts\Html\Form\Template;
use Orchestra\Html\FormBuilder as BaseFormBuilder;
use Orchestra\Html\HtmlBuilder as BaseHtmlBuilder;
use Orchestra\Contracts\Html\Form\Field as FieldContract;

class BootstrapThree implements Template
{
    use Macroable;

    /**
     * Form builder.
     *
     * @var \Orchestra\Html\FormBuilder
     */
    protected $form;

    /**
     * Html builder.
     *
     * @var \Orchestra\Html\HtmlBuilder
     */
    protected $html;

    /**
     * Construct a new presenter.
     *
     * @param \Orchestra\Html\FormBuilder  $form
     * @param \Orchestra\Html\HtmlBuilder  $html
     */
    public function __construct(BaseFormBuilder $form, BaseHtmlBuilder $html)
    {
        $this->form = $form;
        $this->html = $html;
    }

    /**
     * Button template.
     *
     * @param  \Orchestra\Contracts\Html\Form\Field $field
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function button(FieldContract $field): Htmlable
    {
        $attributes = $this->html->decorate($field->get('attributes'), ['class' => 'btn']);

        return $this->form->button($field->get('value'), $attributes);
    }

    /**
     * Checkbox template.
     *
     * @param  \Orchestra\Contracts\Html\Form\Field $field
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function checkbox(FieldContract $field): Htmlable
    {
        return $this->form->checkbox(
            $field->get('name'),
            $field->get('value'),
            $field->get('checked'),
            $field->get('attributes')
        );
    }

    /**
     * Checkboxes template.
     *
     * @param  \Orchestra\Contracts\Html\Form\Field $field
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function checkboxes(FieldContract $field): Htmlable
    {
        return $this->form->checkboxes(
            $field->get('name'),
            $this->asArray($field->get('options')),
            $field->get('checked'),
            $field->get('attributes')
        );
    }

    /**
     * File template.
     *
     * @param  \Orchestra\Contracts\Html\Form\Field $field
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function file(FieldContract $field): Htmlable
    {
        $attributes = $this->html->decorate($field->get('attributes'), ['class' => 'form-control']);

        return $this->form->file($field->get('name'), $attributes);
    }

    /**
     * Input template.
     *
     * @param  \Orchestra\Contracts\Html\Form\Field $field
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function input(FieldContract $field): Htmlable
    {
        $attributes = $this->html->decorate($field->get('attributes'), ['class' => 'form-control']);

        return $this->form->input(
            $field->get('type'),
            $field->get('name'),
            $field->get('value'),
            $attributes
        );
    }

    /**
     * Password template.
     *
     * @param  \Orchestra\Contracts\Html\Form\Field $field
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function password(FieldContract $field): Htmlable
    {
        $attributes = $this->html->decorate($field->get('attributes'), ['class' => 'form-control']);

        return $this->form->password($field->get('name'), $attributes);
    }

    /**
     * Radio template.
     *
     * @param  \Orchestra\Contracts\Html\Form\Field $field
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function radio(FieldContract $field): Htmlable
    {
        return $this->form->radio($field->get('name'), $field->get('value'), $field->get('checked'));
    }

    /**
     * Select template.
     *
     * @param  \Orchestra\Contracts\Html\Form\Field $field
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function select(FieldContract $field): Htmlable
    {
        $attributes = $this->html->decorate($field->get('attributes'), ['class' => 'form-control']);

        return $this->form->select(
            $field->get('name'),
            $this->asArray($field->get('options')),
            $field->get('value'),
            $attributes
        );
    }

    /**
     * Textarea template.
     *
     * @param  \Orchestra\Contracts\Html\Form\Field $field
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function textarea(FieldContract $field): Htmlable
    {
        $attributes = $this->html->decorate($field->get('attributes'), ['class' => 'form-control']);

        return $this->form->textarea(
            $field->get('name'),
            $field->get('value'),
            $attributes
        );
    }

    /**
     * Convert input to actual array.
     *
     * @param  array|\Illuminate\Contracts\Support\Arrayable  $array
     *
     * @return array
     */
    protected function asArray($array): array
    {
        return $array instanceof Arrayable ? $array->toArray() : $array;
    }
}
