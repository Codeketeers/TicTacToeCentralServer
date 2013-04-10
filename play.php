<?php
	include 'common.php';
	
	$gameRow;
	
	if (!(is_numeric($_REQUEST['x']) AND $_REQUEST['x'] >= 0 AND $_REQUEST['x'] <= 3)) {
		myReturn("error", "Invalid value for x. the value for x recieved was '{$_REQUEST['x']}'");
	}
	
	if (!(is_numeric($_REQUEST['y']) AND $_REQUEST['y'] >= 0 AND $_REQUEST['y'] <= 3)) {
		myReturn("error", "Invalid value for y. the value for y recieved was '{$_REQUEST['y']}'");
	}
	
	//verify there is a game, they are in the game, and it is their turn
	if (isset($_REQUEST['x']) && isset($_REQUEST['y']) && isset($_REQUEST['gameID']) && isset($_REQUEST['from'])) {
		$sql = "SELECT * FROM game WHERE GameID='{$_REQUEST['gameID']}'";
		$result = mysql_query($sql);
		
		if ($gameRow = mysql_fetch_assoc($result)) {
			p("Game found");
			if ($gameRow['PlayerA'] != $_REQUEST['from'] && $gameRow['PlayerB'] != $_REQUEST['from']) {
				myReturn("error", $_REQUEST['from'] . "not a player in Game '".$_REQUEST['gameID']."'");
			} else {
				if ($gameRow['Turn'] != $_REQUEST['from']) {
					myReturn("error", "not " . $_REQUEST['from']."'s turn");
				}
			}
		} else {
			myReturn("error","Game '".$_REQUEST['gameID']."' not found");
		}
	} else {
		$recieved = "Recieved: ";
		
		foreach(array_keys($_REQUEST) as $key) {
			$recieved .= "'$key', ";
		}
		myReturn("error","Not all required values set, needs 'x', 'y', 'gameID', and 'from'. " . $recieved);
	}

	//check for flags
	if (isset($_REQUEST['flag'])) { //('winning move', 'draw move', 'challenge win', 'challenge move')
		p("flag set");
		if (strcasecmp($_REQUEST['flag'], 'winning move') == 0) {
			$flag = 'winning move';
		} else if (strcasecmp($_REQUEST['flag'], 'draw move') == 0) {
			$flag = 'draw move';
		} else if (strcasecmp($_REQUEST['flag'], 'accept draw') == 0) {
			$flag = 'accept draw';
		} else if (strcasecmp($_REQUEST['flag'], 'challenge win') == 0) {
			$flag = 'challenge win';
		} else if (strcasecmp($_REQUEST['flag'], 'challenge move') == 0) {
			$flag = 'challenge move';
		} else if (strcasecmp($_REQUEST['flag'], 'accept loss') == 0) {
			$flag = 'accept loss';
			$return['newGameID'] =  $_REQUEST['gameID'] + 1;
			
			//make sure they are set
			$_REQUEST['x'] = -1;
			$_REQUEST['y'] = -1;
			
			//find out who goes first
			$sql = "SELECT turn FROM game WHERE GameID='{$return['newGameID']}'";
			$result = mysql_query($sql);
			if ($row = mysql_fetch_assoc($result)) {
				if ($row['turn'] == $_REQUEST['from']) {
					$res = mysql_query("INSERT INTO move SET GameID='{$_REQUEST['gameID']}', Player='{$_REQUEST['from']}', X='{$_REQUEST['x']}', Y='{$_REQUEST['y']}', flag='{$_REQUEST['flag']}'");
					myReturn('yourTurn', 'true');
				} else {
					$res = mysql_query("INSERT INTO move SET GameID='{$_REQUEST['gameID']}', Player='{$_REQUEST['from']}', X='{$_REQUEST['x']}', Y='{$_REQUEST['y']}', flag='{$_REQUEST['flag']}'");
					waitForPlay($return['newGameID'], 30, $_REQUEST['from']);
					echo json_encode($return);
					exit;
				}
			} else {
				//no more games in match
				$res = mysql_query("INSERT INTO move SET GameID='{$_REQUEST['gameID']}', Player='{$_REQUEST['from']}', X='{$_REQUEST['x']}', Y='{$_REQUEST['y']}', flag='{$_REQUEST['flag']}'");
				myReturn('newGameID', '-1');
			}
		} else {
			myReturn("error","invalid flag '" . $_REQUEST['flag'] . "' set");
		}
	}

	$sql = "INSERT INTO move SET GameID='{$_REQUEST['gameID']}', Player='{$_REQUEST['from']}', X='{$_REQUEST['x']}', Y='{$_REQUEST['y']}'";
	
	if (isset($flag)) {
		$sql .= ", flag='".$flag."' ";
		p("flag set and added");
	}
	
	$result = mysql_query($sql) or die(mysql_error());
	
	waitForPlay($_REQUEST['gameID'], 30, $_REQUEST['from']);

	echo json_encode($return);
?>
