<?php
/**
* 
*/

CBVSCodeFile::disallowDirectAccess();

/**
* 
*/
class CBVSEventArgs
{
    
    /**
    * put your comment there...
    * 
    * @var mixed
    */
    public $args;
    
    /**
    * put your comment there...
    * 
    * @var mixed
    */
    public $event;
    
    /**
    * put your comment there...
    * 
    * @var mixed
    */
    public $src;
    
    /**
    * put your comment there...
    * 
    * @var mixed
    */
    public $srcParams;

    /**
    * put your comment there...
    * 
    * @param mixed $event
    * @param mixed $src
    * @param mixed $srcParams
    * @param mixed $args
    * @return CBVSEventArgs
    */
    public function __construct($event, & $src = null, & $srcParams, & $args = null)
    {
        $this->event = $event;
        $this->src =& $src;
        $this->srcParams =& $srcParams;
        $this->args =& $args;
    }
}