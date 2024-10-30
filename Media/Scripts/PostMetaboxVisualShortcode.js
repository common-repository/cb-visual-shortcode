/**
* 
*/

var CBPostMetaboxViewVisualShortcode;

/**
* 
*/
(function($)
{
    
    /**
    * 
    */
    CBPostMetaboxViewVisualShortcode = new function()
    {

        /**
        * put your comment there...
        * 
        */
        var locals;
        
        /**
        * put your comment there...
        * 
        * @type String
        */
        var metaboxElementId = 'cb-visual-shortcode';
        
        /**
        * put your comment there...
        * 
        * @type String
        */
        var visualizeButtonId = 'cb-visual-shortcode-button-visualize';
        
        /**
        * put your comment there...
        * 
        * @type String
        */
        var eventPrefix = metaboxElementId;

        /**
        * put your comment there...
        * 
        * @type T_JS_FUNCTION
        */
        var htmlEditor;
        
        /**
        * put your comment there...
        * 
        */
        var metaboxElement = null;
        
        /**
        * put your comment there...
        * 
        */
        var model = null;

        /**
        * put your comment there...
        * 
        */
        var shortcodeWrapperTag;
        
        /**
        * put your comment there...
        * 
        */
        var shortcodeTagExpr;
    
        /**
        * put your comment there...
        * 
        */
        var visualizeButtonElement;
        
        /**
        * put your comment there...
        * 
        */
        var visualizedTagsExpr;
        
        /**
        * put your comment there...
        * 
        * @param event
        */
        var _OnVisualizeButtonClick = function(event)
        {
            
            // Prompt if not in Visual View
            if (!this.isVisualViewMode())
            {
                
                if (!confirm(locals.visualizeOnHTMLMode))
                {
                    return;
                }
            
            }
            
            var editor = this.getEditor();
            
            // Visualize
            var visualizedContent = model.visualizeShortcodes(editor.getContent());
            
            // Set editor text back
            editor.setContent(visualizedContent);
            
            this.trigger('visualized');
            
        };
        
        /**
        * 
        */
        this.getDataAttr = function(name)
        {
            return metaboxElement.find('.cb-visual-shortcode-post-metabox').data(name);
        };
        
        /**
        * 
        */
        this.getEditor = function()
        {
            
            var editor = this.isVisualViewMode() ? tinymce.get('content') : htmlEditor;
            
            return editor;
        };
        
        /**
        * put your comment there...
        * 
        * @param event
        */
        var getEventName = function(event)
        {
            
            var eventName = eventPrefix + '-' + event;
            
            return eventName;
        };
        
        /**
        * 
        */
        this.getModel = function()
        {
            return model;
        };
        
        /**
        * 
        */
        this.getShortcodeTagsExpression = function()
        {
            return shortcodeTagExpr;
        };

        /**
        * 
        */
        this.getShortcodeWrapperTag = function()
        {
            return shortcodeWrapperTag;
        };
        
        /**
        * 
        */
        this.getVisualizedTagsExpression = function()
        {
            return visualizedTagsExpr;
        };
        
        /**
        * 
        */
        this.isVisualViewMode = function()
        {
            var isVisualMode = $('#wp-content-wrap').hasClass('tmce-active');
            
            return isVisualMode;
        };
        
        /**
        * put your comment there...
        * 
        */
        this.metaboxElement = function()
        {
            return metaboxElement;
        };

        /**
        * put your comment there...
        * 
        */
        this.metaboxElementId = function()
        {
            return metaboxElementId;
        };

        /**
        * 
        */
        this.on = function(event, callback)
        {
            
            var eventName = getEventName(event);
            
            metaboxElement.on(eventName, callback);
            
            return this;
        };
        
        /**
        * 
        */
        this.setModel = function(value)
        {
            
            model = value;
            
            return this;
        };
        
        /**
        * 
        */
        this.trigger = function(event, args)
        {
            
            var eventName = getEventName(event);
            
            var result = metaboxElement.trigger(eventName, args);
            
            return result;
        };
        
        // Initialization
        $($.proxy(
            
            function()
            {
                
                // Init elements
                visualizeButtonElement = $('#' + visualizeButtonId);
                metaboxElement = $('#' + metaboxElementId);
                
                // Get Shortcode wrapper code  and Expression, from template tag
                var visualizedShortcodeTemplate = $('#cb-visual-shortcode-shortcode-wrapper-template');
                
                shortcodeTagExpr = wp.shortcode.regexp('([a-zA-Z0-9_\-]+)');

                shortcodeWrapperTag = $(visualizedShortcodeTemplate.get(0).content).find('>template').html().trim();
                
                // Use Shortcode expression to create complete expression for vsiualized tags expression
                var visualizedTagsExprText = visualizedShortcodeTemplate.attr('data-expression');
                
                visualizedTagsExprText = visualizedTagsExprText.replace(
                    '%SHORTCODE_EXPRESSION%',
                    shortcodeTagExpr.source
                );
                
                visualizedTagsExpr = new RegExp(visualizedTagsExprText, 'gm');
                
                // Create HTML TextArea Interface similar to TinyMC Editor
                // INterface so accessin them using the same method names
                htmlEditor = new function()
                {
                    
                    /**
                    * put your comment there...
                    * 
                    */
                    var contentTextArea = $('textarea#content');
                    
                    /**
                    * 
                    */
                    this.getContent = function()
                    {
                        return contentTextArea.val();
                    };
                    
                    /**
                    * 
                    */
                    this.setContent = function(value)
                    {
                        contentTextArea.val(value);
                        
                        return this;
                    }
                };
                
                // Locals
                locals = CBPostMetaboxViewVisualShortcodeL10N;
                
                // Pre Init Event
                this.trigger('PreInit', [this]);
                 
                // Setting Visual Shortcode model object
                this.setModel(new CBVisualShortcode(this));
                
                this.trigger('GetModel', [this]);
                
                if (!model)
                {
                    throw "Model is not set!";
                }
                
                visualizeButtonElement.click($.proxy(_OnVisualizeButtonClick, this));
                
                // Init Event
                this.trigger('Init', [this]);
                
                // Global event
                $(document).trigger('VisualShortcodeControllerInit', [this]);
            }
        , this));
        
    };
    
})(jQuery);