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
    | Set default submit button for Orchestra\Html\Form.
    |
    */

    'templates' => [
        'input'    => ['class' => 'twelve columns input-with-feedback'],
        'password' => ['class' => 'twelve columns input-with-feedback'],
        'select'   => ['class' => 'twelve columns input-with-feedback'],
        'textarea' => ['class' => 'twelve columns input-with-feedback'],
    ],

    'presenter' => 'Orchestra\Html\Form\BootstrapThreePresenter',
];
