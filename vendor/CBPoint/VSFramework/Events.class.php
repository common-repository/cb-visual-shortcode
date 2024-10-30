<?php
/**
* 
*/

CBVSCodeFile::disallowDirectAccess();

/**
* 
*/
class CBVSEvents
{
    
    /**
    * put your comment there...
    * 
    * @var mixed
    */
    protected static $delegates = array();
    
    /**
    * put your comment there...
    * 
    * @var mixed
    */
    protected $events = array();

    /**
    * put your comment there...
    * 
    * @var mixed
    */
    private $params;

    /**
    * put your comment there...
    * 
    * @var mixed
    */
    private $src;
        
    /**
    * put your comment there...
    * 
    * @param mixed $src
    * @return CBVSEvents
    */
    public function __construct(& $src, $params = array())
    {
        $this->src =& $src;
    }
    
    /**
    * put your comment there...
    * 
    * @param mixed $event
    * @param mixed $callback
    */
    public function & bind($event, $callback)
    {
        
        $eventId = md5(uniqid(time(), true));
        
        $this->events[$event][$eventId] = $callback;
        
        return $this;
    }
    
    /**
    * put your comment there...
    * 
    * @param mixed $class
    * @param mixed $event
    * @param mixed $handle
    */
    public static function delegate($class, $event, $handle)
    {
        
        $eventId = md5(uniqid(time(), true));
        
        self::$delegates[$class][$event][$eventId] = $handle;
        
    }

    /**
    * put your comment there...
    * 
    * @param mixed $event
    * @param mixed $src
    * @param mixed $args
    */
    public function & trigger($event, & $args = null, $src = null)
    {
        
        if (!$src)
        {
            $src =& $this->src;
        }
        
        // Initialize new events args object
        $eventArgs = new CBVSEventArgs($event, $src, $this->params, $args);

        $observers =    isset($this->events[$event]) ?
                        $this->events[$event] :
                        array();
        
        // Get Delegated observers as well and merge them
        // with this object observers
        $srcClass = get_class($src);
        $delegatedObservers =   isset(self::$delegates[$srcClass][$event]) ?
                                self::$delegates[$srcClass][$event] :
                                array();
        
        $observers += $delegatedObservers;
        
        // Trigger event for all observers
        foreach ($observers as $observer)
        {
            
            if (!is_callable($observer))
            {
                throw new Exception("Could not call {$event} Observer!!");
            }
            
            call_user_func($observer, $eventArgs);
            
        }
        
        return $this;
    }
    
}
