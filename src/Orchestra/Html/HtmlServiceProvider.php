<?php namespace Orchestra\Html;

class HtmlServiceProvider extends \Illuminate\Html\HtmlServiceProvider {
	
	/**
	 * Register the HTML builder instance.
	 *
	 * @return void
	 */
	protected function registerHtmlBuilder()
	{
		$this->app['html'] = $this->app->share(function($app)
		{
			return new HtmlBuilder($app['url']);
		});

		$this->app['orchestra.form'] = $this->app->share(function($app)
		{
			return new Form\Environment;
		});

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