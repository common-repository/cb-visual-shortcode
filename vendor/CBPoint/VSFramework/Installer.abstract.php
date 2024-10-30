<?php
/**
* 
*/

// No direct access
defined('ABSPATH') or die();

/**
* 
*/
abstract class CBVSInstallerBase
extends CBVSStatedObject
{

    /**
    * 
    */
    const INSTALLED = 0;
    
    /**
    * 
    */
    const DOWNGRADE = -1;
    
    /**
    * 
    */
    const UPGRADE = 1;
    
    /**
    * put your comment there...
    * 
    * @var mixed
    */
    protected $installers = array();
    
    /**
    * put your comment there...
    * 
    * @var mixed
    */
    private $installionState;
    
    /**
    * put your comment there...
    * 
    * @var mixed
    */
    protected $installedVersion = null;
    
    /**
    * put your comment there...
    * 
    * @var mixed
    */
    protected $stateVars = array
    (
        'installedVersion'
    );
    
    /**
    * put your comment there...
    * 
    */
    public static function & create()
    {
        
        $class = get_called_class();
        
        $installer = new $class();
        
        return $installer;
    }
    
    /**
    * put your comment there...
    * 
    * @param mixed $file
    * @param mixed $flags
    */
    public function executeSQLFile($file)
    {
        
        global $wpdb;
        
        $stmtsCount = 0;
        
        // Load SQl File
        ob_start();
        
        require $file;
        
        $sql = trim(ob_get_clean(), '\x20\t;');
        
        $stmts = explode(';', $sql);
        
        if (empty($stmts))
        {
            throw new Exception('Nothing To Execute');
        }

        // Execute all Statments
        foreach ($stmts as $stmt)
        {
            
            $stmt = trim($stmt);
            
            if (!$stmt)
            {
                throw new Exception('Empty Statment');
            }
            
            $wpdb->query($stmt);
            
            $stmtsCount++;
        }
        
        return $stmtsCount;
    }
    
    /**
    * put your comment there...
    * 
    */
    public function getCurrentVersion()
    {
        
        $currentVersion = end($this->installers);
        
        return $currentVersion;
    }
    
    /**
    * put your comment there...
    * 
    */
    public function getInstalledVersion()
    {
        return $this->installedVersion;
    }
    
    /**
    * put your comment there...
    * 
    */
    public function getInstalledVersionIndex()
    {
        
        $index = $this->getInstallerIndex($this->getInstalledVersion());
        
        return $index;
    }
    
    /**
    * put your comment there...
    * 
    * @param mixed $version
    */
    public function getInstallerIndex($version)
    {
        
        $index = array_search($version, $this->installers);
        
        $index = ($index === FALSE) ? 0 : $index;
        
        return $index;
    }
    
    /**
    * put your comment there...
    * 
    */
    public static function & getInstance()
    {
        return self::$instance;
    }
    
    /**
    * put your comment there...
    * 
    */
    public function & install()
    {
        
        // Install version 1.0.0
        switch ($this->isInstalled())
        {
            
            case self::UPGRADE:
            
                $this->runInstallers($this->getInstalledVersionIndex());
                
            break;
            
            case self::DOWNGRADE:
            
                throw new Exception('Downgrad doesnt not supported');
                
            break;
        }
        
        // Fire internal event
        $this->onPreWriteInstallState();
        
        // Save new version
        $this->installedVersion = $this->getCurrentVersion();
        
        $this->write();
        
        return $this;
    }
    
    /**
    * put your comment there...
    * 
    */
    public function isInstalled()
    {
        
        $this->installionState = version_compare($this->getCurrentVersion(), $this->getInstalledVersion());
        
        return $this->installionState;
    }

    /**
    * put your comment there...
    * 
    */
    protected function onPreWriteInstallState() {}
    
    /**
    * put your comment there...
    * 
    * @param mixed $startVersion
    */
    public function & runInstallers($index)
    {
        
        for (; $index < count($this->installers); $index++)
        {
            
            $version = $this->installers[$index];
            
            $versionName = str_replace(array('.'), '', $version);
            
            $installerMethodName = "_installer_{$versionName}";
            
            if (method_exists($this, $installerMethodName))
            {
                $this->$installerMethodName();
            }
        }
        
        return $this;
    }

}