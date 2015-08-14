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
    	$facebookid="";
    	if (key_exists('facebookid', $data)){
    		$facebookid=urlencode($data['facebookid']);
    		
    		//if it is a facebook user, maybe it is already registered
    		
    		if($this->facebookUserExists($facebookid)!=""){
    			
    			//If it is registered, we should be transparent and send that the resgistering was successfull
    			$associativeArray = array();
        		$associativeArray ['username'] =$this->facebookUserExists($facebookid);
        		return new JsonModel($associativeArray);
    		}
    		
    	}
    	
    	//Mortal registration
        $url = $this->getConfig()['MOODLE_API_URL'].
                '&users[0][username]=%s'.
                '&users[0][password]=%s'.
                '&users[0][firstname]=%s'.
                '&users[0][lastname]=%s'.
                '&users[0][customfields][7][type]=mothername&users[0][customfields][7][value]=%s'.
                '&users[0][email]=%s'.
                '&users[0][city]=%s'.
                //'&users[0][country]=%s'.
        		'&users[0][customfields][6][type]=country&users[0][customfields][6][value]=%s'.
                '&users[0][customfields][0][type]=secretanswer&users[0][customfields][0][value]=%s'.
                '&users[0][customfields][1][type]=secretquestion&users[0][customfields][1][value]=%s'.
                '&users[0][customfields][2][type]=birthday&users[0][customfields][2][value]=%s'.
                '&users[0][customfields][3][type]=gender&users[0][customfields][3][value]=%s'.
        		'&users[0][customfields][4][type]=alias&users[0][customfields][4][value]=%s'.
        		'&users[0][customfields][5][type]=facebookid&users[0][customfields][5][value]=%s';
        
        $dateinMilis= $data['birthday'];
//      $dateinMilis=strtotime($data['birthday']) * 1000;
//      $dateinMilis+=3600000;

        $alias=urlencode($data['username']);
        if (key_exists('alias', $data)){
        	$alias=urlencode($data['alias']);
        }
        $username=urlencode($data['username']);
        if (key_exists('facebookid', $data)){
        	//$facebookid=urlencode($data['facebookid']);
        	$username=$this->generateUser();
        }
        
        
        $url = sprintf($url, $this->getToken(), "core_user_create_users", 
                $username, 
                urlencode($data['password']),
                urlencode($data['firstname']), 
                urlencode($data['lastname']),
        		urlencode($data['mothername']),
                urlencode($data['email']),
                urlencode($data['city']), urlencode($data['country']), 
                urlencode($data['secretanswer']), 
                urlencode($data['secretquestion']), 
                $data['birthday'], 
                $data['gender'],
        		$alias,
        		$facebookid)
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
        $message=$this->enrol_user($username, $this->getLatestCourse());
        
        if ($message!=""){
        	return new JsonModel($this->throwJSONError($message));
        }
        
        if ($facebookid!=""){
        	$associativeArray = array();
        	$associativeArray ['username'] =$this->facebookUserExists($facebookid);
        		return new JsonModel($associativeArray);
        
        }
        return  new JsonModel(array());
    }
    
    private function generateUser(){
    	$url = $this->getConfig()['MOODLE_API_URL'];
    	
    	$url = sprintf($url, $this->getToken(), "generate_user");
    	
    	$response = file_get_contents($url);
    	$json = json_decode($response,true);
    	if (strpos($response, "error") !== false)
    	{
    		return "";
    	}
    	
    	return $json['username'];
    }
    
    private function facebookUserExists($facebookid){
    	$url = $this->getConfig()['MOODLE_API_URL'].
                '&facebookid=%s';
    	 
    	$url = sprintf($url, $this->getToken(), "get_user_by_facebookid", $facebookid);
    	 
    	$response = file_get_contents($url);
    	$json = json_decode($response,true);
    	if (strpos($response, "exception") !== false)
    	{
    		return "";
    	}
    	 
    	return $json['username'];
    }
    
    private function getId(){
    	$url = $this->getConfig()['MOODLE_API_URL'];
    
    	$url = sprintf($url, $this->getToken(), "core_webservice_get_site_info");
    
    	$response = file_get_contents($url);
    	$json = json_decode($response,true);
    	if (strpos($response, "exception") !== false)
    	{
    		return "-1";
    	}
    
    	return $json['userid'];
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
    		return "Ocurrio un error, contacte al administrador";
    
    }
    
	
	// Action used for GET requests with resource Id
	public function get($id)
	{
		//var$this->getRequest()));
		
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
        	
            $user = new MoodleUserProfile($json[0], $this->getId());
            
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
    	//$url.=$this->createURLParms($data, '&users[0][%s]=%s', 'country' );
    
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
    	
    	
    	
    	$artisticActivities=$this->createTableRows($data,  'artisticActivities' );
    	if (trim($artisticActivities)!=""){
    		$url.=sprintf('&users[0][customfields][24][type]=%s&users[0][customfields][24][value]=%s',
    				"artisticActivities",$artisticActivities);
    	}
    	
    	$hobbies=$this->createTableRows($data,  'hobbies' );
    	if (trim($hobbies)!=""){
    		$url.=sprintf('&users[0][customfields][25][type]=%s&users[0][customfields][25][value]=%s',
    				"hobbies",$hobbies);
    	}
    	
    	$talents=$this->createTableRows($data,  'talents' );
    	if (trim($talents)!=""){
    		$url.=sprintf('&users[0][customfields][26][type]=%s&users[0][customfields][26][value]=%s',
    				"talents",$talents);
    	}
    	
    	$values=$this->createTableRows($data,  'values' );
    	if (trim($values)!=""){
    		$url.=sprintf('&users[0][customfields][27][type]=%s&users[0][customfields][27][value]=%s',
    				"values",$values);
    	}
    	
    	$habilities=$this->createTableRows($data,  'habilities' );
    	if (trim($habilities)!=""){
    		$url.=sprintf('&users[0][customfields][28][type]=%s&users[0][customfields][28][value]=%s',
    				"habilities",$habilities);
    	}
    	
    	
    	$url.=$this->createURLParms($data, '&users[0][customfields][29][type]=%s&users[0][customfields][29][value]=%s', 'iLiveWith' );
    	
    	$currentStudies=$this->createTableObject($data, "currentStudies",   "", 'level', 'grade', 'period' );
    	if (trim($currentStudies)!=""){
    		$url.=sprintf('&users[0][customfields][30][type]=%s&users[0][customfields][30][value]=%s',
    				"currentStudies",$currentStudies);
    	}
    	
    	//$url.=$this->createURLParms($data, '&users[0][customfields][30][type]=%s&users[0][customfields][30][value]=%s', 'currentStudies', 'level', 'grade', 'period' );
    	$url.=$this->createURLParms($data, '&users[0][customfields][31][type]=%s&users[0][customfields][31][value]=%s', 'children' );
    	$url.=$this->createURLParms($data, '&users[0][customfields][32][type]=%s&users[0][customfields][32][value]=%s', 'gotMoneyIncome' );
    	$url.=$this->createURLParms($data, '&users[0][customfields][33][type]=%s&users[0][customfields][33][value]=%s', 'medicalCoverage' );
    	$url.=$this->createURLParms($data, '&users[0][customfields][34][type]=%s&users[0][customfields][34][value]=%s', 'medicalInsurance' );
    	
    	
    	$mainActivity=$this->createTableRows($data,  'mainActivity' );
    	if (trim($mainActivity)!=""){
    		$url.=sprintf('&users[0][customfields][35][type]=%s&users[0][customfields][35][value]=%s',
    				"mainActivity",$mainActivity);
    	}
    	 
    	$moneyIncome=$this->createTableRows($data,  'moneyIncome' );
    	if (trim($moneyIncome)!=""){
    		$url.=sprintf('&users[0][customfields][36][type]=%s&users[0][customfields][36][value]=%s',
    				"moneyIncome",$moneyIncome);
    	}
    	
    	$knownDevices=$this->createTableRows($data,  'knownDevices' );
    	if (trim($knownDevices)!=""){
    		$url.=sprintf('&users[0][customfields][37][type]=%s&users[0][customfields][37][value]=%s',
    				"knownDevices",$knownDevices);
    	}
    	
    	$ownDevices=$this->createTableRows($data,  'ownDevices' );
    	if (trim($ownDevices)!=""){
    		$url.=sprintf('&users[0][customfields][38][type]=%s&users[0][customfields][38][value]=%s',
    				"ownDevices",$ownDevices);
    	}
    	
    	$phoneUsage=$this->createTableRows($data,  'phoneUsage' );
    	if (trim($phoneUsage)!=""){
    		$url.=sprintf('&users[0][customfields][39][type]=%s&users[0][customfields][39][value]=%s',
    				"phoneUsage",$phoneUsage);
    	}
    	
    	$url.=$this->createURLParms($data, '&users[0][customfields][40][type]=%s&users[0][customfields][40][value]=%s', 'playVideogames' );    	
    	$url.=$this->createURLParms($data, '&users[0][customfields][41][type]=%s&users[0][customfields][41][value]=%s', 'videogamesFrecuency' );
    	$url.=$this->createURLParms($data, '&users[0][customfields][42][type]=%s&users[0][customfields][42][value]=%s', 'videogamesHours' );
    	
    	$kindOfVideogames=$this->createTableRows($data,  'kindOfVideogames' );
    	if (trim($kindOfVideogames)!=""){
    		$url.=sprintf('&users[0][customfields][43][type]=%s&users[0][customfields][43][value]=%s',
    				"kindOfVideogames",$kindOfVideogames);
    	}
    	
    //Stars
       // $url.= $this->addStars($data, '&users[0][customfields][24][type]=%s&users[0][customfields][24][value]=%s', 'stars');
        


        $additionalEmails=$this->createTableRows($data,  'additionalEmails' );
        if (trim($additionalEmails)!=""){
        	$url.=sprintf('&users[0][customfields][44][type]=%s&users[0][customfields][44][value]=%s',
        			"additionalEmails",$additionalEmails);
        }
        
        $url.=$this->createURLParms($data, '&users[0][customfields][45][type]=%s&users[0][customfields][45][value]=%s', 'country' );

    	//$url = sprintf($url, $this->getToken(), $this->function);
    	

        $inspirationalCharacters=$this->createTableRows($data,  'inspirationalCharacters', 'characterName', 'characterType' );
        if (trim($inspirationalCharacters)!=""){
        	$url.=sprintf('&users[0][customfields][46][type]=%s&users[0][customfields][46][value]=%s',
        			"inspirationalCharacters",$inspirationalCharacters);
        }
         
        $favoriteGames=$this->createTableRows($data,  'favoriteGames' );
        if (trim($favoriteGames)!=""){
        	$url.=sprintf('&users[0][customfields][47][type]=%s&users[0][customfields][47][value]=%s',
        			"favoriteGames",$favoriteGames);
        }
         
        $favoriteSports=$this->createTableRows($data,  'favoriteSports' );
        if (trim($favoriteSports)!=""){
        	$url.=sprintf('&users[0][customfields][48][type]=%s&users[0][customfields][48][value]=%s',
        			"favoriteSports",$favoriteSports);
        }
        
        $url.=$this->createURLParms($data, '&users[0][customfields][49][type]=%s&users[0][customfields][49][value]=%s', 'maritalStatus' );
        $url.=$this->createURLParms($data, '&users[0][customfields][50][type]=%s&users[0][customfields][50][value]=%s', 'birthCountry' );
    
        $url.=$this->createURLParms($data, '&users[0][customfields][51][type]=%s&users[0][customfields][51][value]=%s', 'showGeneralData' );
        $url.=$this->createURLParms($data, '&users[0][customfields][52][type]=%s&users[0][customfields][52][value]=%s', 'showEducation' );
        $url.=$this->createURLParms($data, '&users[0][customfields][53][type]=%s&users[0][customfields][53][value]=%s', 'showAddress' );
        $url.=$this->createURLParms($data, '&users[0][customfields][54][type]=%s&users[0][customfields][54][value]=%s', 'showSocialNetworks' );
        $url.=$this->createURLParms($data, '&users[0][customfields][55][type]=%s&users[0][customfields][55][value]=%s', 'showFamiliaCompartamos' );
        $url.=$this->createURLParms($data, '&users[0][customfields][56][type]=%s&users[0][customfields][56][value]=%s', 'showInspirationalCharacters' );
        $url.=$this->createURLParms($data, '&users[0][customfields][57][type]=%s&users[0][customfields][57][value]=%s', 'showILiveWith' );
        $url.=$this->createURLParms($data, '&users[0][customfields][58][type]=%s&users[0][customfields][58][value]=%s', 'showMainActivity' );
        $url.=$this->createURLParms($data, '&users[0][customfields][59][type]=%s&users[0][customfields][59][value]=%s', 'showCurrentEducation' );
        $url.=$this->createURLParms($data, '&users[0][customfields][60][type]=%s&users[0][customfields][60][value]=%s', 'showFamiliar' );
        $url.=$this->createURLParms($data, '&users[0][customfields][57][type]=%s&users[0][customfields][57][value]=%s', 'showMoneyIncome' );
        $url.=$this->createURLParms($data, '&users[0][customfields][58][type]=%s&users[0][customfields][58][value]=%s', 'showHealth' );
        $url.=$this->createURLParms($data, '&users[0][customfields][59][type]=%s&users[0][customfields][59][value]=%s', 'showDevices' );
        $url.=$this->createURLParms($data, '&users[0][customfields][60][type]=%s&users[0][customfields][60][value]=%s', 'showVideogames' );
        
        $url.=$this->createURLParms($data, '&users[0][customfields][61][type]=%s&users[0][customfields][61][value]=%s', 'mothername' );
        
        $url.=$this->createURLParms($data, '&users[0][customfields][62][type]=%s&users[0][customfields][62][value]=%s', 'gender' );
        
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
    	//If by mistake we enter a key as Array, we should make that arraya as our keys
    	if (count($keys)>0){
    		if(gettype($keys[0])=="array"){
    			$keys=$keys[0];
    		}
    	}
    	
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
    
    function createTableObject($array,$nameOfTheObject , $sufix, ...$keys ){
    	$result="";
    	$i=0;
    	if (array_key_exists($nameOfTheObject, $array)){
    		$array=$array[$nameOfTheObject];
    		return $this->createTableRow($array, $sufix, $keys );
    	}
    	
    
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