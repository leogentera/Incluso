<?php
namespace MoodleApi\Controller;

use MoodleApi\Utilities\AbstractRestfulJsonController;
use Zend\View\Model\JsonModel;
use MoodleApi\Utilities\Notifications;
use MoodleApi\Model\MoodleNotification;

class NotificationController extends AbstractRestfulJsonController {
    
    public function get($id){
        $url = $this->getConfig()['MOODLE_API_URL'].'&userid=%s';
        $url = sprintf($url, $this->getToken(), "get_all_notifications_by_user", $id);

        $response = file_get_contents($url);
    
        $json = json_decode($response,true);
    
        if (strpos($response, "exception") !== false){
           return new JsonModel($this->throwJSONError("Ocurrió un error al listar las notificaciones, contacte al administrador"));
        }

        if(count($json) == 0){
            return new JsonModel();
        }else{
            $response = array();

            foreach ($json as $row) {
                array_push($response, new MoodleNotification($row));
            }
        }
        
        return new JsonModel($response);

    }

    public function create($data){
        if( !array_key_exists("notificationid", $data) ||
            !array_key_exists("timemodified", $data)   ||
            !array_key_exists("userid", $data)         ||
            !array_key_exists("already_read", $data)
            ){

            return new JsonModel($this->throwJSONError("Parámetros inválidos. Contacte al administrador"));
        }

        $url = $this->getConfig()['MOODLE_API_URL'].'&notificationid=%s&timemodified=%s&userid=%s&already_read=%s';
        $url = sprintf( $url, 
                        $this->getToken(), 
                        "new_user_notification", 
                        $data["notificationid"],
                        strtotime($data["timemodified"]),
                        $data["userid"],
                        $data["already_read"]);

        $response = file_get_contents($url);
    
        $json = json_decode($response,true);
    
        if (strpos($response, "exception") !== false){
           return new JsonModel($this->throwJSONError("Ocurrió un error al guardar la notificación, contacte al administrador"));
        }

        return new JsonModel($json);
    }

    public function update($id, $data){
        if( !array_key_exists("notificationid", $data) ||
            !array_key_exists("userid", $data)         
            ){

            return new JsonModel($this->throwJSONError("Parámetros inválidos. Contacte al administrador"));
        }

        $url = $this->getConfig()['MOODLE_API_URL'].'&userid=%s&notificationid=%s&already_read=%s';
        $url = sprintf( $url, 
                        $this->getToken(), 
                        "update_user_notification", 
                        $data["userid"],
                        $data["notificationid"],
                        1);

        $response = file_get_contents($url);

        $json = json_decode($response,true);
    
        if (strpos($response, "exception") !== false){
           return new JsonModel($this->throwJSONError("Ocurrió un error al actualizar la notificación, contacte al administrador"));
        }

        return new JsonModel($json);
    }
    
    
}