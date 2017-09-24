<?php

/**
 * provider.php 
 * 
 * The provider.php file is used and included in all examples.  It's responsible
 * for importing the config.php file and creating the provider.  The config.php 
 * file should be placed in the same directory (not included in the git).  
 * 
 * This file should return an array that contains:
 * 
 * [
 *  'consumer'  => [
 *      'clientId'      => 'consumer_key',
 *      'clientSecret   => 'consumer_secret',
 *      'redirectUri'   => 'oob'
 *  ],
 *  'token'     => serialize(AuthToken)
 * 
 */

require __DIR__ . '/../vendor/autoload.php';

use Kenjdavidson\OAuth2\YahooFantasySports\Provider\YahooFantasyProvider;
use League\OAuth2\Client\Token\AccessToken;

// Configuration - see comments
$config = require __DIR__ . '/config.php';

// Create the YahooFantasy provider
$provider = new YahooFantasyProvider($config['consumer']);

// Start the PHP session
session_start();

return $provider;