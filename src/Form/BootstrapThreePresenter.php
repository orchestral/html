<?php

namespace Orchestra\Html\Form;

use Illuminate\Support\Traits\Macroable;
use Illuminate\Contracts\Support\Arrayable;
use Orchestra\Contracts\Html\Form\Template;
use Orchestra\Html\FormBuilder as BaseFormBuilder;
use Orchestra\Html\HtmlBuilder as BaseHtmlBuilder;
use Orchestra\Contracts\Html\Form\Field as FieldContract;

class BootstrapThreePresenter implements Template
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
     * @return string
     */
    public function button(FieldContract $field)
    {
        $attributes = $this->html->decorate($field->get('attributes'), ['class' => 'btn']);

        return $this->form->button($field->get('value'), $attributes);
    }

    /**
     * Checkbox template.
     *
     * @param  \Orchestra\Contracts\Html\Form\Field $field
     *
     * @return string
     */
    public function checkbox(FieldContract $field)
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
     * @return string
     */
    public function checkboxes(FieldContract $field)
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
     * @return string
     */
    public function file(FieldContract $field)
    {
        $attributes = $this->html->decorate($field->get('attributes'), ['class' => 'form-control']);

        return $this->form->file($field->get('name'), $attributes);
    }

    /**
     * Input template.
     *
     * @param  \Orchestra\Contracts\Html\Form\Field $field
     *
     * @return string
     */
    public function input(FieldContract $field)
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
     * @return string
     */
    public function password(FieldContract $field)
    {
        $attributes = $this->html->decorate($field->get('attributes'), ['class' => 'form-control']);

        return $this->form->password($field->get('name'), $attributes);
    }

    /**
     * Radio template.
     *
     * @param  \Orchestra\Contracts\Html\Form\Field $field
     *
     * @return string
     */
    public function radio(FieldContract $field)
    {
        return $this->form->radio($field->get('name'), $field->get('value'), $field->get('checked'));
    }

    /**
     * Select template.
     *
     * @param  \Orchestra\Contracts\Html\Form\Field $field
     *
     * @return string
     */
    public function select(FieldContract $field)
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
     * @return string
     */
    public function textarea(FieldContract $field)
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
    protected function asArray($array)
    {
        return ($array instanceof Arrayable ? $array->toArray() : $array);
    }
}
