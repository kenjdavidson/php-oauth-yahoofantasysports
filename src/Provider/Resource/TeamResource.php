<?php

namespace Kenjdavidson\OAuth2\YahooFantasySports\Provider\Resource;

/**
 * Implements the Yahoo Team Resource.
 * 
 * @link https://developer.yahoo.com/fantasysports/guide/#leagues-collection
 * @link https://developer.yahoo.com/fantasysports/guide/#league-resource
 *
 * Team resources have the following JSON format:
 *
 * @author kendavidson
 */
class TeamResource extends YahooFantasyResource {
    
    public function getId() { return $this->get('team_id'); }
    public function getKey() { return $this->get('team_key'); }
    public function getName() { return $this->get('name'); }
    public function isOwnedByCurrent() { return $this->get('is_owned_by_current_login'); }
    public function getUrl() { return $this->get('url'); }
    public function getDivisionId() { return $this->get('division_id'); }
    public function getWaiverPriority() { return $this->get('waiver_priority'); }
    public function getNumberOfMoves() { return $this->get('number_of_moves'); }
    public function getNumberOfTrades() { return $this->get('number_of_trades'); }
    public function getScoringType() { return $this->get('league_scoring_type'); }
    public function getDraftGrade() { return $this->get('draft_grade'); }
    public function getDraftRecap() { return $this->get('draft_recap_url'); }
    public function getLogos() { return $this->get('team_logos'); }
    public function getLogo($logo = 0) { return $this->getLogos()[$logo]['team_logo']['url']; }
    public function getManager() { return $this->get('managers')['manager']; }
    public function getCoManager() { return $this->get('managers')['co-manager']; }
    public function getStandings() { return $this->get('team_standings'); }
    public function getPointsFor() { return $this->get('team_standings')['points_for']; }
    public function getPointsAgainst() { return $this->get('team_standings')['points_against']; }
    
    /**
     * Gets the league key from team key.  The team key is in format 
     * ###.#.#####.#.###; where the league key is the first three 
     * digits, ###.#.#####.
     */
    public function parseLeagueKey() {
        if (preg_match('/(\d+\.\w*.\d+)\..*/', $this->get('team_key'), $matches) == 1) {
            return $matches[1];
        } else {
            throw new Exception('Unable to parse team_key, league_key not found.');
        }
    }
   
    
}
