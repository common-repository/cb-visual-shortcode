/**
* 
*/

var CBVisualShortcode;

/**
* 
*/
(function($)
{

    /**
    * put your comment there...
    * 
    * @param controller
    * @param options
    */
    CBVisualShortcode = function(controller, options)
    {
        
        /**
        * put your comment there...
        * 
        * @param controller
        * @param options
        */
        var $this = this;
                
        /**
        * put your comment there...
        * 
        */
        var shortcode;
        
        /**
        * put your comment there...
        * 
        */
        var shortcodeAttrs;
        
        // Default options
        var opts = $.extend(
            {
                
            },
            options
        );
        
        /**
        * put your comment there...
        * 
        * @param jQElement
        */
        var _onSubmitForm = function(event)
        {
            
            var element = $(event.target);
            
            var formConstructor = element.data('formConstructor');
            
            var serializedFields = element[formConstructor]('serializeFields')[0];
            
            $this.writeShortcodeAttrs(element, serializedFields);
        };
        
        /**
        * 
        */
        this.displayShortcodeForm = function(element)
        {
            
            // Read Shortcode form attributes
            var shortcodeElement = element.find('.cb-visual-shortcode-shortcode');
            
            // Parse Shortcode to get Tag and Attributes
            var shortcodeExpression = controller.getShortcodeTagsExpression();
            
            shortcode = shortcodeElement.html().match(new RegExp(shortcodeExpression.source));

            // As we will join Shortcode segments later when form submitted
            // we don't need to render "undefined" values, set all undefined values to empty string
            // so it print nothing
            for (var seg in shortcode)
            {
                if (shortcode[seg] === undefined)
                {
                    shortcode[seg] = '';
                }
            }
            
            var shortcodeTag = shortcode[2];
            
            shortcodeAttrs = $this.parseShortcodeAttrs(shortcode[4].trim());
            
            // Create new Shortcode form
            // Allow form to be supplied from extensions
            var eventObject = new function()
            {
                
                /**
                * 
                */
                this.formConstructor = 'CBVSSimpleShortcodeForm';
                
                /**
                * put your comment there...
                * 
                */
                this.shortcode = shortcodeTag;
                
                /**
                * put your comment there...
                * 
                */
                this.shortcodeAttrs = shortcodeAttrs;
                
            };
            
            controller.trigger('FormConstructor', [eventObject]);
            
            // Save Form COnstructor used with the element to be used later when serializing shortcode attrs
            element.data('formConstructor', eventObject.formConstructor);
            
            // COnstruct Form
            element[eventObject.formConstructor]({});
            
            element.on('submit', _onSubmitForm);
            
            // Display form with attributes
            element[eventObject.formConstructor](
                'show',
                shortcodeTag,
                shortcodeAttrs
            );
        };
        
        /**
        * 
        */
        this.getOptions = function()
        {
            return opts;
        };
        
        /**
        * 
        */
        this.switchFormControl = function(element, shortcode, attrs, formConstructor)
        {
            
            if (!formConstructor)
            {
                formConstructor = 'CBVSSimpleShortcodeForm';
            }
            
            element[formConstructor]({});
            element[formConstructor]('show', shortcode, attrs);
            element.data('formConstructor', formConstructor);
            
        };
        
        /**
        * 
        */
        this.parseShortcodeAttrs = function(attrsStr)
        {
            
            // Parse Shortcode Attrs
            var attrs = wp.shortcode.attrs(attrsStr);    
            
            return attrs.named;
        };
        
        /**
        * put your comment there...
        * 
        * @param content
        */
        this.visualizeShortcodes = function(content)
        {
            
            // Initialize
            var visualizedShortcodes = [];
            var newWrappedShortcodes = [];
            var visualizedShortcode;
            var shortcode;
            var newWrappedShortcode;
            
            var visualizedTagsExpr = controller.getVisualizedTagsExpression();
            var shortcodeTagExpr = controller.getShortcodeTagsExpression();
            
            /**
            * Check whether the current shortocde is already
            * wrapper with visual shortcode tags VST
            * 
            * @returns {Boolean}
            */
            var isVisualized = function()
            {
                
                var visualizedShortcode;
                var index = 0;
                
                for (; index < visualizedShortcodes.length; index++)
                {
                    
                    visualizedShortcode = visualizedShortcodes[index];
                    
                    // disacrd visualized shortcode
                    if ((shortcode.index > visualizedShortcode.startOffset) && 
                        (shortcode.index < visualizedShortcode.endOffset))
                    {
                        
                        return true;
                    }                    
                }
                
                return false;
            };
            
            // Get all Already visualized Shortcodes to avoid 
            // visualizing (wrap with visual shortcode plugin HTML tags!) it twice
            while (visualizedShortcode = visualizedTagsExpr.exec(content))
            {

                visualizedShortcodes.push(
                    {
                        startOffset : visualizedShortcode.index,
                        endOffset : (visualizedShortcode.index + visualizedShortcode[0].length)
                    }
                );
            }
            
            var shortcodeWrapperTag = controller.getShortcodeWrapperTag();
            
            // Find all Shortcodes, don't process those already visualized
            while (shortcode = shortcodeTagExpr.exec(content))
            {
            
                if (!isVisualized(shortcode))
                {
                    
                    // Wrap snapped shortcode
                    newWrappedShortcodes.push(
                        {
                            shortcode : shortcode,
                            wrapperShortcodeHTML : shortcodeWrapperTag
                                                    .replace('%shortcode_name%', shortcode[2])
                                                    .replace('%shortcode%', shortcode[0])
                        }
                    );
                    
                }

            }
            
            // Avoid index changing issue by reversing elements
            newWrappedShortcodes = newWrappedShortcodes.reverse();
            
            for (var index in newWrappedShortcodes)
            {
                
                newWrappedShortcode = newWrappedShortcodes[index];
                
                // Replace in conent
                content =   content.substring(0, newWrappedShortcode.shortcode.index) + 
                
                            newWrappedShortcode.wrapperShortcodeHTML + 
                            
                            content.substring(( newWrappedShortcode.shortcode.index + 
                                                newWrappedShortcode.shortcode[0].length)
                                                );

            }

            return content;
            
        };
        
        /**
        * put your comment there...
        * 
        * @param jQElement
        * @param fields
        */
        this.writeShortcodeAttrs = function(jQElement, fields)
        {
            
            // Generate Attrs string
            var newAttrs = [];
            var newAttrsStr;
            
            $.each(fields,
            
                function(name, value)
                {
                    newAttrs.push(name + '="' + value + '"');
                }
                
            );
            
            newAttrsStr = newAttrs.join(' ');
            
            // Replace Shortcode attrs with new attrs 
            shortcodeElement = jQElement.find('.cb-visual-shortcode-shortcode');
            
            var newShortcode = '[' + shortcode[2] + ' ' + newAttrsStr + shortcode[5] + ']' + shortcode[6] + shortcode[7];
            
            shortcodeElement.html(newShortcode);
        };
        
    };
    
})(jQuery);