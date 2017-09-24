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
 * Games:
 * @link https://developer.yahoo.com/fantasysports/guide/#game-resource
 * @link https://developer.yahoo.com/fantasysports/guide/#games-collection
 * 
 * Leagues:
 * @link https://developer.yahoo.com/fantasysports/guide/#leagues-collection
 * @link https://developer.yahoo.com/fantasysports/guide/#league-resource
 *
 * Teams:
 * @link https://developer.yahoo.com/fantasysports/guide/#teams-collection
 * @link https://developer.yahoo.com/fantasysports/guide/#team-resource
 * 
 * @author Kenneth Davidson
 */
class YahooFantasyService {
    
    // API base URI
    const API_BASE = 'https://fantasysports.yahooapis.com/fantasy/v2';
    
    // Game related paths
    const GAME_RESOURCE = '/game';
    const GAME_COLLECTION = '/games';
    const USER_GAMES = '/users;use_login=1/games';
    
    // League related paths
    const LEAGUE_RESOURCE = '/league';
    const LEAGUE_COLLECTION = '/leagues';
    const USER_LEAGUES = '/users;use_login=1/games/leagues/standings';
    
    // League related paths
    const TEAM_RESOURCE = '/team';
    const TEAM_COLLECTION = '/teams';
    const USER_TEAMS = '/users;use_login=1/games/leagues/teams/standings'; 
    
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
     * @return SimpleXMLElement
     * 
     * @link http://php.net/manual/en/simplexmlelement.xpath.php
     */
    public function makeApiRequest($method, $url) {
        $request = $this->provider->getAuthenticatedRequest(
                $method, 
                $url,
                $this->token);
        $response = $this->provider->getParsedResponse($request);        
        $xml = new \SimpleXMLElement($response);
        
        foreach($xml->getDocNamespaces() as $strPrefix => $strNamespace) {
            $strPrefix = (strlen($strPrefix) == 0) ? "y" : $strPrefix; 
            $xml->registerXPathNamespace($strPrefix,$strNamespace);            
        }
        
        return $xml;
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
    public function makeYqlRequest($method, $url, $q, $json) {
        $jq = ($json) ? '&format=json' : '';
        $request = $url . $q . $jq;
        echo $request;
        $request = $this->provider->getAuthenticatedRequest(
                $method, 
                $request,
                $this->token);
        
        return $this->provider->getParsedResponse($request);
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
     * Creates and makes a request to the Games Collection, getting all the
     * games in which the user is registered. 
     *      
     * @param boolean $rawXml   flag noting whether the request should return     *                          an array of SimpleXMLElements or Resource
     *                          Objects
     * 
     * @return mixed   
     */
    public function getUserGames($rawXml = false) {
        $url = YahooFantasyService::API_BASE . YahooFantasyService::USER_GAMES;        
        $response = $this->makeApiRequest(YahooFantasyProvider::METHOD_GET, $url);
        
        if ($rawXml) {
            return $response;
        }
        
        $games = array();
        foreach($response->xpath('//y:game') as $game) {
            $games[] = new GameResource(YahooFantasyService::jsonToArray($game));
        }   
        return $games;
    }    
    
    /**
     * Creates and makes a request to the Leagues Collection, getting all the
     * leagues in which the user is registered.
     * 
     * @param boolean $rawXml   flag noting whether the request should return
     *                          an array of SimpleXMLElements or Resource
     *                          Objects
     * 
     * @return mixed        
     */
    public function getUserLeagues($rawXml = false) {
        $url = YahooFantasyService::API_BASE . YahooFantasyService::USER_LEAGUES;        
        $response = $this->makeApiRequest(YahooFantasyProvider::METHOD_GET, $url);
        
        if ($rawXml) {
            return $response;
        } 
        
        $leagues = array();
        foreach($response->xpath('//y:league') as $league) {
            $leagues[] = new LeagueResource(YahooFantasyService::jsonToArray($league));
        }
        return $leagues;
    }     
    
    /**
     * Creates and makes a request to the Teams Collection, getting all the
     * teams in which the user is registered.
     * 
     * @param boolean $rawXml   flag noting whether the request should return
     *                          an array of SimpleXMLElements or Resource
     *                          Objects
     * 
     * @return mixed  
     * 
     * @var $leagueR array
     */
    public function getUserTeams($rawXml = false) {
        $url = YahooFantasyService::API_BASE . YahooFantasyService::USER_TEAMS;        
        $response = $this->makeApiRequest(YahooFantasyProvider::METHOD_GET, $url);
        
        if ($rawXml) {
            return $response;
        } 
       
        $teams = array();
        foreach($response->xpath('//y:team') as $xteam) {
            $teams[] = new TeamResource(YahooFantasyService::jsonToArray($xteam));            
        }
        return $teams;      
    }
}
