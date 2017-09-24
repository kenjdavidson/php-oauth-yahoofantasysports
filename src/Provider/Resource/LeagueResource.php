<?php

namespace Kenjdavidson\OAuth2\YahooFantasySports\Provider\Resource;

/**
 * Implements a Yahoo League.
 * 
 * @link https://developer.yahoo.com/fantasysports/guide/#leagues-collection
 * @link https://developer.yahoo.com/fantasysports/guide/#league-resource
 *
 * League resources have the following JSON format:
 * 
 * { 
 *  "league_key":"371.l.22626",
 *  "league_id":"22626",
 *  "name":"AWKAWESOME FOOTBALL LEAGUE",
 *  "url":"https:\/\/football.fantasysports.yahoo.com\/f1\/22626",
 *  "password":{ },
 *  "draft_status":"postdraft",
 *  "num_teams":"12",
 *  "edit_key":"3",
 *  "weekly_deadline":{ },
 *  "league_update_timestamp":"1506146458",
 *  "scoring_type":"head",
 *  "league_type":"private",
 *  "renew":"359_3884",
 *  "renewed":{ },
 *  "short_invitation_url":"https:\/\/football.fantasysports.yahoo.com\/f1\/22626\/invitation?key=04fa4fe2bc64ff76&ikey=a4e1b7a986bd5443",
 *  "allow_add_to_dl_extra_pos":"0",
 *  "is_pro_league":"0",
 *  "is_cash_league":"0",
 *  "current_week":"3",
 *  "start_week":"1",
 *  "start_date":"2017-09-07",
 *  "end_week":"16",
 *  "end_date":"2017-12-25",
 *  "game_code":"nfl",
 *  "season":"2017"
 * }
 * 
 * @author kendavidson
 */
class LeagueResource extends YahooFantasyResource {
    
    public function getId() { return $this->get('league_id'); }
    public function getKey() { return $this->get('league_key'); }
    public function getName() { return $this->get('name'); }
    public function getUrl() { return $this->get('url'); }    
    public function getDraftStatus() { return $this->get('name'); }    
    public function getNumTeams() { return $this->get('name'); }    
    public function getEditKey() { return $this->get('name'); }    
    public function getScoringType() { return $this->get('name'); }
    public function getCurrentWeek() { return $this->get('name'); }    
    public function getStartWeek() { return $this->get('start_week'); }    
    public function getStartDate() { return $this->get('start_date'); }
    public function getEndWeek() { return $this->get('end_week'); }    
    public function getEndDate() { return $this->get('end_date'); }    
    public function getSeason() { return $this->get('season'); }    
    public function getGameCode() { return $this->get('game_code'); }
    public function getStandings() { return $this->get('standings')['teams']['team']; }

}
 