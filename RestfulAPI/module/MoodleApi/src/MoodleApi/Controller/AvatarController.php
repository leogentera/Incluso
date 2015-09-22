<?php
namespace MoodleApi\Controller;

use MoodleApi\Utilities\AbstractRestfulJsonController;
use Zend\View\Model\JsonModel;


class AvatarController  extends AbstractRestfulJsonController {
    
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

        if( !array_key_exists("userid", $data) ||
            !array_key_exists("filecontent", $data)){
            return new JsonModel($this->throwJSONError("Parámetros inválidos, Contacte al administrador"));
        }

        $fields = array(); 

        $fields["wstoken"] = $this->getToken();
        $fields["wsfunction"] = "upload_user_profile_image";
        $fields["moodlewsrestformat"] = "json";
        $fields["userid"] = $data["userid"];
        $fields["filecontent"] = $data["filecontent"];

        $data_string = http_build_query($fields);   

        $ch = curl_init($this->getConfig()['MOODLE_URL']."/webservice/rest/server.php");                                            
        curl_setopt($ch, CURLOPT_POST, true);                                                                     
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
                                                                                                                                                                                                                           
        $response = curl_exec($ch);

        $json = json_decode($response,true);

        if (strpos($response, "exception") !== false){
            return new JsonModel($this->throwJSONError("Ocurrió un error al ejecutar la acción. Contacte al administrador"));
        }

        if(!$json["result"]){
          return new JsonModel($this->throwJSONError("Ocurrió un error al ejecutar la acción. Contacte al administrador"));  
        }

        //registration
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

        //continue with logging activity
        $url = sprintf($url, $this->getToken(), "create_avatar_configuration",
        urlencode($data['userid']),
        'n/a',
        urlencode($data['aplicacion']),
        urlencode($data['estrellas']),
        urlencode($data['color_cabello']),
        urlencode($data['estilo_cabello']),
        urlencode($data['traje_color_principal']),
        urlencode($data['traje_color_secundario']),
        urlencode($data['rostro']),
        urlencode($data['color_de_piel']),
        'n/a',
        urlencode($data['imagen_recortada']),
        urlencode($data['ultima_modificacion'])
        ); 

        $response = file_get_contents($url);

        if (strpos($response, "exception") !== false){
            return new JsonModel($this->throwJSONError("Ocurrió un error al ejecutar la acción. Contacte al administrador"));
        }
                
        return  new JsonModel(array());

    }
}