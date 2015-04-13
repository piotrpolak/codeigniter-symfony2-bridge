<?php

/**
 * Dummy AppKernel
 */
class AppKernel
{
    private $wasProperlyShutDown = false;

    public function __construct($env, $debug)
    {
    }

    public function loadClassCache()
    {
    }

    public function boot()
    {
    }

    public function getContainer()
    {
    }

    public function shutdown()
    {
        $this->wasProperlyShutDown = true;
    }

    public function _wasProperlyShutDown()
    {
        return $this->wasProperlyShutDown;
    }
}