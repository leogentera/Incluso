<?php
namespace MoodleApi\Model;

class MoodleCourseContents
{
	public $type; // string   //a file or a folder or external link
	public $filename; // string   //filename
	public $filepath; // string   //filepath
	public $filesize; // int   //filesize
	public $fileurl; // string  Optional //downloadable file url
	public $content; // string  Optional //Raw content, will be used when type is content
	public $timecreated; // int   //Time created
	public $timemodified; // int   //Time modified
	public $sortorder; // int   //Content sort order
	public $userid; // int   //User who added this content to moodle
	public $author; // string   //Content owner
	public $license; // string   //Content license
	
	
		
	public function exchangeArray($data)
	{
		
		$this->type = (!empty($data['type'])) ? $data['type'] : null;
		$this->filename = (!empty($data['filename'])) ? $data['filename'] : null;; // string   //filename
		$this->filepath = (!empty($data['filepath'])) ? $data['filepath'] : null;; // string   //filepath
		$this->filesize = (!empty($data['filesize'])) ? $data['filesize'] : null;; // int   //filesize
		$this->fileurl = (!empty($data['fileurl'])) ? $data['fileurl'] : null;; // string  Optional //downloadable file url
		$this->content = (!empty($data['content'])) ? $data['content'] : null;; // string  Optional //Raw content, will be used when type is content
		$this->timecreated = (!empty($data['timecreated'])) ? $data['timecreated'] : null;; // int   //Time created
		$this->timemodified = (!empty($data['timemodified'])) ? $data['timemodified'] : null;; // int   //Time modified
		$this->sortorder = (!empty($data['sortorder'])) ? $data['sortorder'] : null;; // int   //Content sort order
		$this->userid = (!empty($data['userid'])) ? $data['userid'] : null;; // int   //User who added this content to moodle
		$this->author = (!empty($data['author'])) ? $data['author'] : null;; // string   //Content owner
		$this->license = (!empty($data['license'])) ? $data['license'] : null;; // string   //Content license
			
	}
}