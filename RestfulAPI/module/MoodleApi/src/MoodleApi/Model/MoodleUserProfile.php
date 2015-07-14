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
    public $rank=0;
    public $stars=0;
    
    public $attributesAndQualities=array();
    public $strengths=array();
    public $recomendedBachelorDegrees=array();
    public $likesAndPreferences=array();
    public $dreamsToBe=array();
    public $dreamsToHave=array();
    public $dreamsToDo=array();
    public $badgesEarned=array();
    
    public $showMyInformation=true;
    public $showAttributesAndQualities=true;
    public $showLikesAndPreferences	=true;
    public $showBadgesEarned=true;
    public $showStrengths=true;
    public $showRecomendedBachelorDegrees=true;
    public $showMyDreams=true;
    
    
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
        
        if(array_key_exists ( 'attributesAndQualities' , $customFields )){
        	$this->attributesAndQualities=$this->createTableFromCompundField($customFields['attributesAndQualities']);
        	//var_dump($this->phones);
        }
        
        if(array_key_exists ( 'strengths' , $customFields )){
        	$this->strengths=$this->createTableFromCompundField($customFields['strengths']);
        	//var_dump($this->phones);
        }
        
        if(array_key_exists ( 'recomendedBachelorDegrees' , $customFields )){
        	$this->recomendedBachelorDegrees=$this->createTableFromCompundField($customFields['recomendedBachelorDegrees']);
        	//var_dump($this->phones);
        }
        
        if(array_key_exists ( 'likesAndPreferences' , $customFields )){
        	$this->likesAndPreferences=$this->createTableFromCompundField($customFields['likesAndPreferences']);
        	//var_dump($this->phones);
        }
        
        if(array_key_exists ( 'likesAndPreferences' , $customFields )){
        	$this->likesAndPreferences=$this->createTableFromCompundField($customFields['likesAndPreferences']);
        	//var_dump($this->phones);
        }
        
        if(array_key_exists ( 'dreamsToBe' , $customFields )){
        	$this->dreamsToBe=$this->createTableFromCompundField($customFields['dreamsToBe']);
        	//var_dump($this->phones);
        }
        
        if(array_key_exists ( 'dreamsToHave' , $customFields )){
        	$this->dreamsToHave=$this->createTableFromCompundField($customFields['dreamsToHave']);
        	//var_dump($this->phones);
        }
        
        if(array_key_exists ( 'dreamsToDo' , $customFields )){
        	$this->dreamsToDo=$this->createTableFromCompundField($customFields['dreamsToDo']);
        	//var_dump($this->phones);
        }

        
        if(array_key_exists ( 'showMyInformation' , $customFields )){
        	$this->showMyInformation=$customFields['showMyInformation']==0?true:false;
        	//var_dump($this->phones);
        }
        
        if(array_key_exists ( 'showAttributesAndQualities' , $customFields )){
        	$this->showAttributesAndQualities=$customFields['showAttributesAndQualities']==0?true:false;
        	//var_dump($this->phones);
        }
        
        if(array_key_exists ( 'showLikesAndPreferences' , $customFields )){
        	$this->showLikesAndPreferences=$customFields['showLikesAndPreferences']==0?true:false;
        	//var_dump($this->phones);
        }
        
        if(array_key_exists ( 'showBadgesEarned' , $customFields )){
        	$this->showBadgesEarned=$customFields['showBadgesEarned']==0?true:false;
        	//var_dump($this->phones);
        }
        
        
        if(array_key_exists ( 'showStrengths' , $customFields )){
        	$this->showStrengths=$customFields['showStrengths']==0?true:false;
        	//var_dump($this->phones);
        }
        
        
        if(array_key_exists ( 'showRecomendedBachelorDegrees' , $customFields )){
        	$this->showRecomendedBachelorDegrees=$customFields['showRecomendedBachelorDegrees']==0?true:false;
        	//var_dump($this->phones);
        }
        
        if(array_key_exists ( 'showMyDreams' , $customFields )){
        	$this->showMyDreams=$customFields['showMyDreams']==0?true:false;
        	//var_dump($this->phones);
        }
        
        if(array_key_exists ( 'stars' , $customFields )){
        	$this->stars=$customFields['stars'];
        	//var_dump($this->phones);
        }
        
    }
}