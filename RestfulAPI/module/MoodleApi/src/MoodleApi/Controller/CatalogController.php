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

  
}




