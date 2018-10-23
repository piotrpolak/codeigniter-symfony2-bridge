<?php

namespace PiotrPolak\CodeIgniterSymfonyBridge;

/**
 * Symfony2+ bridge
 * Allows to communicate with Symfony2+ Application Kernel
 *
 * Method naming convention is intentionally camelCase, the same as in Symfony2+
 *
 * @version 2.0
 * @author Piotr Polak <piotr@polak.ro>
 * @license MIT, GPLv3
 * @package PepisCMS
 */
class Bridge
{
    /**
     * AppKernel variable, ween need it to keep it null when not initialized
     *
     * @var AppKernel|null
     */
    private $kernel = NULL;

    /**
     * Symfony2+ root dir where the config, logs, cache dir is stored
     *
     * @var String|null
     */
    private $symfony2_root_dir = NULL;

    /**
     * Allowed params: root_dir
     *
     * @param Array $params
     */
    public function __construct($params = array())
    {
        $this->symfony2_root_dir = APPPATH . '../../app/';
        if (isset($params['root_dir'])) {
            $this->symfony2_root_dir = $params['root_dir'];
        }
    }

    /**
     * Returns Symfony application kernel
     *
     * @see http://api.symfony.com/2.7/Symfony/Component/HttpKernel/Kernel.html
     * @return \AppKernel|null
     * @throws \Exception
     */
    public function getKernel()
    {
        // The kernel should be initialized just once
        if ($this->kernel === NULL) {
            // Checking whether bootstrap file exists
            if (!file_exists($this->symfony2_root_dir . 'bootstrap.php.cache')) {
                throw new \Exception('Unable to import application bootstrap. File ' . $this->symfony2_root_dir . 'bootstrap.php.cache does not exist.', 200);
            }

            // Requesting Bootstrap file, the refference to the $loader object is not really needed
            $loader = require_once $this->symfony2_root_dir . 'bootstrap.php.cache';

            // Checking whether the kernel file exists
            if (!file_exists($this->symfony2_root_dir . 'AppKernel.php')) {
                throw new \Exception('Unable to import application kernel. File AppKernel.php does not exist.', 300);
            }

            // Requesting AppKernel file
            require_once $this->symfony2_root_dir . 'AppKernel.php';

            // Initializing kernel
            $environment = ENVIRONMENT == 'development' ? 'dev' : 'prod';
            $debug = ENVIRONMENT == 'development' ? true : false;
            $this->kernel = new \AppKernel($environment, $debug);

            // Loading class cache and booting up
            $this->kernel->loadClassCache();
            $this->kernel->boot();
        }

        return $this->kernel;
    }

    /**
     * Returns Symfony2+ container
     *
     * @return ContainerInterface
     * @throws \Exception
     */
    public function getContainer()
    {
        return $this->getKernel()->getContainer();
    }

    /**
     * Magic method called upon destruction
     */
    public function __destruct()
    {
        // Shutting down kernel upon destruction if the kernel was initialised
        if ($this->kernel !== NULL) {
            $this->kernel->shutdown();
        }
    }
}