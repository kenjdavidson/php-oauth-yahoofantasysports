<?php

use Kenjdavidson\OAuth2\YahooFantasySports\Provider\Service\YahooFantasyService;
use League\OAuth2\Client\Token\AccessToken;

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
    <h1>Yahoo! User Games</h1>
    <p>
        Provider and Access Token were created successfully.  Attempting to 
        lookup of user games. To modify the standard request, you can 
        add ?seasons=YYYY&game_key=### to the URL:
        <ul>
            <li><a href="games.php">Current Games</a></li>
            <li><a href="games.php?seasons=2017">2017 Games</a></li>
            <li><a href="games.php?seasons=2016">2016 Games</a></li>
        </ul>
    </p>
    <?php
    $yahoo = new YahooFantasyService($provider, $token, function($refreshed) {
            $_SESSION['token'] = serialize($refreshed);          
        });
        
    $seasons = filter_input(INPUT_GET, 'seasons');
    $games = $yahoo->getUserGames($seasons);
    echo '<pre>' . json_encode($games, JSON_PRETTY_PRINT) . '</pre>';
    
} catch (Exception $ex) {
    ?>
    <p>
        Games could not be looked up. <?php echo $ex->getMessage(); ?>
    </p>
    <?php
} finally {
    $token = unserialize($_SESSION['token']);   
}