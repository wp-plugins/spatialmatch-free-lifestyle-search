<?php

class SpatialMatch_Model_Map
{
    const DEFAULT_WIDTH = 600;
    const DEFAULT_HEIGHT = 500;
    
    public $id;
    public $title;
    public $licenseKey;
    public $description;
    
    function __construct($id = 0, $licenseKey = null, $title = null, $bookmark = null, $description = null)
    {
        $this->id = $id;
        $this->licenseKey = $licenseKey;
        $this->title = $title;
        $this->bookmark = $bookmark;
        $this->description = $description;
    }
}
