<?php
include_once "classes/game.php";
include_once "classes/word.php";
include_once "classes/user.php";
include_once "config/config.php";
include_once "lib/database.php";
include_once "lib/json.php";

$game = NULL;
$returnStatement = array();
if(isset($_REQUEST['new'])) {
	if(createNewGame($game)) {
		$returnStatement['status'] = 0;
		$returnStatement['message'] = "Your new game has been created.";
		$returnStatement['data']['game'] = $game->getGameData();
	} else {
		$returnStatement['status'] = 1;
		$returnStatement['message'] = "There was a problem creating a new game.";
	}
} else if(isset($_REQUEST['join'])) {
	if(joinGame($game)) {
		$returnStatement['status'] = 0;
		$returnStatement['message'] = "You have joined the game.";
		$returnStatement['data']['game'] = $game->getGameData();
	} else {
		$returnStatement['status'] = 1;
		$returnStatement['message'] = "The game could not be joined.";
	}
} else if(isset($_REQUEST['check'])) {
	if(getData($game)) {
		$returnStatement['status'] = 0;
		$returnStatement['message'] = "The game data was retrieved.";
		$returnStatement['data']['game'] = $game->getGameData();
	} else {
		$returnStatement['status'] = 1;
		$returnStatement['message'] = "The game data could not be retrieved.";
	}
} else if(isset($_REQUEST['play'])) {
	if(playWord($game)) {
		$returnStatement['status'] = 0;
		$returnStatement['message'] = "Your word has been played.";
		$returnStatement['data']['game'] = $game->getGameData();
	} else {
		$returnStatement['status'] = 1;
		$returnStatement['message'] = "The word you played is invalid.";
	}
} else if(isset($_REQUEST['skip'])) {
	if(skipTurn($game)) {
		$returnStatement['status'] = 0;
		$returnStatement['message'] = "You have skipped your turn.";
		$returnStatement['data']['game'] = $game->getGameData();
	} else {
		$returnStatement['status'] = 1;
		$returnStatement['message'] = "There was a problem skipping your turn.";
	}
} else if(isset($_REQUEST['resign'])) {
	if(resignGame($game)) {
		$returnStatement['status'] = 0;
		$returnStatement['message'] = "You have forfeited the game.";
		$returnStatement['data']['game'] = $game->getGameData();
	} else {
		$returnStatement['status'] = 1;
		$returnStatement['message'] = "There was a problem forfeiting the game.";
	}
} else {
	$returnStatement['status'] = 1;
	$returnStatement['message'] = "There was no action specified.";
}
mysql_close($db);
returnJSON($returnStatement);

function createNewGame(&$game) {
	if(!isset($_REQUEST['token'])) {
		return false;
	}
	try {
		$game = new Game($_REQUEST['token']);
	} catch(Exception $e) {
		return false;
	}
	if(!$game->create()) {
		return false;
	}
	return true;
}

function joinGame(&$game) {
	if(!isset($_REQUEST['token'])) {
		return false;
	}
	try {
		if(isset($_REQUEST['game_id'])) {
			$game = new Game($_REQUEST['token'], $_REQUEST['game_id']);
		} else {
			$game = new Game($_REQUEST['token']);
		}
	} catch(Exception $e) {
		return false;
	}
	if(!$game->join()) {
		return false;
	}
	return true;
}

function getData(&$game) {
	if(!isset($_REQUEST['token']) || !isset($_REQUEST['game_id'])) {
		return false;
	}
	try {
		$game = new Game($_REQUEST['token'], $_REQUEST['game_id']);
	} catch(Exception $e) {
		return false;
	}
	return true;
}

function playWord(&$game) {
	if(!isset($_REQUEST['token']) || !isset($_REQUEST['game_id']) || !isset($_REQUEST['word'])) {
		return false;
	}
	try {
		$game = new Game($_REQUEST['token'], $_REQUEST['game_id']);
	} catch(Exception $e) {
		return false;
	}
	if(!$game->playWord($_REQUEST['word'])) {
		return false;
	}
	return true;
}

function skipTurn(&$game) {
	if(!isset($_REQUEST['token']) || !isset($_REQUEST['game_id'])) {
		return false;
	}
	try {
		$game = new Game($_REQUEST['token'], $_REQUEST['game_id']);
	} catch(Exception $e) {
		return false;
	}
	if(!$game->skip()) {
		return false;
	}
	return true;
}

function resignGame(&$game) {
	if(!isset($_REQUEST['token']) || !isset($_REQUEST['game_id'])) {
		return false;
	}
	try {
		$game = new Game($_REQUEST['token'], $_REQUEST['game_id']);
	} catch(Exception $e) {
		return false;
	}
	if(!$game->resign()) {
		return false;
	}
	return true;
}
?>
