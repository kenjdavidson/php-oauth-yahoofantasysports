<?php

namespace Kenjdavidson\OAuth2\YahooFantasySports\Provider\Resource;

/**
 * UserResource implements the Yahoo! Fantasy User Resource API.  The user
 * request contains information regarding the manager profile and fantasy
 * history.
 * 
 * The UserResource outputs JSON in the following format:
 * 
 * {
 *  "profile":{  
 *      "guid":"HJXEGSOPDMQLLZFJIQ2BCZA3Z4",
 *      "ageCategory":"A",
 *      "image":{  
 *          "height":192,
 *          "imageUrl":"https:\/\/s.yimg.com\/wv\/images\/9ee152f0b1eb57b02b8896274d0ec1ad_192.jpeg",
 *          "size":"192x192",
 *          "width":192
 *      },
 *      "intl":"us",
 *      "jurisdiction":"us",
 *      "lang":"en-US",
 *      "memberSince":"2017-05-19T12:57:18Z",
 *      "nickname":"Kenneth",
 *      "notStored":false,
 *      "nux":"2",
 *      "profileMode":"PUBLIC",
 *      "profileStatus":"ACTIVE",
 *      "profileUrl":"http:\/\/profile.yahoo.com\/HJXEGSOPDMQLLZFJIQ2BCZA3Z4",
 *      "updated":"2017-08-11T02:51:33Z",
 *      "isConnected":false,
 *      "profileHidden":false,
 *      "bdRestricted":true,
 *      "profilePermission":"PRIVATE",
 *      "uri":"https:\/\/social.yahooapis.com\/v1\/user\/HJXEGSOPDMQLLZFJIQ2BCZA3Z4\/profile",
 *      "cache":true
 *  }
 * }
 * 
 * @author Kenneth Davidson
 */
class UserResource extends YahooFantasyResource {
   
    /**
     * Return the Yahoo! user Id
     * @return string
     */
    public function getId() {
        return $this->data['profile']['guid'];
    }

    /**
     * Return the User Nickname
     * @return string
     */
    public function getNickname() {
        return $this->data['profile']['nickname'];
    }
    
    /**
     * Returns the User profile URL
     * @return string
     */
    public function getProfileURL() {
        return $this->data['profile']['profileUrl'];
    }
    
    /**
     * Returns the User Image URL
     * @return string
     */
    public function getProfileImageURL() {
        return $this->data['profile']['image']['imageUrl'];
    }
   
}