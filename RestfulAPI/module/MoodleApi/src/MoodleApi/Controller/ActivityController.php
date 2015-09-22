<?php
namespace MoodleApi\Controller;

use MoodleApi\Utilities\AbstractRestfulJsonController;
use Zend\View\Model\JsonModel;

use MoodleApi\Model\MoodleAssignment;
use MoodleApi\Model\MoodleForum;
use MoodleApi\Model\MoodleForumDiscussion;
use MoodleApi\Model\MoodleForumPost;
use MoodleApi\Model\MoodleLabel;
use MoodleApi\Model\MoodlePage;
use MoodleApi\Model\MoodleQuiz;
use MoodleApi\Model\MoodleResource;
use MoodleApi\Model\MoodleUrl;
use MoodleApi\Utilities\Stars;
use MoodleApi\Utilities\Notifications;
use Zend\Http\Header\Vary;

use Zend\Cache\StorageFactory;

class ActivityController extends AbstractRestfulJsonController {

    public function get($coursemoduleid){

        $activity = $this->getIdAndTypeOfActivity($coursemoduleid);

        if(array_key_exists("error", $activity)){
            return new JsonModel($this->throwJSONError("Actividad invalida, Contacte al administrador"));
        }

        $activityid = $activity->id;
        $typeOfActivity = $activity->name;

        switch($typeOfActivity){
            
            case 'assign':
                return $this->getAssignment($activityid);
                break;

            case 'forum':
                return $this->getForum($activityid);
                break;

            case 'label':
                return $this->getLabel($activityid);
                break;

            case 'page':
                return $this->getPage($activityid);
                break;

            case 'quiz':
                return $this->getQuiz($activityid);
                break;

            case 'url':
                return $this->getUrl($activityid);
                break;

            case 'resource':
                return $this->getResource($activityid, $coursemoduleid);
                break;

            default:
                return new JsonModel($this->throwJSONError("Actividad no soportada, Contacte al administrador"));
                break;
        }
    }

    public function getList(){

        if(array_key_exists("courseid", $_GET)){
            return new JsonModel($this->throwJSONError("Curso invÃ¡lido, Contacte al administrador"));
        }

        $courseid = $_GET["course"];

        $url = $this->getConfig()['MOODLE_API_URL'].'&courseid=%s';
        $url = sprintf($url, $this->getToken(), "get_all_activities_by_course", $courseid);

        $response = file_get_contents($url);
    
        $json = json_decode($response,true);
    
        if (strpos($response, "exception") !== false){
           return new JsonModel($this->throwJSONError("Ocurrio un error al listar las actividades, contacte al administrador"));
        }

        if(count($json) == 0){
            return new JsonModel();
        }
        
        return new JsonModel((array)$json);
    }

    private function getIdAndTypeOfActivity($coursemoduleid){
        

        $cache = StorageFactory::factory(array(
            'adapter' => array(
                'name' => 'filesystem',
                'options' => array(
                    'ttl' => 86400,
                    'cache_dir' => __DIR__."\..\Cache"),
            ),
            'plugins' => array(
                // Don't throw exceptions on cache errors
                'exception_handler' => array(
                    'throw_exceptions' => false
                ),
            )
        ));

        $key = 'activity_info_'.$coursemoduleid;

         // see if a cache already exists:
        $activity = $cache->getItem($key, $success);

        if (!$success) {
         error_log("Without cache for resource");
            // cache miss
            $url = $this->getConfig()['MOODLE_API_URL'].'&coursemoduleid=%s';
            $url = sprintf($url, $this->getToken(), "get_activity_id_and_name", $coursemoduleid);

            $response = file_get_contents($url);
            $activity = json_decode($response,true);
    
            if (strpos($response, "exception") !== false){
                return array("error" => $response);
            }

            if(count($activity) == 0){
                return array("error" => "Activity not found");
            }

            $activityCache = $response;
            $cache->setItem($key, $activityCache);

        }else{
            error_log("Using cache for resource");
            $activity = json_decode($activity, true);

        }
        return new JsonModel((array)$activity[0]);
    }

    private function getAssignment($id){
        $url = $this->getConfig()['MOODLE_API_URL'].'&assignmentid=%s';
        $url = sprintf($url, $this->getToken(), "get_assignment", $id);

        $response = file_get_contents($url);
    
        $json = json_decode($response,true);
    
        if (strpos($response, "exception") !== false){
            return array();
        }
       
        $assignment = new MoodleAssignment($json[0]);
        
        return new JsonModel((array)$assignment);
    }

    private function getForum($id){

        try {


            $url = $this->getConfig()['MOODLE_API_URL'].'&discussionid=%s';

            $url = sprintf($url, $this->getToken(), "get_forum_discussion_posts", $id);

            $response = file_get_contents($url);

            $json = json_decode($response,true);

            if (strpos($response, "exception") !== false)
            {
        
                return new JsonModel(array());
            }

            return new JsonModel((array)json_decode($response));
        } 
        catch (Exception $e) {
            return new JsonModel($this->throwJSONError($e));
        }

        return new JsonModel(array());
    }

    private function getLabel($id){
        $url = $this->getConfig()['MOODLE_API_URL'].'&labelid=%s';
        $url = sprintf($url, $this->getToken(), "get_label", $id);

        $response = file_get_contents($url);
    
        $json = json_decode($response,true);
    
        if (strpos($response, "exception") !== false){
            return array();
        }
       
        $label = new MoodleLabel($json[0]);
        
        return new JsonModel((array)$label);
    }

    private function getPage($id){
        $url = $this->getConfig()['MOODLE_API_URL'].'&pageid=%s';
        $url = sprintf($url, $this->getToken(), "get_page", $id);

        $response = file_get_contents($url);
    
        $json = json_decode($response,true);
    
        if (strpos($response, "exception") !== false){
            return array();
        }
       
        $page = new MoodlePage($json[0]);
        
        return new JsonModel((array)$page);
    }

    private function getQuiz($id){
    	
    	if (key_exists('userid', $_GET)){
    		$url = $this->getConfig()['MOODLE_API_URL'].'&quizid=%s&userid=%s';
    		$url = sprintf($url, $this->getToken(), "get_quiz_result", $id,$_GET['userid'] );
    	}
    	else{
    		$url = $this->getConfig()['MOODLE_API_URL'].'&quizid=%s';
    		$url = sprintf($url, $this->getToken(), "get_quiz", $id);
    	}

        $response = file_get_contents($url);
    
        $json = json_decode($response,true);
    
        if (strpos($response, "exception") !== false){
            return array();
        }
        
        if (count($json)==0){
        	return new MoodleQuiz(array());
        }
        
        $quiz = new MoodleQuiz($json[0]);
        
        return new JsonModel((array)$quiz);
    }

    private function getUrl($id){
        $cache = StorageFactory::factory(array(
            'adapter' => array(
                'name' => 'filesystem',
                'options' => array(
                    'ttl' => 86400,
                    'cache_dir' => __DIR__."\..\Cache"),
            ),
            'plugins' => array(
                // Don't throw exceptions on cache errors
                'exception_handler' => array(
                    'throw_exceptions' => false
                ),
            )
        ));

        $key = 'url_'.$id;

         // see if a cache already exists:
        $url_resource = $cache->getItem($key, $success);

        if (!$success) {
         error_log("Without cache for url");
            // cache miss
            $url = $this->getConfig()['MOODLE_API_URL'].'&urlid=%s';
            $url = sprintf($url, $this->getToken(), "get_url", $id);

            $response = file_get_contents($url);

            if (strpos($response, "exception") !== false){
                return array();
            }

            $url_resource = $response;
            $cache->setItem($key, $url_resource);
            $url_resource = json_decode($response,true);

        }else{
            error_log("Using cache for resource");
            $url_resource = json_decode($url_resource, true);

        }

        $url = new MoodleUrl($url_resource[0]);

        return new JsonModel((array)$url);
    }

    private function getActivitySummary($instanceid, $typeOfActivity){
        $url = $this->getConfig()['MOODLE_API_URL'].'&instanceid=%s&typeOfActivity=%s';
        $url = sprintf($url, $this->getToken(), "get_activity_summary", $instanceid, $typeOfActivity);

        $response = file_get_contents($url);

        $json = json_decode($response,true);
        if (strpos($response, "exception") !== false){
            return array();
        }

        return new JsonModel($json[0]);   
    }

    private function getTreeDiscussion($posts){
        
        $new = array();

        foreach ($posts as $a){
            $new[$a['parent']][] = new MoodleForumPost($a);
        }

        $tree = $this->createTree($new, $new[0]); // changed

        return $tree;
    }

    private function createTree(&$list, $parent){
        $tree = array();

        foreach ($parent as $k=>$l){
            if(isset($list[$l->id])){
                $l->replies = $this->createTree($list, $list[$l->id]);
            }

            $tree[] = $l;
        } 
        
        return $tree;
    }
    
    public function update($id, $data){
    	$STARTED=0;
    	$COMPLETED=1;
    	if ($data==null){
    		return new JsonModel($this->throwJSONError("Ocurrio un error, contacte al administrador"));
    	}
    	
    	if (!key_exists("userid", $data)){
    		return new JsonModel($this->throwJSONError("Ocurrio un error, contacte al administrador"));
    	}
    	
    	if (key_exists('updatetype', $data)){
    		if ($data['updatetype']==$STARTED){
    			return $this->setActivityAsStarted($id, $data);
    			
    		}
    		elseif ($data['updatetype']==$COMPLETED){
    			return $this->setActivityData($id, $data);
    		}
    		else{
    			return new JsonModel($this->throwJSONError("Ocurrio un error, accion no valida"));
    		}
    	}
    	else{
    		return $this->setActivityData($id, $data);
    	}
    	
    	
    }
    
    public function setActivityData($id, $data){
    	
    	 
    	$activity = $this->getIdAndTypeOfActivity($id);
    	
    	if(array_key_exists("error", $activity)){
    		return new JsonModel($this->throwJSONError("Actividad inválida, Contacte al administrador"));
    	}
    	 
    	$dateissued=time();
    	if (array_key_exists('dateissued', $data)){
    		$dateissued=$data['dateissued'];
    		$dateissued=strtotime($dateissued);
    	}
    	 
    	$activityid = $activity->id;
    	$typeOfActivity = $activity->name;
    	$message="Por terminar la actividad";
    	switch($typeOfActivity){
    	
    		case 'assign':
    			break;
    	
    		case 'forum':
    			break;
    	
    		case 'label':
    			break;
    	
    		case 'page':
    			break;
    	
    		case 'url':
    			$message="Por haber visto el link, video o imagen";
    			break;
    			 
    		case 'quiz':
    			$message="Por haber contestado el quiz";
    			 
    			$dateStart=time();
    			if (array_key_exists('dateStart', $data)){
    				$dateStart=$data['dateStart'];
    				$dateStart=strtotime($dateStart);
    			}
    			 
    			$dateEnd=time();
    			if (array_key_exists('dateEnd', $data)){
    				$dateEnd=$data['dateEnd'];
    				$dateEnd=strtotime($dateEnd);
    			}
    			 
    			$response= $this->saveQuiz($activityid, $data["userid"], $data["answers"],$dateStart, $dateEnd, $id);
    			if($response['message']!=""){
    				 
    				return new JsonModel($this->throwJSONError("Ocurrió un error al guardar la información, Contacte al administrador"));
    			}
    			break;
			case 'resource' :
				break;
    	
    		default:
    			return new JsonModel($this->throwJSONError("Actividad no soportada, Contacte al administrador"));
    			break;
    	}
    	if(array_key_exists('message', $data)){
    		$message=$data['message'];
    	}
    	 
    	 
    	//Removed... Front end will do this for me
    	//     	$result =$this->setStars($id,  $data["userid"], $message, $dateissued );
    	 
    	//     	if ($result!=null){
    	//     		return $result;
    	//     	}
    	 
    	//     	$result =Notifications::setNotification($this, $data['userid'], $data['activityidnumber'], Notifications::$END, $dateissued);
    	 
    	//     	if ($result!=null){
    	//     		return $result;
    	//     	}
    	
    	if (array_key_exists('like_status', $data)){
    		$result =$this->like($data["userid"],$id,  $data["like_status"],$dateissued );
    	
    		if ($result!=null){
    			return $result;
    		}
    	}
    	 
    	return $this->setActivityAsCompletedAsJSONModel($id);
    }
    
    public function setActivityAsStarted($id, $data){
    	$url = $this->getConfig()['MOODLE_API_URL'].'&userid=%s&moduleid=%s&datestarted=%s';
    	
    	$datestarted=time();
    	if (array_key_exists('datestarted', $data)){
    		$datestarted=$data['datestarted'];
    		$datestarted=strtotime($datestarted);
    	}
    	
    	$url = sprintf($url, $this->getToken(), "set_activity_as_started", $data["userid"], $id, $datestarted);
    	$response = file_get_contents($url);
    	
    	if (strpos($response, "exception") !== false){
    		return new JsonModel($this->throwJSONError("Ocurrió un error al establecer la actividad como iniciada"));
    	}
    	 
    	 
    	return new JsonModel(array());
    }
    private function saveQuiz($activityid, $userid, $answers, $dateStart, $dateEnd, $modulecourseid){
    	$url = $this->getConfig()['MOODLE_API_URL'].'&quizid=%s&userid=%s&datestart=%s&datefinish=%s';
    	
    	$i=0;
    	$answers_string="";
    	
    	//For each regular answer
        	foreach($answers as $answer){
    		if (is_array($answer)){
    			$j=0;
    			$tmp="";
    			$isMultioption=false;
    			foreach($answer as $choice){
    				//If it is a multichoice option...
    				if ((is_string($choice) && $j==0)|| $isMultioption){
    					$isMultioption=true;
    					if ($j==0){
    						$answers_string.= "&answers[$i]=";
    					}
    					else{
    						$tmp.= "\n";
    					}
    					$tmp.= $choice;
    				}
    				else{
    					if ($j==0){
    						$answers_string.= "&answers[$i]=";
    						$tmp.="[";
    					}
    					else{
    						$tmp.= ",";
    					}
    					
    					$tmp.= $choice;
    				}
    				
    				$j++;
    			}
    			if (!$isMultioption){
    				$tmp.= "]";
    			}	
    			else{
    				$tmp=urlencode($tmp);
    			}
    			$answers_string.=$tmp;//urlencode($tmp);
    		}
    		else{
    			if ( is_string($answer)){
    				$answer= urlencode($answer);
    			}
    			$answers_string.= "&answers[$i]=$answer";
    		}
    		
    		$i++;
    	}
    	$url = sprintf($url, $this->getToken(), "save_quiz", $activityid, $userid, $dateStart, $dateEnd);
    	 
    	$url.= $answers_string;
    	
    	$response = file_get_contents($url);
    	
    	$json = json_decode($response,true);
    	if($json['message']!=""){
    		return $json;
    	}
    	 
    	//If there isn't a problem, lets update the activity as completed
    	    	
    	return $json;
    }
    
    public function setActivityAsCompleted($modulecourseid){
    	$url = $this->getConfig()['MOODLE_API_URL'].'&cmid=%s&completed=%s';
    	 
    	$url = sprintf($url, $this->getToken(), "core_completion_update_activity_completion_status_manually", $modulecourseid, 1);
    	
    	$response = file_get_contents($url);
    	
    	return $response;
    }
    
    public function setActivityAsCompletedAsJSONModel($modulecourseid){
    	$response=$this->setActivityAsCompleted($modulecourseid);
    	if (strpos($response, "exception") !== false){
    		return new JsonModel($this->throwJSONError("Ocurrió un error al establecer la actividad como completada"));
    	}
    	
    	
    	return new JsonModel(array());
    }
    
    public function setStars($modulecourseid, $userid, $message, $dateissued){
    	
    	$url = $this->getConfig()['MOODLE_API_URL'].'&moduleid=%s';
    	
    	$url = sprintf($url, $this->getToken(), "get_stars_per_module", $modulecourseid);
    	$response = file_get_contents($url);;
    	
    	if (strpos($response, "exception") !== false){
    		return new JsonModel($this->throwJSONError("Ocurrió un error al establecer puntos"));
    	}
    	$json= json_decode($response);
    	$stars=$json->points;
    	
    	if (Stars::addStars($this, $userid, $stars,$message, $modulecourseid, Stars::$MODULE, $dateissued  )==-1){
    		
    		return new JsonModel($this->throwJSONError("Ocurrió un error al establecer puntos"));
    	}
    	
    	//return new JsonModel(array());
    	
    }
    
    //Notifications are no longer managed by the server
//     public function create($data){
//     	if (!key_exists("userid", $data)){
//     		return new JsonModel($this->throwJSONError("Ocurrio un error, contacte al administrador"));
//     	}
//     	if (!key_exists("activityidnumber", $data)){
//     		return new JsonModel($this->throwJSONError("Ocurrio un error, contacte al administrador"));
//     	}
    	 
//     	$dateissued=time();
//     	if (array_key_exists('dateissued', $data)){
//     		$dateissued=$data['dateissued'];
//     	}
    
//     	$result =Notifications::setNotification($this,$data['userid'] , $data['activityidnumber'], Notifications::$START, $dateissued);
    	 
//     	if ($result!=null){
//     		return $result;
//     	}
//     	return new JsonModel(array());
//     }

    public function like($userid, $moduleid, $like_status, $dateissued){
    	$url = $this->getConfig()['MOODLE_API_URL'].'&userid=%s&moduleid=%s&like_status=%s&dateissued=%s';
    	
    	
    	$url = sprintf($url, $this->getToken(), "set_activity_like", $userid, $moduleid, $like_status, $dateissued);
    	
    	$response = file_get_contents($url);
    	$json = json_decode($response,true);
    	
    	if (strpos($response, "exception") !== false)
    	{
    		return new JsonModel( $this->throwJSONError("Ocurrio un error al establecer el me gusta"));
    	}
    	
    	if (!$json['success'])
    	{
    		return new JsonModel( $this->throwJSONError("No se pudo establecer el me gusta"));
    	}
    }

    private function getResource($resourceid, $coursemoduleid){

        $cache = StorageFactory::factory(array(
            'adapter' => array(
                'name' => 'filesystem',
                'options' => array(
                    'ttl' => 86400,
                    'cache_dir' => __DIR__."\..\Cache"),
            ),
            'plugins' => array(
                // Don't throw exceptions on cache errors
                'exception_handler' => array(
                    'throw_exceptions' => false
                ),
            )
        ));

        $key = 'resource_'.$resourceid.'_'.$coursemoduleid;

         // see if a cache already exists:
        $resource = $cache->getItem($key, $success);

        if (!$success) {
         error_log("Without cache for resource");
            // cache miss
            $url = $this->getConfig()['MOODLE_API_URL'].'&resourceid=%s&coursemoduleid=%s';

            $url = sprintf($url, $this->getToken(), "get_resourse_info", $resourceid, $coursemoduleid);
            
            $response = file_get_contents($url);
            $json = json_decode($response,true);
            
            if (strpos($response, "exception") !== false || !$json['fileurl']){
                return new JsonModel( $this->throwJSONError("Ocurrió un error al obtener los datos de la actividad"));
            }

            $resource = $json;
            $cache->setItem($key, $response);

        }else{
            error_log("Using cache for resource");
            $resource = json_decode($resource);

        }

        return new JsonModel((array)$resource);

    }
    
}




