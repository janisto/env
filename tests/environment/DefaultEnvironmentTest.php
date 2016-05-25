<?php

namespace janisto\env\tests;

use janisto\env\Environment;

class DefaultEnvironmentTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        // Unset environment variable
        putenv("APP_ENV");
    }
    
    public function testDefaultMode()
    {
        $env = new Environment(dirname(__DIR__) . '/config');

        $this->assertEquals('prod', $env->config['environment']);
    }
}
