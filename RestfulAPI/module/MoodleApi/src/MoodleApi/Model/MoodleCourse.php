<?php
namespace MoodleApi\Model;

class MoodleCourse
{
	public $leaderboard = array();
	public $stages = array();

	public function setLeaderboard($leaders){
    	$this->leaderboard=$leaders;
    }

    public function setStages($stages){
    	$this->stages = $stages;
    }

}