<?php
namespace MoodleApi\Controller;

use MoodleApi\Utilities\AbstractRestfulJsonController;
use Zend\View\Model\JsonModel;
use MoodleApi\Model\MoodleException;

class MessagingController extends AbstractRestfulJsonController {

	public function update($id, $data){
		
		if (!key_exists('messagetext', $data)){
			return new JsonModel( $this->throwJSONError("Ocurrio un error, contacte al administrador"));
		}
		
		$message=$data['messagetext'];
		
		
		if (!key_exists('messagedate', $data)){
			return new JsonModel( $this->throwJSONError("Ocurrio un error, contacte al administrador"));
		}
		$timecreated=$data['messagedate'];
		
		
		$timecreated=strtotime($timecreated);
		$url = $this->getConfig()['MOODLE_API_URL'].'&userid=%s&message=%s&timecreated=%s';
		
		
		$url = sprintf($url, $this->getToken(), "send_message", $id, urlencode($message), $timecreated);
		
		$response = file_get_contents($url);
		$json = json_decode($response,true);
		
		if (strpos($response, "exception") !== false)
		{
			return new JsonModel( $this->throwJSONError("Ocurrio un error al enviar el mensaje"));
		}
	
		if (!$json['send'])
		{
			return new JsonModel( $this->throwJSONError("No se pudo enviar el mensaje"));
		}
		
		
		return new JsonModel ( array () );
		
	}
	
	public function get($id){
		$timecreated=-1;
		if (key_exists('messagedate', $_GET)){
			$timecreated=$_GET['messagedate'];
			$timecreated=strtotime($timecreated);
		}
		
		$url = $this->getConfig()['MOODLE_API_URL'].'&userid=%s&timecreated=%s';
		$url = sprintf($url, $this->getToken(), "get_messages", $id, $timecreated);
		$response = file_get_contents($url);
		$json = json_decode($response,true);
		if (strpos($response, "exception") !== false)
		{
			return new JsonModel( $this->throwJSONError("Ocurrio un error al enviar el mensaje"));
		}
		
		
		return new JsonModel ($json );
	}

  
}




