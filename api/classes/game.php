<?php
class Game {
	/*Contains all the variables and methods required to construct a game and take action on it.*/

	//A game is represented by MySQL: id, player1 (token), player2 (token), current_turn (token), board (json), words (json), game_status(pending_public, pending_private, inplay, finished (winner)), skip_count
	//All players are represented by an authentication token.

	/*Public Properties*/
	public $id;
	public $board;
	public $wordList;
	public $activePlayer;
	public $opponent;
	public $currentTurn;

	/*Private Variables*/
	private $player1;
	private $player2;
	private $gameStatus;

	/*Constructor*/
	function __construct($id = NULL) {
		if(isset($id)) {
			$this->id = $id;
			if(!$this->load()) {
				throw(new NotFound());
			}
		}
	}

	/*Public Methods*/
	public function create($user) {
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
					$this->board[$i][$j]["letter"] = substr($letterBank, rand(0, 25), 1);

					//The owner is marked with the player's ID (or a 0 for no owner).
					$this->board[$i][$j]["owner"] = 0;
					$placed[$this->board[$i][$j]["letter"]] = true;
				}
			}
			$valid = true;

			//Make sure a valid game board was produced.
			if(array_key_exists("q", $placed) && !array_key_exists("i", $placed)) {
				$valid = false;
			}
		}
		$this->board = $this->board;
		$this->wordList = array();
		$this->activePlayer = $this->player1 = $this->currentTurn = $user->token;
		$this->gameStatus = "pending";

		//Create new entry in table.
		$query = "INSERT INTO games (player1, current_turn, board, words, game_status) VALUES ('" . $this->player1 . "', '" . $this->currentTurn . "', '" . mysql_real_escape_string(json_encode($this->board)) . "', '" . mysql_real_escape_string(json_encode($this->words)) . "', 'pending');";
		if(!mysql_query($query, $db)) {
			return false;
		}

		//Get game ID.
		$query = "SELECT * FROM games WHERE player1='" . $this->player1 . "' ORDER BY id DESC";
		if(!$result = mysql_query($query, $db)) {
			return false;
		}
		$row = mysql_fetch_array($result);
		$this->id = $row['id'];
		return true;
	}

	public function join() {
		/*Allows a player to join an existing game.*/

		if(!$this->save()) {
			return false;
		}
		return true;
	}

	public function playWord($wordJSON) {
		/*Plays a word*/

		$decoded = json_decode($wordJSON);

		if($this->activePlayer == $this->currentTurn) {
			$playerValue = $this->activePlayer == $this->player1 ? 1 : 2;
			$opponentValue = $playerValue == 1 ? 2 : 1;
			foreach($decoded as $point) {
				if($this->board[$point[0]][$point[1]]['owner'] == $opponentValue) {
					//Give the player the letter if it's not protected.
					if(!$this->isProtectedLetter($point)) {
						$this->board[$point[0]][$point[1]]['owner'] = $playerValue;
					}
				} else if($this->board[$point[0]][$point[1]]['owner'] == $playerValue) {
					//Do nothing, as the player already owns the letter.
				} else {
					//Give all the unowned letters to the player.
					$this->board[$point[0]][$point[1]]['owner'] = $playerValue;
				}
			}
		}
		array_push($this->wordList, $this->deserializeWord($wordJSON));
		$this->currentTurn = $this->opponent;

		//Check for game-ending conditions.

		if(!$this->save()) {
			return false;
		}
		return true;
	}

	public function getGameStatus() {
		/*Gets an array of the game status based on the permissions of the active user.*/

		$returnData = array();
		$returnData['game_id'] = $this->id;
		$returnData['board'] = $this->board;
		if($this->activePlayer == $this->currentTurn) {
			$returnData['current_turn'] = true;
		} else {
			$returnData['current_turn'] = false;
		}
		$returnData['word_list'] = $this->wordList;
		$returnData['game_status'] = $this->gameStatus;
		return $returnData;
	}

	public function skip() {
		/*Allows a player to pass their turn.*/

		$this->currentTurn = $this->opponent;
		if(!$this->save()) {
			return false;
		}
		return true;
	}

	public function resign() {
		/*Allows a player to forfeit a game.*/

		return true;
	}

	/*Private Functions*/
	private function load() {
		/*Loads an existing game from an id.*/

		global $db;

		if(!$db) {
			return false;
		}
		return true;
	}

	private function save() {
		/*Updates the database with any new data for the game.*/

		global $db;

		if(!$db) {
			return false;
		}
		return true;
	}

	private function deserializeWord($wordJSON) {
		/*Represents a word by a JSON array of XY coordinates.*/

		$decoded = json_decode($wordJSON);
		$word = "";
		foreach($decoded as $point) {
			$word .= $this->board[$point[0]][$point[1]]['letter'];
		}
		return $word;
	}

	private function checkWord($word) {
		/*Checks a word against all previously played words to make sure it does not match exactly or is a literal prifix of any.*/

		$wordLength = strlen($word);
		foreach($this->wordList as $playedWord) {
			if(substr($playedWord, 0, $wordLength) == $word) {
				return false;
			}
		}
		return true;
	}

	private function isProtectedLetter($point) {
		/*Checks to see if a letter is surrounded by letters that are owned by the same player.*/

		if(array_key_exists($point[0] + 1, $this->board)) {
			if(!$this->board[$point[0] + 1][$point[1]]['owner'] == $this->board[$point[0]][$point[1]]['owner']) {
				return false;
			}
		}
		if(array_key_exists($point[0] - 1, $this->board)) {
			if(!$this->board[$point[0] - 1][$point[1]]['owner'] == $this->board[$point[0]][$point[1]]['owner']) {
				return false;
			}
		}
		if(array_key_exists($point[1] + 1, $this->board[$point[0]])) {
			if(!$this->board[$point[0]][$point[1] + 1]['owner'] == $this->board[$point[0]][$point[1]]['owner']) {
				return false;
			}
		}
		if(array_key_exists($point[1] - 1, $this->board[$point[0]])) {
			if(!$this->board[$point[0]][$point[1] - 1]['owner'] == $this->board[$point[0]][$point[1]]['owner']) {
				return false;
			}
		}
		return true;
	}
}

class NotFound extends Exception {
}
?>
