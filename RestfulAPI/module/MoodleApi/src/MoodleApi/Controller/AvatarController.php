<?php
namespace MoodleApi\Controller;

use MoodleApi\Utilities\AbstractRestfulJsonController;
use Zend\View\Model\JsonModel;
use MoodleApi\Model\MoodleActivity;
use MoodleApi\Model\MoodleCourse;
use MoodleApi\Model\MoodleException;
use MoodleApi\Model\MoodleLeader;
use MoodleApi\Model\MoodleStage;
use MoodleApi\Model\MoodleChallenge;

class AvatarController  extends AbstractRestfulJsonController {
    
    private $token = "";

   public function get($userid){
    
        $url = $this->getConfig()['MOODLE_API_URL'].'&userid=%s';
        $url = sprintf($url, $this->getToken(), "get_avatar_configuration", $userid);

        $response = file_get_contents($url);
        
        $json = json_decode($response,true);
    
        if (strpos($response, "exception") !== false)
        {
        	return new JsonModel($json);
		}
        
        return new JsonModel($json);
    }
    
    public function create($data){
    
    	//Mortal registration
        $url = $this->getConfig()['MOODLE_API_URL'].
                '&avatars[0][userid]=%s'.
                '&avatars[0][alias]=%s'.
                '&avatars[0][aplicacion]=%s'.
                '&avatars[0][estrellas]=%s'.
                '&avatars[0][color_cabello]=%s'.
                '&avatars[0][estilo_cabello]=%s'.
                '&avatars[0][traje_color_principal]=%s'.
                '&avatars[0][traje_color_secundario]=%s'.
                '&avatars[0][rostro]=%s'.
                '&avatars[0][color_de_piel]=%s'.
                '&avatars[0][escudo]=%s'.
                '&avatars[0][imagen_recortada]=%s'.
                '&avatars[0][ultima_modificacion]=%s';    	    	

        
        $url = sprintf($url, $this->getToken(), "create_avatar_configuration",
        		urlencode($data['userid']),
        		urlencode($data['alias']),
        		urlencode($data['aplicacion']),
        		urlencode($data['estrellas']),
        		urlencode($data['color_cabello']),
        		urlencode($data['estilo_cabello']),
        		urlencode($data['traje_color_principal']),
        		urlencode($data['traje_color_secundario']),
        		urlencode($data['rostro']),
        		urlencode($data['color_de_piel']),
        		urlencode($data['escudo']),
        		urlencode($data['imagen_recortada']),
        		urlencode($data['ultima_modificacion'])
        		);     
        		
        $response = file_get_contents($url);
        
    	return  new JsonModel(array());

    }
}