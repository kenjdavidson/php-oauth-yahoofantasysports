<?php

use Kenjdavidson\OAuth2\YahooFantasySports\Provider\Service\YahooFantasyService;
use League\OAuth2\Client\Token\AccessToken;

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
} else {
    ?>
    <h1>No Session Token Found</h1>
    <p>
        No session token was found, you'll need to go back to the
        <a href="/index.php">index.php</a> page and attempt to refresh the token.
    </p>
    <?php
}

try {
    ?>
    <h1>Yahoo! User Leagues</h1>
    <p>
        Provider and Access Token were created successfully.  Attempting to 
        lookup of user games.  To modify the standard request, you can 
        add ?seasons=YYYY&game_key=### to the URL:
        <ul>
            <li><a href="leagues.php">Current Leagues</a></li>
            <li><a href="leagues.php?seasons=2017">2017 Leagues</a></li>
            <li><a href="leagues.php?seasons=2016">2016 Leagues</a></li>
        </ul>
    </p>
    <?php 
    $yahoo = new YahooFantasyService($provider, $token, function($refreshed) {
            $_SESSION['token'] = serialize($refreshed);          
        });
        
    $seasons = filter_input(INPUT_GET, 'seasons');
    $leagues = $yahoo->getUserLeagues($seasons);
    echo '<pre>' . json_encode($leagues, JSON_PRETTY_PRINT) . '</pre>';
    //echo json_encode($leagues[0]->getStandings(), JSON_PRETTY_PRINT);
    
} catch (Exception $ex) {
    ?>
    <p>
        Leagues could not be looked up. <?php echo $ex->getMessage(); ?>
    </p>
    <?php
} finally {
    $token = unserialize($_SESSION['token']);   
}