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

    public function getLeaderboard(){
    	return $this->leaderboard;
    }

    public function getStages(){
    	return $this->stages;
    }

    public function __toString(){
    	$returnString = "{";
    	$returnString.= '"leaderboard":[';

    	$quantityOfleaders = count($this->getLeaderboard());

    	for($x = 0; $x < $quantityOfleaders; $x++){
    		$returnString.= $this->leaderboard[$x]; 
    		$returnString.= ",";
    	}

        if($quantityOfleaders > 0){
    	   $returnString.= $this->leaderboard[$quantityOfleaders-1];
        }

    	$returnString.= '],';
    	$returnString.= '"stages":[';

        $quantityOfStages = count($this->getStages());
        for($x = 0; $x < $quantityOfStages; $x++){
            $returnString.= $this->stages[$x]; 
            $returnString.= ",";
        }

        if($quantityOfStages > 0){
           $returnString.= $this->stages[$quantityOfStages-1];
        }

    	$returnString.= ']';
    	$returnString.= "}";

    	return $returnString;
    }
}