Vote Denton
===========

Resources to Help Voters in City of Denton Elections

# Goals

**Help Dentonites...**

- Find which district they live in.
- Find out if they are registered to vote (and in the right distrct).
- Find information about candidates.

# Contribute

If you would like to contribute, fork and clone this repository to your machine then initiate the submodules by running the following commands:

	$ git submodule init
	$ git submodule update

## Contributors

- [Patrick Daly](http://developdaly.com) - Development
- [Darren Smitherman](http://cargocollective.com/darrensmitherman) - Design
- [Adam Krawiec](http://bbttxu.com/) - Development
- [Kyle Taylor](http://kyletaylored.com/) - Development
- [Brad Koehn](http://twitter.com/daresayer) - Development
- [David Myers](http://davidmyers.name/) - Development
- [Devin Taylor](https://twitter.com/mr_fnord) - Data
- [Jet Regan](https://twitter.com/JetRegan) - Planning
- [Andrew Lewis](https://twitter.com/androoRL) - Development
- [Kevin Roden](http://rodenfordenton.com/) - Organizer
- **[All code contributors](https://github.com/daresayer/votedenton/contributors)**

## Setup

You will need to setup a local database and create a virtual host to point at the `website` directory. It's suggested to use the domain `local.votedenton.org`.

First, start by copying the `local-config-sample.php` file in the `website directory` and pasting it without the `-sample` in the same directory.

Change the database credentials in `local-config.php` to point to your local database.

Install WordPress by visiting the local website.

Configure WordPress after installation:

1. Change the Theme to "Vote Denton".
2. Create a "Home" page and change its Page Template to "Home".
3. Go to Settings > Reading and set the static front page to "Home".
4. Activate all necessary plugins (namely, WP-LESS)

Make all style changes in the `style.less` file which will compile to CSS on each page load if the file has changed.
