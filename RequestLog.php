<?php
include("common.php");

if (isset($_REQUEST['gameID']) AND isset($_REQUEST['from']) {
	$sql = "SELECT Challenger, Challengee FROM game WHERE GameID='{$_REQUEST['gameID']}'";
	$result = mysql_query($sql);
	
	if ($row = mysql_fetch_assoc($sql)) {
		if ($row['Challenger'] == $_REQUEST['from'] && $row['Challengee'] == $_REQUEST['from']) {
			$sql = "SELECT  * FROM Move WHERE GameID='{$_REQUEST['gameID']}' ORDER BY MoveID";
			$result = mysql_query($sql);
			
			while ($row = mysql_fetch_assoc($result)) {
				$return['log'] .= "MoveID={$row['MoveID']}: {$row['Player']} attempted to play at {$row['X']}, {$row['Y']} at {$row['time']}\n";
			}
		} else {
			$return['log'] = "GameID '{$_REQUEST['gameID']}' does not have player '{$_REQUEST['from']}'";
		}
	} else {
		$return['log'] = "GameID '{$_REQUEST['gameID']}' not found";
	}
} else {
	$return['log'] = "Not all required fields set, needs 'gameID' and 'from'";
}

echo json_encode($return);
?>