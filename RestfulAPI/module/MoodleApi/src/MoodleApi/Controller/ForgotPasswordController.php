<?php
namespace MoodleApi\Controller;

use MoodleApi\Utilities\AbstractRestfulJsonController;
use Zend\View\Model\JsonModel;

use MoodleApi\Model\MoodleCourse;
use MoodleApi\Model\MoodleException;
use Zend\Mail;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mail\Message;
class ForgotPasswordController extends AbstractRestfulJsonController {
	
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
    				->addFrom('humberto.castaneda@definityfirst.com')
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
    
    
}

class SMTPClient
{
	public $SmtpServer, $to, $subject, $body, $SmtpUser, $SmtpPass, $from, $SmtpPort, $PortSMTP;

	function SMTPClient ($SmtpServer, $SmtpPort, $SmtpUser, $SmtpPass, $from, $to, $subject, $body)
	{

		$this->SmtpServer = $SmtpServer;
		$this->SmtpUser = base64_encode ($SmtpUser);
		$this->SmtpPass = base64_encode ($SmtpPass);
		$this->from = $from;
		$this->to = $to;
		$this->subject = $subject;
		$this->body = $body;

		if ($SmtpPort == "")
		{
			$this->PortSMTP = 25;
		}
		else
		{
			$this->PortSMTP = $SmtpPort;
		}
	}

	function SendMail ()
	{
		if ($SMTPIN = fsockopen ($this->SmtpServer, $this->PortSMTP))
		{
			fputs ($SMTPIN, "EHLO ".$this->SmtpServer."\r\n");
			$talk["hello"] = fgets ( $SMTPIN, 1024 );
			fputs($SMTPIN, "auth login\r\n");
			$talk["res"]=fgets($SMTPIN,1024);
			fputs($SMTPIN, $this->SmtpUser."\r\n");
			$talk["user"]=fgets($SMTPIN,1024);
			fputs($SMTPIN, $this->SmtpPass."\r\n");
			$talk["pass"]=fgets($SMTPIN,256);
			fputs ($SMTPIN, "MAIL FROM: <".$this->from.">\r\n");
			$talk["From"] = fgets ( $SMTPIN, 1024 );
			fputs ($SMTPIN, "RCPT TO: <".$this->to.">\r\n");
			$talk["To"] = fgets ($SMTPIN, 1024);
			fputs($SMTPIN, "DATA\r\n");
			$talk["data"]=fgets( $SMTPIN,1024 );
			fputs($SMTPIN, "To: <".$this->to.">\r\nFrom: <".$this->from.">\r\nSubject:".$this->subject."\r\n\r\n\r\n".$this->body."\r\n.\r\n");
			$talk["send"]=fgets($SMTPIN,256);
			//CLOSE CONNECTION AND EXIT ...
			fputs ($SMTPIN, "QUIT\r\n");
			var_dump($SMTPIN);
			fclose($SMTPIN);
			//
		}
		return $talk;
	}
}



?>
