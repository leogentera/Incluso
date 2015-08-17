<?php
namespace MoodleApi\Model;

class MoodleChallenge
{
	public $id;
    public $name;
    public $description;
    public $image;
    public $activityType;
    public $activities;
    
    //Using only in UserCourse
    public $status;

    public function __construct($data)
    {
        $this->id          = (!empty($data['sectionid'])) ? $data['sectionid'] : null;
        $this->name        = (!empty($data['name'])) ? $data['name'] : null;
        $this->description = (!empty($data['summary'])) ? $data['summary'] : null;        
        $this->activities  = array();
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

    public function setActivityType($type){
        $this->activityType = $type;
    }

    public function setActivities($activities){
        $this->activities = $activities;
    }

    public function setStatus($status){
        $this->status = $status;
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

    public function getImage(){
        return $this->image;
    }

    public function getActivityType(){
        return $this->activityType;
    }

    public function getActivities(){
        return $this->activities;
    }

    public function getStatus(){
        return $this->status;
    }

    public function __toString(){
        $returnString = '{';
        $returnString .= '"id":'.$this->getId().',';
        $returnString .= '"name":"'.$this->getName().'",';
        $returnString .= '"description":"'.$this->getDescription().'",';
        $returnString .= '"image":';

        if($this->getImage() == null){
            $returnString.= '"",';
        }else{
            $returnString.= $this->getImage().',';
        }

        $returnString .= '"activityType":"'.$this->getActivityType().'",';
        $returnString .= '"activities":[';

        $quantityOfActivities = count($this->getActivities());

        for($x = 0; $x < $quantityOfActivities; $x++){
            $returnString.= $this->activities[$x]; 
            $returnString.= ",";
        }

        if($quantityOfActivities > 0){
           $returnString.= $this->activities[$quantityOfActivities-1];
        }

        $returnString .= '],';
        $returnString .= '"status":';

        if($this->getStatus() == null){
            $returnString.= '""';
        }else{
            $returnString.= $this->getStatus();
        }

        $returnString .= '}';

        return $returnString;
    }

}