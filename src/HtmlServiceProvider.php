<?php namespace Orchestra\Html;

use Orchestra\Html\Form\Factory as FormFactory;
use Orchestra\Support\Providers\ServiceProvider;
use Orchestra\Html\Table\Factory as TableFactory;

class HtmlServiceProvider extends ServiceProvider
{
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

        $this->app->alias('html', 'Orchestra\Html\HtmlBuilder');
        $this->app->alias('form', 'Orchestra\Html\FormBuilder');
    }

    /**
     * Register the HTML builder instance.
     *
     * @return void
     */
    protected function registerHtmlBuilder()
    {
        $this->app->singleton('html', function ($app) {
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
        $this->app->singleton('form', function ($app) {
            $form = new FormBuilder($app['html'], $app['url'], $app['session']->getToken());

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
        $this->app->singleton('Orchestra\Contracts\Html\Form\Control', 'Orchestra\Html\Form\Control');

        $this->app->singleton('Orchestra\Contracts\Html\Form\Template', function ($app) {
            $class = $app['config']->get('orchestra/html::form.fieldset');

            return $app->make($class);
        });

        $this->app->singleton('orchestra.form', function ($app) {
            return new FormFactory($app);
        });
    }

    /**
     * Register the Orchestra\Table builder instance.
     *
     * @return void
     */
    protected function registerOrchestraTableBuilder()
    {
        $this->app->singleton('orchestra.table', function ($app) {
            return new TableFactory($app);
        });
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $path = realpath(__DIR__.'/../resources');

        $this->package('orchestra/html', 'orchestra/html', $path);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['html', 'form', 'orchestra.form', 'orchestra.form.control', 'orchestra.table'];
    }
}
