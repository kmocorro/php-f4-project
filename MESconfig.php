<?php


	$serverName = "";
    $userName = "";
    $password = "";
    $dbname = "";
	
	try{
		
		$conn = new PDO("mysql:host=$serverName;dbname=$dbname", $userName, $password);
		// set PDO error to exception
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
	}
	catch(PDOException $e){
			echo $sql. "<br>" . $e->getMessage();
	}	


?>