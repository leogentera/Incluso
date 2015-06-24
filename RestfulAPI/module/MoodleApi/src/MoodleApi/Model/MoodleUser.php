<?php
namespace MoodleApi\Model;

class MoodleUser
{
	public $country;
    public $department;
    public $description;
    public $descriptionformat;
    public $email;
    public $firstaccess;
    public $firstname;
    public $fullname;
    public $id;
    public $lastaccess;
    public $lastname;
    public $preferences;
    public $profileimageurl;
    public $profileimageurlsmall;
    public $username;
    
    public function exchangeArray($data)
    {
        $this->country =     (!empty($data['country'])) ? $data['country'] : null;
        $this->department =     (!empty($data['department'])) ? $data['department'] : null;
        $this->description =     (!empty($data['description'])) ? $data['description'] : null;
        $this->descriptionformat =     (!empty($data['descriptionformat'])) ? $data['descriptionformat'] : null;
        $this->email =     (!empty($data['email'])) ? $data['email'] : null;
        $this->firstaccess =     (!empty($data['firstaccess'])) ? $data['firstaccess'] : null;
        $this->firstname =     (!empty($data['firstname'])) ? $data['firstname'] : null; 
        $this->fullname =     (!empty($data['fullname'])) ? $data['fullname'] : null; 
        $this->id =     (!empty($data['id'])) ? $data['id'] : null; 
        $this->lastaccess =     (!empty($data['lastaccess'])) ? $data['lastaccess'] : null;
        $this->lastname =     (!empty($data['lastname'])) ? $data['lastname'] : null; 
        $this->preferences =     (!empty($data['preferences'])) ? $data['preferences'] : null;
        $this->profileimageurl =     (!empty($data['profileimageurl'])) ? $data['profileimageurl'] : null;
        $this->profileimageurlsmall =     (!empty($data['profileimageurlsmall'])) ? $data['profileimageurlsmall'] : null;
        $this->username =     (!empty($data['id'])) ? $data['username'] : null;
    }
}