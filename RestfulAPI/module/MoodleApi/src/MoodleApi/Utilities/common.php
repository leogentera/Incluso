<?php
namespace MoodleApi\Utilities;

class Common {
	function createTableFromCompundField($data){
		
		if (trim($data)==""){
			return array();
		}
		
		
		$rows_temp=explode("\n", $data);
	
		$rows= array();
		foreach ($rows_temp as $row){
			if (strpos($row, "\t") !== false){
				$fields=explode("\t", $row);
			}
			else{
				$fields=$row;
			}
			array_push($rows, $fields);
		}
		return $rows;
	}
}

?>