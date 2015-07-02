<?php
namespace MoodleApi\Controller;

use MoodleApi\Utilities\AbstractRestfulJsonController;
use Zend\View\Model\JsonModel;
use MoodleApi\Model\MoodleCourse;
use MoodleApi\Model\MoodleException;

class RegisterController extends AbstractRestfulJsonController {
    
    private $token = "";
    private $function = "core_user_create_users";
	
    // Action used for POST requests
    public function create($data)
    {
    	$url = $this->getConfig()['MOODLE_API_URL'].
    	'&users[0][username]=%s&users[0][password]=%s&users[0][firstname]=%s&users[0][lastname]=%s&users[0][email]=%s&users[0]'/*[auth]=%s&users[0][idnumber]=%s&users[0][lang]=%s&users[0][calendartype]=%s&users[0]*/.'[city]=%s&users[0][country]=%s'.
    			'&users[0][customfields][0][type]=respsecreta&users[0][customfields][0][value]=%s'.
    			'&users[0][customfields][1][type]=pregsecreta&users[0][customfields][1][value]=%s'.
    			'&users[0][customfields][2][type]=birthday&users[0][customfields][2][value]=%s'.
    			'&users[0][customfields][3][type]=gender&users[0][customfields][3][value]=%s';
    	
    	//$url = sprintf($url, $this->getToken(), $this->function, $data['username'], $data['password'], $data['firstname'], $data['lastname'], $data['email'], $data['auth'], $data['idnumber'], $data['lang'], $data['calendartype']);
    	$url = sprintf($url, $this->getToken(), $this->function, $data['username'], urlencode($data['password']), "", "",  $data['email'], /*$data['auth'], $data['idnumber'], $data['lang'], $data['calendartype'],*/ urlencode($data['city']), urlencode($data['country'])
    			, urlencode($data['respsecreta']), urlencode($data['pregsecreta']), $data['birthday'], $data['gender']);
    	//var_dump($url);
    	
    	
    	$response = file_get_contents($url);
    	$json = json_decode($response,true);
    	if (strpos($response, "error") !== false)
    	{
    		$this->getResponse()->setStatusCode(401);
    		//return new JsonModel ($json);
    		// 	    		// Error
    		// 	    		$error = new MoodleException();
    		// 	    		$error->exchangeArray($json);
    		// 	    		array_push($courses, $error);
    	}
    	var_dump($url);
    	return new JsonModel($json);
    }
    
    
}



?>
