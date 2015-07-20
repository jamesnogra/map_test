<?php

	require_once("_db_open.php");
	require_once("_other_scripts.php");
	
	$id = $_POST["id"];
	
	$sql = "SELECT visits FROM places WHERE id='".$id."'";
	$result = $conn->query($sql) or die($sql."\n".$conn->error);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			echo $row["visits"];
		}
	} else { //we have to add this new record manually
		echo 0;
	}
	
	require_once("_db_close.php");	
	
?>