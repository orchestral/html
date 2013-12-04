<?php namespace Orchestra\Html\Form;

interface FormPresenterInterface
{
    /**
     * Build form action URL.
     *
     * @param  string  $url
     * @return string
     */
    public function handles($url);

    /**
     * Setup form layout.
     *
     * @param  \Orchestra\Html\Form\Grid    $form
     * @return void
     */
    public function setupForm($form);
}
