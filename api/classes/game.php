<?php
class Game {
	/*Contains all the variables and methods required to construct a game and take action on it.*/

	//A game is represented in MySQL as: id, player1 (user), player2 (user), current_turn (user), board (json), word_list (json), game_status(pending, inplay, finished), skip_count, winner (user).
	//All players are represented by a user id.

	/*Public Properties*/
	public $id;
	public $currentTurn;
	public $board;
	public $wordList;
	public $gameStatus;
	public $skipCount;
	public $winner;

	/*Private Variables*/
	private $user;
	private $player1;
	private $player2;
	private $activePlayer;
	private $opponent;

	/*Constructor*/
	function __construct($user = NULL, $id = NULL) {
		if(!isset($user)) {
			throw(new Exception());
		}
		$this->user = $user;
		if(isset($id)) {
			$this->id = $id;
			$this->activePlayer = $this->user->token;
			if(!$this->load()) {
				throw(new Exception());
			}
		}
	}

	/*Public Methods*/
	public function create() {
		/*Creates a fresh, empty game.*/

		global $db;

		if(!$db) {
			return false;
		}
		$letterBank = "abcdefghijklmnopqrstuvwxyz";
		$this->board = array();
		$valid = false;
		while(!$valid) {
			for($i = 0; $i < 5; $i ++) {
				for($j = 0; $j < 5; $j ++) {
					$this->board[$i][$j]->letter = substr($letterBank, rand(0, 25), 1);
					$this->board[$i][$j]->owner = 0;
					$placed[$this->board[$i][$j]->letter] = true;
				}
			}
			$valid = true;

			//Make sure a valid game board was produced.
			if(array_key_exists("q", $placed) && !array_key_exists("i", $placed)) {
				$valid = false;
			}
		}
		$this->wordList = array();
		$this->activePlayer = $this->player1 = $this->currentTurn = $this->user->token;
		$this->gameStatus = "pending";
		$this->skipCount = 0;

		//Create new entry in table.
		$query = "INSERT INTO games (player1, current_turn, board, word_list, game_status, skip_count) VALUES ('" . $this->player1 . "', '" . $this->currentTurn . "', '" . mysql_real_escape_string(json_encode($this->board)) . "', '" . mysql_real_escape_string(json_encode($this->wordList)) . "', '" . $this->gameStatus . "', '" . $this->skipCount . "')";
		if(!mysql_query($query, $db)) {
			return false;
		}

		//Get game ID.
		$query = "SELECT id FROM games WHERE player1='" . $this->player1 . "' ORDER BY id DESC LIMIT 1";
		if(!$result = mysql_query($query, $db)) {
			return false;
		}
		$row = mysql_fetch_array($result);
		$this->id = $row['id'];
		
		return true;
	}

	public function join() {
		/*Allows a player to join an existing game.*/

		global $db;

		if(!$db) {
			return false;
		}
		$query = "SELECT id FROM games WHERE game_status='pending' ORDER BY id ASC LIMIT 1";
		if(!$result = mysql_query($query, $db)) {
			return false;
		}
		$row = mysql_fetch_array($result);
		$this->id = $row['id'];
		if(!$this->load()) {
			return false;
		}
		$this->player2 = $this->user->token;
		$this->gameStatus = "inplay";
		if(!$this->save()) {
			return false;
		}
		return true;
	}

	public function playWord($wordJSON) {
		/*Plays a word*/

		if($this->gameStatus == "finished") {
			return false;
		}
		if($this->user->token != $this->player1 && $this->user->token != $this->player2) {
			return false;
		}
		$this->activePlayer = $this->user->token;
		if($this->activePlayer != $this->currentTurn) {
			return false;
		}
		if(!$this->checkWord($this->deserializeWord($wordJSON))) {
			return false;
		}
		$this->opponent = ($this->activePlayer == $this->player1) ? $this->player2 : $this->player1;
		$decoded = json_decode($wordJSON);
		$playerValue = ($this->activePlayer == $this->player1) ? 1 : 2;
		$opponentValue = ($playerValue == 1) ? 2 : 1;
		$playableLetters = array();
		foreach($decoded as $point) {
			if(!$this->isProtectedLetter($point)) {
				array_push($playableLetters, $point);
			}
		}
		foreach($playableLetters as $point) {
			$this->board[$point[0]][$point[1]]->owner = $playerValue;
		}
		array_push($this->wordList, $this->deserializeWord($wordJSON));
		$this->currentTurn = $this->opponent;
		$this->skipCount = 0;
		$this->checkWinner();
		if(!$this->save()) {
			return false;
		}
		return true;
	}

	public function getGameData() {
		/*Gets an array of the game data based on the permissions of the active user.*/

		$returnData = array();
		$returnData['id'] = $this->id;
		$returnData['board'] = $this->board;
		$returnData['currrent_turn'] = ($this->activePlayer == $this->currentTurn) ? true : false;
		$returnData['player_id'] = ($this->activePlayer == $this->player1) ? 1 : 2;
		$returnData['word_list'] = $this->wordList;
		$returnData['game_status'] = $this->gameStatus;
		return $returnData;
	}

	public function skip() {
		/*Allows a player to pass their turn.*/

		if($this->gameStatus == "finished") {
			return false;
		}
		if($this->user->token != $this->player1 && $this->user->token != $this->player2) {
			return false;
		}
		$this->activePlayer = $this->user->token;
		if($this->activePlayer != $this->currentTurn) {
			return false;
		}
		$this->opponent = $this->activePlayer == $this->player1 ? $this->player2 : $this->player1;
		$this->currentTurn = $this->opponent;
		$this->skipCount ++;
		$this->checkWinner();
		if(!$this->save()) {
			return false;
		}
		return true;
	}

	public function resign() {
		/*Allows a player to forfeit a game.*/

		if($this->gameStatus == "finished") {
			return false;
		}
		if($this->user->token != $this->player1 && $this->user->token != $this->player2) {
			return false;
		}
		$this->activePlayer = $this->user->token;
		$this->opponent = $this->activePlayer == $this->player1 ? $this->player2 : $this->player1;
		$this->winner = $this->opponent;
		$this->gameStatus = "finished";
		if(!$this->save()) {
			return false;
		}
		return true;
	}

	/*Private Functions*/
	private function load() {
		/*Loads an existing game from an id.*/

		global $db;

		if(!$db) {
			return false;
		}
		if(!isset($this->id)) {
			return false;
		}
		$query = "SELECT * FROM games WHERE id='" . $this->id . "'";
		if(!$result = mysql_query($query, $db)) {
			return false;
		}
		$row = mysql_fetch_array($result);
		$this->player1 = $row['player1'];
		$this->player2 = $row['player2'];
		$this->currentTurn = $row['current_turn'];
		$this->board = json_decode($row['board']);
		$this->wordList = json_decode($row['word_list']);
		$this->gameStatus = $row['game_status'];
		$this->skipCount = $row['skip_count'];
		$this->winner = $row['winner'];
		return true;
	}

	private function save() {
		/*Updates the database with any new data for the game.*/

		global $db;

		if(!$db) {
			return false;
		}
		if(!isset($this->id)) {
			return false;
		}
		$query = "UPDATE games SET player1='" . $this->player1 . "', player2='" . $this->player2 . "', current_turn='" . $this->currentTurn . "', board='" . mysql_real_escape_string(json_encode($this->board)) . "', word_list='" . mysql_real_escape_string(json_encode($this->wordList)) . "', game_status='" . $this->gameStatus . "', skip_count='" . $this->skipCount . "', winner='" . $this->winner . "' WHERE id='" . $this->id . "'";
		if(!mysql_query($query, $db)) {
			return false;
		}
		return true;
	}

	private function deserializeWord($wordJSON) {
		/*Represents a word by a JSON array of XY coordinates.*/

		$decoded = json_decode($wordJSON);
		$string = "";
		foreach($decoded as $point) {
			$string .= $this->board[$point[0]][$point[1]]->letter;
		}
		return $string;
	}

	private function checkWord($wordString) {
		/*Checks a word against all previously played words to make sure it does not match exactly or is a literal prifix of any.*/

		$wordLength = strlen($wordString);
		foreach($this->wordList as $playedWord) {
			if(substr($playedWord, 0, $wordLength) == $wordString) {
				return false;
			}
		}
		$word = new Word($wordString);
		if(!$word->validate()) {
			return false;
		}
		return true;
	}

	private function isProtectedLetter($point) {
		/*Checks to see if a letter is surrounded by letters that are owned by the same player.*/

		if($this->board[$point[0]][$point[1]]->owner == 0) {
			return false;
		}
		if(array_key_exists($point[0] + 1, $this->board)) {
			if(!$this->board[$point[0] + 1][$point[1]]->owner == $this->board[$point[0]][$point[1]]->owner) {
				return false;
			}
		}
		if(array_key_exists($point[0] - 1, $this->board)) {
			if(!$this->board[$point[0] - 1][$point[1]]->owner == $this->board[$point[0]][$point[1]]->owner) {
				return false;
			}
		}
		if(array_key_exists($point[1] + 1, $this->board[$point[0]])) {
			if(!$this->board[$point[0]][$point[1] + 1]->owner == $this->board[$point[0]][$point[1]]->owner) {
				return false;
			}
		}
		if(array_key_exists($point[1] - 1, $this->board[$point[0]])) {
			if(!$this->board[$point[0]][$point[1] - 1]->owner == $this->board[$point[0]][$point[1]]->owner) {
				return false;
			}
		}
		return true;
	}

	private function checkWinner() {
		/*Checks to see if there is a winner. It will set all the necessary variables if so.*/

		return true;
	}
}
?>
