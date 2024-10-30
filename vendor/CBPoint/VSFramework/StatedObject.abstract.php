<?php
/**
* 
*/

// No direct access
defined('ABSPATH') or die();

/**
* 
*/
abstract class CBVSStatedObject
extends CBVSObject
{
    
    const STORAGE_WP_CUSTOM = 'custom';
    const STORAGE_WP_NETWORK = 'wpnetwork';
    const STORAGE_WP_OPTIONS = 'wpoption';
    const STORAGE_WP_USERS = 'wpuser';
    
    /**
    * put your comment there...
    * 
    * @var mixed
    */
    protected $stateVars = array();
    
    /**
    * put your comment there...
    * 
    * @var mixed
    */
    private $storage;
    
    /**
    * put your comment there...
    * 
    * @var mixed
    */
    protected $storageType = array();
    
    /**
    * put your comment there...
    * 
    * @var mixed
    */
    protected $storages = array
    (
        'wpoption' => array
        (
            'class' => 'CBVSStatedObjectStorageWPOption',
        ),
        'wpuser' => array
        (
            'class' => 'CBVSStatedObjectStorageWPUser',
        ),
        'wpnetwork' => array
        (
            'class' => 'CBVSStatedObjectStorageWPNetwork',
        ),
        'custom' => array
        (
            'class' => 'CBVSStatedObjectStorageCustom',
        ),
    );

    /**
    * put your comment there...
    * 
    * @param mixed $storage
    * @param mixed $params
    * @return CBVSStatedObject
    */
    protected function __construct() 
    {
        
        // Storage Tyupe defauls
        $this->storageType = array_merge(
            array
            (
                'name'      => null,
                'options'   => array(),
                'type'      => self::STORAGE_WP_OPTIONS
            ),
            $this->storageType
        );
        
        // Default storage name
        if ( !$this->storageType['name'])
        {
            $this->storageType['name'] = get_class($this);
        }
        
        // Create storage
        $this->createStorage();

        // Read values
        $this->read();
        
        // INit object
        $this->_init();
        
    }
    
    /**
    * put your comment there...
    * 
    * @param mixed $type
    * @param mixed $params
    */
    protected function createStorage()
    {
        
        $storageType =& $this->storageType;
        
        // Valid storage must be supplied
        if (!$storageType['type'] ||
            !isset($this->storages[$storageType['type']]))
        {
            throw new Exception('Invalid Storage type supplied');
        }
        
        $storageTypeName = $storageType['type'];
        
        $storageClass = $this->storages[$storageTypeName]['class'];
        
        $this->storage = new $storageClass(
            $storageType['name'],
            $storageType['options']
        );
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
    public function & read()
    {

        $data = $this->storage()->read();
        
        foreach ($data as $varName => $value)
        {
            $this->$varName = $value;
        }
        
        return $this;
    }
    
    /**
    * put your comment there...
    * 
    */
    public function & storage()
    {
        return $this->storage;
    }
    
    /**
    * put your comment there...
    * 
    */
    public function & write()
    {
        
        $data = array();
        
        foreach ($this->stateVars as $varname)
        {
            $data[$varname] = $this->$varname;
        }
        
        $this->storage()->write($data);
        
        return $this;
    }
    
}