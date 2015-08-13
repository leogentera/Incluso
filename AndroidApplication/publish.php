<?php
include 'dbHelper.php';
include 'common.php';


add();
commit();

function add(){
	$return= shell_exec("git add -A 2>&1");
	if ($return!="")
		echo ("Add Message: $return <br>");
	
	if (strpos(strtolower($return), "failed") !==false ){
		exit ;
	}
}

function commit($comment=""){
	global $helper;
	$status= shell_exec("git status 2>&1");
	if (strpos($status, "nothing to commit, working directory clean") !==false){
		echo("Commit Message: Nothing to commit");
		return;
	}
	if (trim($comment)==""){
		$comment="(Publish.php message) New versión added";
	}
	
	if(isset($_GET['major'])) {
		// id index exists
		$version=getNewVersion($_GET['major'], false);
	}
	else if(isset($_GET['minor'])) {
		$version=getNewVersion(false, $_GET['minor']);
	}
	else{
		$version=getNewVersion();
	}
	
	$previousVersion=getCurrentVersion();
	
	if ($version==""){
		die("Commit Message: Error retrieving DB version code <br>");
	}
	
	$return =  shell_exec("git commit -m \"{$comment}\" 2>&1");
// 	if(strpos($return, "files changed") === false && strpos($return, "deletion") === false && strpos($return, "insertion") === false && strpos($return, "use \"git push\" to publish your local commits" 
// 			&& strpos($return, "warning: LF will be replaced by CRLF"))=== false){
	echo ("Commit Message: $return <br>");
	if (strpos(strtolower($return), "failed") !==false){
		exit ;
	}
	
	$tuples=explode(".", $version);
	$day= date('YmdHis');
	
	$helper->query("insert into version(major, minor, consecutive, dateAdded) values ({$tuples[0]},{$tuples[1]},{$tuples[2]}, str_to_date(\"{$day}\", \"%Y%m%d%H%i%s\"))");
	$last_id=$helper->last_id;
	$result= shell_exec("git tag v$version  2>&1");
	
	$contents=getContent($previousVersion, $version);
	
	
	foreach ($contents as $content ){
		
		
		if ($content[0]=="A" ||$content[0]=="M"){
			$insert= "insert into versioncontent(idVersion, content, typeOfDiff) values ({$last_id}, \"{$content[1]}\", \"{$content[0]}\")";
			$helper->query($insert);
	}
		else if ($content[0]=="D"){
			$insert= "update versioncontent set typeOfDiff=\"{$content[0]}\" where content=\"{$content[1]}\" ";
			$helper->query($insert);
		}
	}
	
	//push();
	
	echo "Commit successfully done. $version<br>";
	
}
	

	
	function push(){
		
		//$result= shell_exec("git  config -l");
 		$result= shell_exec("git  push 2>&1");
		if (strpos($result,"Writing objects: 100%") === false)
			exit( $result);
		if ($result!="")
			echo "Push message:" . $result . "<br>";
	}
	
	
	function getNewVersion($major=false, $minor=false){
		global $helper;
		$query="select major, minor, consecutive from version order by cast( concat(lpad(major, 4, 0), lpad(minor, 4, 0), lpad(consecutive, 8, 0)) as UNSIGNED ) desc limit 1";
		$result = $helper->query($query);
		$version="";
	
		if (mysqli_num_rows($result) > 0) {
			// output data of each row
			while($row = mysqli_fetch_assoc($result)) {
				if ($major){
					$majorInt= $row['major']+1;
					$minorInt= 0;
					$consecutive=0;
				}
				else if ($minor){
					$majorInt= $row['major'];
					$minorInt= $row['minor']+1;
					$consecutive=0;
				}
				else{
					$majorInt= $row['major'];
					$minorInt= $row['minor'];
					$consecutive=$row['consecutive'] +1 ;
				}
				$version=  "{$majorInt}.{$minorInt}.{$consecutive}";
			}
		}
		else{
			$version=  "0.0.1";
		}
		return  $version;
	}
	
	
	
	

?>