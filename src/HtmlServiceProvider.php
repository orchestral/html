<?php

namespace Orchestra\Html;

use Orchestra\Html\Form\Control;
use Orchestra\Html\Form\Factory as FormFactory;
use Illuminate\Contracts\Foundation\Application;
use Orchestra\Html\Form\BootstrapThreePresenter;
use Orchestra\Support\Providers\ServiceProvider;
use Orchestra\Html\Table\Factory as TableFactory;
use Illuminate\Contracts\Support\DeferrableProvider;
use Orchestra\Contracts\Html\Form\Template as TemplateContract;
use Orchestra\Contracts\Html\Form\Control as FormControlContract;

class HtmlServiceProvider extends ServiceProvider implements DeferrableProvider
{
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

        $this->app->alias('html', HtmlBuilder::class);
        $this->app->alias('form', FormBuilder::class);
    }

    /**
     * Register the HTML builder instance.
     *
     * @return void
     */
    protected function registerHtmlBuilder(): void
    {
        $this->app->singleton('html', function (Application $app) {
            return new HtmlBuilder($app->make('url'), $app->make('view'));
        });
    }

    /**
     * Register the form builder instance.
     *
     * @return void
     */
    protected function registerFormBuilder(): void
    {
        $this->app->singleton('form', function (Application $app) {
            $form = new FormBuilder($app->make('html'), $app->make('url'), $app->make('view'));

            return $form->setSessionStore($app->make('session.store'));
        });
    }

    /**
     * Register the Orchestra\Form builder instance.
     *
     * @return void
     */
    protected function registerOrchestraFormBuilder(): void
    {
        $this->app->singleton(FormControlContract::class, Control::class);

        $this->app->singleton(TemplateContract::class, function (Application $app) {
            $namespace = $this->hasPackageRepository() ? 'orchestra/html::form' : 'orchestra.form';

            $class = $app->make('config')->get("{$namespace}.presenter", BootstrapThreePresenter::class);

            return $app->make($class);
        });

        $this->app->singleton('orchestra.form', function (Application $app) {
            return new FormFactory($app);
        });
    }

    /**
     * Register the Orchestra\Table builder instance.
     *
     * @return void
     */
    protected function registerOrchestraTableBuilder(): void
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
        $this->bootComponents();

        $this->bootConfiguration();
    }

    /**
     * Boot extension configurations.
     *
     * @return void
     */
    protected function bootConfiguration(): void
    {
        $config = $this->app->make('config');
        $namespace = $this->hasPackageRepository() ? 'orchestra/html::' : 'orchestra.';

        $this->app->make('orchestra.form')->setConfig($config->get("{$namespace}form"));
        $this->app->make('orchestra.table')->setConfig($config->get("{$namespace}table"));
    }

    /**
     * Boot extension components.
     *
     * @return void
     */
    protected function bootComponents(): void
    {
        $path = realpath(__DIR__.'/../resources');

        $this->addConfigComponent('orchestra/html', 'orchestra/html', "{$path}/config");
        $this->addViewComponent('orchestra/html', 'orchestra/html', "{$path}/views");

        if (! $this->hasPackageRepository()) {
            $this->bootUsingLaravel($path);
        }
    }

    /**
     * Boot using Laravel setup.
     *
     * @param  string  $path
     *
     * @return void
     */
    protected function bootUsingLaravel(string $path): void
    {
        $this->mergeConfigFrom("{$path}/config/form.php", 'orchestra.form');
        $this->mergeConfigFrom("{$path}/config/table.php", 'orchestra.table');

        $this->publishes([
            "{$path}/config/form.php" => \config_path('orchestra/form.php'),
            "{$path}/config/table.php" => \config_path('orchestra/table.php'),
        ]);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'html',
            'form',
            'orchestra.form',
            'orchestra.form.control',
            'orchestra.table',
            HtmlBuilder::class,
            FormBuilder::class,
            TemplateContract::class,
            FormControlContract::class,
        ];
    }
}
