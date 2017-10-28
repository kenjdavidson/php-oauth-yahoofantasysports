<?php

namespace Kenjdavidson\OAuth2\YahooFantasySports\Provider;

use Mockery as Mock;

class YahooFantasyProviderTest extends \PHPUnit_Framework_TestCase {
      
    protected $provider;
    
    protected function setUp() {
        global $config;
        $this->provider = new YahooFantasyProvider($config['consumer']);
    }
    
    protected function tearDown() {
        Mock::close();
        parent::tearDown();
    }
    
    public function testAuthorizationUrl() {
        global $config;
        
        $url = $this->provider->getAuthorizationUrl();
        $uri = parse_url($url);
        parse_str($uri['query'], $query);
        $this->assertEquals('/oauth2/request_auth', $uri['path']);
        $this->assertArrayHasKey('client_id', $query);
        $this->assertArrayHasKey('redirect_uri', $query);
        $this->assertArrayHasKey('state', $query);
        $this->assertArrayHasKey('response_type', $query);        
        $this->assertEquals('code', $query['response_type']);
        
        $this->assertEquals($config['consumer']['redirectUri'], $query['redirect_uri']);
		        
        $this->assertAttributeNotEmpty('state', $this->provider);
    }    
    
    public function testBaseAcccessTokenUrl() {
        $url = $this->provider->getBaseAccessTokenUrl([]);        
        $uri = parse_url($url);
        $this->assertEquals('/oauth2/get_token', $uri['path']);
    }    
    
    public function testResourceOwnerDetailsUrl() {
        $token = Mock::mock('League\OAuth2\Client\Token\AccessToken');
		$token->shouldReceive('getResourceOwnerId')->once()->andReturn('mocguid');
        $url = $this->provider->getResourceOwnerDetailsUrl($token);
        
        $uri = parse_url($url);
        $this->assertEquals('/v1/user/mocguid/profile', $uri['path']);
        $this->assertEquals('format=json', $uri['query']);
    } 
    
    public function testUserData() {
        
    }    
}
