<?php
namespace MoodleApi\Model;

class MoodleActivity
{
	public $id;
    public $name;
    public $description;
    public $activityType;
    public $coursemoduleid;

    //Using only in UserCourse
    public $status;
    public $timemodified;
    
    public function __construct($data)
    {
        $this->id             = (!empty($data['instance'])) ? $data['instance'] : null;
        $this->activityType   = (!empty($data['name'])) ? $data['name'] : null;
        $this->coursemoduleid = (!empty($data['coursemoduleid'])) ? $data['coursemoduleid'] : null;
    
    }

    public function setId($id){
        $this->id = $id;
    }

    public function setName($name){
        $this->name = $name;
    }

    public function setDescription($description){
    	$this->description = $description;
    }

    public function setActivityType($activityType){
        $this->activityType = $activityType;
    }

    public function setCourseModuleId($courseModuleId){
        $this->coursemoduleid = $courseModuleId;
    }

    public function setStatus($status){
    	$this->status = $status;
    }

    public function setTimeModified($time){
        $this->timemodified = $time;
    }

    public function getId(){
        return $this->id;
    }

    public function getName(){
        return $this->name;
    }

    public function getDescription(){
        return $this->description;
    }

    public function getActivityType(){
        return $this->activityType;
    }

    public function getCourseModuleId(){
        return $this->coursemoduleid;
    }

    public function getStatus(){
        return $this->status;
    }

    public function getTimeModified(){
        return $this->timemodified;
    }

    public function __toString(){
        $returnString = '{';
        $returnString.= '"id":'.$this->getId().',';
        $returnString.= '"name":"'.$this->getName().'",';
        $returnString.= '"description":"'.$this->getDescription().'",';
        $returnString.= '"activityType":"'.$this->getActivityType().'",';
        $returnString.= '"coursemoduleid":'.$this->getCourseModuleId().',';
        $returnString.= '"status":';

        if($this->getStatus() == null){
            $returnString.= '"",';
        }else{
            $returnString.= $this->getStatus().',';
        }

        $returnString.= '"timemodified":';

        if($this->getTimeModified() == null){
            $returnString.= '""';
        }else{
            $returnString.=$this->getTimeModified().'';
        }

        $returnString.= '}';
        return $returnString;
    }
}