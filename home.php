<?php
session_start();

if(!isset($_SESSION['user_session']))
{
	header("Location: index.php");
}

include_once 'dbconfig.php';

$stmt = $db_con->prepare("SELECT * FROM tbl_users WHERE user_id=:uid");
$stmt->execute(array(":uid"=>$_SESSION['user_session']));
$row=$stmt->fetch(PDO::FETCH_ASSOC);

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

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="src/jquery.table2excel.js"></script>

<script type="text/javascript" src="tablesorter-master/jquery.tablesorter.js"></script> 

<!--Created by: Kevin Mocorro
	FAB4-->
<style>
.button {
  display: inline-block;
  border-radius: 4px;
  background-color: #28a745;
  border: none;
  color: #FFFFFF;
  text-align: center;
  font-size: 14px;
  padding: 10px;
  width: 180px;
  transition: all 0.5s;
  cursor: pointer;
  margin: 5px;
  float: right;
}

.button span {
  cursor: pointer;
  display: inline-block;
  position: relative;
  transition: 0.5s;
}

.button span:after {
  content: '\00bb';
  position: absolute;
  opacity: 0;
  top: 0;
  right: -20px;
  transition: 0.5s;
}

.button:hover span {
  padding-right: 25px;
}

.button:hover span:after {
  opacity: 1;
  right: 0;
}
</style>
</head>
<body>

<div class="adjust" style="padding-top:60px;"></div>
	
<?php
// CONNECT TO MySQL via PDO weheheh
$serverName = "";
$userName = "";
$password = "";
$dbname = "";

//start
if(isset($_POST['var1'])){
 $var1 = $_POST['var1'];

 if($var1 == ""){
	echo '<div class="container">';
	echo '<div class="alert alert-success">';
	echo '<button class="close" data-dismiss="alert">&times;</button>';
	echo "No results for: ".$var1;
	echo '</div>';
 } else if(strlen($var1) <= 14) {
	echo '<div class="container">';
	echo '<div class="alert alert-success">';
	echo '<button class="close" data-dismiss="alert">&times;</button>';
	echo "No results for: ".$var1;
	echo '</div>';
 }

 else{
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
		
		$header_query = "SELECT A.process_id, A.eq_name, A.created_date AS tIn, A.current_qty AS qtyIn, B.created_date AS tOut, B.current_qty AS qtyOut, A.created_by,
		TRUNCATE(((A.current_qty-B.current_qty)/A.current_qty)*100,2) AS yLoss,
		TIMEDIFF(B.created_date, A.created_date) AS processTime,
    	TIMEDIFF(if(Y.created_date IS NULL,TIMEDIFF(X.created_date,X.created_date), X.created_date), 
		if(Y.created_date IS NULL,TIMEDIFF(X.created_date,X.created_date), Y.created_date)) AS cycleTime,
    	TIMEDIFF(B.created_date,
		IF((SELECT A.created_date 
			FROM MES_LOT_TRACKING A 
			JOIN MES_LOTS B 
			ON A.lot_id = B.lot_id
			WHERE B.lot_name = :search
			AND A.status_id = 'STAT_LOT_IP'
			AND A.process_id = 'DAMAGE'
		) > B.created_date, B.created_date,
        (SELECT A.created_date 
			FROM MES_LOT_TRACKING A 
			JOIN MES_LOTS B 
			ON A.lot_id = B.lot_id
			WHERE B.lot_name = :search 
			AND A.status_id = 'STAT_LOT_IP'
			AND A.process_id = 'DAMAGE'
		))) AS cumulativeCT 
		 
		FROM ";
		
		$stat_IP = "(SELECT A.process_id, B.eq_name, A.created_date, A.current_qty, A.created_by
			FROM MES_LOT_TRACKING A
            JOIN MES_EQ_INFO B
				ON A.eq_id = B.eq_id
			JOIN MES_LOTS C
				ON A.lot_id = C.lot_id
			WHERE A.status_id = 'STAT_LOT_IP'
				AND C.lot_name= :search
			ORDER BY A.created_date) A";
		
		$stat_CP = "(SELECT A.process_id, A.created_date, B.created_date as bdate, A.current_qty
			FROM MES_LOT_TRACKING A
            JOIN MES_LOTS B
				ON A.lot_id = B.lot_id
			WHERE A.status_id = 'STAT_LOT_CP'
				AND B.lot_name = :search
            ORDER BY A.created_date) B";

		$stat_CP_X = "(SELECT  @x := @x + 1 as counter,  A.process_id, A.created_date
						FROM
						(SELECT A.process_id, A.created_date 
							FROM MES_LOT_TRACKING A
							JOIN MES_EQ_INFO B
							ON A.eq_id = B.eq_id
							JOIN MES_LOTS C
							ON A.lot_id = C.lot_id,
							(SELECT @x := 0)init
							WHERE C.lot_name = :search 
								AND A.status_id = 'STAT_LOT_CP'
							ORDER BY A.created_date) A) X";
		
		$stat_CP_Y = "(SELECT  @y := @y + 1 as counter,  A.process_id, A.created_date
						FROM
							((SELECT A.created_date, A.process_id
									FROM MES_LOT_TRACKING A
									JOIN MES_EQ_INFO B
									ON A.eq_id = B.eq_id
									JOIN MES_LOTS C
									ON A.lot_id = C.lot_id,
									(SELECT @y := 0)init
									WHERE C.lot_name = :search 
										AND A.status_id = 'STAT_LOT_CP'
									ORDER BY A.created_date)
							UNION
								(SELECT NULL as created_date, NULL AS process_id
									FROM MES_LOT_TRACKING A
									JOIN MES_EQ_INFO B
									ON A.eq_id = B.eq_id
									JOIN MES_LOTS C
									ON A.lot_id = C.lot_id,
									(SELECT @y := 0)init
									WHERE C.lot_name = :search 
										AND A.status_id = 'STAT_LOT_CP'
									ORDER BY A.created_date
								 )) A
								 ORDER BY A.created_date) Y";

		
		$main_query = "$header_query $stat_IP JOIN $stat_CP ON A.process_id = B.process_id LEFT JOIN $stat_CP_X LEFT JOIN $stat_CP_Y USING (counter) ON X.process_id = A.process_id";


		$stmt = $conn->prepare($main_query); 
		$stmt->bindValue(':search', $var1 , PDO::PARAM_INT);
		$stmt->execute();
		
			//table
				

				echo '<div id="toPrint" class="main_container" style="float:center; padding-left:50px; padding-right:50px;padding-top:5px">';
				echo '<div class="sub_container" style="float:center;">';
							echo "<button id='button_excel' class='button'>
									<span class='fa fa-save'></span>&nbsp;&nbsp;Download Excel</button>";
							echo "<button id='button_print' class='button' style='border-color:#ddd; background-color:#3b5998; color:white; width: 100px'>
									<span class='fa fa-print'></span>&nbsp;&nbsp;Print</button>";
				
				echo '</div>';

				echo "<table class ='table2excel tablesorter' id='mainTable'>";
				echo "	
				<thead>
				<tr>
				<th>Process &nbsp; <span class='fa fa-sort'></span>
				<th>Tool Name &nbsp; <span class='fa fa-sort'></span>
				<th>Tracked In &nbsp; <span class='fa fa-sort'></span>
				<th>Qty  &nbsp; <span class='fa fa-sort'></span>
				<th>Tracked Out  &nbsp; <span class='fa fa-sort'></span>
				<th>Qty  &nbsp; <span class='fa fa-sort'></span>
				<th>User  &nbsp; <span class='fa fa-sort'></span>
				<th>% YL  &nbsp; <span class='fa fa-sort'></span>
				<th>Process Time  &nbsp; <span class='fa fa-sort'></span>
				<th>Cycle Time  &nbsp; <span class='fa fa-sort'></span>
				<th>Cumulative CT &nbsp; <span class='fa fa-sort'></span>
				</thead>
				<tbody>";
						class TableRows extends RecursiveIteratorIterator { 
							function __construct($it) { 
								parent::__construct($it, self::LEAVES_ONLY); 
							}
							function current() {
								//return "<td>" . parent::current(). "</td>";
								return "<td>" . parent::current();
							}
							function beginChildren() { 
								echo "</tr>";
							} 	
							function endChildren() { 
								//echo "<tr>";
							} 
						
						} 
						// !important- set the resulting array to associative 
						$result= $stmt->setfetchmode(PDO::FETCH_ASSOC);

						// fetch all rows on the stmt object
						$main = (new TableRows(new RecursiveArrayIterator($stmt->fetchAll())));
							foreach($main as $key=>$rows) { 
								echo $rows;
							}
						$conn = null;
				echo "</table>";
				echo '</div>';
				
 }
}
?>


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
		  	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
				
				<form name="frmSearch" method="post" action="home.php">
				  <table name="search">	
					<tr>

					  <th name="searchBar" style="font-size:14px; color:black">	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <i class="fa fa-th-large" aria-hidden="true" style="font-size:18px; color:Grey" >&nbsp;</i>&nbsp;&nbsp;Extract-a-lot &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;
					  <input name="var1" type="text" id="var1" value ="<?php echo isset($_POST['var1']) ? $_POST['var1'] : ''; ?>" placeholder="Search Lot name"><input name="sicon" type="submit" style="font-size:15px; color:#ddd" class="search" value="&#xf002; "/>
					</tr>
				  </table>
				</form>
          </ul>

          <ul class="nav navbar-nav navbar-right" >
		  	
		  	<li><a class="btn" data-toggle="modal" href="#myModal" ><span class="fa fa-question"></span></a></li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" style="background-color:#f9f9f9; color: black;">&nbsp;&nbsp;&nbsp;
			  <span class="fa fa-user-circle-o"></span>&nbsp;&nbsp;<?php echo $row['user_name']; ?>&nbsp;<span class="caret"></span></a>
              <ul class="dropdown-menu">
				<li><a href="mailto:kevin.mocorro@sunpowercorp.com?Subject=Feedback%20(Please use Outlook when sending an Email)" target="_top">&nbsp;&nbsp;Send Feedback</a></li> 
                <li><a href="logout.php">&nbsp;&nbsp;Logout</a></li>
				<li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;v2.0.0</li>
              </ul>
            </li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
</nav>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                 <h4 class="modal-title" id="myModalLabel">How to?</h4>

            </div>
            <div class="modal-body">Type the Lot number on the Search bar and Press Enter.</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

</div>
</div>				
</div>


		<script>
		$(document).ready(function() 
			{ 
				$("#mainTable").tablesorter(); 
			} 
		); 
		</script>

		<script>
		file='<?php echo json_encode($var1); ?>';

			$("#button_excel").one('click', function() {
				$(".table2excel").table2excel({
					name: "Excel Document Name",
					filename: file,
					fileext: ".xls",
					exclude_img: true,
					exclude_links: true,
					exclude_inputs: true
				});
			});
			
		</script>

		<script type="text/javascript">
			$("tr").not(':first').hover(
			function () {
				$(this).css("background","#f6f8fa");
			},
			function () {
				$(this).css("background","");
			}
			);
		</script>

		<script>
			$("#button_print").on('click', function(){
				$('#mainTable').printThis({
					importCSS: false,            // import page CSS
    				importStyle: false
				});

			});
		</script>

<script src="bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="jquery-1.11.3-jquery.min.js"></script>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="src/jquery.table2excel.js"></script>

<script type="text/javascript" src="tablesorter-master/jquery.tablesorter.js"></script>
<script type="text/javascript" src="printThis-master/printThis.js"></script> 



</body>
</html>