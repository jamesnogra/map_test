<?php

	require_once("_db_open.php");
	require_once("_other_scripts.php");
	
	$id = $_POST["id"];
	
	$sql = "SELECT specialty FROM places WHERE id='".$id."'";
	$result = $conn->query($sql) or die($sql."\n".$conn->error);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			echo $row["specialty"];
		}
	} else { //we have to add this new record manually
		$name = $name = cleanString($_POST["name"]);
		$lat = $_POST["lat"];
		$lng = $_POST["lng"];
		$new_specialty = $default_specialty;
		
		$sql = "INSERT INTO places (`id`, `name`, `lat`, `lng`, `visits`, `specialty`) VALUES ('".$id."', '".$name."', '".$lat."', '".$lng."', 0, '".$new_specialty."')";
		$conn->query($sql) or die($sql."\n".$conn->error);
		
		echo $default_specialty;
	}
	
	require_once("_db_close.php");	
	
?>