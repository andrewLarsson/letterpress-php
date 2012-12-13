<?php
class Game {
	/*Contains all the variables and methods required to construct a game and take action on it.*/

	//A game is represented by MySQL: id, player1 (token), player2 (token), current_turn (token), board (json), played_words (json), game_status(pending_public, pending_private, inplay, finished (winner)), skip_count
	//All players are represented by an authentication token.

	/*Public Properties*/
	public $game;
	public $gameBoard;
	public $wordList;
	public $activePlayer;
	public $opponent;
	public $currentTurn;

	/*Private Variables*/
	private $player1;
	private $player2;
	private $gameStatus;

	function __construct($game = NULL) {
		if(isset($game)) {
			$this->game = $game;
		}
		return true;
	}

	function create($token) {
		/*Creates a fresh, empty game.*/

		$letterBank = "abcdefghijklmnopqrstuvwxyz";
		$gameBoard = array();
		$valid = false;
		while(!$valid) {
			$placed["i"] = false;
			$placed["q"] = false;
			for($i = 0; $i < 5; $i++) {
				for($j = 0; $j < 5; $j++) {
					$gameBoard[$i][$j]["letter"] = substr($letterBank, rand(0, 25), 1);

					//The owner is marked with an auth token (or 0 for no owner).
					$gameBoard[$i][$j]["owner"] = 0;
					if($gameBoard[$i][$j]["letter"] == "q") {
						$placed["q"] = true;
					}
					if($gameBoard[$i][$j]["letter"] == "i") {
						$placed["i"] = true;
					}
				}
			}
			$valid = true;
			if($placed["q"] && !$placed["i"]) {
				$valid = false;
			}
		}
		$this->gameBoard = $gameBoard;
		$this->wordList = array();
		$this->activePlayer = $this->player1 = $this->currentTurn = $token;
	}

	function load($token) {
		/*Loads an existing game from a token.*/

	}

	function save() {
		/*Updates the database with any new data for the game.*/

	}

	function resign() {
		/*Allows a player to forfeit a game.*/

	}

	function skip() {
		/*Allows a player to pass their turn.*/

	}

	function deserializeWord($wordJSON) {
		/*Represents a word by a JSON array of XY coordinates.*/

		$decoded = json_decode($wordJSON);
		$word = "";
		foreach ($decoded as $point) {
			$word .= $this->gameBoard[$point[0]][$point[1]]['letter'];
		}
		return $word;
	}

	function checkWord($word) {
		/*Checks a word against all previously played words.*/

		$wordLength = strlen($word);
		foreach ($this->wordList as $playedWord) {
			if (substr($playedWord, 0, $wordLength) == $word) {
				//The word either matches exactly or is a literal prefix of an previously played word.
				return false;
			}
		}

		//The word is unique.
		return true;
	}

	function playWord($wordJSON) {
		$decoded = json_decode($wordJSON);

		if ($this->activePlayer == $this->currentTurn) {
			$playerValue = $this->activePlayer == $this->player1 ? 1 : 2;
			$opponentValue = $playerValue == 1 ? 2 : 1;
			$protectedLetters = array();
			foreach ($decoded as $point) {
				if ($this->gameBoard[$point[0]][$point[1]]['owner'] == $opponentValue) {
					//Check to see if the letter is protected, and add it to protectedLetters.
				}
			}
			foreach ($decoded as $point) {
				if ($this->gameBoard[$point[0]][$point[1]]['owner'] == $opponentValue) {
					//Check to see if letter is in protectedLetters, and if not, switch to playerValue.
				}
			}

			//Give all of the unowned squares to the player who captured them.
			foreach ($decoded as $point) {
				if ($this->gameBoard[$point[0]][$point[1]]['owner'] == 0) {
					$this->gameBoard[$point[0]][$point[1]]['owner'] = $playerValue;
				}
			}
		}
		array_push($this->wordList, deserializeWord($wordJSON));
		$this->currentTurn = $this->opponent;
		//Check for ending conditions.
	}
}
?>
