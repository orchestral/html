<?php

use Orchestra\Html\Form\Field;

return [

    /*
    |----------------------------------------------------------------------
    | Default Error Message String
    |----------------------------------------------------------------------
    |
    | Set default error message string format for Orchestra\Form.
    |
    */

    'format' => '<p class="help-block error">:message</p>',

    /*
    |----------------------------------------------------------------------
    | Default Submit Button String
    |----------------------------------------------------------------------
    |
    | Set default submit button string or language replacement key for
    | Orchestra\Form.
    |
    */

    'submit' => 'orchestra/foundation::label.submit',

    /*
    |----------------------------------------------------------------------
    | Default View Layout
    |----------------------------------------------------------------------
    |
    | Orchestra\Form would require a View to parse the provided form instance.
    |
    */

    'view' => 'orchestra/html::form.horizontal',

    /*
    |----------------------------------------------------------------------
    | Layout Configuration
    |----------------------------------------------------------------------
    |
    | Set default submit button for Orchestra\Form.
    |
    */

    'fieldset' => [
        'input'    => ['class' => 'twelve columns input-with-feedback'],
        'password' => ['class' => 'twelve columns input-with-feedback'],
        'select'   => ['class' => 'twelve columns input-with-feedback'],
        'textarea' => ['class' => 'twelve columns input-with-feedback'],
    ],

    'templates' => [
        'button' => function (Field $data) {
            return app('form')->button(
                $data->value,
                app('html')->decorate($data->attributes, ['class' => 'btn'])
            );
        },
        'checkbox' => function (Field $data) {
            return app('form')->checkbox(
                $data->name,
                $data->value,
                $data->checked,
                $data->attributes
            );
        },
        'checkboxes' => function (Field $data) {
            return app('form')->checkboxes(
                $data->name,
                $data->options,
                $data->checked,
                $data->attributes
            );
        },
        'file' => function (Field $data) {
            return app('form')->file(
                $data->name,
                app('html')->decorate($data->attributes, ['class' => 'form-control'])
            );
        },
        'input' => function (Field $data) {
            return app('form')->input(
                $data->type,
                $data->name,
                $data->value,
                app('html')->decorate($data->attributes, ['class' => 'form-control'])
            );
        },
        'password' => function (Field $data) {
            return app('form')->password(
                $data->name,
                app('html')->decorate($data->attributes, ['class' => 'form-control'])
            );
        },
        'radio' => function (Field $data) {
            return app('form')->radio(
                $data->name,
                $data->value,
                $data->checked
            );
        },
        'select' => function (Field $data) {
            return app('form')->select(
                $data->name,
                $data->options,
                $data->value,
                app('html')->decorate($data->attributes, ['class' => 'form-control'])
            );
        },
        'textarea' => function (Field $data) {
            return app('form')->textarea(
                $data->name,
                $data->value,
                app('html')->decorate($data->attributes, ['class' => 'form-control'])
            );
        },
    ],
];
