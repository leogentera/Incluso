<?php
namespace MoodleApi\Model;

class Video
{
	public $url;
    public $contentType;
    
    public function __construct($url, $contentType)
    {
        $this->url=$url;
        $this->contentType=$contentType;
    }
}