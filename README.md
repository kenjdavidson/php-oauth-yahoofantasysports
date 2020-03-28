# kenjdavidson/oauth-yahoofantasysports

Provides League/oauth2-client provider and resources to communicate specifically with the Yahoo! Fantasy Sports API.  The YahooFantasyService provides basic calls to Games, Leagues and Teams collections; custom calls can be made through the as well.  Data is returned as: League/oauth2-client/ResourceOwnerInterface 

## Installation

Package is available on composer: https://packagist.org/packages/kenjdavidson/oauth-yahoofantasysports

```json
"require": {
  "composer/installers": "v1.4.0",
  "kenjdavidson/oauth-yahoofantasysports": "dev-master"
}
```

## Development

To get the project:

1) fork/clone https://github.com/kenjdavidson/oauth-yahoofantasysports.git
2) install composer (https://getcomposer.org/doc/00-intro.md)
3) go

### Prerequisites

The following are required:

1) Composer

## Running the tests

Examples and Tests (todo) are available for a number of resources.

### Examples

Currently the examples are physical pages that need to be manually loaded and validated.  This is a painful process but I didn't have much experience with PHP at the time and hadn't gotten around to writing tests.  I'm thinking of coming back to this project to get a little more experience so actual tests may be incoming.

```
/index.php - Displays the link to request OAuth2 or the token itself.
/config.php - Requires creation, follow structure in index.php
/games.php - Displays games JSON
/leagues.php - Displays leagues JSON
/teams.php - Displays teams JSON
```

### Testing

TODO - actually learn how to write PHP tests

## Contributing

Always looking for contributions, whether it be bug fixes, features or code review.  Feel free to pull request if you feel like you're adding any kind of benefit.

## License

This project is licensed under the MIT License.

## Acknowledgments

* League/oauth2-client (https://github.com/thephpleague/oauth2-client)
* Hayageek/oauth2-yahoo (https://github.com/hayageek/oauth2-yahoo)
