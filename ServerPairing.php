<?php
include "common.php";

if (isset($_REQUEST['challenge'])) {
	$sql = "SELECT * FROM challenges WHERE Challengee='{$_REQUEST['from']}' AND Challenger='{$_REQUEST['challenge']}' AND accepted=0";
	$result = mysql_query($sql) or die(mysql_error() . "<br />" . $sql);
	
	if ($row = mysql_fetch_assoc($result)) {
		p("Challenge found");
	
		$sql = "INSERT INTO t3match SET PlayerA='{$row['Challenger']}', PlayerB='{$row['Challengee']}', MatchID='{$row['ID']}'";
		$result = mysql_query($sql) or die(mysql_error() . "<br />" . $sql);
		
		//clear the challenge
		$sql = "UPDATE challenges SET accepted='1' WHERE ID='{$row['ID']}'";
		$result = mysql_query($sql) or die(mysql_error() . "<br />" . $sql);
		
		$return["message"] = "You have accepted " . $row['Challenger'] . " challenge";
		$return['challenger'] = $row['Challenger'];
		$return["gameID"] = $row['ID'] . "1";
		
		waitForPlay($row['ID'] . "1", 30, $_REQUEST['from']);
		
		echo json_encode($return);
		exit;
	} else {
		p("Challenge not found");
	
		//no challenge found, creating challenge
		$sql = "INSERT INTO challenges SET Challenger='{$_REQUEST['from']}', Challengee='{$_REQUEST['challenge']}'";
		$result = mysql_query($sql) or die(mysql_error() . "<br />" . $sql);
		
		$sql = "SELECT ID FROM challenges WHERE Challenger='{$_REQUEST['from']}' AND Challengee='{$_REQUEST['challenge']}' AND Accepted='0' ORDER BY ID DESC";
		$result = mysql_query($sql) or die(mysql_error() . "<br />" . $sql);
		
		$row = mysql_fetch_assoc($result);
		
		$sql = "SELECT * FROM challenges WHERE ID='{$row['ID']}' AND accepted='1'";
		$i = 0;
		
		$return['gameID'] = $row['ID'] . "1";
		
		while(true) {
			$i++;
			
			if ($i < 30) {
				sleep(1);
				
				$result = mysql_query($sql) or die(mysql_error() . "<br />" . $sql);
				if (mysql_num_rows($result) > 0) {
					$return['yourTurn'] = 'true';
					
					p("challenge finally found");
					break;
				}
			} else {
				$return['gameID'] = -1;
				$return['error'] = "Pairing failed";
				
				//clear the challenge
				$sql = "DELETE FROM challenges WHERE ID='{$row['ID']}'";
				$result = mysql_query($sql) or die(mysql_error() . "<br />" . $sql);
				
				echo json_encode($return);
				exit;
			}
		}
		
		$return['message'] = "Your challenge has been accepted";
		echo json_encode($return);
	}
}
?>