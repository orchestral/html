<?php

namespace Orchestra\Html;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Support\DeferrableProvider;
use Orchestra\Contracts\Html\Form\Control as FormControlContract;
use Orchestra\Contracts\Html\Form\Template as TemplateContract;
use Orchestra\Html\Form\BootstrapThreePresenter;
use Orchestra\Html\Form\Control;
use Orchestra\Html\Form\Factory as FormFactory;
use Orchestra\Html\Table\Factory as TableFactory;
use Orchestra\Support\Providers\ServiceProvider;

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
     */
    protected function registerHtmlBuilder(): void
    {
        $this->app->singleton('html', static function (Container $app) {
            return new HtmlBuilder($app->make('url'), $app->make('view'));
        });
    }

    /**
     * Register the form builder instance.
     */
    protected function registerFormBuilder(): void
    {
        $this->app->singleton('form', static function (Container $app) {
            $form = new FormBuilder($app->make('html'), $app->make('url'), $app->make('view'));

            return $form->setSessionStore($app->make('session.store'));
        });
    }

    /**
     * Register the Orchestra\Form builder instance.
     */
    protected function registerOrchestraFormBuilder(): void
    {
        $this->app->singleton(FormControlContract::class, Control::class);

        $this->app->singleton(TemplateContract::class, function (Container $app) {
            $namespace = $this->hasPackageRepository() ? 'orchestra/html::form' : 'orchestra.form';

            $class = $app->make('config')->get("{$namespace}.presenter", BootstrapThreePresenter::class);

            return $app->make($class);
        });

        $this->app->singleton('orchestra.form', static function (Container $app) {
            return new FormFactory($app);
        });
    }

    /**
     * Register the Orchestra\Table builder instance.
     */
    protected function registerOrchestraTableBuilder(): void
    {
        $this->app->singleton('orchestra.table', static function (Container $app) {
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
     */
    protected function bootComponents(): void
    {
        $path = realpath(__DIR__.'/../');

        $this->addConfigComponent('orchestra/html', 'orchestra/html', "{$path}/config");
        $this->addViewComponent('orchestra/html', 'orchestra/html', "{$path}/resources/views");

        if (! $this->hasPackageRepository()) {
            $this->bootUsingLaravel($path);
        }
    }

    /**
     * Boot using Laravel setup.
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
