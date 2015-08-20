<?php
namespace MoodleApi\Controller;

use MoodleApi\Utilities\AbstractRestfulJsonController;
use Zend\View\Model\JsonModel;

class ForumController extends AbstractRestfulJsonController {

    public function create($data){

        if( !array_key_exists("discussionid", $data) ||
            !array_key_exists("parentid", $data)     ||
            !array_key_exists("message", $data)      ||
            !array_key_exists("createdtime", $data)  ||
            !array_key_exists("modifiedtime", $data) ||
            !array_key_exists("posttype", $data)){
            return new JsonModel($this->throwJSONError("Parámetros inválidos, Contacte al administrador"));
        }

        $discussionid = $data["discussionid"];
        $parentid = $data["parentid"];
        $message = urlencode($data["message"]);
        $createdtime = strtotime($data["createdtime"]);
        $modifiedtime = strtotime($data["modifiedtime"]);
        $posttype = $data["posttype"];

        $url = $this->getConfig()['MOODLE_API_URL'].'&discussionid=%s&parentid=%s&createdtime=%s&modifiedtime=%s&posttype=%s&message=%s';
        $url = sprintf(
                    $url, 
                    $this->getToken(), 
                    "create_forum_discussion_post",
                    $discussionid,
                    $parentid,
                    $createdtime,
                    $modifiedtime,
                    $posttype,
                    $message);

        $response = file_get_contents($url);
    
        $json = json_decode($response,true);
    
        if (strpos($response, "exception") !== false){
            return new JsonModel($this->throwJSONError("Ocurrió un error al ejecutar la acción. Contacte al administrador"));
        }

        if(!$json["result"]){
          return new JsonModel($this->throwJSONError("Ocurrió un error al ejecutar la acción. Contacte al administrador"));  
        }

        return new JsonModel($json);
    }
}

?>