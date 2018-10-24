<?php

namespace PiotrPolak\CodeIgniterSymfonyBridge\Tests;

use PiotrPolak\CodeIgniterSymfonyBridge\Bridge;

class BridgeTest extends \PHPUnit_Framework_TestCase
{
    private $tempDir = null;

    public static function setUpBeforeClass()
    {
        putenv('APP_ENV=prod');
        putenv('SYMFONY_ENV=prod');
    }

    protected function tearDown()
    {
        if ($this->tempDir !== null) {
            if (strpos($this->tempDir, sys_get_temp_dir()) !== 0) {
                throw new \Exception($this->tempDir . ' points to an invalid location.');
            }
            system('rm -rf ' . escapeshellarg($this->tempDir));
            $this->tempDir = null;
        }
    }

    /**
     * @runInSeparateProcess
     */
    public function testGetKernelDefaultConfiguration()
    {
        $bridge = new Bridge(__DIR__ . DIRECTORY_SEPARATOR . 'mocks' . DIRECTORY_SEPARATOR . 'app');
        $kernel = $bridge->getKernel();
        $this->assertNotNull($kernel);
        unset($bridge);
        $this->assertTrue($kernel->_wasProperlyShutDown());
    }

    /**
     * @runInSeparateProcess
     */
    public function testGetKernelManualConfigurationSymfony27()
    {
        if (version_compare(PHP_VERSION, '5.3.9', '<')) {
            return;
        }
        $tempDir = $this->generateTempDir();
        $installationDir = 'tmp_symfony_2_7';
        system('composer --no-dev --no-interaction --working-dir=' . escapeshellarg($tempDir) . ' create-project symfony/framework-standard-edition ' . escapeshellarg($installationDir) . ' "2.7.*" 2>&1 || echo "An error occurred."');

        $symfonyAppBasePath = $tempDir . DIRECTORY_SEPARATOR . $installationDir . DIRECTORY_SEPARATOR . 'app';
        $this->doTestBridge($symfonyAppBasePath);
    }

    /**
     * @runInSeparateProcess
     */
    public function testGetKernelManualConfigurationSymfony34()
    {
        if (version_compare(PHP_VERSION, '5.5.9', '<')) {
            return;
        }
        $tempDir = $this->generateTempDir();
        $installationDir = 'tmp_symfony_3_4';
        system('composer --no-dev --working-dir=' . escapeshellarg($tempDir) . ' create-project symfony/website-skeleton ' . escapeshellarg($installationDir) . ' "3.4.*" 2>&1 || echo "An error occurred."');

        $symfonyAppBasePath = $tempDir . DIRECTORY_SEPARATOR . $installationDir;
        $this->doTestBridge($symfonyAppBasePath);
    }

    /**
     * @runInSeparateProcess
     */
    public function testGetKernelManualConfigurationSymfony40()
    {
        if (version_compare(PHP_VERSION, '7.1.3', '<')) {
            return;
        }
        $tempDir = $this->generateTempDir();
        $installationDir = 'tmp_symfony_4_1';
        system('composer --no-dev --working-dir=' . escapeshellarg($tempDir) . ' create-project symfony/website-skeleton ' . escapeshellarg($installationDir) . ' "4.1.*" 2>&1 || echo "An error occurred."');

        $symfonyAppBasePath = $tempDir . DIRECTORY_SEPARATOR . $installationDir;
        $this->doTestBridge($symfonyAppBasePath);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionCode 300
     */
    public function testGetKernelWrongManualConfiguration()
    {
        $bridge = new Bridge('inexistentLocation');

        $kernel = $bridge->getKernel();
        $this->assertNull($kernel);

        unset($bridge);
    }

    private function generateTempDir()
    {
        $tempDir = tempnam(sys_get_temp_dir(), 'BridgeTest_');
        if (file_exists($tempDir)) {
            unlink($tempDir);
        }
        mkdir($tempDir);
        if (is_dir($tempDir)) {
            $this->tempDir = $tempDir;
            return $tempDir;
        }

        throw new \RuntimeException('Unable to generate temp dir.');
    }

    /**
     * @param $symfonyAppBasePath
     * @throws \Exception
     */
    private function doTestBridge($symfonyAppBasePath)
    {
        $this->assertFileExists($symfonyAppBasePath);
        $bridge = new Bridge($symfonyAppBasePath);

        $this->assertNotNull($bridge->getKernel());;
        $this->assertNotNull($bridge->getContainer());
        $this->assertNotNull($bridge->getContainer()->getServiceIds());
        $this->assertGreaterThan(0, count($bridge->getContainer()->getServiceIds()));

        unset($bridge);
    }
}