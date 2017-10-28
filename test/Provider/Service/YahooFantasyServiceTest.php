<?php

namespace Kenjdavidson\OAuth2\YahooFantasySports\Provider\Service;

use Kenjdavidson\OAuth2\YahooFantasySports\Provider\YahooFantasyProvider;
use Mockery as Mock;

global $config;
        
class YahooFantasyServiceTest extends \PHPUnit_Framework_TestCase {

    protected $provider;

    protected function setUp() {
        global $config;
        $this->provider = new YahooFantasyProvider($config['consumer']);
        
        $tokenJson = json_decode($config['token'], true);                
        $this->token = new \League\OAuth2\Client\Token\AccessToken($tokenJson);                       
    }
    
    protected function tearDown() {
        Mock::close();
        parent::tearDown();
    }    
    
    public function testGetUserResource() {
        
    } 
    
    public function testGetGamesCollection() {

    }
    
    public function testGetLeaguesCollection() {
        
    }
    
    public function testGetTeamsCollection() {
        
    }
    
    public function testMakeYqlRequest() {
        
    }
    
    public function testMakeApiRequest() {
        
    }
}


