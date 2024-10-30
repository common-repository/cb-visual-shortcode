<?php
/**
* 
*/

CBVSCodeFile::disallowDirectAccess();

/**
* 
*/
abstract class CBVSStatedObjectStorage
implements CBVSIStatedObjectStorage
{
    
    /**
    * put your comment there...
    * 
    * @var mixed
    */
    protected $name;
    
    /**
    * put your comment there...
    * 
    * @var mixed
    */
    protected $options;

    /**
    * put your comment there...
    * 
    * @param mixed $name
    * @param mixed $params
    * @return CBVSStatedObjectStorage
    */
    public function __construct($name, $options = array())
    {
        
        $this->name = $name;
        $this->options = $options;
        
        $this->_init();
    }
    
    /**
    * put your comment there...
    * 
    */
    protected function _init() {}
    
    /**
    * put your comment there...
    * 
    */
    public function getName()
    {
        return $this->name;
    }
    
    /**
    * put your comment there...
    * 
    * @param mixed $name
    */
    public function getOpt($name)
    {
        
        $value =    isset($this->option[$name]) ?
                    $this->option[$name] :
                    null;
        
        return $value;
    }
    
}
