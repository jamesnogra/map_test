<?php

	require_once("_db_open.php");
	require_once("_other_scripts.php");
	
	$id = $_POST["id"];
	$name = cleanString($_POST["name"]);
	$lat = $_POST["lat"];
	$lng = $_POST["lng"];
	
	//check first if this is duplicate
	$sql = "SELECT id FROM places WHERE id='".$id."'";
	$result = $conn->query($sql) or die($sql."\n".$conn->error);
	if ($result->num_rows == 0) { //insert if no duplicates
		$sql = "INSERT INTO places (`id`, `name`, `lat`, `lng`, `visits`, `specialty`) VALUES ('".$id."', '".$name."', '".$lat."', '".$lng."', 0, '".$default_specialty."')";
		$conn->query($sql) or die($sql."\n".$conn->error);
		echo $name . " has been inserted into the database.";
	}
	
	require_once("_db_close.php");	

?>