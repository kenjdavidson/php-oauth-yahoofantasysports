<?php
namespace Kenjdavidson\OAuth2\YahooFantasySports\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

/**
 * UserResource implements the Yahoo! Fantasy User Resource API.  The user
 * request contains information regarding the manager profile and fantasy
 * history.
 * 
 * @author Kenneth Davidson
 */
class UserResource implements ResourceOwnerInterface {
    
    /**
     * UserResource response as Array
     * @var Array
     */
    protected $response;
    
    /**
     * Creates a new UserResource Object
     * @param type $response
     */
    public function __construct($response) {
        $this->response = $response;       
    }
    
    /**
     * Return the Yahoo! user Id
     * @return string
     */
    public function getId() {
        return $this->response['profile']['guid'];
    }

    /**
     * Return the User Resource as an array
     * @return array
     */
    public function toArray() {
        return $this->response;
    }

    /**
     * Return the User Nickname
     * @return string
     */
    public function getNickname() {
        return $this->response['profile']['nickname'];
    }
}