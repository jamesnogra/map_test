<?php

	$servername = "localhost";
	$username = "map_test";
	$password = "map_test";
	$dbname = "map_test";

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
?> 