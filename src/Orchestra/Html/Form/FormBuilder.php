<?php namespace Orchestra\Html\Form;

use Closure,
	Illuminate\Support\Facades\Config,
	Illuminate\Support\Facades\Lang,
	Illuminate\Support\Facades\View,
	Orchestra\Html\AbstractableBuilder;

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
		// Localize Grid instance.
		$grid         = $this->grid;
		$form         = $grid->attributes;
		$submitButton = $grid->submitButton;

		if ( ! ($submitButton instanceof Lang))
		{
			$submitButton = Lang::get($submitButton);
		}

		$data = array(
			'token'        => $grid->token,
			'hiddens'      => $grid->hiddens,
			'row'          => $grid->row,
			'form'         => $form,
			'submitButton' => $submitButton,
			'errorMessage' => $grid->errorMessage,
			'fieldsets'    => $grid->fieldsets(),
		);

		// Build the view and render it.
		return View::make($grid->view)->with($data)->render();
	}
} 