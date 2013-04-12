<?php namespace Orchestra\Html\Table;

use Closure,
	Illuminate\Support\Facades\Config,
	Illuminate\Support\Facades\Lang,
	Illuminate\Support\Facades\Request,
	Illuminate\Support\Facades\View,
	Orchestra\Html\AbstractableBuilder;

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
		// Initiate Table\Grid, this wrapper emulate table designer
		// script to create the table.
		$this->grid = new Grid(Config::get('orchestra/html::table', array()));
		
		$this->extend($callback);
	}

	/**
	 * Render the table
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

		$emptyMessage = $grid->emptyMessage;

		if ( ! ($emptyMessage instanceof Lang))
		{
			$emptyMessage = Lang::get($emptyMessage);
		}

		$data = array(
			'attributes' => array(
				'row'   => $grid->rows->attributes,
				'table' => $grid->attributes,
			),
			'emptyMessage' => $emptyMessage,
			'columns'      => $grid->columns(),
			'rows'         => $grid->rows(),
			'pagination'   => $paginate,
		);

		// Build the view and render it.
		return View::make($grid->view)->with($data)->render();
	}
} 