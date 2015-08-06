<?php
namespace MoodleApi\Model;

class MoodleForumDiscussion{

	public $id;
	public $message;
	public $posts = array();

	public function __construct($data){
        $this->id      = (!empty($data['id'])) ? $data['id'] : null;
        $this->message = (!empty($data['message'])) ? $data['message'] : null;    
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