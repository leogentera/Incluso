<?php
namespace MoodleApi\Model;

class MoodleStage
{
	public $id;
    public $name;
    public $section;
    public $challenges = array();

    //Using only in UserCourse
    public $stageStatus;
    public $stageProgress;
    public $firstTime;
    
    public function __construct($data)
    {
        $this->id         = (!empty($data['stageid'])) ? $data['stageid'] : null;
        $this->name       = (!empty($data['stage'])) ? $data['stage'] : null;    
        $this->section    = (!empty($data['section'])) ? $data['section'] : null;
        $this->firstTime  = (!empty($data['firsttime'])) ? $data['firsttime'] : null;
        $this->challenges = array();
    }

    public function setId($id){
        $this->id = $id;
    }

    public function setName($name){
        $this->name = $name;
    }

    public function setSection($section){
        $this->section = $section;
    }

    public function setChallenges($challenges){
        $this->challenges = $challenges;
    }

    public function setFirstTime($time){
        $this->firstTime = $time;
    }

    public function setStageStatus($status){
        $this->stageStatus = $status;
    }

    public function setStageProgress($progress){
        $this->stageProgress = $progress;
    }

    public function getId(){
        return $this->id;
    }

    public function getName(){
        return $this->name;
    }

    public function getSection(){
        return $this->section;
    }

    public function getChallenges(){
        return $this->challenges;
    }

    public function getFirstTime(){
        return $this->firstTime;
    }

    public function getStageStatus(){
        return $this->stageStatus;
    }

    public function getStageProgress(){
        return $this->stageProgress;
    }

    public function __toString(){
        $returnString = "{";
        $returnString.= '"id":'.$this->getId().',';
        $returnString.= '"name":"'.$this->getName().'",';
        $returnString.= '"section":'.$this->getSection().',';
        $returnString.= '"challenges":[';

        $quantityOfChallengues = count($this->getChallenges());

        for($x = 0; $x < $quantityOfChallengues; $x++){
            $returnString.= $this->challenges[$x]; 
            $returnString.= ",";
        }

        if($quantityOfChallengues > 0){
           $returnString.= $this->challenges[$quantityOfChallengues-1];
        }

        $returnString.= '],';
        $returnString.= '"stageStatus":';

        if($this->getStageStatus() == null){
            $returnString.= '"",';
        }else{
            $returnString.= $this->getStageStatus().',';
        }

        $returnString.= '"stageProgress":';

        if($this->getStageProgress() == null){
            $returnString.= '"",';
        }else{
            $returnString.= $this->getStageProgress().',';
        }

        $returnString.= '"firstTime":';

        if($this->getFirstTime() == null){
            $returnString.= '""';
        }else{
            $returnString.= $this->getFirstTime().'';            
        }
        
        $returnString.= "}";
        return $returnString;
    }
}