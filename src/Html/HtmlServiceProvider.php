<?php namespace Orchestra\Html;

use Illuminate\Support\ServiceProvider;


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
        $this->registerCustomFormMacros();
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
        $this->app->bindShared('orchestra.form.control', function ($app) {
            return new Form\Control($app['config'], $app['html'], $app['request']);
        });

        $this->app->bindShared('orchestra.form', function ($app) {
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
        $this->app->bindShared('orchestra.table', function ($app) {
            return new Table\Environment($app);
        });
    }



	/**
	 * Register the custom form macros.
	 *
	 * @return void
	 */
	protected function registerCustomFormMacros()
	{
		\Form::macro('checkboxes', function($name, $options, $checked, $attributes)
		{
			$checkbox_holder = array();
			foreach($options as $id => $label)
			{
				$identifier = str_replace('[]', '',$name) . '_'. $id; // {$nameWithout[]}_{$id}
				$temp = \Form::checkbox(
					$name . (strpos('[]',$name) ? '': '[]'),
					$id,
					($id === $checked),
					array('id' => $identifier)
				);
				// add text
				$temp .= ' ' .\Form::label($identifier, $label);

				$checkbox_holder[] = $temp;
			}

			return implode('<br>',$checkbox_holder);
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
