<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Add Specialty for <?php echo $_GET["name"]; ?></title>
		<link rel="stylesheet" href="../css/bootstrap.min.css">
		<style>
			#main-div {
				width: 50%;
				margin: 0 auto;
			}
			.center-text {
				text-align: center;
			}
			.my-text-input {
				width: 250px;
			}
		</style>
	</head>
	
	<body>
	
		<div id="main-div">
			<h2 class="center-text">Add Specialty for <strong><?php echo $_GET["name"]; ?></strong></h2>
			<form class="center-text" action="add_specialty_db.php?id=<?php echo $_GET["id"]; ?>" method="POST">
				<br />
				<label for="specialty">New Specialty: </label>
				<input name="specialty" id="specialty" type="text" class="my-text-input" />
				<br /><br />
				<button type="button" class="btn btn-info dropdown-toggle">Add</button>
			</form>
		</div>
		
	</body>
	
</html>