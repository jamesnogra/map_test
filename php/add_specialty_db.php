<?php

	require_once("_db_open.php");
	require_once("_other_scripts.php");
	
	$id = $_POST["id"];
	$new_specialty = cleanString($_POST["specialty"]);
	
	$sql = "SELECT id, specialty FROM places WHERE id='".$id."'";
	$result = $conn->query($sql) or die($sql."\n".$conn->error);
	$existing_specialty = "";
	if ($result->num_rows > 0) { //insert if this id exists
		while($row = $result->fetch_assoc()) {
			$existing_specialty = $row["specialty"];
		}
		//append the new specialty to the existing
		$new_specialty = $existing_specialty . "|" . $new_specialty;
		
		$sql = "UPDATE places SET specialty='".$new_specialty ."' WHERE id='".$id."'";
		$conn->query($sql) or die($sql."\n".$conn->error);
		
		echo cleanString($_POST["specialty"]) . " has been added to id " . $id . ".";
	} else { //we have to add this new record manually
		$name = $name = cleanString($_POST["name"]);
		$lat = $_POST["lat"];
		$lng = $_POST["lng"];
		$new_specialty = $default_specialty . "|" . $new_specialty;
		
		$sql = "INSERT INTO places (`id`, `name`, `lat`, `lng`, `visits`, `specialty`) VALUES ('".$id."', '".$name."', '".$lat."', '".$lng."', 0, '".$new_specialty."')";
		$conn->query($sql) or die($sql."\n".$conn->error);
		
		echo cleanString($_POST["specialty"]) . " has been added MANUALY to id " . $id . ".";
	}
	
	require_once("_db_close.php");	

?>