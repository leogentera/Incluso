<?php
namespace MoodleApi\Model;

class MoodleFamiliaCompartamosMember
{
	public $idClient;
    public $relativeName;
    public $relationship;
    
    
    public function __construct($idClient, $name, $relationship)
    {
    	$this->idClient=$idClient;
    	$this->relativeName=$name;
    	$this->relationship=$relationship;
    }
}