letterpress-php
===============

Letterpress ported to PHP.

API Documentation:
<br/>
Example:
`http://www.letterpress-server.com/api/api_call.json.php?action&parameter=value`
- __*api_call*__
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
	- join
		- game_id
	- play_word
		- game_id
		- word
	- skip_turn
		- game_id
	- resign
		- game_id
