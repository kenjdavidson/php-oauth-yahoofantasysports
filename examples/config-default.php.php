<?php
// File should be updated and renamed to config.php

// $consumer Array contains the clientId, clientSecret and redirectUri.  This 
// is the information used to make the request for authentication.
$consumer = array(
    'clientId'      => '',
    'clientSecret'  => '',
    'redirectUri'   => 'oob'
);

// The token should be a JSON Stringified AccessToken object
return array(
    'consumer'  => $consumer,
    'token' => ''
);                    