<?php

namespace PiotrPolak\CodeIgniterSymfonyBridge\Tests;

use PiotrPolak\CodeIgniterSymfonyBridge\Bridge;

class BridgeTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        if (!defined('APPPATH')) {
            define('APPPATH', __DIR__ . '/mocks/ciapp/application/');
            define('BASEPATH', __DIR__);
            define('ENVIRONMENT', 'production');
        }
    }

    public function testGetKernelDefaultConfiguration()
    {
        $bridge = new Bridge();

        $kernel = $bridge->getKernel();
        $this->assertNotNull($kernel);

        unset($bridge);
        $this->assertTrue($kernel->_wasProperlyShutDown());
    }

    public function testGetKernelManualConfiguration()
    {
        $bridge = new Bridge(array('root_dir' => APPPATH . '../../app/'));

        $kernel = $bridge->getKernel();
        $this->assertNotNull($kernel);

        unset($bridge);
        $this->assertTrue($kernel->_wasProperlyShutDown());
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionCode 200
     */
    public function testGetKernelWrongManualConfiguration()
    {
        $bridge = new Bridge(array('root_dir' => 'inexistentLocation'));

        $kernel = $bridge->getKernel();
        $this->assertNull($kernel);

        unset($bridge);
        $this->assertFalse($kernel->_wasProperlyShutDown());
    }
}