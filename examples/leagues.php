<?php

use Kenjdavidson\OAuth2\YahooFantasySports\Provider\Service\YahooFantasyService;

/**
 * game.php
 * 
 * Game.php file is responsible for displaying examples of the GameCollection
 * and GameResource files.
 * 
 * @link https://developer.yahoo.com/fantasysports/guide/#leagues-collection
 * @link https://developer.yahoo.com/fantasysports/guide/#league-resource
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
    $leagues = $yahoo->getUserLeagues();
    echo '<pre>' . json_encode($leagues, JSON_PRETTY_PRINT) . '</pre>';
    //echo json_encode($leagues[0]->getStandings(), JSON_PRETTY_PRINT);
    
} catch (Exception $ex) {
    exit($ex->getMessage());
} finally {
    $token = unserialize($_SESSION['token']);   
}