<?php
/**
* 
*/

defined('ABSPATH') or die(-1);

/**
* 
*/
return array
(

    'plugin' => array
    (
    
        'store' => array
        (
            'apiEndPoint' => 'http://www.cbspoint.com/cbstore',
            'apiAddons' => array
            (
                'url' => 'http://www.cbspoint.com/plugins/visual-shortcode-community-edition/feed/?items=addons',
                'expires' => 864000, /* Retrieve Every Ten days */
            ),
        ),

    )
);