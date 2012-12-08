<?php
include_once "../config/config.php";

class Game {
	public $game_id;
	
	function __construct($game_id) {
		if (isset($game_id)) {
			$this->game_id = $game_id;
		}
	}
	
	function create() {
		$letter_bank = "abcdefghijklmnopqrstuvwxyz";
		$game_board = array();
		$valid = false;
		
		while (!$valid) {
			$has_placed_i = false;
			$has_placed_q = false;
			for ($i = 0; $i < 5; $i++) {
				for ($j = 0; $j < 5; $j++) {
					$game_board[$i][$j]["letter"] = substr($letter_bank, rand(0, 25), 1);
					$game_board[$i][$j]["owner"] = 0;
					if ($game_board[$i][$j]["letter"] == "q") {
						$has_placed_q = true;
					}
					if ($game_board[$i][$j]["letter"] == "i") {
						$has_placed_i = true;
					}
				}
			}
			$valid = true;
			if ($has_placed_q && !$has_placed_i) {
				$valid = false;
			}
		}
		
		return json_encode($game_board);
	}
}

$myGame = new Game();
echo $myGame->create();
?>