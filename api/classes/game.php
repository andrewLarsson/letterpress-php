<?php
include_once "token.php";

class Game {
	//A game will be represented by mysql: gameID, player1 (token), player2 (token), currentTurn (token), board (json), playedWords (json), gameStatus(pendingPublic, pendingPrivate, inplay, finished (token of winner)), skipCount
	
	public $game;
	
	public $gameBoard;
	public $wordList;
	
	private $player1;
	private $player2;
	private $gameStatus;
	
	public $activePlayer; //represented by an auth token
	public $opponent; //represented by an auth token
	
	public $currentTurn; //represented by an auth token
	
	function __construct($game = NULL) {
		if(isset($game)) {
			$this->game = $game;
		}
		return true;
	}

	function create($token) {
		//creates a new empty game state
		
		$letterBank = "abcdefghijklmnopqrstuvwxyz";
		$gameBoard = array();
		$valid = false;

		while(!$valid) {
			$placed["i"] = false;
			$placed["q"] = false;
			for($i = 0; $i < 5; $i++) {
				for($j = 0; $j < 5; $j++) {
					$gameBoard[$i][$j]["letter"] = substr($letterBank, rand(0, 25), 1);
					$gameBoard[$i][$j]["owner"] = 0; // 0 = none; 1 = player1; 2 = player2
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
		//Loads an existing game from mysql based off a user token
	}
	
	function save() {
		//updates the database with any added data
	}
	
	function resign() {
		
	}
	
	function skip() {
		
	}
	
	function deserializeWord($wordJSON) {
		//Words are represented by a json array of points
		$decoded = json_decode($wordJSON);
		$word = "";
		
		foreach ($decoded as $point) {
			$word .= $this->gameBoard[$point[0]][$point[1]]['letter'];
		}
		
		return $word;
	}
	
	function checkWord($word) {
		//Checks a word agaisnt all previously played words
		$wordLength = strlen($word);
		
		foreach ($this->wordList as $playedWord) {
			if (substr($playedWord, 0, $wordLength) == $word) {
				//either matches exactly, or word is a literal prefix of an allready played word
				return false;
			}
		}
		
		//word is unique
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
					//check to see if letter is protected, add it to protectedLetters
				}
			}
			
			foreach ($decoded as $point) {
				if ($this->gameBoard[$point[0]][$point[1]]['owner'] == $opponentValue) {
					//check to see if letter is in protectedLetters, if not, switch to playerValue
				}
			}
			
			//give all white squares to the player
			foreach ($decoded as $point) {
				if ($this->gameBoard[$point[0]][$point[1]]['owner'] == 0) {
					$this->gameBoard[$point[0]][$point[1]]['owner'] = $playerValue;
				}
			}
		}
		
		array_push($this->wordList, deserializeWord($wordJSON));
		$this->currentTurn = $this->opponent;
		//check for ending conditions
	}
}
?>
