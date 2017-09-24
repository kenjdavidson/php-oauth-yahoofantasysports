<?php

namespace Kenjdavidson\OAuth2\YahooFantasySports\Provider;

use Kenjdavidson\OAuth2\YahooFantasySports\Provider\Resource\UserResource;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

/**
 * YahooFantasySportsProvider implements Yahoo Fantasy Sports specific details
 * using the League/oath2-client library.  The provider allows connectivity
 * to most of the major Yahoo Fantasy resources.
 *
 * @author kendavidson
 */
class YahooFantasyProvider extends AbstractProvider {       
    
    /**
     * Enables `Bearer` header authorization for providers.
     */
    use BearerAuthorizationTrait;
    
    /**
     * Yahoo OAuth returns user Id information in the xoath_yahoo_guid
     * field of the Access Token.  This isn't really used for most of
     * the request methods, instead use ;use_login=1
     */
    const ACCESS_TOKEN_RESOURCE_OWNER_ID = 'xoauth_yahoo_guid';

    /**
     * Implement a new YahooFantasy provider.  The $options array should include
     * the:
     * clientId     => consumerKey
     * clientSecret => consumerSecret
     * redirectUrl  => 'none'
     * 
     * @param array $options
     * @param array $collaborators
     */
    public function __construct(array $options = array(), array $collaborators = array()) {
        parent::__construct($options, $collaborators);
    }
    
    /**
     * Checks a provider response for errors.
     *
     * @throws IdentityProviderException
     * @param  ResponseInterface $response
     * @param  array|string $data Parsed response data
     * @return void
     */
    protected function checkResponse(ResponseInterface $response, $data) {
        
        if (!empty($data['error'])) {
            $code  = 0;
            $error = $data['error'];
            
            if (is_array($error)) {
                $code  = -1;
                $error = $error['description'];
            }
            throw new IdentityProviderException($error, $code, $data);
        }        
    }

    /**
     * Generates a resource owner object from a successful resource owner
     * details request.
     *
     * @param  array $response
     * @param  AccessToken $token
     * @return ResourceOwnerInterface
     */
    protected function createResourceOwner(array $response, AccessToken $token) {
        $user = new UserResource($response);
        return $user;
    }

    /**
     * Returns the default scopes used by this provider.
     *
     * This should only be the scopes that are required to request the details
     * of the resource owner, rather than all the available scopes.
     *
     * @return array the fantasy sports read/write scope
     */
    protected function getDefaultScopes() {
        return array('fspt-w');
    }

    /**
     * Returns the base URL for requesting an access token.
     *
     * Eg. https://oauth.service.com/token
     *
     * @param array $params
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params) {
        return 'https://api.login.yahoo.com/oauth2/get_token';
    }

    /**
     * Returns the base URL for authorizing a client.
     *
     * Eg. https://oauth.service.com/authorize
     *
     * @return string
     */
    public function getBaseAuthorizationUrl() {
        return 'https://api.login.yahoo.com/oauth2/request_auth';
    }

    /**
     * Returns the base URL for Collection resources.
     * 
     * @return string
     */
    public function getBaseResourceUrl() {
        return 'https://fantasysports.yahooapis.com/fantasy/v2';
    }
    
    /**
     * Returns the URL for requesting the resource owner's details.  In this case
     * the users Social account is returned as JSON.  
     *
     * @param AccessToken $token
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token) {
        $guid = $token->getResourceOwnerId();    
        return 'https://social.yahooapis.com/v1/user/'.$guid.'/profile?format=json';
    }

    /**
     * Used to make a full Resource request through the YahooFatnasy provider.  By 
     * passing in a FantasyResource Class object, the YahooFantasy provider
     * makes an authenticated request and returns the resource based on the
     * request provided.
     * 
     * @param type $resource
     */
    public function getResource($resource) {
        
    }
}
