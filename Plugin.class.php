<?php
/**
* 
*/

// No direct access
defined('ABSPATH') or die();

/**
* 
*/
class CBVSPlugin
extends CBVSPluginBase
{

    /**
    * put your comment there...
    * 
    * @var mixed
    */
    private static $hooks;
    
    /**
    * 
    */
    public static function & hooks()
    {
        return self::$hooks;
    }
    
    /**
    * put your comment there...
    * 
    */
    protected function init()
    {
                
        // Use Localization
        $this->useLocalization();
        
        // Hooks instance
        self::$hooks =& CBVSPluggable::getInstance();
        
        // This include installation!
        parent::init();
        
        // Use Resources
        $this->useResources();
        
        // Services
        if (is_admin())
        {
            // Post metabox
            $this->defineServicesController(
                'CBVSServiceDashboardPostMetabox'
            );
        }
        
    }
    
}