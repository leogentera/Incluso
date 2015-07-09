<?php

namespace MoodleApi\Controller;

use MoodleApi\Utilities\AbstractRestfulJsonController;
use Zend\View\Model\JsonModel;
use MoodleApi\Model\MoodleCourse;
use MoodleApi\Model\MoodleException;

/**
 * AuthenticationController
 *
 * @author
 *
 * @version
 *
 */
class AuthenticationController extends AbstractRestfulJsonController {
	/**
	 * The default action - show the home page
	 */
    // Action used for POST requests
 public function create($data)
    {
	    	$url = $this->getConfig()['TOKEN_GENERATION_URL'];
	    	$url = sprintf($url, $data['username'], $data['password'], $this->getConfig()['MOODLE_SERVICE_NAME']);

	    	$response = file_get_contents($url);
	    	$json = json_decode($response,true);
	    	if (strpos($response, "error") !== false)
	    	{
	    		return new JsonModel ($this->throwJSONError("Verifique usuario y contraseña", 401));
	    	}
	    	
	    	$url = $this->getConfig()['MOODLE_API_URL'].'&field=username&values[0]=%s';
	    	$url = sprintf($url, $this->getToken(), "core_user_get_users_by_field", $data['username']);
	    	 
	    	$response = file_get_contents($url);
	    	$json_user = json_decode($response,true);
	    	 
	    	if (strpos($response, "exception") !== false || count($json_user)==0 )
	    	{
	    		return new JsonModel( $this->throwJSONError("Ocurrio un error, Contacte al administrador", 401));
	    	}
	    	$json['id']=$json_user[0]['id'];
    	return new JsonModel($json);
    }
}