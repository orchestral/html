<?php namespace Orchestra\Html;

use Illuminate\Html\FormBuilder;
use Illuminate\Support\ServiceProvider;
use Orchestra\Html\Form\Control;
use Orchestra\Html\Form\Environment as FormFactory;
use Orchestra\Html\Table\Environment as TableFactory;

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
        $this->registerCheckboxesFormMacro();
    }

    /**
     * Register the HTML builder instance.
     *
     * @return void
     */
    protected function registerHtmlBuilder()
    {
        $this->app->bindShared('html', function ($app) {
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
        $this->app->bindShared('form', function ($app) {
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
        $this->app->bindShared('orchestra.form.control', function ($app) {
            return new Control($app['config'], $app['html'], $app['request']);
        });

        $this->app->bindShared('orchestra.form', function ($app) {
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
        $this->app->bindShared('orchestra.table', function ($app) {
            return new TableFactory($app);
        });
    }

    /**
     * Register the custom checkboxes form macro.
     *
     * @return void
     */
    protected function registerCheckboxesFormMacro()
    {
        $form = $this->app['form'];

        $form->macro('checkboxes', function ($name, $options, $checked, $attributes) use ($form) {
            $group = array();

            foreach ($options as $id => $label) {
                $name = str_replace('[]', '', $name);
                $identifier = sprintf('%s_%s', $name, $id);

                $attributes['id'] = $identifier;

                $control = $form->checkbox(
                    sprintf('%s[]', $name),
                    $id,
                    in_array($id, (array) $checked),
                    $attributes
                );

                $label = $form->label($identifier, $label);

                $group[] = implode(' ', array($control, $label));
            }

            return implode('<br>', $group);
        });
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $path = realpath(__DIR__.'/../');

        $this->package('orchestra/html', 'orchestra/html', $path);
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
