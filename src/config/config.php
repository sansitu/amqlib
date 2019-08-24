<?php

/**
 * If the framework is CodeIgniter then uncomment the below one
 */
// $env = ENVIRONMENT;

/**
 * If the framework is Laravel or Lumen then uncomment the below one
 */
//$env = env('APP_ENV');

if (($env != 'staging') || ($env != 'production') || ($env != 'development')) {
    $env = 'development';
}

include "$env.php";
