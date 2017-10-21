<?php

namespace Kenjdavidson\OAuth2\YahooFantasySports\Provider;

/**
 * YahooFantasyUriBuilder is a utility that can be used to easily 
 * create Yahoo Fantasy URI for request with the 
 * YahooFantasyService/provider.
 * 
 * The goal is to be able to use it:
 * 
 * YahooFantasyUriBuilder
 *  ::resource(team, team_key)
 *  ->with(sub-resource, filters)
 *  ->with(sub-resource, filters)
 *  ->format('json')
 *  ->build();
 * 
 * Results in the string
 * 
 * @author kendavidson
 */
class YahooFantasyUriBuilder {
    
    const RESOURCE_URI = '/%s';
    
    /**
     * Resource name
     * @var String
     */
    private $resource;

    /**
     * Resource key
     * @var String
     */
    private $key;
    
    /**
     * Array of sub-resources
     * @var array
     */
    private $subResources;
    
    /**
     * Format being requested
     * @var String
     */
    private $format;
    
    /**
     * Private function, should only be used
     * by the resource method
     * @param String $resource
     * @param String $key
     * @return YahooFantasyUriBuilder
     */
    private function __construct($resource, $key) {
        $this->resource = $resource;
        $this->key = $key;
        $this->subResources = array();
    }
    
    /**
     * Creates a the YahooFantasyUriBuilder and returns
     * an instance
     * @param type $resource
     * @param type $key
     * @return \Kenjdavidson\OAuth2\YahooFantasySports\Provider\YahooFantasyUriBuilder
     */
    public static function resource($resource, $key) {
        return new YahooFantasyUriBuilder($resource, $key);
    }
    
    /**
     * Adds a specific sub-resource to the request.  See yahoo API
     * for details. 
     * TODO: update to include sub-sub-resources using an array 
     * @param string $subResource
     * @param array $filters associative array of filter => value
     */
    public function with($subResource, $filters) {
        $this->subResources[$subResource] = $filters;
        return $this;
    }
    
    /**
     * Sets the return format for the request
     * @param String $format
     */
    public function format($format = null) {
        $this->format = $format;
        return $this;
    }
    
    /**
     * Put it all together and return a String representing the 
     * complete URI.
     * @return String
     */
    public function build() {
        
    }
}
