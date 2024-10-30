<?php
/**
* 
*/

CBVSCodeFile::disallowDirectAccess();

/**
* 
*/
class CBVSStatedObjectStorageCustom
extends CBVSStatedObjectStorage
{
    
    /**
    * put your comment there...
    * 
    * @var mixed
    */
    protected $customStorage;
    
    /**
    * put your comment there...
    * 
    */
    protected function _init()
    {
        
        $storage = $this->getOpt('storage');
        
        CBVSObject::validateType($storage, 'CBVSIStatedObjectStorage');
        
        $this->customStorage = $storage;
    }
    
    /**
    * put your comment there...
    * 
    */
    public function read()
    {
        $data = $this->customStorage->read();
        
        return $data;
    }
    
    /**
    * put your comment there...
    * 
    * @param mixed $data
    */
    public function & write($data)
    {
        $this->customStorage->write($data);
        
        return $this;
    }
}