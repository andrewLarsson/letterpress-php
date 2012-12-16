<?php
class Game {
	/*Contains all the variables and methods required to construct a game and take action on it.*/

	//A game is represented by MySQL: id, player1 (token), player2 (token), current_turn (token), board (json), played_words (json), game_status(pending_public, pending_private, inplay, finished (winner)), skip_count
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

	/*Public Methods*/
	function __construct($id = NULL) {
		if(isset($id)) {
			$this->id = $id;
			$this->load();
		}
		return true;
	}

	function create($token) {
		/*Creates a fresh, empty game.*/

		$letterBank = "abcdefghijklmnopqrstuvwxyz";
		$board = array();
		$valid = false;
		while(!$valid) {
			for($i = 0; $i < 5; $i++) {
				for($j = 0; $j < 5; $j++) {
					$board[$i][$j]["letter"] = substr($letterBank, rand(0, 25), 1);

					//The owner is marked with the player's ID (or a 0 for no owner).
					$board[$i][$j]["owner"] = 0;
					$placed[$board[$i][$j]["letter"]] = true
				}
			}
			$valid = true;

			//This is the logic for making sure a valid game board was produced.
			if(array_key_exists("q", $placed) && !array_key_exists("i", $placed) {
				$valid = false;
			}
		}
		$this->board = $board;
		$this->wordList = array();
		$this->activePlayer = $this->player1 = $this->currentTurn = $token;
		if(!$this->save()) {
			return false;
		}
		return true;
	}

	function join() {
		/*Allows a player to join an existing game.*/

		return true;
	}

	function playWord($wordJSON) {
		$decoded = json_decode($wordJSON);

		if($this->activePlayer == $this->currentTurn) {
			$playerValue = $this->activePlayer == $this->player1 ? 1 : 2;
			$opponentValue = $playerValue == 1 ? 2 : 1;
			$protectedLetters = array();
			foreach($decoded as $point) {
				if($this->board[$point[0]][$point[1]]['owner'] == $opponentValue) {
					//Check to see if the letter is protected, and add it to protectedLetters.
				}
			}
			foreach($decoded as $point) {
				if($this->board[$point[0]][$point[1]]['owner'] == $opponentValue) {
					//Check to see if letter is in protectedLetters, and if not, switch to playerValue.
				}
			}

			//Give all of the unowned squares to the player who captured them.
			foreach($decoded as $point) {
				if($this->board[$point[0]][$point[1]]['owner'] == 0) {
					$this->board[$point[0]][$point[1]]['owner'] = $playerValue;
				}
			}
		}
		array_push($this->wordList, deserializeWord($wordJSON));
		$this->currentTurn = $this->opponent;
		//Check for ending conditions.

		return true;
	}

	function skip() {
		/*Allows a player to pass their turn.*/

		return true;
	}

	function resign() {
		/*Allows a player to forfeit a game.*/

		return true;
	}

	/*Private Functions*/
	private function load($token) {
		/*Loads an existing game from a token.*/

		return true;
	}

	private function save() {
		/*Updates the database with any new data for the game.*/

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
		/*Checks a word against all previously played words.*/

		$wordLength = strlen($word);
		foreach($this->wordList as $playedWord) {
			if(substr($playedWord, 0, $wordLength) == $word) {
				//The word either matches exactly or is a literal prefix of an previously played word.
				return false;
			}
		}

		//The word is unique.
		return true;
	}
}
?>
