<?php


error_reporting(E_ALL);
ini_set('display_errors', 1);
putenv("path=C:\Users\humberto.castaneda\AppData\Local\GitHub\PortableGit_c2ba306e536fdf878271f7fe636a147ff37326ad\bin");
$helper=new dbHelper();
chdir ( "E:\proyectos\incluso" );

class tuple{
	public $data1, $data2;
	function __construct($data1, $data2){
		$this->data1=$data1;
		$this->data2=$data2;
	}
}


function getContent($previousVersion, $currentVersion){
	$result = shell_exec("git diff v{$previousVersion} v{$currentVersion} --raw  2>&1");
	$lines = explode("\n", $result);

	$content= array();
	foreach  ($lines as $line){
		if ($line==""){
			continue;
		}
		
		$lastPos=0;
		for ($i=0;$i<4;$i++){
			$lastPos=strpos($line, " ", $lastPos);
			$lastPos++;
		}
		
		$lastSpace=substr($line, $lastPos);
		$lastSpace = explode("\t", $lastSpace);
		//if ($lastSpace[0]!="D")
		//	$content[]=$lastSpace[1];
		$content[]=$lastSpace;
	}

	return $content;
}
function getCurrentVersion(){
	global $helper;
	$query="select major, minor, consecutive from version order by cast( concat(lpad(major, 4, 0), lpad(minor, 4, 0), lpad(consecutive, 8, 0)) as UNSIGNED ) desc limit 1";
	$result = $helper->query($query);
	$version="";

	if (mysqli_num_rows($result) > 0) {
		// output data of each row
		while($row = mysqli_fetch_assoc($result)) {
			$version=  "{$row['major']}.{$row['minor']}.{$row['consecutive']}";
		}
	}
	else{
			$version=  "0.0.0";
		
	}

	return  $version;
}
?>