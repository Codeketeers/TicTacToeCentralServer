<?php
$conn = mysql_connect("localhost", "root", "") or die(mysql_error());
mysql_select_db("t3", $conn) or die(mysql_error());

$return = array();
$debug = false;

if (isset($_REQUEST['debug'])) {
	$debug = $_REQUEST['debug'];
	if ($debug) {
		echo "debug is true: $debug";
	}
}

function p($val) {
	GLOBAL $debug;
	
	if ($debug) {
		echo $val;
	}
}

foreach($_REQUEST as &$val) {
	$val = htmlentities($val, ENT_QUOTES, "UTF-8");
}

p(print_r($_REQUEST, true));

function waitForPlay($gameID, $timeout, $turn) {
	GLOBAL $return;

	$sql = "SELECT * FROM game WHERE GameID='$gameID' AND Turn='$turn' ";

	for ($i = 0; $i < $timeout; $i++) {
		sleep(1);
		
		$result = mysql_query($sql);
		
		if (mysql_num_rows($result) > 0) {
			$sql = "SELECT X,Y FROM move WHERE GameID='$gameID' ORDER BY MoveID DESC"; //get the most recent move
			$result = mysql_query($sql);
			$row = mysql_fetch_assoc($result);
			
			$return['x'] = $row['X'];
			$return['y'] = $row['Y'];
			
			return;
		}
	}
	
	$return['error'] = "Max turn time reached";
	echo json_encode($return);
	exit;
}
?>