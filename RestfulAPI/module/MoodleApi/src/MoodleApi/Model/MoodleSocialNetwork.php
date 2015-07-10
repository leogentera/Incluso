<?php
namespace MoodleApi\Model;

class MoodleSocialNetwork
{
	public $socialNetwork; //Name of the social network
    public $socialNetworkId; //Id from facebook or whatever
    
    
    public function __construct($socialNetwork, $id)
    {
    	$this->socialNetworkId=$id;
    	$this->socialNetwork=$socialNetwork;
    }
}