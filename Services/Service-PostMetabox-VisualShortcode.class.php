<?php
/**
* 
*/

// No direct access
defined('ABSPATH') or die();

/**
* 
*/
final class CBVSServiceDashboardPostMetabox
{
    
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
    public function __construct()
    {
        // Add metabox
        add_action('add_meta_boxes', array($this, '_define'));
    }
    
    /**
    * put your comment there...
    * 
    */
    public function _enqueueScripts()
    {
        
        $plugin =& CBVSPlugin::me();
        
        wp_enqueue_script('jquery-ui-dialog');
        
        // Visual Shortcode Metabox Controller
        wp_enqueue_script(
            'cb-visual-shortcode-visualize-post-metabox-controller',
            $plugin->urlScript('PostMetaboxVisualShortcode.js')
        );
        
        wp_localize_script(
            'cb-visual-shortcode-visualize-post-metabox-controller',
            'CBPostMetaboxViewVisualShortcodeL10N',
            array
            (
                'visualizeOnHTMLMode' => $plugin->__('Message'),
            )
        );
        
        // Extensions Visualize JS Model to take controler of all Visualization actions
        CBVSPlugin::hooks()->visualizeMetaboxEnqueueModel();
        
        // Visual Shortcode Metabox Default/Base Model
        wp_enqueue_script(
            'cb-visual-shortcode-visualize-post-metabox-default-model',
            $plugin->urlScript('VisualShortcode.js')
        );

        // Visual Shortcode Metabox Default/Base Model
        wp_enqueue_script(
            'cb-visual-shortcode-visualize-post-metabox-simple-shortcode-form',
            $plugin->urlScript('ShortcodeSimpleForm.jquery.js')
        );
        
        // Extensions Scripts
        CBVSPlugin::hooks()->visualizeMetaboxEnqueueScripts();
    }
    
    /**
    * put your comment there...
    * 
    */
    public function _enqueueStyles()
    {
        
        wp_enqueue_style(
            'cb-visual-shortcode-jquery-ui', 
            CBVSPlugin::me()->urlStyle('jQuery-UI/jquery-ui.min.css')
        );
        
        wp_enqueue_style(
            'cb-visual-shortcode-visualize-post-metabox',
            CBVSPlugin::me()->urlStyle('PostMetaboxVisualShortcode.css')
        );

        // Extensions Styles
        CBVSPlugin::hooks()->visualizeMetaboxEnqueueStyles();
    }

    /**
    * put your comment there...
    * 
    * @param mixed $postType
    */
    public function _define($postType)
    {

        $plugin =& CBVSPlugin::me();
    
        // Filter Metabox Parameters
        $metaboxParams = array
        (
            'title'             => $plugin->__('Visual Shortcodes'),
            'displayCallback'   => array($this, '_display'),
            'postTypes'         => array('post'),
            'context'           => 'normal',
            'priority'          => 'high',
            'callbackArgs'      => array
            (
                'viewDisplayExtensionsBlock' => true
            ),
        );
        
        $metaboxParams = CBVSPlugin::hooks()->getVisualizeMetaboxParameters($metaboxParams);
        
        // Don't process until Post Type is configured to have Visual Shortcode Metabox
        
        if (!in_array($postType, $metaboxParams['postTypes']))
        {
            return;
        }
        
        add_meta_box(
            'cb-visual-shortcode',
            $metaboxParams['title'],
            $metaboxParams['displayCallback'],
            $postType,
            $metaboxParams['context'],
            $metaboxParams['priority'],
            $metaboxParams['callbackArgs']
        );
            
        // TinyMCE PLugins
        add_action('mce_external_plugins', array($this, '_tinyMCEExternalPlugins'));
        
        // TinMCE Styles
        add_filter('mce_css', array($this, '_tinyMCEStyles'));

        // Enqueue scripts and styles
        add_action('admin_enqueue_scripts', array($this, '_enqueueScripts'));
        add_action('admin_print_styles', array($this, '_enqueueStyles'));
        
    }

    /**
    * put your comment there...
    * 
    * @param mixed $post
    * @param mixed $args
    */
    public function _display($post, $metaboxDef)
    {
        
        // Filter visuaizxed shortcode expression
        $visualizedShortcodesExpression = '\<div\x20+class\x20*\=\x20*\"cb-visual-shortcode-wrapper\x20+mceNonEditable\"\x20*\>[\s\n]*\<div\x20+class\x20*\=\x20*\"cb-visual-shortcode-name\"\x20*\>[\s\na-zA-Z0-9]+\<\/div\x20*\>[\s\n]*\<div\x20+class\x20*\=\x20*\"cb-visual-shortcode-shortcode\"[^\>]*\>(%SHORTCODE_EXPRESSION%)\<\/div\x20*\>[\s\n]*\<\/div\x20*\>';
        
        $visualizedShortcodesExpression = CBVSPlugin::hooks()->visualizedShortcodesExpression(
            $visualizedShortcodesExpression
        );
        
        // Filter View and View parameters
        $view = array
        (
            'template' => 'VisualShortcodeMetabox',
            'args' => $metaboxDef['args'],
        );
        
        $view = CBVSPlugin::hooks()->visualizeMetaboxView($view);
        
        // Display all defined blocks
        $view['args']['visualizedShortcodesExpression'] = $visualizedShortcodesExpression;
        
        // Get Addons List
        try
        {
            $view['args']['extra']['addons'] = CBVSPAddons::createNamedInstance('ins')->addons();    
        }
        catch (Exception $exception)
        {
            
            // In case of error fetching feeds sliently wait for another try and
            // don't display addons for now
            $view['args']['viewDisplayExtensionsBlock'] = false;
        }
        
        $form = CBVSPlugin::me()->renderView(
            $view['template'],
            $view['args']
        );
        
        // Filter output
        $form = CBVSPlugin::hooks()->visualizeMetaboxViewTemplate($form);
        
        echo $form;
        
        // After Metabox action
        CBVSPlugin::hooks()->visualizeMetaboxViewBelow();
        
    }

    /**
    * put your comment there...
    * 
    * @param mixed $plugins
    */
    public function _tinyMCEExternalPlugins($plugins)
    {
        
        $vsPlugin =& CBVSPlugin::me();
        
        // tinyMC Non Editable Offical PLugin
        $plugins['noneditable'] = $vsPlugin->urlScript('TinyMCE-Plugins/Bundles/NonEditable/Plugin.min.js');
        
        // TinyMCE Visual Shortcode Plugin to handle Shortcode click
        $plugins['VisualShortcode'] = $vsPlugin->urlScript('TinyMCE-Plugins/Externals/VisualShortcode/Plugin.js');
        
        $plugins = CBVSPlugin::hooks()->tinyMCEExternalPlugins($plugins);
        
        return $plugins;
    }
    
    /**
    * put your comment there...
    * 
    * @param mixed $csStyles
    */
    public function _tinyMCEStyles($csStyles)
    {
        
        // Extract into array
        $styles = explode(',', $csStyles);
        
        // Styles
        $styles[] = CBVSPlugin::me()->urlStyle('TinyMCEEditor.css');
        
        // Filter styles
        $styles = CBVSPlugin::hooks()->tinyMCEStyles($styles);
        
        // Append to the comma sep list
        $csStyles = join(',', $styles);
        
        return $csStyles;
    }
    
    /**
    * put your comment there...
    * 
    */
    protected function checkNonce()
    {
        
        if (!check_ajax_referer('cb-visual-shortcode-metabox', 'cb-visual-shortcode-nonce'))
        {
            return false;
        }
        
        return true;
    }
    
    /**
    * put your comment there...
    * 
    */
    public static function & run()
    {
        
        if (!self::$instance)
        {
            self::$instance = new CBVSServiceDashboardPostMetabox();
        }
        
        return self::$instance;
    }
    
}
