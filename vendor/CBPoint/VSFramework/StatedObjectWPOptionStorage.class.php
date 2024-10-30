<?php
/**
* 
*/

CBVSCodeFile::disallowDirectAccess();

/**
* 
*/
class CBVSStatedObjectStorageWPOption
extends CBVSStatedObjectStorage
{
    
    /**
    * put your comment there...
    * 
    */
    public function read()
    {
        $data = get_option($this->name, array());
        
        return $data;
    }
    
    /**
    * put your comment there...
    * 
    * @param mixed $data
    */
    public function & write($data)
    {
        update_option($this->name, $data);
        
        return $this;
    }
}