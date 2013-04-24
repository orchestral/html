<?php namespace Orchestra\Html;

use \Illuminate\Support\ServiceProvider;

class HtmlBuilderServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerFormBuilder();
		$this->registerTableBuilder();
	}

	/**
	 * Register the Orchestra\Form builder instance.
	 *
	 * @return void
	 */
	protected function registerFormBuilder()
	{
		$this->app['orchestra.form'] = $this->app->share(function($app)
		{
			return new Form\Environment;
		});
	}

	/**
	 * Register the Orchestra\Table builder instance.
	 *
	 * @return void
	 */
	protected function registerTableBuilder()
	{
		$this->app['orchestra.table'] = $this->app->share(function($app)
		{
			return new Table\Environment;
		});
	}

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('orchestra/html', 'orchestra/html');
	}
}