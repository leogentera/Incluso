<?php
namespace MoodleApi\Model;

class MoodleForumPost{

	public $id;
	public $discussion;
	public $parent;
	public $created;
	public $subject;
	public $message;
	public $hasAttachment;
	public $attachments = array();
	public $postAutor;
	public $picturePostAutor;
	public $likes;
	public $liked;
	public $replies = array();

	public function __construct($data){
        $this->id               = (!empty($data['id'])) ? $data['id'] : null;
        $this->discussion       = (!empty($data['discussion'])) ? $data['discussion'] : null;
        $this->parent           = (!empty($data['parent'])) ? $data['parent'] : null;    
        $this->created          = (!empty($data['created'])) ? $data['created'] : null;       
        $this->subject          = (!empty($data['subject'])) ? $data['subject'] : null;    
        $this->message          = (!empty($data['message'])) ? $data['message'] : null;    
        $this->hasAttachment    = (!empty($data['attachment'])) ? $data['attachment'] : null;    
        $this->attachments      = (!empty($data['attachments'])) ? $data['attachments'] : null;    
        $this->postAutor        = (!empty($data['userfullname'])) ? $data['userfullname'] : null;    
        $this->picturePostAutor = (!empty($data['userpictureurl'])) ? $data['userpictureurl'] : null;    
        $this->likes            = (!empty($data['likes'])) ? $data['likes'] : null;    
        $this->liked            = (!empty($data['liked'])) ? $data['liked'] : null;    
        $this->replies          = (!empty($data['replies'])) ? $data['replies'] : null;
    }

}
?>