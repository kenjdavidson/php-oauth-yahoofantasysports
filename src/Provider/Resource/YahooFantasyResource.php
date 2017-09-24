<?php

namespace Kenjdavidson\OAuth2\YahooFantasySports\Provider\Resource;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use JsonSerializable;

/**
 * Implements default functionality for all Yahoo OAuth resource Objects.  This
 * provides a common constructor and methods.
 *
 * @author kendavidson
 */
abstract class YahooFantasyResource implements ResourceOwnerInterface, JsonSerializable {
    
    /**
     * Data Object which stores the Yahoo content as an array
     * @var array
     */
    protected $data;
    
    /**
     * Creates a new Resource Object saving the $resouce information, which
     * should be either an associated array.
     * @param mixed $resource
     */
    public function __construct($resource) {
        $this->data = $resource;
    }  
    
    /**
     * Return the data of a specified key.  Looks through the GameResource
     * for the specified key.
     */
    public function get($key) {
        return array_key_exists($key, $this->data) ? $this->data[$key] : null;
    }

    /**
     * Converts the GameResource instance to an array
     * @return array
     */
    public function toArray() {
        return $this->data;
    }

    /**
     * Returns an array that can easily be encoded into JSON. 
     * @return array
     * @see JsonSerializable
     */
    public function jsonSerialize() {
        return $this->toArray();
    }    
}
