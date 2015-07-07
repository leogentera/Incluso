<?php
namespace MoodleApi\Controller;

use MoodleApi\Utilities\AbstractRestfulJsonController;
use Zend\View\Model\JsonModel;
use MoodleApi\Model\MoodleException;

class CatalogController extends AbstractRestfulJsonController {
    
    private $token = "";
    private $function = "local_catalogs_values";
	
   
    //test
    // Action used for GET requests without resource Id
    public function getList()
    {    	
    	$request = $this->getRequest();
    	$catalogName = $request->getQuery('catalogname');
    	
        $url = $this->getConfig()['MOODLE_API_URL'];
        $url = sprintf($url . '&catalogname=%s', $this->getToken(), $this->function, $catalogName);
        
        $response = file_get_contents($url);
        $json = json_decode($response,true);
        
        return new JsonModel($json);
    }

    private function hasToken() {
    	$request = $this->getRequest();
        if (isset($request->getCookie()->MOODLE_TOKEN)) {
        	return true;
        }else{
        	return false;
        }
    }

    private function generateToken() {
        $url = $this->getConfig()['TOKEN_GENERATION_URL'];
        $url = sprintf($url, 'admin', 'Admin123!', $this->getConfig()['MOODLE_SERVICE_NAME']);
        
        $response = file_get_contents($url);
        $json = json_decode($response,true);
                
        setcookie('MOODLE_TOKEN', $json['token'], time() + 3600, '/',null, false); //the true indicates to store only if there´s a secure connection

        return $json['token'];
    }
    
    public function getToken() {
    	$token = '';
    	$request = $this->getRequest();
    	if ($this->hasToken()) {
    		$token = $request->getCookie()->MOODLE_TOKEN;
    	} else {
    		$token = $this->generateToken();
    	}
    	return $token;
    }
}




