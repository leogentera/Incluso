<?php
namespace MoodleApi\Model;

class MoodleForumDiscussion{

	public $id;
    public $name;
	public $message;
    public $image = array();
	public $posts = array();

	public function __construct($data){
        $this->id      = (!empty($data['id'])) ? $data['id'] : null;
        $this->name    = (!empty($data['name'])) ? $data['name'] : null;;
        $this->message = (!empty($data['message'])) ? $data['message'] : null; 
        $this->image   = (!empty($data['attachments'])) ? $data['attachments'] : null;       
    }

    public function setId($id){
    	$this->id = $id;
    }

    public function setMessage($message){
    	$this->message = $message;
    }

    public function setPosts($posts){
        $this->posts = $posts;
    }
}
?>