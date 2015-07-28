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
        $this->id      = (!empty($data['stageid'])) ? $data['stageid'] : null;
        $this->name    = (!empty($data['stage'])) ? $data['stage'] : null;    
        $this->section = (!empty($data['section'])) ? $data['section'] : null;    
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
}