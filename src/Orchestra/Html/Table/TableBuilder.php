<?php namespace Orchestra\Html\Table;

use Closure;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;

class TableBuilder extends \Orchestra\Html\Abstractable\Builder {

	/**
	 * {@inheritdoc} 
	 */
	public function __construct(Container $app, Closure $callback)
	{
		$this->app = $app;

		// Initiate Table\Grid, this wrapper emulate Table designer
		// script to create the Table.
		$this->grid = new Grid($app);
		
		$this->extend($callback);
	}

	/**
	 * {@inheritdoc}
	 */
	public function render()
	{
		$grid = $this->grid;
		
		// Add paginate value for current listing while appending query string,
		// however we also need to remove ?page from being added twice.
		$input = $this->app['request']->query();
		if (isset($input['page'])) unset($input['page']);

		$pagination = (true === $grid->paginate ? $grid->model->appends($input)->links() : '');
		
		$data = array(
			'attributes' => array(
				'row'   => $grid->rows->attributes,
				'table' => $grid->attributes,
			),
			'columns'    => $grid->columns(),
			'empty'      => $this->app['translator']->get($grid->empty),
			'grid'       => $grid,
			'pagination' => $pagination,
			'rows'       => $grid->rows(),
		);

		// Build the view and render it.
		return $this->app['view']->make($grid->view)->with($data)->render();
	}
} 
