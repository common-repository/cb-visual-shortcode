<?php
/**
* 
*/

// No direct access
defined('ABSPATH') or die();

/**
* 
*/
abstract class CBVSPluggableInterface
{
    
    /**
    * put your comment there...
    * 
    * @var mixed
    */
    private $prefix;
    
    /**
    * put your comment there...
    * 
    * @var mixed
    */
    private static $instance;
    
    /**
    * put your comment there...
    * 
    */
    private function __construct()
    {
        $this->prefix = strtolower(get_class($this));
    }

    /**
    * put your comment there...
    * 
    * @param mixed $actionName
    * @param mixed $callback
    * @param mixed $priority
    * @param mixed $argsCount
    * @return CBVSPluggableInterface
    */
    public function & addAjaxPrivAction($actionName, $callback, $priority = 10, $argsCount = 1)
    {

        add_action(
            "wp_ajax_{$actionName}",
            $callback,
            $priority,
            $argsCount
        );
        
        return $this;
    }
    
    /**
    * put your comment there...
    * 
    * @param mixed $action
    * @param mixed $callback
    * @param mixed $priority
    * @param mixed $argsCount
    */
    public function & addAction($action, $callback, $priority = 10, $argsCount = 1)
    {
        
        $actionName = $this->getHookName($action);
        
        add_action(
            $actionName,
            $callback,
            $priority,
            $argsCount
        );
        
        return $this;
    }
    
    /**
    * put your comment there...
    * 
    * @param mixed $action
    * @param mixed $callback
    * @param mixed $priority
    * @param mixed $argsCount
    */
    public function & addFilter($filter, $callback, $priority = 10, $argsCount = 1)
    {
        
        $filterName = $this->getHookName($filter);
        
        add_filter(
            $filterName,
            $callback,
            $priority,
            $argsCount
        );
        
        return $this;
    }
    
    /**
    * put your comment there...
    * 
    * @param mixed $filter
    */
    public function applyFilter($filter)
    {
        
        $result = $this->hook(
            'apply_filters', 
            $filter,
            func_get_args()
        );
        
        return $result;
    }

    /**
    * put your comment there...
    * 
    * @param mixed $action
    */
    public function doAction($action)
    {
        
        $this->hook(
            'do_action',
            $action, 
            func_get_args()
        );
    }
    
    /**
    * put your comment there...
    * 
    * @param mixed $hook
    */
    public function getHookName($hook)
    {
        
        $hookName = "{$this->prefix}-{$hook}";
        
        return $hookName;
    }
    
    /**
    * put your comment there...
    * 
    */
    public static function & getInstance()
    {
        
        if (!self::$instance)
        {
            self::$instance = new CBVSPluggable();
        }
        
        return self::$instance;
    }
    
    /**
    * put your comment there...
    * 
    * @param mixed $method
    * @param mixed $hook
    * @param mixed $args
    */
    private function hook($method, $hook, $args)
    {
        
        // Hook name
        $hookName = $this->getHookName($hook);
        
        // Replace original hook name with full/prefixed hook name
        $args[0] = $hookName;
        
        // Get arg
        $result = call_user_func_array($method, $args);
        
        return $result;
    }
}