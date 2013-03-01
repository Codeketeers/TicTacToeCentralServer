<?php
include "common.php";

if (isset($_REQUEST['x']) && isset($_REQUEST['y']) && isset($_REQUEST['gameID']) && isset($_REQUEST['from'])) {
	$sql = "SELECT * FROM game WHERE GameID='{$_REQUEST['gameID']}' AND (PlayerA='{$_REQUEST['from']}' OR PlayerB='{$_REQUEST['from']}')";
	$result = mysql_query($sql) or die(mysql_error() . "<br />" . $sql);
	
	if ($row = mysql_fetch_assoc($result)) {
		p("Game found");
		
		if ($row['Turn'] == $_REQUEST['from']) {
			$sql = "INSERT INTO move SET GameID='{$_REQUEST['gameID']}', Player='{$_REQUEST['from']}', X='{$_REQUEST['x']}', Y='{$_REQUEST['y']}'";
			$result = mysql_query($sql) or die(mysql_error());
			
			/*$sql = "COMMIT";
			$result = mysql_query($sql) or die(mysql_error());*/
			
			waitForPlay($_REQUEST['gameID'], 30, $_REQUEST['from']);
		} else {
			$return['error'] = "Not " . $_REQUEST['from'] . "'s turn";
			echo json_encode($return);
			exit;
		}
	} else {
		p("Game not found");
		$return['error'] = "Game {$_REQUEST['gameID']} not found";
		echo json_encode($return);
		exit;
	}
} else {
	$return['error'] = "Not all required values set, needs 'x', 'y', 'gameID', and 'from'";
	echo json_encode($return);
	exit;
}

echo json_encode($return);
?>
