<?php namespace Orchestra\Html\Form;

use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;
use Orchestra\Html\AbstractableBuilder;

class FormBuilder extends AbstractableBuilder {

	/**
	 * Create a new Form instance.
	 * 			
	 * @access public
	 * @param  Illuminate\Foundation\Application    $app
	 * @param  Closure                              $callback
	 * @return void	 
	 */
	public function __construct($app, Closure $callback)
	{
		$this->app = $app;

		// Initiate Form\Grid, this wrapper emulate Form designer
		// script to create the Form.
		$this->grid = new Grid($app);
		
		$this->extend($callback);
	}

	/**
	 * Render the Form
	 *
	 * @access  public
	 * @return  string
	 */
	public function render()
	{
		$grid   = $this->grid;
		$form   = $grid->attributes;

		$data = array(
			'token'     => $grid->token,
			'hiddens'   => $grid->hiddens,
			'row'       => $grid->row,
			'form'      => $form,
			'submit'    => $this->app['translator']->get($grid->submit),
			'format'    => $grid->format,
			'fieldsets' => $grid->fieldsets(),
		);

		return $this->app['view']->make($grid->view)->with($data)->render();
	}
} 
