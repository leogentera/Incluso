<?php

namespace MoodleApi\Controller;

use MoodleApi\Utilities\AbstractRestfulJsonController;
use MoodleApi\Utilities\SMTPClient;
use Zend\View\Model\JsonModel;
use MoodleApi\Model\MoodleCourse;
use MoodleApi\Model\MoodleException;
use Zend\Mail;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mail\Message;

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
                return $this->forgotPassword($data);
                break;
            case 'logout':
                return $this->logout($data);
                break;
            default:
                return $this->authentication($data);
                break;
        }

    }
	
}

// Action used for PUT requests
public function replaceList($data){

    return $this->resetPassword($data);
}

private function authentication($data){
	
	$url = $this->getConfig()['MOODLE_API_URL'].'&field=username&values[0]=%s';
	$url = sprintf($url, $this->getToken(), "core_user_get_users_by_field", $data['username']);
	 
	$response = file_get_contents($url);
	$json_user = json_decode($response,true);
	 
	if (strpos($response, "exception") !== false || count($json_user)==0 )
	{
		return new JsonModel( $this->throwJSONError("Verifique el usuario", 401));
	}
	
	
	for($i=0;count($json_user[0]['customfields'])>$i;$i++){
    			$customFields[$json_user[0]['customfields'][$i]['name']]=$json_user[0]['customfields'][$i]['value'];
    }
	
	$authenticationExpiration='';
	if (key_exists('authenticationExpiration', $customFields)){
		$authenticationExpiration=$customFields['authenticationExpiration'];
	}
	 
	if  ($authenticationExpiration!='' && $authenticationExpiration>round(microtime(true) * 1000)) {
		return new JsonModel( $this->throwJSONError("Ha superado la cantidad de intentos para ingresar al sistema, intente en una hora", 401));
	}

    $url = $this->getConfig()['TOKEN_GENERATION_URL'];
    $url = sprintf($url, $data['username'], $data['password'], $this->getConfig()['MOODLE_SERVICE_NAME']);

    $response = file_get_contents($url);
    $json = json_decode($response,true);
    if (strpos($response, "error") !== false)
    {
    	$currentTries=0;
    	$authenticationFirstTryDate='';
    	if (key_exists('authenticationTries', $customFields)){
    		$currentTries=$customFields['authenticationTries'];
    	}
    	
    	if (key_exists('authenticationFirstTryDate', $customFields)){
    		$authenticationFirstTryDate=$customFields['authenticationFirstTryDate'];
    	}
    	
    	
    	$this->saveAuthenticationTries($json_user[0]['id'], $currentTries, $authenticationFirstTryDate );
    	
        return new JsonModel ($this->throwJSONError("Verifique usuario y contrase�a", 401));
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

    		$codeexpirationdate=round(microtime(true) * 1000)+1800000;
    		
    		$id=$json[0]['id'];
    		
	    	for($i=0;count($json[0]['customfields'])>$i;$i++){
	        	$customFields[$json[0]['customfields'][$i]['name']]=$json[0]['customfields'][$i]['value'];
	        }
	        
	        $passwordRecoveryExpiration='';
	        if (key_exists('passwordRecoveryExpiration', $customFields)){
	        	$passwordRecoveryExpiration=$customFields['passwordRecoveryExpiration'];
	        }
	        
	        if  ($passwordRecoveryExpiration!='' && $passwordRecoveryExpiration>round(microtime(true) * 1000)) {
	        	return new JsonModel( $this->throwJSONError("Ha superado la cantidad de intentos para restablecer un password, intente en una hora"));
	        }
	        
	        if ($customFields["secretquestion"]!=$data["secretquestion"] || $customFields["secretanswer"]!=$data["secretanswer"]){
	        	//We save the tries
	        	$currentTries=0;
	        	$passwordRecoveryFirstTryDate='';
	        	if (key_exists('passwordRecoveryTries', $customFields)){
	        		$currentTries=$customFields['passwordRecoveryTries'];
	        	}
	        	
	        	if (key_exists('passwordRecoveryFirstTryDate', $customFields)){
	        		$passwordRecoveryFirstTryDate=$customFields['passwordRecoveryFirstTryDate'];
	        	}
	        	
	        	
	        	$this->saveRecoveryTries($id, $currentTries, $passwordRecoveryFirstTryDate,$data['email'] );
	        	
	        	return new JsonModel( $this->throwJSONError("La pregunta o la respuesta secreta son incorrectas"));
	        }
    		
    		$url = $this->getConfig()['MOODLE_API_URL'].'&users[0][id]=%s'.
    		'&users[0][customfields][0][type]=recoverycode&users[0][customfields][0][value]=%s'.
    		'&users[0][customfields][1][type]=codeexpirationdate&users[0][customfields][1][value]=%s';
    		 
    		$url = sprintf($url, $this->getToken(), "core_user_update_users", $id, $recoverycode, $codeexpirationdate);
    		 
    		$response = file_get_contents($url);
    		if ($response=="null"){
    			
    			
//     			$to =$data['email'];
//     			$from = 'desarrollo.definityfist@gmail.com';
//     			$subject = 'Reseteo de Contrase�a Incluso';
//     			$body = "Hola!, has indicado que has olvidado tu contrase�a y para ello tendras que ingresar el codigo $recoverycode en la aplicacion para continuar";
//     			//     	$SMTPMail = new SMTPClient ($SmtpServer, $SmtpPort, $SmtpUser, $SmtpPass, $from, $to, $subject, $body);
//     			//     	$SMTPChat = $SMTPMail->SendMail();
    			 
//     			//mail('caffeinated@example.com', 'My Subject', $message);
//     			$mail= new SMTPClient();
//     			$mail->SMTPClient ('mtymaildf-v05.sieenasoftware.com', 25, 'humberto.castaneda','Hh.83420676', 'humberto.castaneda@definityfirst.com', $data['email'], $subject, $body);
//     			var_dump($mail->SendMail ());
    			
    			//$mail= new SMTPClient();
    			//$mail->SMTPClient ('ssl://smtp.gmail.com', 465, 'desarrollo.definityfist@gmail.com','Admin123!', 'desarrollo.definityfist@gmail.com', $data['email'], "hello", "hola");
    			
    			
    			//Anterior codigo para enviar mails
//     			try {
//     				$transport = new SmtpTransport();
//     				$options   = new SmtpOptions(array(
//     						'name' => 'mtymaildf-v05.sieenasoftware.com',
//     						'host' => 'mail.definityfirst.com',
//     						'port' => 25,
//     						'connection_class'  => 'login',
//     						'connection_config' => array(
//     								'username' => 'gentera',
//     								'password' => 'An15b4r4',
//     								'ssl'      => 'tls',
//     						),
//     				));
//     				$transport->setOptions($options);
    				 
//     				$message = new Message();
    				 
//     				$message->addTo($data['email'])
//     				->addFrom('gentera@definityfirst.com')
//     				->setSubject('Reseteo de Contrase�a Incluso')
//     				->setBody("Hola!, has indicado que has olvidado tu contrase�a y para ello tendras que ingresar el codigo $recoverycode en la aplicacion para continuar");
//     				$transport->send($message);
//     			} catch (\Exception $e) {
//     				return new JsonModel( $this->throwJSONError("Ocurrio un error al momento de enviar el mail con el c�digo de confirmacion, contacte al administrador"));
//     			}

    				$this->sendEmail($data['email'],'Incluso - Recupera tu contrase�a',  "�Hola!\n\n Olvidaste tu contrase�a. Usa el c�digo $recoverycode para cambiarla por una nueva.");
    			
    			
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
    			return new JsonModel( $this->throwJSONError("No se ha solicitado un cambio de contrase�a"));
    		}
    		if($codeexpirationdate<round(microtime(true) * 1000)){
    			return new JsonModel( $this->throwJSONError("El c�digo ha expirado"));
    		}
    		if($recoverycode!=$data['recoverycode']){
    			return new JsonModel( $this->throwJSONError("El c�digo ingresado es incorrecto"));
    		}
    		
	    	$url = $this->getConfig()['MOODLE_API_URL'].'&users[0][password]=%s&users[0][id]=%s'.
    		'&users[0][customfields][0][type]=recoverycode&users[0][customfields][0][value]=%s'.
    		'&users[0][customfields][1][type]=codeexpirationdate&users[0][customfields][1][value]=%s';
	    	
	    	$url = sprintf($url, $this->getToken(), "core_user_update_users", $data['password'], $id, '', '');
	    	
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
	    				$message = 'El usuario ingresado no es v�lido';
	    			}
	    			else{
	    				return new JsonModel($this->throwJSONError());
	    			}
	    			return new JsonModel($this->throwJSONError($message));
	    		}
	    	
	    	
	    	
	    	
	    }

    }
    
    function saveRecoveryTries($id, $currentTries, $passwordRecoveryFirstTryDate, $email){
    	$isFirstTime=$currentTries==0;
    	$tries= $currentTries + 1;
    	$passwordRecoveryExpiration='';
    	
    	
    	if ($isFirstTime){
    		$passwordRecoveryFirstTryDate=round(microtime(true) * 1000);
    	}
     	elseif($passwordRecoveryFirstTryDate +14400000 < round(microtime(true) * 1000)){ //If 4 hours had passed
//    		elseif($passwordRecoveryFirstTryDate +60000 < round(microtime(true) * 1000)){ //If 4 hours had passed
    	
    		$passwordRecoveryFirstTryDate=round(microtime(true) * 1000); //we reset the code
    		$tries= 1;
    		$passwordRecoveryExpiration='';
    	}
    	
    	
    	if ($tries==3){
     		$passwordRecoveryExpiration=round(microtime(true) * 1000)+3600000 ;
//    		$passwordRecoveryExpiration=round(microtime(true) * 1000)+60000 ;
    		$tries='0';
    		$passwordRecoveryFirstTryDate='';
    		$this->sendMailToAdmins($email);
    	}
    	$url = $this->getConfig()['MOODLE_API_URL'].'&users[0][id]=%s'.
    			'&users[0][customfields][0][type]=passwordRecoveryTries&users[0][customfields][0][value]=%s'.
    		    '&users[0][customfields][1][type]=passwordRecoveryFirstTryDate&users[0][customfields][1][value]='.$passwordRecoveryFirstTryDate.
    		    '&users[0][customfields][2][type]=passwordRecoveryExpiration&users[0][customfields][2][value]='.$passwordRecoveryExpiration ;
    	 
    	
    	$url = sprintf($url, $this->getToken(), "core_user_update_users", $id, $tries);
    	 
    	$response = file_get_contents($url);
    	if ($response=="null"){
    		return "";
    	}
    	else{
    		return $response;
    	}
    }
    
    function sendEmail($sendTo,$subject,  $text){
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
    			
    		$message->addTo($sendTo)
    		->addFrom('gentera@definityfirst.com')
    		->setSubject($subject)
    		->setBody($text);
    		$transport->send($message);
    	} catch (\Exception $e) {
    		return $this->throwJSONError("Ocurrio un error al momento de enviar el mail, contacte al administrador");
    	}
    }
    
    function sendMailToAdmins($emailLocked){
    	$url = $this->getConfig()['MOODLE_API_URL'] ;
    	
    	 
    	$url = sprintf($url, $this->getToken(), "extra_service_get_admin_emails");
    	
    	$response = file_get_contents($url);
    	$json = json_decode($response,true);
    	if (strpos($response, "exception") !== false){
    		return $response;
    		
    	}
    		foreach ($json as $email){
    			$text= "El usuario $emailLocked intento en mas de 3 intentos en menos de 4 horas desbloquear su contrase�a ingresando una pregunta y respuesta secreta incorrecta";
    			$this->sendEmail($email['email'],'Bloqueo de Olvidar Contrase�a',  $text);
    			 
    		}
    	
    }

    private function logout($data){
        if(!array_key_exists("token", $data) || !array_key_exists("userid", $data)){
            return new JsonModel($this->throwJSONError("Par�metros inv�lidos"));
        }

        $url = $this->getConfig()['MOODLE_API_URL'].'&userid=%s&token=%s';
        $url = sprintf($url, $this->getToken(), "set_token_valid_time", $data["userid"], $data["token"]);

        $response = file_get_contents($url);
    
        $json = json_decode($response,true);
    
        if (strpos($response, "exception") !== false){
            return JsonModel(array());
        }
        
        return new JsonModel($json);
    }
    
    function saveAuthenticationTries($id, $currentTries, $authenticationFirstTryDate){
    	$isFirstTime=$currentTries==0;
    	$tries= $currentTries + 1;
    	$authenticationExpiration='';
    	 
    	 
    	if ($isFirstTime){
    		$authenticationFirstTryDate=round(microtime(true) * 1000);
    	}
    	elseif($passwordRecoveryFirstTryDate +14400000 < round(microtime(true) * 1000)){ //If 4 hours had passed
    	//elseif($authenticationFirstTryDate +60000 < round(microtime(true) * 1000)){ //If 4 hours had passed
    		 
    		$authenticationFirstTryDate=round(microtime(true) * 1000); //we reset the code
    		$tries= 1;
    		$authenticationExpiration='';
    	}
    	 
    	 
    	if ($tries==4){
    		$passwordRecoveryExpiration=round(microtime(true) * 1000)+3600000 ;
    		//    		$authenticationExpiration=round(microtime(true) * 1000)+60000 ;
    		$tries='0';
    		$authenticationFirstTryDate='';
    		//$this->sendMailToAdmins($email);
    	}
    	$url = $this->getConfig()['MOODLE_API_URL'].'&users[0][id]=%s'.
    			'&users[0][customfields][0][type]=authenticationTries&users[0][customfields][0][value]=%s'.
    			'&users[0][customfields][1][type]=authenticationFirstTryDate&users[0][customfields][1][value]='.$authenticationFirstTryDate.
    			'&users[0][customfields][2][type]=authenticationExpiration&users[0][customfields][2][value]='.$authenticationExpiration ;
    
    	 
    	$url = sprintf($url, $this->getToken(), "core_user_update_users", $id, $tries);
    
    	$response = file_get_contents($url);
    	if ($response=="null"){
    		return "";
    	}
    	else{
    		return $response;
    	}
    }
    
}


