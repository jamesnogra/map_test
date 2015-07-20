<?php

	require_once("_db_open.php");
	require_once("_other_scripts.php");
	
	$id = $_POST["id"];
	
	$sql = "SELECT visits FROM places WHERE id='".$id."'";
	$result = $conn->query($sql) or die($sql."\n".$conn->error);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$current_visits = $row["visits"];
			$new_visits = $current_visits + 1;
			$sql = "UPDATE places SET visits='".$new_visits ."' WHERE id='".$id."'";
			$conn->query($sql) or die($sql."\n".$conn->error);
			
			echo "Visits updated";
		}
	} else { //we have to add this new record manually
		$name = $name = cleanString($_POST["name"]);
		$lat = $_POST["lat"];
		$lng = $_POST["lng"];
		$new_specialty = $default_specialty;
		
		$sql = "INSERT INTO places (`id`, `name`, `lat`, `lng`, `visits`, `specialty`) VALUES ('".$id."', '".$name."', '".$lat."', '".$lng."', 1, '".$new_specialty."')";
		$conn->query($sql) or die($sql."\n".$conn->error);
		
		echo "This establishment has been addded and then it's visits is updated.\n".$sql;
	}
	
	require_once("_db_close.php");	
	
?>