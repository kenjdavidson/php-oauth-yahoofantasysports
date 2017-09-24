<?php

namespace Kenjdavidson\OAuth2\YahooFantasySports\Provider\Resource;

/**
 * GameResource implements the requests to the Yahoo! Fantasy sports 
 * Game Resource API.  Game API only supports the GET method, as there is 
 * nothing needed to update or save.
 * 
 * Example requests are:
 *
 * https://fantasysports.yahooapis.com/fantasy/v2/game/{game_key}
 * 
 * The game resource has the following JSON format:
 * 
 * {
 *  "game_key":"376",
 *  "game_id":"376",
 *  "name":"Hockey",
 *  "code":"nhl",
 *  "type":"full",
 *  "url":"https:\/\/hockey.fantasysports.yahoo.com\/hockey",
 *  "season":"2017",
 *  "is_registration_over":"0",
 *  "is_game_over":"0",
 *  "is_offseason":"0"
 * }
 *
 * @link https://developer.yahoo.com/fantasysports/guide/#games-collection
 * @link https://developer.yahoo.com/fantasysports/guide/#game-resource
 */
class GameResource extends YahooFantasyResource {
    
    public function getKey() { return $this->get('game_key'); }
    public function getId() { return $this->get('game_id'); } 
    public function getName() {return $this->get('name'); }
    public function getCode() { return $this->get('code'); }
    public function getType() { return $this->get('type'); }
    public function getYear() { return $this->get('year'); }
    public function isRegistrationOver() { return $this->get('is_registration_over'); }
    public function isGameOver() { return $this->get('is_game_over'); }
    public function isOffseason() { return $this->get('is_offseason'); }
    
}
