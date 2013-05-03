<?php namespace Orchestra\Html;

use \Illuminate\Support\ServiceProvider;

class HtmlServiceProvider extends ServiceProvider {
	
	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerHtmlBuilder();
		$this->registerFormBuilder();
		$this->registerOrchestraFormBuilder();
		$this->registerOrchestraTableBuilder();
	}

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
	}

	/**
	 * Register the form builder instance.
	 *
	 * @return void
	 */
	protected function registerFormBuilder()
	{
		$this->app['form'] = $this->app->share(function($app)
		{
			$form = new \Illuminate\Html\FormBuilder($app['html'], $app['url'], $app['session']->getToken());

			return $form->setSessionStore($app['session']);
		});
	}

	/**
	 * Register the Orchestra\Form builder instance.
	 *
	 * @return void
	 */
	protected function registerOrchestraFormBuilder()
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
	protected function registerOrchestraTableBuilder()
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

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('html', 'form', 'orchestra.form', 'orchestra.table');
	}
}
