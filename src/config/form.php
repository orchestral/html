<?php

return array(

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

    'fieldset' => array(
        'button'   => array('class' => 'btn'),
        'checkbox' => array(),
        'file'     => array(),
        'input'    => array('class' => 'twelve columns input-with-feedback'),
        'password' => array('class' => 'twelve columns input-with-feedback'),
        'radio'    => array(),
        'select'   => array('class' => 'twelve columns input-with-feedback'),
        'textarea' => array('class' => 'twelve columns input-with-feedback'),
    ),

    'templates' => array(
        'button' => function ($data) {
            return Form::button(
                $data->value,
                $data->attributes
            );
        },
        'checkbox' => function ($data) {
            return Form::checkbox(
                $data->name,
                $data->value,
                $data->checked
            );
        },
        'file' => function ($data) {
            return Form::file(
                $data->name,
                HTML::decorate($data->attributes, array('class' => 'form-control'))
            );
        },
        'input' => function ($data) {
            return Form::input(
                $data->type,
                $data->name,
                $data->value,
                HTML::decorate($data->attributes, array('class' => 'form-control'))
            );
        },
        'password' => function ($data) {
            return Form::password(
                $data->name,
                HTML::decorate($data->attributes, array('class' => 'form-control'))
            );
        },
        'radio' => function ($data) {
            return Form::radio(
                $data->name,
                $data->value,
                $data->checked
            );
        },
        'select' => function ($data) {
            return Form::select(
                $data->name,
                $data->options,
                $data->value,
                HTML::decorate($data->attributes, array('class' => 'form-control'))
            );
        },
        'textarea' => function ($data) {
            return Form::textarea(
                $data->name,
                $data->value,
                HTML::decorate($data->attributes, array('class' => 'form-control'))
            );
        },
    ),
);
