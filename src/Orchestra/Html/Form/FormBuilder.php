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
	 * @param  Closure  $callback
	 * @return void	 
	 */
	public function __construct(Closure $callback)
	{
		// Initiate Form\Grid, this wrapper emulate Form designer
		// script to create the Form.
		$this->grid = new Grid(Config::get('orchestra/html::form', array()));
		
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
		$submit = $grid->submit;

		if ( ! ($submit instanceof Lang)) $submit = Lang::get($submit);

		$data = array(
			'token'     => $grid->token,
			'hiddens'   => $grid->hiddens,
			'row'       => $grid->row,
			'form'      => $form,
			'submit'    => $submit,
			'format'    => $grid->format,
			'fieldsets' => $grid->fieldsets(),
		);

		// Build the view and render it.
		return View::make($grid->view)->with($data)->render();
	}
} 
