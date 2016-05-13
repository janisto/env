<?php

require(dirname(__DIR__) . '/vendor/autoload.php');

$env = new \janisto\env\Environment(dirname(__DIR__) . '/config');

// $env->config // environment configuration array
// $env->showDebug(); // show produced environment configuration
