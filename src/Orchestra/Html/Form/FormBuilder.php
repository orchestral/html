<?php namespace Orchestra\Html\Form;

use Closure;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;

class FormBuilder extends \Orchestra\Html\Abstractable\Builder
{
    /**
     * {@inheritdoc}
     */
    public function __construct(Container $app, Closure $callback)
    {
        $this->app = $app;

        // Initiate Form\Grid, this wrapper emulate Form designer
        // script to create the Form.
        $this->grid = new Grid($app);

        $this->extend($callback);
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $grid = $this->grid;
        $form = $grid->attributes;

        $data = array(
            'grid'      => $grid,
            'fieldsets' => $grid->fieldsets(),
            'form'      => $form,
            'format'    => $grid->format,
            'hiddens'   => $grid->hiddens,
            'row'       => $grid->row,
            'submit'    => $this->app['translator']->get($grid->submit),
            'token'     => $grid->token,
        );

        return $this->app['view']->make($grid->view)->with($data)->render();
    }
}
