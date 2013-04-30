<?php
header('Access-Control-Allow-Origin: *');

$conn = mysql_connect("localhost", "root", "") or die(mysql_error());
mysql_select_db("t3", $conn) or die(mysql_error());

$return = array();
$debug = false;

if (isset($_REQUEST['debug'])) {
	$debug = $_REQUEST['debug'];
	$return['debug'] = "Debug = true";
}

function p($val) {
	GLOBAL $debug;
	GLOBAL $return;
	
	if ($debug) {
		//echo $val;
		$return['debug'] .= ",$val";
	}
}

foreach($_REQUEST as &$val) {
	$val = htmlentities($val, ENT_QUOTES, "UTF-8");
}

p(print_r($_REQUEST, true));

function myReturn($key, $val) {
	GLOBAL $return;

	$return[$key] = $val;
	echo json_encode($return);
	exit;
}

function waitForPlay($gameID, $timeout, $turn, $flag = "") {
	GLOBAL $return;

	$sql = "SELECT * FROM game WHERE GameID='$gameID' AND Turn='$turn' ";

	for ($i = 0; $i < $timeout; $i++) {
		sleep(1);
		
		$result = mysql_query($sql);
		
		if (mysql_num_rows($result) > 0) {
			$sql = "SELECT X,Y,flag,time FROM move WHERE GameID='$gameID' ORDER BY MoveID DESC LIMIT 1"; //get the most recent move
			$result = mysql_query($sql);
			$row = mysql_fetch_assoc($result);
			
			$return['x'] = $row['X'];
			$return['y'] = $row['Y'];
			$return['time'] = $row['time'];
			p("GameID: $gameID");
			if ($flag != "") {
				$return['flag'] = $flag;
			} else if ($row['flag'] != "") {
				$return['flag'] = $row['flag'];
				if ($return['flag'] == "accept loss") {
					p("End of game - next game or end match " . $return['flag']);
				
					$return['newGameID'] = $gameID + 1;
					$sql = "SELECT turn FROM game WHERE GameID='{$return['newGameID']}'";
					$res = mysql_query($sql);
					
					if (mysql_num_rows($res) == 0) {
						myReturn("newGameID", '-1');
					}
					
					waitForPlay($return['newGameID'], 30, $turn, $return['flag']);
					
					echo json_encode($return);
					exit;
				}
			}
			
			return;
		}
	}
	
	$return['error'] = "Max turn time reached";
	echo json_encode($return);
	exit;
}
?>