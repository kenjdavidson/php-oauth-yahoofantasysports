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
 * @link https://developer.yahoo.com/fantasysports/guide/#player-collection
 * @link https://developer.yahoo.com/fantasysports/guide/#player-resource
 * 
 * The player collection has the available sub-resourceS: stats 
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
        'leagues'    =>  '/users;use_login=1/games%s/leagues;out=standings,scoreboard',
        'teams'      =>  '/users;use_login=1/games%s/leagues/teams;out=standings,roster,players'         
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
     * @var Callable    function(AccessToken $token)
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
     * @param boolean $json returns the JSON response
     * @return Mixed    SimpleXMLElement or JSON Object
     * 
     * @link http://php.net/manual/en/simplexmlelement.xpath.php
     */
    public function makeApiRequest($method, $url, $json = false) {
        $apiUrl = $url . ($json ? '?format=json' : '');
        $request = $this->provider->getAuthenticatedRequest(
                $method, 
                $apiUrl,
                $this->token);
        $response = $this->provider->getParsedResponse($request);        
        
        if ($json) {
            return json_encode($response);
        }
        
        $xml = new \SimpleXMLElement($response);  
        return YahooFantasyService::addXmlNamespace($xml);
    }
    
    /**
     * Makes a YQL request against the Yahoo! YQL services.
     * @param string $method
     * @param string $url
     * @param string $q
     * @param boolean $json
     * @return Mixed    SimpleXMLElement or JSON
     * @deprecated YQL seem to not work due to security issues.
     */
    public function makeYqlRequest($method, $url, $q, $json = false) {
        $apiUrl = $url . $q . ($json ? '?format=json' : '');
        $request = $this->provider->getAuthenticatedRequest(
                $method, 
                $apiUrl,
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
     * Helper method to add name spaces to the SimpleXMLElement Object
     * provided.  Name spaces are required in order to perform XPath lookups, 
     * since Yahoo doesn't provide their own, we need to add the 'yf:' name space.
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
     * Makes a user resource request by season.  The request is made based 
     * on the resource and season.  Always defaults to the current season, which
     * requires no filter criteria.
     * @param String $resource
     * @param String $seasons
     * @return SimpleXMLElement
     */
    private function getUserResourceBySeason($resource, $seasons = null) {
        $theSeason = ';seasons=' . (isset($seasons) ? $seasons : '');
        $collection = sprintf(YahooFantasyService::USERS_URI[$resource], $theSeason);
        $url = YahooFantasyService::API_BASE . $collection;  
        return $this->makeApiRequest(YahooFantasyProvider::METHOD_GET, $url);        
    }
    
    /**
     * Get user account method.  Direct request to the AbstractProvider->getResourceOwner
     * method.
     * @return JSON 
     */
    public function getUserAccount() {
        return $this->provider->getResourceOwner($this->token);
    }
    
    /**
     * Creates and makes a request to the Games Collection, getting all the
     * games in which the user is registered.  The user games can be filtered
     * by season, which should be a comma separated list of years .  This is a
     * shortcut for the full getGames method.
     *      
     * @param $seasons the season(s) in which to look for games collection
     * @return Array    of GameResource Objects   
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
     * leagues in which the user is registered.  This method does some magic
     * to convert the SimpleXMLElement arrays into regular object arrays (this
     * ensures there are no array[@attribute] => 0 entries.
     * 
     * @param String $seasons the season(s) in which to look for games collection
     * @return Array    of LeagueResource objects        
     */
    public function getUserLeagues($seasons = null) {
        $resource = $this->getUserResourceBySeason('leagues', $seasons);
       
        $leagues = array();
        foreach($resource->xpath('//y:league') as $leagueXml) {
            YahooFantasyService::addXmlNamespace($leagueXml);
            $standingsXml = $leagueXml->xpath('./y:standings/y:teams/y:team');
            $scoreboardXml = $leagueXml->xpath('./y:scoreboard/y:matchups/y:matchup');
            
            $league = YahooFantasyService::jsonToArray($leagueXml);
            $league['standings'] = YahooFantasyService::jsonToArray($standingsXml);
            $league['scoreboard'] = YahoofantasyService::jsonToArray($scoreboardXml);
                                    
            $allTeams = [];
            foreach ($league['standings'] as $team) {
                $allTeams[$team['team_id']] = $team;
            }
            
            // Workaround for xml to array for scoreboard teams                  
            for ($i = 0; $i < count($league['scoreboard']); $i++) {
                $league['scoreboard'][$i]['teams'] = array(
                    $allTeams[$league['scoreboard'][$i]['teams']['team'][0]['team_id']],
                    $allTeams[$league['scoreboard'][$i]['teams']['team'][1]['team_id']]
                );
            }
            
            $leagues[] = new LeagueResource($league);
        }
        return $leagues;
    }     
    
    /**
     * Creates and makes a request to the Teams Collection, getting all the
     * teams in which the user is registered.
     * 
     * @param $seasons the season(s) in which to look for games collection
     * @return Array    of TeamResource objects  
     */
    public function getUserTeams($seasons = null) {
        $resource = $this->getUserResourceBySeason('teams', $seasons);
        
        $teams = array();
        foreach($resource->xpath('//y:team') as $teamXml) {
            YahooFantasyService::addXmlNamespace($teamXml);
            $playersXml = $teamXml->xpath('./y:roster/y:players/y:player');
            
            $team = YahooFantasyService::jsonToArray($teamXml);
            $team['roster']['players'] = YahooFantasyService::jsonToArray($playersXml);
            $teams[] = new TeamResource($team);            
        }
        return $teams;      
    }
}
