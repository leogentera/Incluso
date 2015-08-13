<?php

class dbHelper{
	public $last_id=-1 ;
	public $error;
	public $servername="localhost", $username="root", $password="paso", $database="Incluso";//$servername, $username, $password, $database;
	// 		function __construct($servername="localhost", $username="root", $password="paso", $database="Incluso"){
	// 			$this->servername=servername;
	// 			$this->username = $username;
	// 			$this->password= $password;
	// 			$this->database = $database;
	// 		}
	function connect_to_mysql(){
		// Create connection
		$conn =mysqli_connect($this->servername, $this->username, $this->password, $this->database);

		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		
		
		

		return $conn;
	}

	function query($query){
		$conn=$this->connect_to_mysql();
		if ($conn===false){
			return;
		}
			
		$result = mysqli_query($conn, $query);
		$this->last_id= mysqli_insert_id($conn);
		
		if(!$result){
			$this->error=mysqli_error($conn);
		}
		else{
			$this->error=false;
		}
		mysqli_close($conn);
		return $result;
	}


}


?>