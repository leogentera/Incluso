<?php
namespace MoodleApi\Model;

class MoodleLeader
{
	public $userId;
    public $rank;
    public $fullname;
    public $progress=0;
    public $stars=-1;
    
    public function __construct($data)
    {
    	//var_dump($data);
        $this->userId   = (!empty($data['id'])) ? $data['id'] : null;
        $this->fullname = (!empty($data['name'])) ? $data['name'] : null;
        $this->rank     = (!empty($data['place'])) ? $data['place'] : null;
        $this->stars    = (!empty($data['stars'])) ? $data['stars'] : 0;
        $this->progress = (!empty($data['percentage_completed'])) ? $data['percentage_completed'] : 0;
        
    }

    public function getUserId(){
        return $this->userId;
    }

    public function getRank(){
        return $this->rank;
    }

    public function getFullname(){
        return $this->fullname;
    }

    public function getProgress(){
        return $this->progress;
    }

    public function getStars(){
        return $this->stars();
    }

    public function __toString(){
        $returnString = "{";
        $returnString.= '"userId":'.$this->getUserId().",";
        $returnString.= '"rank":'.$this->getRank().",";
        $returnString.= '"fullname":"'.$this->getFullname().'",';
        $returnString.= '"progress":'.$this->getProgress().",";
        $returnString.= '"stars":'.$this->getStars();
        $returnString.= "}";

        return $returnString;
    }
}