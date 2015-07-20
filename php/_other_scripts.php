<?php

	$default_specialty = "Ordinary Food";

	function cleanString($string) {
		$invalid_characters = array("$", "%", "#", "<", ">", "|", "^", "@", ";", "'", "\"", "(", ")", "{", "}", "[", "]");
		return str_replace($invalid_characters, " ", $string);
	}

?>