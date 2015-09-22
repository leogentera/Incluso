<?php
namespace MoodleApi\Controller;

use MoodleApi\Utilities\AbstractRestfulJsonController;
use Zend\View\Model\JsonModel;
use MoodleApi\Model\MoodleException;
use MoodleApi\Utilities\Stars;

class StarsController extends AbstractRestfulJsonController {

	public function update($userid, $data){
		//var_dump($data);
		if ($data==null){
			return new JsonModel($this->throwJSONError("Datos invalidos, contacte al administrador"));
		}
		
// 		if (!key_exists("userid", $data)){
// 			return new JsonModel($this->throwJSONError("Ocurrio un error, contacte al administrador"));
// 		}
		
		if (!key_exists("stars", $data)){
			return new JsonModel($this->throwJSONError("Parametros invalidos, contacte al administrador"));
		}
		
		$dateIssued = 0;
		if (key_exists ( "dateIssued", $data )) {
			$dateIssued = $data ['dateIssued'];
			$dateIssued=strtotime($dateIssued);
		}
		
		$message = "";
		if (key_exists ( "message", $data )) {
			$message = $data ['message'];
		}
		
		$instance=-1;
		if (key_exists ( "instance", $data )) {
			$instance = $data ['instance'];
		}
		
		$instancetype=Stars::$WEBAPP;
		if (key_exists ( "instancetype", $data )) {
			$instancetype = $data ['instancetype'];
		}
		
		if (Stars::addStars ( $this, $userid, $data ['stars'] , $message, $instance, $instancetype, $dateIssued) < 0) {
			return new JsonModel ( $this->throwJSONError ( "Ocurrió un error al establecer puntos" ) );
		}
		
		return new JsonModel ( array () );
		
	}

	function get($userid){
		$url =$this->getConfig()['MOODLE_API_URL'] .'&userid=%s';
		$url = sprintf($url, $this->getToken(), "get_stars_log",$userid);
		
		$response = file_get_contents($url);
		$json = json_decode($response,true);
		if (strpos($response, "exception") !== false)
		{
			return new JsonModel( $this->throwJSONError("El usuario no esta registrado"));
		}
		
		return new JsonModel ( $json );
	}
  
}




