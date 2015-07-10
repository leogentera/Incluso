<?php
namespace MoodleApi\Controller;

use MoodleApi\Utilities\AbstractRestfulJsonController;
use Zend\View\Model\JsonModel;
use MoodleApi\Model\MoodleUserProfile;
use MoodleApi\Model\MoodleException;
use MoodleApi\Model\MoodleApi\Model;

class UserProfileController extends AbstractRestfulJsonController{
	

	private $token = "";
	private $function = "core_user_get_users_by_field";
	
	
	// Action used for GET requests with resource Id
	public function get($id)
	{
        $url = $this->getConfig()['MOODLE_API_URL'].'&field=id&values[0]=%s';
        $url = sprintf($url, $this->getToken(), $this->function, $id);

        $response = file_get_contents($url);
        
        $json = json_decode($response,true);

        $users="";
        
		if (strpos($response, "exception") !== false) 
        {
            // Error
//             $error = new MoodleException();
//             $error->exchangeArray($json);
//             array_push($users, $error);
        }
        else
        {
            // Good
                $user = new MoodleUserProfile($json[0]);
                return new JsonModel((array) $user);

        }
        return new JsonModel($user);
    }

    
   
}