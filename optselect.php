<?php
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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

<?php
// CONNECT TO MySQL via PDO
$serverName = "";
$userName = "";
$password = "";
$dbname = "";

// tool process selection isset
if(isset($_POST['process'])){
 $process = $_POST["process"];
    try {  
			$conn = new PDO("mysql:host=$serverName;dbname=$dbname", $userName, $password);
			// set PDO error to exception
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			//echo "Results found:<br>";
		}
	catch(PDOException $e)
		{
			echo $sql. "<br>" . $e->getMessage();
		}	

    $eq_name_query = "SELECT DISTINCT(B.eq_name)
                        FROM
                        MES_EQ_PROCESS A 
                        JOIN MES_EQ_INFO B
                        ON A.eq_id = B.eq_id
                        WHERE A.proc_id = :process_select";

    $stmt = $conn->prepare($eq_name_query); 
	$stmt->bindValue(':process_select', $process , PDO::PARAM_INT);
	$stmt->execute();
    $eq_name_array = $stmt->fetchAll();
}

// tool equipment selection isset
if(isset($_POST['equipment'])){
 $equipment = $_POST["equipment"];
    try {  
			$conn = new PDO("mysql:host=$serverName;dbname=$dbname", $userName, $password);
			// set PDO error to exception
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			//echo "Results found:<br>";
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
  
    echo $total_outs['totalOuts'];
}

?>

<div class="selectContainer">
    <form method="post" >
        <div class="form-group">
        
            <select class="form-control" id="SelectProcess" name ="process" onchange="if(this.value != 0) { this.form.submit(); }">>
                <option value="" disabled selected>Select your Process Name</option>
                <option <?php if (isset($process) && $process=="DAMAGE") echo "selected";?>>DAMAGE</option>
                <option <?php if (isset($process) && $process=="POLY") echo "selected";?>>POLY</option>
                <option <?php if (isset($process) && $process=="BSGDEP") echo "selected";?>>BSGDEP</option>
                <option <?php if (isset($process) && $process=="NTM") echo "selected";?>>NTM</option>
                <option <?php if (isset($process) && $process=="NOXE") echo "selected";?>>NOXE</option>
                <option <?php if (isset($process) && $process=="NDEP") echo "selected";?>>NDEP</option>
                <option <?php if (isset($process) && $process=="PTM") echo "selected";?>>PTM</option>
                <option <?php if (isset($process) && $process=="TOXE") echo "selected";?>>TOXE</option>
                <option <?php if (isset($process) && $process=="CLEANTEX") echo "selected";?>>CLEANTEX</option>
                <option <?php if (isset($process) && $process=="PDRIVE") echo "selected";?>>PDRIVE</option>
                <option <?php if (isset($process) && $process=="ARC_BARC") echo "selected";?>>ARC_BARC</option>
                <option <?php if (isset($process) && $process=="PBA") echo "selected";?>>PBA</option>
                <option <?php if (isset($process) && $process=="LCM") echo "selected";?>>LCM</option>
                <option <?php if (isset($process) && $process=="SEED") echo "selected";?>>SEED</option>
                <option <?php if (isset($process) && $process=="FGA") echo "selected";?>>FGA</option>
                <option <?php if (isset($process) && $process=="PLM") echo "selected";?>>PLM</option>
                <option <?php if (isset($process) && $process=="EDG_CTR") echo "selected";?>>EDG_CTR</option>
                <option <?php if (isset($process) && $process=="PLATING") echo "selected";?>>PLATING</option>
                <option <?php if (isset($process) && $process=="ETCHBK") echo "selected";?>>ETCHBK</option>
            </select>
       
    </div>
    </form>


    <form id="optview-form" method="GET" action="optview.php">
    <div class="form-group">
        <select class="form-control" id="SelectEquip" name="equipment">
            <option value="" disabled selected>Select your Tool Name</option>
            <?php foreach ($eq_name_array as $row): ?>
                <option><?=$row["eq_name"]?></option>
            <?php endforeach ?>
        </select>
    </div>
    <div class="optButtonView" >
        <button disabled="disabled" type="submit" class="btn btn-default btn-block" id="btnView" name="btnView" >View</button>
    </div>
    <div class="resetSelect">
        <a disabled="disabled" href="/optselect.php" >Reset Selection</a>
    </div>
    <div class="backSelect">
        <a disabled="disabled" href="./index.php" ><< Home</a>
    </div>
     </form>
</div>



<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" ></a>
        </div>

        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
		
          </ul>
        </div>
    </div>
</nav>


<script>
$(function() {
    $('#SelectEquip').on('input', function() {
        var completed = $('#SelectEquip').filter(function() { return !!this.value; }).length > 0;
        $('button').prop('disabled', false);
        $('#SelectProcess').prop('disabled', true);
        $(':input[id="SelectEquip"]').prop('disabled', false);
    });
});
</script>



</body>
</html>