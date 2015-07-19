<?php
namespace MoodleApi\Controller;

use MoodleApi\Utilities\AbstractRestfulJsonController;
use Zend\View\Model\JsonModel;
use MoodleApi\Model\MoodleUserProfile;
use MoodleApi\Model\MoodleException;
use MoodleApi\Model\MoodleApi\Model;
use MoodleApi\Model\MoodleBadge;
use MoodleApi\Model\MoodleMainDashboard;
use MoodleApi\Model\MoodleLeader;
use MoodleApi\Model\MoodleStage;

class MainDashboardController extends AbstractRestfulJsonController{
	

	private $token = "";
	private $function = "core_user_get_users_by_field";
	
	
	// Action used for GET requests with resource Id
	public function get($id)
	{
		
        $url = $this->getConfig()['MOODLE_API_URL'].'&field=id&values[0]=%s';
        $url = sprintf($url, $this->getToken(), $this->function, $id);

        $response = file_get_contents($url);
        
        $json = json_decode($response,true);

        $dashboard="";
        
		if (strpos($response, "exception") !== false) 
        {
            // Error
        	return new JsonModel($this->throwJSONError());
        }
            // Good
            $dashboard = new MoodleMainDashboard($json[0]);
            $dashboard->setRank($this->getRank($id));
            $dashboard->setLeaderboard($this->getLeaderboard());
            $dashboard->setProgress($this->getProgress($id));
            $dashboard->setFinishedStages($this->getFinishedStages($id));
            $dashboard->setAvalilableStages($this->getAvailableStages($id));
            
            return new JsonModel((array) $dashboard);

        
        return new JsonModel($dashboard);
    }
    
    
    public function getBadgesByMethod($id, $function)
    {
    
    	$url = $this->getConfig()['MOODLE_API_URL'].'&id=%s';
    	$url = sprintf($url, $this->getToken(), $function, $id);
    
    	$response = file_get_contents($url);
    
    	$json = json_decode($response,true);
    
    	if (strpos($response, "exception") !== false)
    	{
    		return array();
    	}
    		// Good
    		$badges= array();
    		
    		foreach($json as $badge){
    			$badge = new MoodleBadge($badge);
    			array_push($badges, $badge);
    		}
    		
    		return $badges;
    
    	
    }
    
    public function getRank($id)
    {
    
    	$url = $this->getConfig()['MOODLE_API_URL'].'&userid=%s';
    	$url = sprintf($url, $this->getToken(), "get_user_rank", $id);
    
    	$response = file_get_contents($url);
    
    	$json = json_decode($response,true);
    
    	if (strpos($response, "exception") !== false)
    	{
    		
    		return -1;
    	}
    	// Good
    	return $json[0]['place'];
    
    	 
    }
    
    public function getLeaderboard()
    {
    
    	$url = $this->getConfig()['MOODLE_API_URL'].'&amount=3';
    	$url = sprintf($url, $this->getToken(), "get_leaderboard");
    
    	$response = file_get_contents($url);
    
    	$json = json_decode($response,true);
    
    	if (strpos($response, "exception") !== false)
    	{
    
    		return array();
    	}
    	// Good
    	$leaders= array();
    	foreach($json as $leader){
    		$leader = new MoodleLeader($leader);
    		array_push($leaders, $leader);
    	}
    	return $leaders;
    
    
    }
    
    public function getProgress($userid)
    {
    
    	$url = $this->getConfig()['MOODLE_API_URL']."&userid=$userid";
    	$url = sprintf($url, $this->getToken(), "get_global_progress");
    
    	$response = file_get_contents($url);
    
    	$json = json_decode($response,true);
    
    	if (strpos($response, "exception") !== false)
    	{
    
    		return array();
    	}
    	// Good
    	if (count($json)==0){
    		return "-1";
    	}
    	$progress=$json[0]['percentage_completed'];
    	return $progress;
    
    
    }
    
    public function getFinishedStages($userid)
    {
    
    	$url = $this->getConfig()['MOODLE_API_URL']."&userid=$userid";
    	$url = sprintf($url, $this->getToken(), "get_finished_stages");
    
    	$response = file_get_contents($url);
    
    	$json = json_decode($response,true);
    
    	if (strpos($response, "exception") !== false)
    	{
    
    		return array();
    	}
    	// Good
    	$stages= array();
    	foreach($json as $stage){
    		$stage = new MoodleStage($stage);
    		array_push($stages, $stage);
    	}
    	return $stages;
    
    
    }

    public function getAvailableStages($userid)
    {
    
    	$url = $this->getConfig()['MOODLE_API_URL'];
    	$url = sprintf($url, $this->getToken(), "get_all_stages");

    	$response = file_get_contents($url);
    
    	$json = json_decode($response,true);
    
    	if (strpos($response, "exception") !== false)
    	{
    		return array();
    	}
    	// Good
    	$stages= array();
    	foreach($json as $stage){
    		$stage = new MoodleStage($stage);
    		array_push($stages, $stage);
    	}
    	return $stages;
    
    
    }
    
   
}