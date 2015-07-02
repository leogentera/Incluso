<?php
namespace MoodleApi\Model;

use MoodleApi\Model\MoodleCourseContents;

class MoodleModule
{
	
	public $id;// int   //activity id
	public $url;// string  Optional //activity url
	public $name;// string   //activity module name
	public $instance;// int  Optional //instance id
	public $description;// string  Optional //activity description
	public $visible;// int  Optional //is the module visible
	public $modicon;// string   //activity icon url
	public $modname;// string   //activity module type
	public $modplural;// string   //activity module plural name
	public $availability;// string  Optional //module availability settings
	public $indent;// int   //number of identation in the site
	public $contents;//
	
	
		
	public function exchangeArray($data)
	{
		$this->id= (!empty($data['id'])) ? $data['id'] : null;;
		$this->url= (!empty($data['url'])) ? $data['url'] : null;;
		$this->name= (!empty($data['name'])) ? $data['name'] : null;;
		$this->instance= (!empty($data['instance'])) ? $data['instance'] : null;;
		$this->description= (!empty($data['description'])) ? $data['description'] : null;;
		$this->visible= (!empty($data['visible'])) ? $data['visible'] : null;;
		$this->modicon= (!empty($data['modicon'])) ? $data['modicon'] : null;;
		$this->modname= (!empty($data['modname'])) ? $data['modname'] : null;;
		$this->modplural= (!empty($data['modplural'])) ? $data['modplural'] : null;;
		$this->availability= (!empty($data['availability'])) ? $data['availability'] : null;;
		$this->indent= (!empty($data['indent'])) ? $data['indent'] : null;
		$this->contents;
		if (!empty($data['contents'])){
			$json = json_decode($data['contents'],true);
			foreach ($json as $res) {
				$content = new MoodleCourseContent();
				$content->exchangeArray($res);
				array_push($this->contents, $content);
			}
		}
		else{
			$this->contents=null;
		}
	}
}