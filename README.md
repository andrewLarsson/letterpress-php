letterpress-php
===============

Letterpress ported to PHP.
Copyright 2013 developersBliss.com

API Documentation:
<br/>
Example:
`http://www.letterpress-server.com/api/api_call.json.php?action&parameter=value`
- __*api_call.json.php*__
	- _action_
		- _parameter_
<br/><br/>
- __auth.json.php__
	- register
	- authenticate
		- token
<br/><br/>
- __game.json.php__
	- new
		- token
	- join
		- game_id
		- token
	- check	
		- game_id
		- token
	- play_word
		- game_id
		- word
		- token
	- skip_turn
		- game_id
		- token
	- resign
		- game_id
		- token
