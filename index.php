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

p(print_r($_REQUEST, true));

if (isset($_REQUEST['challenge'])) {
	$sql = "SELECT * FROM challenges WHERE Challengee='{$_REQUEST['from']}' AND Challenger='{$_REQUEST['challenge']}'";
	$result = mysql_query($sql) or die(mysql_error() . "<br />" . $sql);
	
	if ($row = mysql_fetch_assoc($result)) {
		p("Challenge found");
	
		$sql = "INSERT INTO game SET PlayerA='{$row['Challenger']}', PlayerB='{$row['Challengee']}', GameID='{$row['ID']}'";
		$result = mysql_query($sql) or die(mysql_error() . "<br />" . $sql);
		
		//clear the challenge
		$sql = "DELETE FROM challenges WHERE ID='{$row['ID']}'";
		$result = mysql_query($sql) or die(mysql_error() . "<br />" . $sql);
		
		$return["Message"] = "You have accepted " . $row['Challenger'] . " challenge";
		$return["GameID"] = $row['ID'];
		
		echo json_encode($return);
		exit;
	} else {
		p("Challenge not found");
	
		//no challenge found, creating challenge
		$sql = "INSERT INTO challenges SET Challenger='{$_REQUEST['from']}', Challengee='{$_REQUEST['challenge']}'";
		$result = mysql_query($sql) or die(mysql_error() . "<br />" . $sql);
		
		$sql = "SELECT ID FROM challenges WHERE Challenger='{$_REQUEST['from']}' AND Challengee='{$_REQUEST['challenge']}'";
		$result = mysql_query($sql) or die(mysql_error() . "<br />" . $sql);
		
		$row = mysql_fetch_assoc($result);
		
		//wait for pairing
		$notFound = true;
		
		$sql = "SELECT * FROM challenges WHERE ID='{$row['ID']}'";
		$i = 0;
		
		$return['GameID'] = $row['ID'];
		
		while($notFound) {
			$i++;
			
			if ($i < 30) {
				sleep(1);
				
				$result = mysql_query($sql) or die(mysql_error() . "<br />" . $sql);
				if (mysql_num_rows($result) == 0) {
					$notFound = true;
					p("challenge finally found");
					break;
				}
			} else {
				$return['GameID'] = -1;
				$return['Message'] = "Pairing failed";
				
				//clear the challenge
				$sql = "DELETE FROM challenges WHERE ID='{$row['ID']}'";
				$result = mysql_query($sql) or die(mysql_error() . "<br />" . $sql);
				
				echo json_encode($return);
				exit;
			}
		}
		
		$return['Message'] = "Your challenge has been accepted";
		echo json_encode($return);
	}
}
?>