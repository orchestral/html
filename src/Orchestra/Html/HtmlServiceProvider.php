<?php namespace Orchestra\Html;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

class HtmlServiceProvider extends ServiceProvider {
	
	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var boolean
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
		$this->registerAliases();
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

			return $form->setSessionStore($app['session.store']);
		});
	}

	/**
	 * Register the Orchestra\Form builder instance.
	 *
	 * @return void
	 */
	protected function registerOrchestraFormBuilder()
	{
		$this->app['orchestra.form.control'] = $this->app->share(function($app)
		{
			return new Form\Control($app);
		});

		$this->app['orchestra.form'] = $this->app->share(function($app)
		{
			return new Form\Environment($app);
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
			return new Table\Environment($app);
		});
	}

	/**
	 * Register aliases.
	 *
	 * @return void
	 */
	protected function registerAliases()
	{
		$this->app->booting(function()
		{
			$loader = AliasLoader::getInstance();
			$loader->alias('Orchestra\Form', 'Orchestra\Support\Facades\Form');
			$loader->alias('Orchestra\Table', 'Orchestra\Support\Facades\Table');
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
		return array('html', 'form', 'orchestra.form', 'orchestra.form.control', 'orchestra.table');
	}
}
