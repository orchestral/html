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
	}
}