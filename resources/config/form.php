<?php

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
    | Orchestra\Html\Form would require a View to parse the provided form
    | instance.
    |
    */

    'view' => 'orchestra/html::form.horizontal',

    /*
    |----------------------------------------------------------------------
    | Layout Configuration
    |----------------------------------------------------------------------
    |
    | Set default attributes for Orchestra\Html\Form.
    |
    */

    'templates' => [
        'input' => ['class' => 'input-with-feedback'],
        'password' => ['class' => 'input-with-feedback'],
        'select' => ['class' => 'input-with-feedback'],
        'textarea' => ['class' => 'input-with-feedback'],
        'checkboxes' => [],
    ],

    /*
    |----------------------------------------------------------------------
    | Presenter
    |----------------------------------------------------------------------
    |
    | Set default presenter class for Orchestra\Html\Form.
    |
    */

    'presenter' => Orchestra\Html\Form\Presenters\BootstrapThree::class,
];
