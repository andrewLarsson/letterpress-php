letterpress-php
===============

Letterpress ported to PHP.

API Documentation:

Example:
http://www.letterpress-server.com/api/api_call.json.php?action&parameter=value
api_call
	action
		parameter

auth.json.php
	register
	authenticate
		token

game.json.php
	new
	join
		game_id
	play_word
		game_id
		word
	skip_turn
		game_id
	resign
		game_id
