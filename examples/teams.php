<?php

use Kenjdavidson\OAuth2\YahooFantasySports\Provider\Service\YahooFantasyService;

/**
 * game.php
 * 
 * Game.php file is responsible for displaying examples of the GameCollection
 * and GameResource files.
 * 
 * @link https://developer.yahoo.com/fantasysports/guide/#games-collection
 * @link https://developer.yahoo.com/fantasysports/guide/#game-resource
 */
/* @var YahooFantasyProvider $provider */
$provider = require __DIR__ . '/provider.php';

if (!empty($_SESSION['token'])) {
    $token = unserialize($_SESSION['token']);
}

if (empty($token)) {
    header('Location: /index.php');
    exit;
}

try {
    $yahoo = new YahooFantasyService($provider, $token, function($refreshed) {
            $_SESSION['token'] = serialize($refreshed);          
        });
    $teams = $yahoo->getUserTeams();
    echo '<pre>' . json_encode($teams, JSON_PRETTY_PRINT) . '</pre>';
    
} catch (Exception $ex) {
    exit($ex->getMessage());
} finally {
    $token = unserialize($_SESSION['token']);   
}