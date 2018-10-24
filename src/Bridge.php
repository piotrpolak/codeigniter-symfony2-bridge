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
    private $kernel = null;

    /**
     * Symfony2+ root dir where the config, logs, cache dir is stored
     *
     * @var String|null
     */
    private $symfonyAppRootDirectory = null;

    /**
     * @var bool
     */
    private $isProductionEnvironment;

    /**
     * Allowed params: root_dir
     *
     * @param $symfonyAppRootDirectory
     * @param bool $isProductionEnvironment
     */
    public function __construct($symfonyAppRootDirectory, $isProductionEnvironment = true)
    {
        $this->symfonyAppRootDirectory = $symfonyAppRootDirectory;
        $this->isProductionEnvironment = $isProductionEnvironment;
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
        if ($this->kernel === NULL) {
            if (file_exists($this->getSymfony2KernelPath())) {
                $this->initializeSymfony2Kernel();
            } else {
                if (file_exists($this->getSymfony3KernelPath())) {
                    $this->initializeSymfony3Kernel();
                } else {
                    throw new \Exception('Unable to import application kernel. File '
                        . $this->getSymfony2KernelPath() . ' nor ' . $this->getSymfony3KernelPath() . ' does not exist.',
                        300);
                }
            }
        }

        return $this->kernel;
    }

    /**
     * Returns Symfony2+ container
     *
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
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
        if ($this->kernel !== null) {
            $this->kernel->shutdown();
        }
    }

    private function initializeSymfony2Kernel()
    {
        if (!file_exists($this->getSymfony2BootstrapPath())) {
            throw new \Exception('Unable to import application bootstrap. File '
                . $this->getSymfony2BootstrapPath() . ' does not exist.', 200);
        }
        require_once $this->getSymfony2BootstrapPath();
        require_once $this->getSymfony2KernelPath();

        $this->kernel = new \AppKernel($this->getEnvironment(), !$this->isProductionEnvironment);
        $this->kernel->loadClassCache();
        $this->kernel->boot();
    }

    private function initializeSymfony3Kernel()
    {
        require $this->symfonyAppRootDirectory . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
        require_once $this->getSymfony3KernelPath();
        $this->kernel = new \App\Kernel($this->getEnvironment(), !$this->isProductionEnvironment);
        $this->kernel->boot();
    }

    /**
     * @return string
     */
    private function getEnvironment()
    {
        return $this->isProductionEnvironment ? 'prod' : 'dev';
    }

    /**
     * @return string
     */
    private function getSymfony2KernelPath()
    {
        return $this->symfonyAppRootDirectory . DIRECTORY_SEPARATOR . 'AppKernel.php';
    }

    /**
     * @return string
     */
    private function getSymfony3KernelPath()
    {
        return $this->symfonyAppRootDirectory . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Kernel.php';
    }

    /**
     * @return string
     */
    private function getSymfony2BootstrapPath()
    {
        return $this->symfonyAppRootDirectory . DIRECTORY_SEPARATOR . 'bootstrap.php.cache';
    }
}