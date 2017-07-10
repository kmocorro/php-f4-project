<?php
session_start();
$serverName = "";
$userName = "";
$password = "";
$dbname = "";

$equipment = $_GET["equipment"];

header("Refresh: 30;url='/optview.php?equipment=$equipment'");

// tool equipment selection isset from optselect.php
if(isset($_GET['equipment'])){
//to remain the selected process on the select box
 $equipment = $_GET["equipment"];
    try {  
			$conn = new PDO("mysql:host=$serverName;dbname=$dbname", $userName, $password);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
	catch(PDOException $e)
		{
			echo $sql. "<br>" . $e->getMessage();
		}	

    $tool_outs = "SELECT SUM(A.out_qty) AS totalOuts FROM MES_OUT_DETAILS A JOIN MES_EQ_INFO B ON A.eq_id = B.eq_id WHERE B.eq_name = :equip_select AND	A.date_time >= CONCAT(CURDATE(),' 06:30:00') AND
	A.date_time <= CONCAT(CURDATE(),' 18:29:59')";

    $stmt = $conn->prepare($tool_outs); 
	$stmt->bindValue(':equip_select', $equipment , PDO::PARAM_INT);
	$stmt->execute();
    $total_outs = $stmt->fetch(PDO::FETCH_ASSOC);

   echo $equipment;
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
<title>Fab4 Lot Engineering</title>
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen"> 
<link href="bootstrap/css/bootstrap-theme.min.css" rel="stylesheet" media="screen"> 
<script type="text/javascript" src="jquery-1.11.3-jquery.min.js"></script>
<link href="style.css" rel="stylesheet" media="screen">

<!--Created by: Kevin Mocorro
	FAB4-->
</head>
<body>

<div class="adjust" style="padding-top:60px;"></div>
    <div class="row">
        <div class="col-sm-4">
			<?php 
				echo $total_outs['totalOuts']; 
			?>
		</div>
        <div class="col-sm-4">.COLUMN2</div>
        <div class="col-sm-4">.COLUMN3</div>
    </div>

</body>
</html>