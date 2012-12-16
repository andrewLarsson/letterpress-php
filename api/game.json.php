<?php
include_once "classes/game.php";
include_once "classes/word.php"
include_once "config/config.php";
include_once "lib/database.php";
include_once "lib/json.php";

action();
finish();

function action() {
	global $game;

	if(isset($_REQUEST['new'])) {
		if(createNewGame()) {
			$returnStatement['status'] = 0;
			$returnStatement['message'] = "Your new game has been created.";
			$returnStatement['data']['game_id'] = $game->id;
			returnJSON($returnStatement);
		} else {
			$returnStatement['status'] = 1;
			$returnStatement['message'] = "There was a problem creating a new game.";
			returnJSON($returnStatement);
		}
	} else if(isset($_REQUEST['join'])) {
		if(joinGame()) {
			$returnStatement['status'] = 0;
			$returnStatement['message'] = "You have joined the game.";
			$returnStatement['data']['game_id'] = $game->id;
			returnJSON($returnStatement);
		} else {
			$returnStatement['status'] = 1;
			$returnStatement['message'] = "The game could not be joined.";
			returnJSON($returnStatement);
		}
	} else if(isset($_REQUEST['play'])) {
		if(playWord()) {
			$returnStatement['status'] = 0;
			$returnStatement['message'] = "Your word has been played.";
			$returnStatement['data']['game_id'] = $game->id;
			returnJSON($returnStatement);
		} else {
			$returnStatement['status'] = 1;
			$returnStatement['message'] = "The word you played is invalid.";
			returnJSON($returnStatement);
		}
	} else if(isset($_REQUEST['skip'])) {
		if(skipTurn()) {
			$returnStatement['status'] = 0;
			$returnStatement['message'] = "You have skipped your turn.";
			$returnStatement['data']['game_id'] = $game->id;
			returnJSON($returnStatement);
		} else {
			$returnStatement['status'] = 1;
			$returnStatement['message'] = "There was a problem skipping your turn.";
			returnJSON($returnStatement);
		}
	} else if(isset($_REQUEST['resign'])) {
		if(resignGame()) {
			$returnStatement['status'] = 0;
			$returnStatement['message'] = "You have foreited the game.";
			$returnStatement['data']['game_id'] = $game->id;
			returnJSON($returnStatement);
		} else {
			$returnStatement['status'] = 1;
			$returnStatement['message'] = "There was a problem forfeiting the game.";
			returnJSON($returnStatement);
		}
	} else {
		$returnStatement['status'] = 1;
		$returnStatement['message'] = "No action specified.";
		returnJSON($returnStatement);
	}
}

function createNewGame() {
	global $game;

	$game = new Game();
	if(!$game->create()) {
		return false;
	}
	return true;
}

function joinGame() {
	global $game;

	if(isset($_REQUEST['game_id'])) {
		$game = new Game($_REQUEST['game_id']);
	} else {
		$game = new Game();
	}
	if(!$game->join() {
		return false;
	}
	return true;
}

function playWord() {
	global $game;

	if(!isset($_REQUEST['game_id']) || !isset($_REQUEST['word'])) {
		return false;
	}
	$game = new Game($_REQUEST['game_id']);
	if(!$game->playWord($_REQUEST['word'])) {
		return false;
	}
	return true;
}

function skipTurn() {
	global $game;

	if(!isset($_REQUEST['game_id'])) {
		return false;
	}
	$game = new Game($_REQUEST['game_id']);
	if(!$game->skip()) {
		return false;
	}
	return true;
}

function resignGame() {
	global $game;

	if(!isset($_REQUEST['game_id'])) {
		return false
	}
	$game = new Game($_REQUEST['game_id']);
	if(!$game->resign()) {
		return false;
	}
	return true;
}

function finish() {
	global $db;

	mysql_close($db);
}
?>
