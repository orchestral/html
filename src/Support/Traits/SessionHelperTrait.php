<?php namespace Orchestra\Html\Support\Traits;

use Illuminate\Session\Store;

trait SessionHelperTrait
{
    /**
     * The session store implementation.
     *
     * @var \Illuminate\Session\Store
     */
    protected $session;

    /**
     * Get the session store implementation.
     *
     * @return  \Illuminate\Session\Store  $session
     */
    public function getSessionStore()
    {
        return $this->session;
    }

    /**
     * Set the session store implementation.
     *
     * @param  \Illuminate\Session\Store  $session
     * @return $this
     */
    public function setSessionStore(Store $session)
    {
        $this->session = $session;

        return $this;
    }
}
