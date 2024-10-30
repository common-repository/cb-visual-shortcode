<?php
/**
* 
*/

CBVSCodeFile::disallowDirectAccess();

/**
* 
*/
interface CBVSIStatedObjectStorage
{
    
    /**
    * 
    */
    public function read();
    
    /**
    * 
    */
    public function & write($data);
}
