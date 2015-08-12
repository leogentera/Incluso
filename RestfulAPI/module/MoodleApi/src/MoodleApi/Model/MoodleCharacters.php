<?php
namespace MoodleApi\Model;
use MoodleApi\Model\MoodleFamiliaCompartamosMember;
use MoodleApi\Utilities\Common;
class MoodleCharacters extends Common
{
	public $characters;
    
    
    public function get($data)
    {
    	$characters=array();
    	//for($i=0;count($data)>$i;$i++){
    		if(array_key_exists ( 'inspirationalCharacters' , $data )){
    			$characters_tmp=$data['inspirationalCharacters'];
    			if (trim($characters_tmp)==""){
    				return array();
    			}
    			$characters_tmp=$this->createTableFromCompundField($characters_tmp);
    			foreach ($characters_tmp as $member){
    				array_push($characters, new MoodleCharacter($member[0], $member[1]));
    			}
    		}
    		return $characters;
    		
    	//}
    }
}