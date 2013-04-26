<?php namespace Orchestra\Html\Table;

use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;
use Orchestra\Html\AbstractableBuilder;

class TableBuilder extends AbstractableBuilder {

	/**
	 * Create a new Table instance.
	 * 			
	 * @access public
	 * @param  Closure  $callback
	 * @return void	 
	 */
	public function __construct(Closure $callback)
	{
		// Initiate Table\Grid, this wrapper emulate Table designer
		// script to create the Table.
		$this->grid = new Grid(Config::get('orchestra/html::table', array()));
		
		$this->extend($callback);
	}

	/**
	 * Render the Table
	 *
	 * @access  public
	 * @return  string
	 */
	public function render()
	{
		// localize Table\Grid object
		$grid  = $this->grid;
		
		// Add paginate value for current listing while appending query string
		$input = Request::query();

		// we shouldn't append ?page
		if (isset($input['page'])) unset($input['page']);

		$paginate = (true === $grid->paginate ? $grid->model->appends($input)->links() : '');
		$empty    = $grid->empty;

		if ( ! ($empty instanceof Lang)) $empty = Lang::get($empty);

		$data = array(
			'attributes' => array(
				'row'   => $grid->rows->attributes,
				'table' => $grid->attributes,
			),
			'empty'      => $empty,
			'columns'    => $grid->columns(),
			'rows'       => $grid->rows(),
			'pagination' => $paginate,
		);

		// Build the view and render it.
		return View::make($grid->view)->with($data)->render();
	}
} 
