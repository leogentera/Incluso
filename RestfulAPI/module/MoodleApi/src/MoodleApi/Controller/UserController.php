<?php
namespace MoodleApi\Controller;

use MoodleApi\Utilities\AbstractRestfulJsonController;
use Zend\View\Model\JsonModel;
use MoodleApi\Model\MoodleUserProfile;
use MoodleApi\Model\MoodleException;
use MoodleApi\Model\MoodleApi\Model;
use MoodleApi\Model\MoodleBadge;
use MoodleApi\Model\MoodleCourse;
use Zend\Mail;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mail\Message;

class UserController extends AbstractRestfulJsonController{
	

	private $token = "";
	//private $function = "core_user_get_users_by_field";
	
    // Action used for POST requests
    public function create($data)
    {
        $url = $this->getConfig()['MOODLE_API_URL'].
                '&users[0][username]=%s'.
                '&users[0][password]=%s'.
                '&users[0][firstname]=%s'.
                '&users[0][lastname]=%s'.
                '&users[0][email]=%s'.
                '&users[0][city]=%s'.
                '&users[0][country]=%s'.
                '&users[0][customfields][0][type]=secretanswer&users[0][customfields][0][value]=%s'.
                '&users[0][customfields][1][type]=secretquestion&users[0][customfields][1][value]=%s'.
                '&users[0][customfields][2][type]=birthday&users[0][customfields][2][value]=%s'.
                '&users[0][customfields][3][type]=gender&users[0][customfields][3][value]=%s'.
        		'&users[0][customfields][4][type]=alias&users[0][customfields][4][value]=%s';
        
        $dateinMilis= $data['birthday'];
//      $dateinMilis=strtotime($data['birthday']) * 1000;
//      $dateinMilis+=3600000;
        
        $url = sprintf($url, $this->getToken(), "core_user_create_users", 
                urlencode($data['username']), 
                urlencode($data['password']),
                /*urlencode($data['firstname']), */ "", //Register page doesn't send firstname and last name
                /*urlencode($data['lastname']), */ "",
                urlencode($data['email']),
                urlencode($data['city']), urlencode($data['country']), 
                urlencode($data['secretanswer']), 
                urlencode($data['secretquestion']), 
                $data['birthday'], 
                $data['gender'],
        		urlencode($data['username']))
        ;
        
        
        $response = file_get_contents($url);
        
        $json = json_decode($response,true);
        if (strpos($response, "error") !== false)
        {
            
        	
            
            if ($json["debuginfo"]=="Email address is invalid"){
                //$associativeArray ['messageerror'] = ;
                $message='El email no es válido';
            }
            else if (strpos($json["debuginfo"], "Username already exists") !== false){
                $message = 'El usuario que ingresaste ya esta registrado';
            }
            else if (strpos($json["debuginfo"], "Email address already exists") !== false){
                $message = 'El email que ingresaste ya esta registrado';
            }
            else{
                return new JsonModel($this->throwJSONError());
            }
            
            return new JsonModel($this->throwJSONError($message));
        }
        $message=$this->enrol_user($data['username'], $this->getLatestCourse());
        
        if ($message!=""){
        	return new JsonModel($message);
        }
        return  new JsonModel(array());
    }
    
    private function enrol_user($username, $courseid){
    	if ($courseid<0){
    		
    		return "Ocurrio un error, contacte al administrador";
    	}
    	 
    	$url = $this->getConfig()['MOODLE_API_URL'].'&field=username&values[0]=%s';
    	$url = sprintf($url, $this->getToken(), "core_user_get_users_by_field", $username);
    
    	$response = file_get_contents($url);
    	$json_user = json_decode($response,true);
    
    	if (strpos($response, "exception") !== false || count($json_user)==0 )
    	{
    		
    		return "Ocurrio un error, contacte al administrador";
    	}
    	$id=$json_user[0]['id'];//id
    	 
    	$url = $this->getConfig()['MOODLE_API_URL'].'&enrolments[0][userid]=%s&enrolments[0][courseid]=%s&enrolments[0][roleid]=5';
    	$url = sprintf($url, $this->getToken(), "enrol_manual_enrol_users", $id, $courseid);
    	 
    	$response = file_get_contents($url);
    	$json_user = json_decode($response,true);
    	 
    	if (strpos($response, "exception") !== false  )
    	{
    		return "Ocurrio un error, contacte al administrador";
    	}
    	
    	
    	
    	return $this->updateCurrentCourseId($id, $courseid);
    }
    
    private function getLatestCourse(){
    
    	$url = $this->getConfig()['MOODLE_API_URL'];
    	$url = sprintf($url, $this->getToken(), "get_latest_course");
    
    	$response = file_get_contents($url);
    	$json_user = json_decode($response,true);
    
    	if (strpos($response, "exception") !== false )
    	{
    		var_dump($response);
    		return -1;
    	}
    	return $json_user['id'];
    }
    
    private function updateCurrentCourseId($id, $courseid)
    {
    	
    		$url = $this->getConfig()['MOODLE_API_URL'].'&users[0][id]=%s'.
    				'&users[0][customfields][0][type]=course&users[0][customfields][0][value]=%s';
    
    		$url = sprintf($url, $this->getToken(), "core_user_update_users", $id, $courseid);
    
    		$response = file_get_contents($url);
    		if ($response=="null"){
    			return "";
    		}
    		else{
    			$json = json_decode($response,true);
    		}
    		
    		return $this->throwJSONError();
    
    }
    
	
	// Action used for GET requests with resource Id
	public function get($id)
	{
		
        $url = $this->getConfig()['MOODLE_API_URL'].'&field=id&values[0]=%s';
        $url = sprintf($url, $this->getToken(), "core_user_get_users_by_field", $id);

        $response = file_get_contents($url);
        
        $json = json_decode($response,true);

        $users="";
        
		if (strpos($response, "exception") !== false) 
        {
            // Error
        	return new JsonModel($this->throwJSONError());
        }
            // Good
        	
            $user = new MoodleUserProfile($json[0]);
            
            $badgesEarned=$this->getBadgesByMethod($id, "earned_badges");
            $badgesToEarn=$this->getBadgesByMethod($id, "posible_badges_to_earn",$user->course );
            $user->setBadges($badgesEarned, $badgesToEarn);
            $user->setRank($this->getRank($id));
            return new JsonModel((array) $user);

        
        return new JsonModel($user);
    }
    
    
    public function getBadgesByMethod($id, $function, $courseid="")
    {
    
    	$url = $this->getConfig()['MOODLE_API_URL'].'&id=%s&moodleurl=%s';
    	$url = sprintf($url, $this->getToken(), $function, $id, $this->getConfig()['MOODLE_URL']);
    	
    	if ($courseid!=""){
    		$url.="&courseid=$courseid";
    	}
    	
    	
    	
    	$response = file_get_contents($url);
    	$json = json_decode($response,true);
    	//var_dump($response);
    	if (strpos($response, "exception") !== false)
    	{
    		return array();
    	}
    		// Good
    		$badges= array();
    		
    		
    		foreach($json as $badge){
    			$badge = new MoodleBadge($badge);
    			array_push($badges, $badge);
    		}
    		
    		return $badges;
    
    	
    }
    
    public function getRank($id)
    {
    
    	$url = $this->getConfig()['MOODLE_API_URL'].'&userid=%s';
    	$url = sprintf($url, $this->getToken(), "get_user_rank", $id);
    
    	$response = file_get_contents($url);
    
    	$json = json_decode($response,true);
    
    	if (strpos($response, "exception") !== false)
    	{
    		var_dump($response);
    		return -1;
    	}
    	// Good
    	return $json[0]['place'];
    
    	 
    }
    
    // Action used for POST requests
    public function  update($id, $data)
    {
    	$url = $this->getConfig()['MOODLE_API_URL']."&users[0][id]=%s";
    	$url = sprintf($url, $this->getToken(), "core_user_update_users",$id);
    	 
    	//$url.=createURLParms($data, '&users[0][%s]=%s', 'country' );
    	$url.=$this->createURLParms($data, '&users[0][%s]=%s', 'firstname' );
    	$url.=$this->createURLParms($data, '&users[0][%s]=%s', 'lastname' );
    
    	$address=$this->createTableRow($data, "", 'street', 'num_ext', 'num_int', 'colony' );
    	if (trim($address)!=""){
    		$url.=sprintf('&users[0][customfields][0][type]=%s&users[0][customfields][0][value]=%s',
    				"address",$address);
    	}
    	$url.=$this->createURLParms($data, '&users[0][%s]=%s', 'city' );
    	$url.=$this->createURLParms($data, '&users[0][%s]=%s', 'country' );
    
    	$url.=$this->createURLParms($data, '&users[0][customfields][1][type]=%s&users[0][customfields][1][value]=%s', 'town' );
    	$url.=$this->createURLParms($data, '&users[0][customfields][2][type]=%s&users[0][customfields][2][value]=%s', 'state' );
    	$url.=$this->createURLParms($data, '&users[0][customfields][3][type]=%s&users[0][customfields][3][value]=%s', 'postalCode' );
    
    	$studies=$this->createTableRows($data, 'studies', 'school', 'levelOfStudies' );
    
    	if (trim($studies)!=""){
    		$url.=sprintf('&users[0][customfields][4][type]=%s&users[0][customfields][4][value]=%s',
    				"studies",$studies);
    	}
    
    	$familiaCompartamos=$this->createTableRows($data, 'familiaCompartamos',  'idClient', 'relativeName', 'relationship' );
    	if (trim($familiaCompartamos)!=""){
    		$url.=sprintf('&users[0][customfields][5][type]=%s&users[0][customfields][5][value]=%s',
    				"familiaCompartamos",$familiaCompartamos);
    	}
    
    	$phones=$this->createTableRows($data,  'phone' );
    	if (trim($phones)!=""){
    		$url.=sprintf('&users[0][customfields][6][type]=%s&users[0][customfields][6][value]=%s',
    				"phones",$phones);
    	}
    
    
    	$url.=$this->createURLParms($data, '&users[0][customfields][7][type]=%s&users[0][customfields][7][value]=%s', 'stage' );
    
    	$socialNetworks=$this->createTableRows($data,  'socialNetworks', 'socialNetwork', 'socialNetworkId' );
    
    	if (trim($socialNetworks)!=""){
    		$url.=sprintf('&users[0][customfields][8][type]=%s&users[0][customfields][8][value]=%s',
    				"socialNetworks",$socialNetworks);
    	}
    
    
    	$attributesAndQualities=$this->createTableRows($data,  'attributesAndQualities' );
    	if (trim($attributesAndQualities)!=""){
    		$url.=sprintf('&users[0][customfields][9][type]=%s&users[0][customfields][9][value]=%s',
    				"attributesAndQualities",$attributesAndQualities);
    	}
    
    	$dreamsToBe=$this->createTableRows($data,  'dreamsToBe' );
    	if (trim($dreamsToBe)!=""){
    		$url.=sprintf('&users[0][customfields][10][type]=%s&users[0][customfields][10][value]=%s',
    				"dreamsToBe",$dreamsToBe);
    	}
    
    	$dreamsToHave=$this->createTableRows($data,  'dreamsToHave' );
    	if (trim($dreamsToHave)!=""){
    		$url.=sprintf('&users[0][customfields][11][type]=%s&users[0][customfields][11][value]=%s',
    				"dreamsToHave",$dreamsToHave);
    	}
    
    	$dreamsToDo=$this->createTableRows($data,  'dreamsToDo' );
    	if (trim($dreamsToDo)!=""){
    		$url.=sprintf('&users[0][customfields][12][type]=%s&users[0][customfields][12][value]=%s',
    				"dreamsToDo",$dreamsToDo);
    	}
    
    	$likesAndPreferences=$this->createTableRows($data,  'likesAndPreferences' );
    	if (trim($likesAndPreferences)!=""){
    		$url.=sprintf('&users[0][customfields][13][type]=%s&users[0][customfields][13][value]=%s',
    				"likesAndPreferences",$likesAndPreferences);
    	}
        
    	//
    	$url.=$this->createURLParms($data, '&users[0][customfields][14][type]=%s&users[0][customfields][14][value]=%s', 'showMyInformation' );
    	$url.=$this->createURLParms($data, '&users[0][customfields][15][type]=%s&users[0][customfields][15][value]=%s', 'showAttributesAndQualities' );
    	$url.=$this->createURLParms($data, '&users[0][customfields][16][type]=%s&users[0][customfields][16][value]=%s', 'showLikesAndPreferences' );
    	$url.=$this->createURLParms($data, '&users[0][customfields][17][type]=%s&users[0][customfields][17][value]=%s', 'showBadgesEarned' );
    	$url.=$this->createURLParms($data, '&users[0][customfields][18][type]=%s&users[0][customfields][18][value]=%s', 'showStrengths' );
    	$url.=$this->createURLParms($data, '&users[0][customfields][19][type]=%s&users[0][customfields][19][value]=%s', 'showRecomendedBachelorDegrees' );
    	$url.=$this->createURLParms($data, '&users[0][customfields][20][type]=%s&users[0][customfields][20][value]=%s', 'showMyDreams' );
    	$url.=$this->createURLParms($data, '&users[0][customfields][21][type]=%s&users[0][customfields][21][value]=%s', 'alias' );
    	$url.=$this->createURLParms($data, '&users[0][customfields][22][type]=%s&users[0][customfields][22][value]=%s', 'termsAndConditions' );
    	$url.=$this->createURLParms($data, '&users[0][customfields][23][type]=%s&users[0][customfields][23][value]=%s', 'informationUsage' );
    
    //Stars
        $url.= $this->addStars($data, '&users[0][customfields][24][type]=%s&users[0][customfields][24][value]=%s', 'stars');

    	//$url = sprintf($url, $this->getToken(), $this->function);
    
    	$response = file_get_contents($url);
    
    
    	if ($response=="null"){
    		return  new JsonModel(array());
    	}
    	else{
    		$json = json_decode($response,true);
    	}
    
    	 
    
    	if (strpos($response, "error") !== false)
    	{
    		if (strpos($json["errorcode"], "authpluginnotfound") !== false){
    			$message = 'El usuario ingresado no es válido';
    		}
    		else{
    			return new JsonModel($this->throwJSONError());
    		}
    		return new JsonModel($this->throwJSONError($message));
    	}
    
    
    	 
    
    	//}
    
    }
    
    function createURLParms($array, $format, $key ){
    	if(array_key_exists ( $key , $array )){
    		 $variable=$array[$key];
    		 
    		 if (gettype($variable)=="boolean"){
    		 	if ($variable){
    		 		$variable=0;
    		 	}
    		 	else{
    		 		$variable=1;
    		 	}
    		 }
    		return sprintf($format, $key, urlencode($variable));
    
    	}
    	return "";
    	 
    }
    
    function createTableRow($array, $sufix, ...$keys ){
    	$result="";
    	$i=0;
    	foreach ($keys as $key){
    		if($i>0){
    			$result.="\t";
    		}
    		if(array_key_exists ( $key.$sufix , $array )){
    			$result.=$array[$key.$sufix];
    			$i++;
    		}
    	}
    	if (count($keys)==0){
    		$i=0;
    		$result.=$array;
    	}
    	$result= urlencode($result);
    	 
    	return $result;
    
    }
    
    function createTableRows($array, $arrayname, ...$keys ){
    	$result="";
    	$i=0;
    	$subarray=array();
    	 
    	//foreach ($keys as $key){
    	 
    	if (array_key_exists ( $arrayname , $array )){
    		$subarray=$array[$arrayname];
    
    	}
    
    	foreach($subarray as $key){
    		if($i>0){
    			$result.=urlencode("\n");
    		}
    		$result.=$this->createTableRow($key, "", ...$keys );
    		 
    		 
    		$i++;
    
    	}
    	return $result;
    
    }
    
    public function getProgress($userid)
    {
    
        $url = $this->getConfig()['MOODLE_API_URL']."&userid=$userid";
        $url = sprintf($url, $this->getToken(), "get_global_progress");
    
        $response = file_get_contents($url);
    
        $json = json_decode($response,true);
    
        if (strpos($response, "exception") !== false)
        {
    
            return array();
        }
        // Good
        if (count($json)==0){
            return "-1";
        }
        $progress=$json[0]['percentage_completed'];
        return $progress;
    
    
    }
    
    public function getFinishedStages($userid)
    {
    
        $url = $this->getConfig()['MOODLE_API_URL']."&userid=$userid";
        $url = sprintf($url, $this->getToken(), "get_finished_stages");
    
        $response = file_get_contents($url);
    
        $json = json_decode($response,true);
    
        if (strpos($response, "exception") !== false)
        {
    
            return array();
        }
        // Good
        $stages= array();
        foreach($json as $stage){
            $stage = new MoodleStage($stage);
            array_push($stages, $stage);
        }
        return $stages;
    
    
    }

    public function getAvailableStages($userid)
    {
    
        $url = $this->getConfig()['MOODLE_API_URL'];
        $url = sprintf($url, $this->getToken(), "get_all_stages");

        $response = file_get_contents($url);
    
        $json = json_decode($response,true);
    
        if (strpos($response, "exception") !== false)
        {
            return array();
        }
        // Good
        $stages= array();
        foreach($json as $stage){
            $stage = new MoodleStage($stage);
            array_push($stages, $stage);
        }
        return $stages;
    
    
    }
    
    private function addStars($data, $format, $field){
        if (!key_exists('stars', $data)){
            return "";
        }

        $url = $this->getConfig()['MOODLE_API_URL'].'&field=id&values[0]=%s';
        
        
        $url = sprintf($url, $this->getToken(), "core_user_get_users_by_field", $data['id']);
        
        $response = file_get_contents($url);
        $json = json_decode($response,true);
         
        if (strpos($response, "exception") !== false)
        {
            return new JsonModel( $this->throwJSONError("El usuario no esta registrado"));
        }
            
        $customFields=array();

        for($i=0;count($json[0]['customfields'])>$i;$i++){
            $customFields[$json[0]['customfields'][$i]['name']]=$json[0]['customfields'][$i]['value'];
        }

        $currentStars=$data['stars'];
        
        if (key_exists('stars', $customFields)){
            $currentStars+=$customFields['stars'];
        }

        $url=sprintf($format,$field,$currentStars);

        return $url;
    }
   
}