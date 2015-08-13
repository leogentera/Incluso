<?php
include 'dbHelper.php';
include 'common.php';
include_once("Zip.php");


if ($_GET["version"]==""){
	die("No current version especified");
}

if (getCurrentVersion()==$_GET["version"]){
	exit;
}

$files= getUpdatedFiles($_GET["version"], getCurrentVersion());
createZip($files);

function getUpdatedFiles($phoneVersion, $currentVersion){
	global  $helper;
	
	$result=$helper->query("select distinct content from versioncontent vc where vc.idVersion between (select idVersion from version v where CONCAT_WS(\".\", v.major, v.minor, v.consecutive)= \"$phoneVersion\")
		and (select idVersion from version v where CONCAT_WS(\".\", v.major, v.minor, v.consecutive)= \"$currentVersion\") and typeOfDiff in (\"A\", \"M\")");
	
	$files= array();
	if (mysqli_num_rows($result) > 0) {
		// output data of each row
		while($row = mysqli_fetch_assoc($result)) {
			$files[]= $row["content"];
		}
	}
	
	return $files;
}

function createZip($files){
	$zip = new Zip();
	
	foreach ($files as $file){
		$zip->addFilePath($file, $file);
	}
	if(count($files)>0){
		$zip->addFile(getCurrentVersion(), "app/initializr/version.txt");
		$zip->finalize(); // as we are not using getZipData or getZipFile, we need to call finalize ourselves.
		$zip->sendZip("app.zip");
	}
	
	
}

?>