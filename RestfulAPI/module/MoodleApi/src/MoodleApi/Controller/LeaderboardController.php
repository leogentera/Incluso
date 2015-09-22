<?php
namespace MoodleApi\Controller;

use MoodleApi\Utilities\AbstractRestfulJsonController;
use MoodleApi\Model\MoodleLeader;

use Zend\View\Model\JsonModel;

class LeaderboardController extends AbstractRestfulJsonController {

    public function get($course){
        $url = $this->getConfig()['MOODLE_API_URL'].'&amount=3&courseid=%s';
        $url = sprintf($url, $this->getToken(), "get_leaderboard", $course);

        $response = file_get_contents($url);
    
        $json = json_decode($response,true);
    
        if (strpos($response, "exception") !== false){
            return array();
        }

        return new JsonModel((array)$json);
    }
}

?>