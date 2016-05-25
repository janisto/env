<?php

namespace janisto\env\tests;

use janisto\env\Environment;

class EnvironmentTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        // Set environment variable
        putenv("APP_ENV=test");
    }

    public function testEnvironmentVariable()
    {
        $this->assertEquals('test', getenv('APP_ENV'));
    }
    
    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Invalid configuration directory
     */
    public function testInvalidConfigurationDirectory()
    {
        $env = new Environment(dirname(__DIR__) . '/invalid-directory');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Cannot find main config file
     */
    public function testMainFileInConfigurationDirectory()
    {
        $env = new Environment(dirname(__DIR__));
    }
    
    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Cannot find mode specific config file
     */
    public function testModeFileInConfigurationDirectory()
    {
        $env = new Environment(dirname(__DIR__) . '/config-missing');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Invalid environment mode supplied or selected
     */
    public function testInvalidEnvironmentMode()
    {
        $env = new Environment(dirname(__DIR__) . '/config', 'invalid-mode');
    }

    public function testConfigurationDirectory()
    {
        $env = new Environment(dirname(__DIR__) . '/config');

        $this->assertEquals('value-2', $env->config['key']);

        $this->assertEquals('test', $env->config['environment']);
    }

    public function testTwoConfigurationDirectories()
    {
        $env = new Environment([
            dirname(__DIR__) . '/config-common',
            dirname(__DIR__) . '/config',
        ]);

        $this->assertEquals('value-2', $env->config['key']);

        $this->assertEquals('value-2', $env->config['common']);

        $this->assertEquals('test', $env->config['environment']);
    }

    public function testForceDevelopmentMode()
    {
        $env = new Environment(dirname(__DIR__) . '/config', 'dev');

        $this->assertEquals('value-1', $env->config['key']);

        $this->assertEquals('dev', $env->config['environment']);
    }


    public function testForceTestingMode()
    {
        $env = new Environment(dirname(__DIR__) . '/config', 'test');

        $this->assertEquals('value-2', $env->config['key']);

        $this->assertEquals('test', $env->config['environment']);
    }

    public function testForceStagingMode()
    {
        $env = new Environment(dirname(__DIR__) . '/config', 'stage');

        $this->assertEquals('value-3', $env->config['key']);

        $this->assertEquals('stage', $env->config['environment']);
    }

    public function testForceProductionMode()
    {
        $env = new Environment(dirname(__DIR__) . '/config', 'prod');

        $this->assertEquals('value-4', $env->config['key']);

        $this->assertEquals('prod', $env->config['environment']);
    }

    public function testMerge()
    {
        $env = new Environment(dirname(__DIR__) . '/config');

        $expected = [
            'key' => 'value-2',
            'version' => '1.1',
            'options' => [
                'unittest' => true,
            ],
            'features' => [
                'app',
                'test',
                'local',
            ],
            42 => [
                'meaning' => true,
            ],
            'environment' => 'test',
        ];

        $this->assertEquals($expected, $env->config);
    }

    public function testShowDebug()
    {
        $env = new Environment(dirname(__DIR__) . '/config');

        ob_start();
        $env->showDebug();
        $message = ob_get_contents();
        ob_end_clean();

        $this->assertStringStartsWith('<div style="position: absolute;', $message);
        $this->assertRegExp('/Environment/', $message);
        $this->assertStringEndsWith('</pre></div>', $message);
    }
}
