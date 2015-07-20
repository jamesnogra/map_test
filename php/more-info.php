<!DOCTYPE html>
<html>

	<head>
		<meta charset="utf-8">
		<title>Restaurants in Cebu</title>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
		<link rel="stylesheet" href="../css/bootstrap.min.css">
		<script src="../js/jquery.js"></script>
		<script src="../js/bootstrap.min.js"></script>
		<script type="text/javascript" src="https://www.google.com/jsapi"></script>
		<style>
			#main-container {
				width: 800px;
				margin: 0 auto;
			}
		</style>
	</head>
	
	
	<body>
	
		<div id="main-container">
			<h2 align="center">Food Specialties in Cebu</h2>
			<div align="center"><button onClick="window.location='../';" type="button" class="btn btn-success" id="show-marker-button">Back to Home</button></div>
			<div id="chart_div_2"></div>
			<div id="chart_div_1"></div>
		</div>
	
	</body>
	
	
</html>



<?php

	require_once("_db_open.php");
	
	//get unique specialties from all stores with records
	$sql = "SELECT specialty FROM places";
	$result = $conn->query($sql) or die($sql."\n".$conn->error);
	if ($result->num_rows > 0) {
		$unique_specialty = array();
		while($row = $result->fetch_assoc()) {
			$temp_array = explode("|", $row["specialty"]);
			foreach ($temp_array as $specialty_item) {
				if (!in_array($specialty_item, $unique_specialty)) {
					$unique_specialty[] = $specialty_item;
				}
			}
		}
		$new_array = array();
		$temp_array = array();
		//iterate through the unique specialties and count how many stores have it
		foreach ($unique_specialty as $specialty_item) {
			$sql = "SELECT specialty FROM places WHERE specialty LIKE '%".$specialty_item."%'";
			$result = $conn->query($sql) or die($sql."\n".$conn->error);
			$num_restaurants = mysqli_num_rows($result);
			$temp_array = array("name"=>$specialty_item, "count"=>$num_restaurants);
			$new_array[] = $temp_array;
			//exit($specialty_item . "<br />" . var_dump($rowcount) . "<br />" . $sql);
		}
		//var_dump($new_array);
		//format for google graphs
		//only display 10
		$count = 0;
		$limit = 10;
		$temp_str_1 = "";
		usort($new_array, 'sortByOrder');
		$new_array = array_reverse($new_array);
		foreach ($new_array as $item) {
			if ($count>=$limit) {
				break;
			}
			$count++;
			$temp_str_1 .= '["' . $item["name"] . '", ' . $item["count"] . ', "#B2E0B2"], ';
		}
		$temp_str_1 = trim($temp_str_1, ", ");
	}
	
	//require_once("_db_close.php");
	
	
	function sortByOrder($a, $b) {
		return $a['count'] - $b['count'];
	}
	
?>

<?php

	//require_once("_db_open.php");
	
	//get unique specialties from all stores with records
	$sql = "SELECT name, visits FROM places ORDER BY visits DESC LIMIT 0,10";
	$result = $conn->query($sql) or die($sql."\n".$conn->error);
	if ($result->num_rows > 0) {
		$temp_str_2 = "";
		while($row = $result->fetch_assoc()) {
			$temp_str_2 .= '["' . $row["name"] . '", ' . $row["visits"] . ', "#B2E0B2"], ';
			//echo $row["name"] . " - " . $row["visits"] . "<br />";
		}
		$temp_str_2 = trim($temp_str_2, ", ");
	}
	
	require_once("_db_close.php");
	
?>

<script type="text/javascript">

	google.load("visualization", "1", {packages:["corechart"]});
	google.setOnLoadCallback(drawChart);
	function drawChart() {
		var data = google.visualization.arrayToDataTable([
			["Specialty", "Number of Restaurants", { role: "style" } ],
			<?php echo $temp_str_1; ?>
			/*["Copper", 8.94, "#B2E0B2"],
			["Silver", 10.49, "#B2E0B2"],
			["Gold", 19.30, "#B2E0B2"],
			["Platinum", 21.45, "color: #B2E0B2"]*/
		]);

		var view = new google.visualization.DataView(data);
		view.setColumns([0, 1,
			{ 	calc: "stringify",
				sourceColumn: 1,
				type: "string",
				role: "annotation" },
			2
		]);

		var options = {
			title: "Number of Restaurants with these Specialties",
			width: 800,
			height: 500,
			bar: {groupWidth: "95%"},
			legend: { position: "none" },
		};
		var chart = new google.visualization.ColumnChart(document.getElementById("chart_div_1"));
		chart.draw(view, options);
	}
	
</script>

<script type="text/javascript">

	google.load("visualization", "1", {packages:["corechart"]});
	google.setOnLoadCallback(drawChart);
	function drawChart() {
		var data = google.visualization.arrayToDataTable([
			["Specialty", "Number of Restaurants", { role: "style" } ],
			<?php echo $temp_str_2; ?>
			/*["Copper", 8.94, "#B2E0B2"],
			["Silver", 10.49, "#B2E0B2"],
			["Gold", 19.30, "#B2E0B2"],
			["Platinum", 21.45, "color: #B2E0B2"]*/
		]);

		var view = new google.visualization.DataView(data);
		view.setColumns([0, 1,
			{ 	calc: "stringify",
				sourceColumn: 1,
				type: "string",
				role: "annotation" },
			2
		]);

		var options = {
			title: "Restaurants with their Number of Visits",
			width: 800,
			height: 500,
			bar: {groupWidth: "95%"},
			legend: { position: "none" },
		};
		var chart = new google.visualization.ColumnChart(document.getElementById("chart_div_2"));
		chart.draw(view, options);
	}
	
</script>