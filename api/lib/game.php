<?php
class Game {
	public $game;

	function __construct($game = NULL) {
		if(isset($game)) {
			$this->game = $game;
		}
		return true;
	}

	function create() {
		$letterBank = "abcdefghijklmnopqrstuvwxyz";
		$gameBoard = array();
		$valid = false;

		while(!$valid) {
			$placed["i"] = false;
			$placed["q"] = false;
			for($i = 0; $i < 5; $i++) {
				for($j = 0; $j < 5; $j++) {
					$gameBoard[$i][$j]["letter"] = substr($letterBank, rand(0, 25), 1);
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
		return $gameBoard;
	}
}
?>
