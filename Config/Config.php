<?php
/**
* 
*/

CBVSCodeFile::disallowDirectAccess();

/**
* 
*/
return array
(
    'env' => array
    (
    
        'state' =>  getenv('cb-visual-shortcode-dev-status') ?
                    getenv('cb-visual-shortcode-dev-status') :
                    CBVSPluginBase::ENV_STATE_PRO,
        'configsDir' => 'Config',
        
        'states' => array
        (

            CBVSPluginBase::ENV_STATE_DEV => array
            (
                'config' => 'Plugin.dev.php',
                'development' => true,
            ),
                    
            CBVSPluginBase::ENV_STATE_PRE_PRO => array
            (
                'config' => 'Plugin.pre-pro.php',
                'development' => true,
            ),
            
            CBVSPluginBase::ENV_STATE_PRO => array
            (
                'config' => 'Plugin.inc.php',
                'development' => false,
            )
            
        )
    ),
    
    'uninstallable' => false,
    'config' => array
    (
    
        'local' => array
        (
            'textDomain' => 'cb-visual-shortcode',
            'dir' => 'Languages',
        ),
        
        'slugNamespace' => 'cbvs',
    ),
    
    'installer' => 'CBVSInstaller',
    
    'view' => array
    (
    
        'extension' => 'html.php',
        
        'views' => array
        (
            'VisualShortcodeMetabox' => 'Views/PostMetaboxVisualShortcode',
        )
        
    ),
    
    'resource' => array
    (
        'scriptsPath' => 'Media/Scripts',
        'stylesPath' => 'Media/Styles',
    ),
);
