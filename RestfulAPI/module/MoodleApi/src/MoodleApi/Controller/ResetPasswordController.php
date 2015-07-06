<?php
namespace MoodleApi\Controller;

use MoodleApi\Utilities\AbstractRestfulJsonController;
use Zend\View\Model\JsonModel;
use MoodleApi\Model\MoodleCourse;
use MoodleApi\Model\MoodleException;

class ResetPasswordController extends AbstractRestfulJsonController {
    
    private $token = "";
    private $function = "core_user_update_users";
	
    // Action used for POST requests
    public function create($data)
    {
    	
    	$url = $this->getConfig()['MOODLE_API_URL'].'&field=email&values[0]=%s';
    	$url = sprintf($url, $this->getToken(), "core_user_get_users_by_field", $data['email']);
    	 
    	$response = file_get_contents($url);
    	$json = json_decode($response,true);
    	 
    	if (strpos($response, "exception") !== false)
    	{
    		return new JsonModel( $this->throwJSONError("El usuario no esta registrado"));
    	}
    	else
    	{
    		if(count($json)==0){
    			return new JsonModel( $this->throwJSONError("El usuario no esta registrado"));
    		}
    			
    		$id=$json[0]['id'];
    		
    		$recoverycode="";
    		$codeexpirationdate="";
    		for($i=0;count($json[0]['customfields'])>$i;$i++){
    			if($json[0]['customfields'][$i]['name']=='recoverycode'){
    				$recoverycode=$json[0]['customfields'][$i]['value'];
    			}
    			
    			if($json[0]['customfields'][$i]['name']=='codeexpirationdate'){
    				$codeexpirationdate=$json[0]['customfields'][$i]['value'];
    			}
    		}
    	
    		if($recoverycode==""){
    			return new JsonModel( $this->throwJSONError(mb_convert_encoding("No se ha solicitado un cambio de contraseña", "UTF-8")));
    		}
    		if($codeexpirationdate<round(microtime(true) * 1000)){
    			return new JsonModel( $this->throwJSONError(mb_convert_encoding("El codigo ya expiró", "UTF-8")));
    		}
    		if($recoverycode!=$data['recoverycode']){
    			return new JsonModel( $this->throwJSONError("El codigo ingresado es incorrecto"));
    		}
    		
	    	$url = $this->getConfig()['MOODLE_API_URL'].'&users[0][password]=%s&users[0][id]=%s'.
    		'&users[0][customfields][0][type]=recoverycode&users[0][customfields][0][value]=%s'.
    		'&users[0][customfields][1][type]=codeexpirationdate&users[0][customfields][1][value]=%s';
	    	
	    	$url = sprintf($url, $this->getToken(), $this->function, $data['password'], $id, '', '');
	    	
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
	    				$message = 'El usuario ingresado no es valido';
	    			}
	    			else{
	    				return new JsonModel($this->throwJSONError());
	    			}
	    			return new JsonModel($this->throwJSONError($message));
	    		}
	    	
	    	
	    	
	    	
	    }

    }
}



