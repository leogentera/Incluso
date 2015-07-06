<?php
namespace MoodleApi\Controller;

use MoodleApi\Utilities\AbstractRestfulJsonController;
use Zend\View\Model\JsonModel;
use MoodleApi\Model\MoodleCourse;
use MoodleApi\Model\MoodleException;
//use MoodleApi\Utilities\SMTPClient;
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
    		
    		$url = $this->getConfig()['MOODLE_API_URL'].'&users[0][id]=%s'.
    		'&users[0][customfields][0][type]=recoverycode&users[0][customfields][0][value]=%s'.
    		'&users[0][customfields][1][type]=codeexpirationdate&users[0][customfields][1][value]=%s';
    		 
    		$url = sprintf($url, $this->getToken(), $this->function, $id, $recoverycode, $codeexpirationdate);
    		 
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
