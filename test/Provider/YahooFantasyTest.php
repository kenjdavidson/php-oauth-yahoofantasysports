<?php

namespace Kenjdavidson\OAuth2\YahooFantasySports\Test\Provider;
require(__DIR__ . '/../../vendor/autoload.php');

use Kenjdavidson\OAuth2\YahooFantasy\Provider\YahooFantasy;
use Mockery as Mock;

/**
 * Description of YahooFantasyTest.  Using Mockery this class should test
 * all the methods available in the YahooFantasy Provider.
 * 
 * TODO: Complete
 *
 * @author kendavidson
 */
class YahooFantasyTest extends \PHPUnit_Framework_TestCase {
    
    /**
     * The provider.
     * @var YahooFantasy 
     */
    protected $provider;
    
    /**
     * Create the Provider class using test consumer details.
     */
    protected function setUp() {
        $this->provider = new YahooFantasy(array(
            'clientId'      => '',
            'clientSecret'  => '',
            'redirectUrl'   => 'none'
        ));
    }
    
    /**
     * Close the Mockery object
     */
    protected function tearDown() {
        Mock::close();
        parent::tearDown();
    }
}
