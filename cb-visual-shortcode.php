<?php
/**
* Plugin Name: CB Visual Shortcode
* Description: Modify Shortcode attributes using web form
* Version: 1.0.0
* Author: AHMeD SAiD
* Author URI: http://www.cbspoint.com
* Plugin URI: http://www.cbspoint.com/plugins/visual-shortcode-community-edition/
* Text Domain: cb-visual-shortcode
* License: GPL2

    CB Visual Shortcode is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 2 of the License, or
    any later version.
     
    CB Visual Shortcode is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    GNU General Public License for more details.
     
    You should have received a copy of the GNU General Public License
    along with CB Visual Shortcode.
*/

// No direct access
defined('ABSPATH') or die(-1);

##################### Compatibility Check #####################

require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Services' .
        DIRECTORY_SEPARATOR . 'Core-CompatibilityCheck.class.php';
        
if (!CBVSServiceControllerCoreSCC::check()->isCompatible())
{
    return;
}
        
############# Autoload and Start The Plugin ##################

require __DIR__ . DIRECTORY_SEPARATOR . 'Autoload.php';

// Plug
CBVSPlugin::plug(

    __FILE__,
    
    CBVSPlugin::envLoadConfig(
        __DIR__,
        require __DIR__ . DIRECTORY_SEPARATOR .
                'Config' . DIRECTORY_SEPARATOR . 'Config.php'
    )
);

##############################################################