<?php

namespace MoodleApi\Controller;

use MoodleApi\Utilities\AbstractRestfulJsonController;
use MoodleApi\Utilities\SMTPClient;
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

// Action used for POST requests
 public function create($data)
{
    if(!array_key_exists("action", $data)){
        return $this->authentication($data);
    }else{
        switch($data["action"]){
            case 'forgot':
                $this->forgotPassword($data);
                break;
            default:
                $this->authentication($data);
                break;
        }

    }
	
}

// Action used for PUT requests
public function replaceList($data){

    return $this->resetPassword($data);
}

private function authentication($data){

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


//Forgot password utiliza la clase de mail
private function forgotPassword($data)
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
    		// Good
    		
    		if(count($json)==0){
    			return new JsonModel( $this->throwJSONError("El usuario no esta registrado"));
    		}
    		
    		$recoverycode= rand(100000, 999999);

    		$codeexpirationdate=round(microtime(true) * 1000)+86400000;
    		
    		$id=$json[0]['id'];
    		
	    	for($i=0;count($json[0]['customfields'])>$i;$i++){
	        	$customFields[$json[0]['customfields'][$i]['name']]=$json[0]['customfields'][$i]['value'];
	        }
	        
	        if ($customFields["secretquestion"]!=$data["secretquestion"] || $customFields["secretanswer"]!=$data["secretanswer"]){
	        	        	
	        	return new JsonModel( $this->throwJSONError("La pregunta o la respuesta secreta son incorrectas"));
	        }
    		
    		$url = $this->getConfig()['MOODLE_API_URL'].'&users[0][id]=%s'.
    		'&users[0][customfields][0][type]=recoverycode&users[0][customfields][0][value]=%s'.
    		'&users[0][customfields][1][type]=codeexpirationdate&users[0][customfields][1][value]=%s';
    		 
    		$url = sprintf($url, $this->getToken(), $this->function, $id, $recoverycode, $codeexpirationdate);
    		 
    		$response = file_get_contents($url);
    		if ($response=="null"){
    			
    			
//     			$to =$data['email'];
//     			$from = 'desarrollo.definityfist@gmail.com';
//     			$subject = 'Reseteo de Contraseña Incluso';
//     			$body = "Hola!, has indicado que has olvidado tu contraseña y para ello tendras que ingresar el codigo $recoverycode en la aplicacion para continuar";
//     			//     	$SMTPMail = new SMTPClient ($SmtpServer, $SmtpPort, $SmtpUser, $SmtpPass, $from, $to, $subject, $body);
//     			//     	$SMTPChat = $SMTPMail->SendMail();
    			 
//     			//mail('caffeinated@example.com', 'My Subject', $message);
//     			$mail= new SMTPClient();
//     			$mail->SMTPClient ('mtymaildf-v05.sieenasoftware.com', 25, 'humberto.castaneda','Hh.83420676', 'humberto.castaneda@definityfirst.com', $data['email'], $subject, $body);
//     			var_dump($mail->SendMail ());
    			
    			//$mail= new SMTPClient();
    			//$mail->SMTPClient ('ssl://smtp.gmail.com', 465, 'desarrollo.definityfist@gmail.com','Admin123!', 'desarrollo.definityfist@gmail.com', $data['email'], "hello", "hola");
    			
    			try {
    				$transport = new SmtpTransport();
    				$options   = new SmtpOptions(array(
    						'name' => 'mtymaildf-v05.sieenasoftware.com',
    						'host' => 'mail.definityfirst.com',
    						'port' => 25,
    						'connection_class'  => 'login',
    						'connection_config' => array(
    								'username' => 'gentera',
    								'password' => 'An15b4r4',
    								'ssl'      => 'tls',
    						),
    				));
    				$transport->setOptions($options);
    				 
    				$message = new Message();
    				 
    				$message->addTo($data['email'])
    				->addFrom('gentera@definityfirst.com')
    				->setSubject('Reseteo de Contraseña Incluso')
    				->setBody("Hola!, has indicado que has olvidado tu contraseña y para ello tendras que ingresar el codigo $recoverycode en la aplicacion para continuar");
    				$transport->send($message);
    			} catch (\Exception $e) {
    				return new JsonModel( $this->throwJSONError("Ocurrio un error al momento de enviar el mail con el código de confirmacion, contacte al administrador"));
    			}
    			
    			
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


    // Reset password
    private function resetPassword($data)
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
    			return new JsonModel( $this->throwJSONError("El código ingresado es incorrecto"));
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
	    				$message = 'El usuario ingresado no es válido';
	    			}
	    			else{
	    				return new JsonModel($this->throwJSONError());
	    			}
	    			return new JsonModel($this->throwJSONError($message));
	    		}
	    	
	    	
	    	
	    	
	    }

    }
}


