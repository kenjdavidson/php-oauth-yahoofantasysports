<?php

use League\OAuth2\Client\Token\AccessToken;

/**
 * index.php
 * 
 * The index.php page provides an example for requesting and refreshing an
 * access token using the YahooFantasy provider.  
 */

$provider = require __DIR__ . '/provider.php';

// If there was an error in the GET parameters then we want to exit displaying
// that there was an error.
if (!empty($_GET['error'])) {   
    exit('Got error: ' . $_GET['error']);

// If there is no code provided in the request parameters, or the config.php
// file didn't contain any token information, then the user needs to 
// authenticate themselves with Yahoo
} elseif (empty($_GET['code']) && !isset($config['token'])) {
    $authUrl = $provider->getAuthorizationUrl();
    $_SESSION['oauth2state'] = $provider->getState();
    displayAuthorizationUrl($authUrl);
    exit;

// Finally attempt to get an access token using the code returned.  If this is 
// successful then the token is output and a list of available Resources
// is displayed    
} else {   
    try {
        if (isset($config['token'])) { 
            $oldConfig = json_decode($config['token'], true);                
            $oldToken = new AccessToken($oldConfig);

            $token = $provider->getAccessToken('refresh_token', [
                'refresh_token' => $oldToken->getRefreshToken()
            ]);            
        } else if (isset($_GET['code'])) {
            $token = $provider->getAccessToken('authorization_code', [
                'code' => $_GET['code']
            ]);        
        } else {       
            throw new Exception('Neither the code or config was provided.');
        }    
        
        $_SESSION['token'] = serialize($token);
        displayAccessToken($token);
        displayUser($provider, $token);
        
    } catch (Exception $ex) {
        exit($ex->getMessage());
    }   
}

/**
 * Display the authorization URL details.  This is the first step in the process
 * of requesting an AccessToken.  The HTML section provides details on how
 * to login and supply the request code to this page.
 * @param String $authUrl
 */
function displayAuthorizationUrl($authUrl) {
    ?>
    <div>
        <h1>Yahoo Fantasy Provider</h1>
        <p>
            Create a config.php file in the examples folder with the following.  Note - that 'token' should only 
            if you've completed the authorization process and have a token available, otherwise it should be undefined.
            format:
        <pre>
$consumer = array(
    'clientId'      => '{CLIENT_CODE}',
    'clientSecret'  => '{CLIENT_SECRET}',
    'redirectUri'   => '{CALLBACK | oob}'
);

return array(
    'consumer'  => $consumer,
    'token' => '' // Only populate if you've received an auth token and wish to have it refreshed
); 
        </pre>
        </p>
        <p>
            Follow the link provided to continue signing into Yahoo!  If the 
            redirect URL you provided is available, this page will update accordingly.
            If you used 'oob' as a callback redirect URL then you will need to 
            update the browser url, once complete to:            
            <pre>
http://localhost/examples/index.php?code=XXXXXX
            </pre>
            where XXXXXX is the code returned after login.
        </p>
    </div>
    <div>
        <p>Continue to <a href="<?php echo $authUrl; ?>">Yahoo Login</a></p>
    </div>
    <?php    
}

/**
 * Display the users information.  This requires that a successful Provider
 * and AccessToken is provided.
 * @param YahooFantasy $provider
 * @param AccessToken $token
 */
function displayUser($provider, $token) {    
    $user = $provider->getResourceOwner($token);
    ?>
<div>
    <h1>Yahoo! User <?php echo $user->getNickname(); ?></h1>
    <p>
        <strong>User:</strong> <pre><?php echo json_encode($user, JSON_PRETTY_PRINT); ?></pre>
    </p>
</div>
    <?php
}

/**
 * Display the access token details.  Access token can either be the original
 * or the refreshed token.  As long as the token is not expired, then a list
 * of resources will be made available.
 * @param AccessToken $token
 * @param Boolean $refreshed
 */
function displayAccessToken($token) {
    ?>
    <div>
        
        <h1>Access Token Details</h1>
        <?php if (isset($_GET['code'])) : ?>
        <p>
            <strong>Code:</strong> <?php echo $_GET['code']; ?>
        </p>
        <?php endif; ?>
        <p>
            <strong>Token (json):</strong> <?php echo json_encode($token); ?>        
        </p>  
        <p>
            <strong>Refresh Token:</strong> <?php echo $token->getRefreshToken(); ?>
        </p>
        <?php if(!$token->hasExpired()): ?>
        <p>
            The Access Token has not expired, or was refreshed successfully.  Here
            is a list of available resources.
            <ul>
                <li><a href="games.php">Games</a></li>
                <li><a href="leagues.php">Leagues</a></li>
                <li>Teams</li>
            </ul>
        </p>
        <?php endif; ?>
    </div>
    <?php
}
