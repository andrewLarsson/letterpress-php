<?php
function returnJSON($JSON) {
	header('Content-type: application/json');
	echo(json_encode($JSON));
}
?>
