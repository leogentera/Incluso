<?php
namespace MoodleApi\Model;
use MoodleApi\Model\MoodleFamiliaCompartamosMember;
use MoodleApi\Utilities\Common;
class MoodleFamiliaCompartamos extends Common
{
	public $familiaCompartamos;
    
    
    public function get($data)
    {
    	$familiaCompartamos=array();
    	//for($i=0;count($data)>$i;$i++){
    		if(array_key_exists ( 'familiaCompartamos' , $data )){
    			$familiaCompartamos_tmp=$data['familiaCompartamos'];
    			if (trim($familiaCompartamos_tmp)==""){
    				return array();
    			}
    			$familiaCompartamos_tmp=$this->createTableFromCompundField($familiaCompartamos_tmp);
    			foreach ($familiaCompartamos_tmp as $member){
    				if ($member===""){
    					continue;
    				}
    				array_push($familiaCompartamos, new MoodleFamiliaCompartamosMember($member[0], $member[1], $member[2]));
    			}
    		}
    		return $familiaCompartamos;
    		
    	//}
    }
}