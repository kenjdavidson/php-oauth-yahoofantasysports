<?php

namespace Kenjdavidson\OAuth2\YahooFantasySports\Provider\Service;

use Kenjdavidson\OAuth2\YahooFantasySports\Provider\YahooFantasyProvider;
use Kenjdavidson\OAuth2\YahooFantasySports\Provider\Resource\GameResource;
use Kenjdavidson\OAuth2\YahooFantasySports\Provider\Resource\LeagueResource;
use Kenjdavidson\OAuth2\YahooFantasySports\Provider\Resource\TeamResource;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;

/**
 * Standard method used within Yahoo Resource Services.  This service makes
 * calls to the API (which respond in XML) and convert the message to JSON.  
 * This is annoying, but the YQL service doesn't like the authorized request
 * and will not respond.
 * 
 * Regular API:
 * 
 * Games:
 * @link https://developer.yahoo.com/fantasysports/guide/#game-resource
 * @link https://developer.yahoo.com/fantasysports/guide/#games-collection
 * 
 * The games collection has the available sub-resource: leagues
 * 
 * Leagues:
 * @link https://developer.yahoo.com/fantasysports/guide/#leagues-collection
 * @link https://developer.yahoo.com/fantasysports/guide/#league-resource
 * 
 * The leagues collection has the available sub-resources: settings, standings,
 * scoreboard and teams
 *
 * Teams:
 * @link https://developer.yahoo.com/fantasysports/guide/#teams-collection
 * @link https://developer.yahoo.com/fantasysports/guide/#team-resource
 * 
 * The teams collection has the available sub-resources: matchups;weeks=, 
 * stats;type=[season|date], roster
 * 
 * Roster:
 * @link https://developer.yahoo.com/fantasysports/guide/#roster-collection
 * @link https://developer.yahoo.com/fantasysports/guide/#roster-resource
 * 
 * The roster collection has the available sub-resources: player
 * 
 * Player:
 * @link https://developer.yahoo.com/fantasysports/guide/#roster-collection
 * @link https://developer.yahoo.com/fantasysports/guide/#roster-resource
 * 
 * The player collection has the available sub-resourceS: stats
 * 
 *  
 * 
 * @author Kenneth Davidson
 */
class YahooFantasyService {
    
    // API base URI
    const API_BASE = 'https://fantasysports.yahooapis.com/fantasy/v2';
    
    // Collection URIs for specific request types
    const COLLECTION_URI = array(
        'games'      =>  '/users%s/games;game_keys=%s',
        'leagues'    =>  '/users%s/games/leagues;league_keys=%s%s',
        'teams'      =>  '/users%s/games/leagues/teams;team_keys=%s%s'        
    );
    
    // User Data URIs for specific request types
    const USERS_URI = array(
        'games'      =>  '/users;use_login=1/games%s',
        'leagues'    =>  '/users;use_login=1/games%s/leagues/standings',
        'teams'      =>  '/users;use_login=1/games%s/leagues/teams/standings'         
    );   
    
    /**
     * AbstractProvider used to make requests
     * @var YahooFantasyProvider
     */
    protected $provider;
    
    /**
     * AccessToken used to make requests
     * @var AccessToken
     */
    protected $token; 
    
    /**
     * On Refresh callback.
     * @var Callable
     */
    protected $onRefresh;
    
    /**
     * Creates a new GameService.  The GameService will automatically attempt
     * to refresh the token if it's expired. 
     * @param YahooFantasyProvider $provider
     * @param AccessToken $token
     * @param mixed $onRefresh
     */
    public function __construct($provider, $token, $onRefresh = null) {
        $this->provider = $provider;
        $this->token = $token; 
        $this->onRefresh = $onRefresh;
        
        if ($token->hasExpired()) {
            $this->refreshToken();
        }
    }
    
    /**
     * Convert an object into a JSON array
     * @param Object $obj
     * @return array
     */
    private static function jsonToArray($obj) {
        return json_decode(json_encode($obj), true);
    }
    
    /**
     * Makes an authenticated request and returns the result from the Yahoo!
     * API.  The XML returned has namespaces that need to be registered if 
     * XPath will be used on the XML.
     * @param string $method
     * @param string $url
     * @param boolean $json returns the XML converted to JSON
     * @return SimpleXMLElement
     * 
     * @link http://php.net/manual/en/simplexmlelement.xpath.php
     */
    public function makeApiRequest($method, $url, $json = false) {
        $request = $this->provider->getAuthenticatedRequest(
                $method, 
                $url,
                $this->token);
        $response = $this->provider->getParsedResponse($request);        
        $xml = new \SimpleXMLElement($response);  
        
        if ($json) {
            return json_encode($xml);
        }
        return YahooFantasyService::addXmlNamespace($xml);
    }
    
    /**
     * Makes a YQL request against the Yahoo! YQL services.
     * @param string $method
     * @param string $url
     * @param string $q
     * @param boolean $json
     * @return mixed if the response is JSON then the request is a Assoc array
     *      otherwise its a string of the result.
     * @deprecated YQL seem to not work due to security issues.
     */
    public function makeYqlRequest($method, $url, $q, $json = false) {
        $jq = ($json) ? '&format=json' : '';
        $request = $url . $q . $jq;
        echo $request;
        $request = $this->provider->getAuthenticatedRequest(
                $method, 
                $request,
                $this->token);
        
        $response = $this->provider->getParsedResponse($request);
        
        if ($json) {
            return json_encode($response);
        } else {
            $xml = new \SimpleXMLElement($response);
            return YahooFantasyService::addXmlNamespace($xml);
        }
    }
    
    /**
     * Helper method to add namespaces to the SimpleXMLElement Object
     * provided.
     * @param SimpleXmlElement $xml
     */
    private static function addXmlNamespace($xml) {
        foreach($xml->getDocNamespaces() as $strPrefix => $strNamespace) {
            $strPrefix = (strlen($strPrefix) == 0) ? "y" : $strPrefix; 
            $xml->registerXPathNamespace($strPrefix,$strNamespace);            
        }
        return $xml;
    }
    
    /**
     * Private function used to refresh the token.  If successful, and the
     * onRefresh method is valid, then it attempts to call the onRefresh
     * callback with the new token.
     */
    private function refreshToken() {
        $this->token = $this->provider->getAccessToken('refresh_token', [
            'refresh_token' => $this->token->getRefreshToken()
        ]);
        
        if ($this->onRefresh && is_callable($this->onRefresh)) {
            call_user_func($this->onRefresh, $this->token);
        }
    }
    
    /**
     * Makes a user resource request by season
     * @param String $season
     */
    private function getUserResourceBySeason($resource, $seasons = null) {
        $theSeason = ';seasons=' . (isset($seasons) ? $seasons : getDate()['year']);
        $collection = sprintf(YahooFantasyService::USERS_URI[$resource], $theSeason);
        $url = YahooFantasyService::API_BASE . $collection;  
        return $this->makeApiRequest(YahooFantasyProvider::METHOD_GET, $url);        
    }
    
    /**
     * Creates and makes a request to the Games Collection, getting all the
     * games in which the user is registered.  The user games can be filtered
     * by season, which should be a comma separated list of years .  This is a
     * shortcut for the full getGames method.
     *      
     * @param $seasons the season(s) in which to look for games collection
     * @return mixed   
     */
    public function getUserGames($seasons = null) {
        $resource = $this->getUserResourceBySeason('games', $seasons);
        $games = array();
        foreach($resource->xpath('//y:game') as $game) {
            $games[] = new GameResource(YahooFantasyService::jsonToArray($game));
        }   
        return $games;
    }    
    
    /**
     * Creates and makes a request to the Leagues Collection, getting all the
     * leagues in which the user is registered.
     * 
     * @param String $seasons the season(s) in which to look for games collection
     * @return mixed        
     */
    public function getUserLeagues($seasons = null) {
        $resource = $this->getUserResourceBySeason('leagues', $seasons);
        $leagues = array();
        foreach($resource->xpath('//y:league') as $league) {
            $leagues[] = new LeagueResource(YahooFantasyService::jsonToArray($league));
        }
        return $leagues;
    }     
    
    /**
     * Creates and makes a request to the Teams Collection, getting all the
     * teams in which the user is registered.
     * 
     * @param $seasons the season(s) in which to look for games collection
     * @return mixed  
     */
    public function getUserTeams($seasons = null) {
        $resource = $this->getUserResourceBySeason('teams', $seasons);
        $teams = array();
        foreach($resource->xpath('//y:team') as $xteam) {
            $teams[] = new TeamResource(YahooFantasyService::jsonToArray($xteam));            
        }
        return $teams;      
    }
}
