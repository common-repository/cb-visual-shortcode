<?php
/**
* 
*/

defined('ABSPATH') or die(-1);

/**
* System Compatibility check core service
*/
final class CBVSServiceControllerCoreSCC 
{
    
    /**
    * put your comment there...
    * 
    * @var mixed
    */
    private $isCompatible = null;
    
    /**
    * 
    */
    private $phpVersion = '5.5';
    
    /**
    * put your comment there...
    * 
    * @var CBVSServiceControllerCoreSCC
    */
    private static $instance;
    
    /**
    * put your comment there...
    * 
    */
    public function __construct()
    {
                   
        $requirments = array();
        $requirments['php'] = version_compare(PHP_VERSION, $this->phpVersion, '>=');
        
        foreach ($requirments as $require)
        {
            if (!$require)
            {
                
                $this->isCompatible = false;
                
                $this->showAdminNotice();
                
                return $this;
            }
        }
        
        $this->isCompatible = true;

    }
    
    /**
    * put your comment there...
    * 
    */
    public function _OnAdminNotice()
    {
        require dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' .
                DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 
                'Core-CompatibilityCheckNotice.html.php';
    }
    
    /**
    * put your comment there...
    * 
    */
    public static function & check()
    {

        if (!self::$instance)
        {
            self::$instance = new CBVSServiceControllerCoreSCC();

        }

        return self::$instance;
    }
    
    /**
    * put your comment there...
    * 
    */
    public function isCompatible()
    {
        return $this->isCompatible;
    }
    
    /**
    * put your comment there...
    * 
    */
    private function showAdminNotice()
    {
        add_action('admin_notices', array($this, '_OnAdminNotice'));
        add_action('network_admin_notices', array($this, '_OnAdminNotice'));
    }
    
}