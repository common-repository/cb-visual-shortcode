<?php
/**
* 
*/

// No direct access
defined('ABSPATH') or die();

// Filter Metabox attributes
$metaboxAttrs = array
(
    'class' => array
    (
        'cb-visual-shortcode-post-metabox'
    )
);

$metaboxAttrs = CBVSPlugin::hooks()->visualizeMetaboxViewAttrs($metaboxAttrs);
?>
<div <?= join(' ', $metaboxAttrs) ?>>

    <div class="cb-visual-shortcode-pm-visualize-block">
    
        <p><?= $this->__('Click "Visualize !" Button below to make all -- Textual Shortcodes inside current post editor -- visualized') ?></p>
        <p><?= $this->__('After visualize process is finished all shortcodes will be clickable, and then would be possible to edit Shortcode attributes via Web form instead of doing that manually') ?></p>
        <input id="cb-visual-shortcode-button-visualize" type="button" value="<?= $this->__('Visualize Shortcodes !') ?>" />    
        
    </div>

<?php if ($viewDisplayExtensionsBlock) : ?>

    <div class="cb-visual-shortcode-pm-get-pro-block">
        <strong><?= $this->__('Extend Visual Shortcode') ?></strong>
        <ul class="addons">
<?php foreach ($extra['addons'] as $addon) : ?>
            <li>
                <a target="_blank" href="<?= $addon['link'] ?>"><?= $addon['title'] ?></a>
                <br>
                <span><?= $addon['description'] ?></span>
            </li>
<?php endforeach; ?>
        </ul>
    </div>
    
<?php endif; ?>

<?php ob_start(); ?>

    <template   id="cb-visual-shortcode-shortcode-wrapper-template" 
                data-expression='<?php echo $visualizedShortcodesExpression; ?>'>
                
         <template>
         
            <div class="cb-visual-shortcode-wrapper mceNonEditable"><div class="cb-visual-shortcode-name">%shortcode_name%</div><div class="cb-visual-shortcode-shortcode">%shortcode%</div></div>
            
         </template>
        
    </template>

<?php
    // Filter Visualized Shortcode template
    $visualizedShortcodeTemplate = ob_get_clean();
    
    $visualizedShortcodeTemplate = CBVSPlugin::hooks()->
    visualizeShortcodeWrapperTemplate(
        $visualizedShortcodeTemplate
    );
    
    echo $visualizedShortcodeTemplate;
?>

    <div id="cb-visual-shortcode-live-simple-form">
    
        <ul></ul>
        <div>
            <button id="cbvs-simple-form-close"><?= $this->__('Close') ?></button>
            <button id="cbvs-simple-form-submit"><?= $this->__('Submit') ?></button>        
        </div>

    </div>

</div>