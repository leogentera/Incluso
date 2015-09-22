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
            !array_key_exists("posttype", $data)     ||
            !array_key_exists("userid", $data)){
            return new JsonModel($this->throwJSONError("Parámetros inválidos, Contacte al administrador"));
        }

        $fields = array(); 

        $fields["discussionid"] = $data["discussionid"];
        $fields["parentid"] = $data["parentid"];
        $fields["message"] = $data["message"];
        $fields["createdtime"] = strtotime($data["createdtime"]);
        $fields["modifiedtime"] = strtotime($data["modifiedtime"]);
        $fields["posttype"] = $data["posttype"];
        $fields["userid"] = $data["userid"];
        $fields["filename"] = "";
        $fields["filecontent"] = "";

        //Check if is a file
        if($fields["posttype"] == 4){
            if(empty($data["filename"]) || empty($data["filecontent"])){
                return new JsonModel($this->throwJSONError("Parámetros inválidos, Contacte al administrador"));
            }
            $message = "";
            $fields["filename"] = $data["filename"];
            $fields["filecontent"] = $data["filecontent"];
        }

        $fields["wstoken"] = $this->getToken();
        $fields["wsfunction"] = "create_forum_discussion_post";
        $fields["moodlewsrestformat"] = "json";
        
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

        return new JsonModel($json);
    }

    public function update($id, $data){
        //$id is the postid

        //check params
        if(!array_key_exists("userid", $data)){
            return new JsonModel($this->throwJSONError("Parámetros inválidos, Contacte al administrador"));
        }

        $userid = $data["userid"];

        $url = $this->getConfig()['MOODLE_API_URL'].'&postid=%s&userid=%s';
        $url = sprintf($url, $this->getToken(), "like_forum_discussion_post", $id, $userid);

        $response = file_get_contents($url);
    
        $json = json_decode($response,true);
    
        if (strpos($response, "exception") !== false){
            return array();
        }

        return new JsonModel((array)$json);
    }
}

?>