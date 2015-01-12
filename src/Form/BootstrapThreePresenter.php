<?php namespace Orchestra\Html\Form;

use Illuminate\Support\Traits\Macroable;
use Orchestra\Contracts\Html\Form\Template;
use Orchestra\Contracts\Html\Form\Field as FieldContract;

class BootstrapThreePresenter implements Template
{
    use Macroable;

    protected $form;

    protected $html;

    public function __construct(FormBuilder $form, HtmlBuilder $html)
    {
        $this->form = $form;
        $this->html = $html;
    }

    /**
     * Button template.
     *
     * @param  \Orchestra\Contracts\Html\Form\Field $field
     * @return string
     */
    public function button(FieldContract $field)
    {
        $attributes = $this->html->decorate($field->attributes, ['class' => 'btn']);

        return $this->form->button($field->value, $attributes);
    }

    /**
     * Checkbox template.
     *
     * @param  \Orchestra\Contracts\Html\Form\Field $field
     * @return string
     */
    public function checkbox(FieldContract $field)
    {
        return $this->form->checkbox(
            $field->name,
            $field->value,
            $field->checked,
            $field->attributes
        );
    }

    /**
     * Checkboxes template.
     *
     * @param  \Orchestra\Contracts\Html\Form\Field $field
     * @return string
     */
    public function checkboxes(FieldContract $field)
    {
        return $this->form->checkboxes(
            $field->name,
            $field->options,
            $field->checked,
            $field->attributes
        );
    }

    /**
     * File template.
     *
     * @param  \Orchestra\Contracts\Html\Form\Field $field
     * @return string
     */
    public function file(FieldContract $field)
    {
        $attributes = $this->html->decorate($field->attributes, ['class' => 'form-control']);

        return $this->form->file($field->name, $attributes);
    }

    /**
     * Input template.
     *
     * @param  \Orchestra\Contracts\Html\Form\Field $field
     * @return string
     */
    public function input(FieldContract $field)
    {
        $attributes = $this->html->decorate($field->attributes, ['class' => 'form-control']);

        return $this->form->input(
            $field->type,
            $field->name,
            $field->value,
            $attributes
        );
    }

    /**
     * Password template.
     *
     * @param  \Orchestra\Contracts\Html\Form\Field $field
     * @return string
     */
    public function password(FieldContract $field)
    {
        $attributes = $this->html->decorate($field->attributes, ['class' => 'form-control']);

        return $this->form->password($field->name, $attributes);
    }

    /**
     * Radio template.
     *
     * @param  \Orchestra\Contracts\Html\Form\Field $field
     * @return string
     */
    public function radio(FieldContract $field)
    {
        return $this->form->radio($field->name, $field->value, $field->checked);
    }

    /**
     * Select template.
     *
     * @param  \Orchestra\Contracts\Html\Form\Field $field
     * @return string
     */
    public function select(FieldContract $field)
    {
        $attributes = $this->form->decorate($field->attributes, ['class' => 'form-control']);

        return $this->form->select(
            $field->name,
            $field->options,
            $field->value,
            $attributes
        );
    }

    /**
     * Textarea template.
     *
     * @param  \Orchestra\Contracts\Html\Form\Field $field
     * @return string
     */
    public function textarea(FieldContract $field)
    {
        $attributes = $this->html->decorate($field->attributes, ['class' => 'form-control']);

        return $this->form->textarea(
            $field->name,
            $field->value,
            $attributes
        );
    }
}
