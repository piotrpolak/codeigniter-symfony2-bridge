<?php

class Symfony2_bridgeTest extends PHPUnit_Framework_TestCase {

    public static function setUpBeforeClass()
    {
        if( !defined('APPPATH') )
        {
            define('APPPATH', __DIR__.'/../../tests/mocks/ciapp/application/');
            define('BASEPATH', __DIR__);
            define('ENVIRONMENT', 'production');
            require_once(__DIR__.'/../../src/libraries/Symfony2_bridge.php');
        }
    }

    public function testGetKernelDefaultConfiguration()
    {
        $bridge = new Symfony2_bridge();

        $kernel = $bridge->getKernel();
        $this->assertNotNull($kernel);

        unset($bridge);
        $this->assertTrue($kernel->_wasProperlyShutDown());
    }

    public function testGetKernelManualConfiguration()
    {
        $bridge = new Symfony2_bridge(array('root_dir' => APPPATH.'../../app/'));

        $kernel = $bridge->getKernel();
        $this->assertNotNull($kernel);

        unset($bridge);
        $this->assertTrue($kernel->_wasProperlyShutDown());
    }

    /**
     * @expectedException       Exception
     * @expectedExceptionCode   200
     */
    public function testGetKernelWrongManualConfiguration()
    {
        $bridge = new Symfony2_bridge(array('root_dir' => 'inexistentLocation'));

        $kernel = $bridge->getKernel();
        $this->assertNull($kernel);

        unset($bridge);
        $this->assertFalse($kernel->_wasProperlyShutDown());
    }
}