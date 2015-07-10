<?php
namespace MoodleApi\Model;

class MoodleAddress
{
	public $street="";
	public $num_ext="";
	public $num_int="";
	public $colony="";
	public $city="";
	public $town="";
	public $state="";
	public $country="";
	public $postalCode="";
	
    public function __construct($data, $customFields)
    {
    //for($i=0;count($data['customfields'])>$i;$i++){
    //var_dump($data);
    //var_dump($customFields);
    			if(array_key_exists ( "address" , $customFields )){
    				
    				
    				$address=$customFields['address'];
    				if (trim($address)!=""){
    					$address=explode("\t", $address);
    					$this->street =     $address[0];
    					$this->num_ext =     $address[1];
    					$this->num_int =     $address[2];
    					$this->colony =     $address[3];
    				}
    				
    				
    			}
    			if(array_key_exists ( "town" , $customFields )){
    				$this->town=$customFields['town'];
    			}
    			if(array_key_exists ( "state" , $customFields )){
    				$this->state=$customFields['state'];
    			}
    			if(array_key_exists ( "postalCode" , $customFields )){
    				$this->postalCode=$customFields['postalCode'];
    			}
    		//}
    	
    	
    	$this->city =     (!empty($data['city'])) ? $data['city'] : null;
    	$this->country =     (!empty($data['country'])) ? $data['country'] : null;
    	
        
    }
}