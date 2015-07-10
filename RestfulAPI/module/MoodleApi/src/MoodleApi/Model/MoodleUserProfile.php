<?php
namespace MoodleApi\Model;
use MoodleApi\Model\MoodleStudies;
use MoodleApi\Model\MoodleAddress;
use MoodleApi\Model\MoodleSocialNetworks;
use MoodleApi\Model\MoodleFamiliaCompartamos;
use MoodleApi\Utilities\Common;

class MoodleUserProfile extends Common
{
	public $country;
    public $email;
    public $firstname;
    public $fullname;
    public $id;
    public $lastname;
    public $profileimageurl;
    public $profileimageurlsmall;
    public $username;
    
    //From this point the generic variables
    public $studies;
    public $address;
    public $phones;
    public $socialNetworks;
    public $familiaCompartamos;
    public $stage="";
    
    public function __construct($data)
    {
        $this->country =     (!empty($data['country'])) ? $data['country'] : null;
        $this->email =     (!empty($data['email'])) ? $data['email'] : null;
        $this->firstname =     (!empty($data['firstname'])) ? $data['firstname'] : null; 
        $this->fullname =     (!empty($data['fullname'])) ? $data['fullname'] : null; 
        $this->id =     (!empty($data['id'])) ? $data['id'] : null; 
        $this->lastname =     (!empty($data['lastname'])) ? $data['lastname'] : null; 
        $this->profileimageurl =     (!empty($data['profileimageurl'])) ? $data['profileimageurl'] : null;
        $this->profileimageurlsmall =     (!empty($data['profileimageurlsmall'])) ? $data['profileimageurlsmall'] : null;
        $this->username =     (!empty($data['username'])) ? $data['username'] : null;
        
        //We turn the custom fields to an array because otherwise, we should do a for loop to search for each value
        $customFields=array();
        for($i=0;count($data['customfields'])>$i;$i++){
        	$customFields[$data['customfields'][$i]['name']]=$data['customfields'][$i]['value'];
        }
         
        $this->address=new MoodleAddress($data, $customFields);
        $this->studies=new MoodleStudies();
        $this->studies=$this->studies->get($customFields);
        if(array_key_exists ( 'phones' , $customFields )){
        	$this->phones=$this->createTableFromCompundField($customFields['phones']);
        	//var_dump($this->phones);
        }
       
        
        $this->socialNetworks=new MoodleSocialNetworks();
        $this->socialNetworks=$this->socialNetworks->get($customFields);
        $this->familiaCompartamos=new MoodleFamiliaCompartamos();
        $this->familiaCompartamos=$this->familiaCompartamos->get($customFields);
        if(array_key_exists ( 'stage' , $customFields )){
        	$this->stage=$customFields['stage'];
        }
        
        
        
    }
}