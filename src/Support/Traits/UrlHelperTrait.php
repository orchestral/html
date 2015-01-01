<?php namespace Orchestra\Html\Support\Traits;

trait UrlHelperTrait
{
    /**
     * The URL generator instance.
     *
     * @var \Illuminate\Routing\UrlGenerator  $url
     */
    protected $url;

    /**
     * Parse the form action method.
     *
     * @param  string  $method
     * @return string
     */
    protected function getMethod($method)
    {
        $method = strtoupper($method);

        return $method != 'GET' ? 'POST' : $method;
    }
    /**
     * Get the form action from the options.
     *
     * @param  array   $options
     * @return string
     */
    protected function getAction(array $options)
    {
        // We will also check for a "route" or "action" parameter on the array so that
        // developers can easily specify a route or controller action when creating
        // a form providing a convenient interface for creating the form actions.
        if (isset($options['url'])) {
            return $this->getUrlAction($options['url']);
        }

        // If an action is available, we are attempting to open a form to a controller
        // action route. So, we will use the URL generator to get the path to these
        // actions and return them from the method. Otherwise, we'll use current.

        if (isset($options['route'])) {
            return $this->getRouteAction($options['route']);
        } elseif (isset($options['action'])) {
            return $this->getControllerAction($options['action']);
        }

        return $this->url->current();
    }

    /**
     * Get the action for a "url" option.
     *
     * @param  array|string  $options
     * @return string
     */
    protected function getUrlAction($options)
    {
        if (is_array($options)) {
            return $this->url->to($options[0], array_slice($options, 1));
        }

        return $this->url->to($options);
    }

    /**
     * Get the action for a "route" option.
     *
     * @param  array|string  $options
     * @return string
     */
    protected function getRouteAction($options)
    {
        if (is_array($options)) {
            return $this->url->route($options[0], array_slice($options, 1));
        }

        return $this->url->route($options);
    }

    /**
     * Get the action for an "action" option.
     *
     * @param  array|string  $options
     * @return string
     */
    protected function getControllerAction($options)
    {
        if (is_array($options)) {
            return $this->url->action($options[0], array_slice($options, 1));
        }

        return $this->url->action($options);
    }
}
