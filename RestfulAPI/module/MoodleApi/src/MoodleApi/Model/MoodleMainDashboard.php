<?php
namespace MoodleApi\Model;
use MoodleApi\Model\Video;
use MoodleApi\Utilities\Common;

class MoodleMainDashboard extends Common
{
	public $progressPercentage=0;
	public $welcomeMessage;
	public $video;
	public $shield="";
	public $leaderboard=array();
	public $rank=-1;
	
   
    public function __construct($data)
    {
    	$this->welcomeMessage="Bienvenido ".$data['fullname'];
        $this->video=array(
        		new Video("http://techslides.com/demos/sample-videos/small.webm", "video/webm"), 
        		new Video("http://techslides.com/demos/sample-videos/small.ogv", "video/ogg"),
        		new Video("http://techslides.com/demos/sample-videos/small.mp4", "video/mp4"),
        		new Video("http://techslides.com/demos/sample-videos/small.3gp", "video/3gp")
        );
    }
    
    public function  setShield($shield){
    	$this->shield=$shield;
    }
    
    public function  setLeaderboard($leaders){
    	$this->leaderboard=$leaders;
    }
    
    public function  setRank($rank){
    	$this->rank=$rank;
    }
    
    public function  setProgress($progress){
    	$this->progress=$progress;
    }
}