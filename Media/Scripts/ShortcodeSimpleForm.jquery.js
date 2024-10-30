/**
* 
*/
(function($)
{

    /**
    * put your comment there...
    * 
    * @param jQElement
    * @param options
    */
    var CBVSSimpleShortcodeForm = function(jQElement, options)
    {
        
        /**
        * put your comment there...
        * 
        */
        var $this = this;
        
        /**
        * put your comment there...
        * 
        */
        var btnClose;
        
        /**
        * put your comment there...
        * 
        */
        var btnSubmit;
        
        /**
        * put your comment there...
        * 
        */
        var liveFormElement;
        
        /**
        * 
        */
        var __init = function()
        {
            
            // Default Options
            options = $.extend({}, options);
            
            // Live Form Element
            liveFormElement = $('#cb-visual-shortcode-live-simple-form');
            
            btnSubmit = liveFormElement.find('#cbvs-simple-form-submit');
            btnClose = liveFormElement.find('#cbvs-simple-form-close');

        }
        
        /**
        * put your comment there...
        * 
        */
        var _onClose = function()
        {
            
            close();
            
            return false;
        };
        
        /**
        * put your comment there...
        * 
        */
        var _onSubmit = function()
        {
            
            jQElement.trigger('submit');
            
            close();
            
            return false;
        };

        var close = function()
        {
            liveFormElement.dialog('close');
        }
        
        /**
        * 
        */
        this.show = function(shortcode, shortcodeAttrs)
        {
            
            var fieldsListElement = liveFormElement.find('ul');
            
            liveFormElement.find('ul').empty();
            
            // Display Simple form from the provided values/shortcode-attrs
            $.each(shortcodeAttrs,
            
                function(name, value)
                {
                    // Create Label and Input for every attribute
                    // and add as a single simple row
                    fieldsListElement.append(
                        '<li><label>%LABEL%</label><input data-label="%LABEL%" type="text" value="%VALUE%"/></li>'
                        .replace(/\%LABEL\%/g, name)
                        .replace(/\%VALUE\%/g, value)
                    );
                }
            );
            
            // Display Thickbox form
            liveFormElement.dialog(
                {
                    modal : true,
                    draggable : false,
                    close : terminate,
                    closeOnEscape : false,
                    maxHeight : 200
                }
            );
            
            // Events
            btnSubmit.bind('click.shortcodeForm', _onSubmit);
            btnClose.bind('click.shortcodeForm', _onClose);
        };
        
        /**
        * 
        */
        this.serializeFields = function()
        {
            
            var fields = {};
            
            liveFormElement.find('ul').find('>li>:text').each(
            
                function()
                {
                    var jInput = $(this);
                    
                    fields[jInput.data('label')] = jInput.val();
                }
            );
            
            return fields;
        };
   
        /**
        * put your comment there...
        * 
        */
        var terminate = function()
        {
            
            console.log('terminae');
            
            btnSubmit.unbind('click.shortcodeForm');
            btnClose.unbind('click.shortcodeForm');
            
            jQElement.data('__CBVSSimpleShortcodeForm__', null);
            
            delete $this;
        };
        
        __init();
        
    };
    
    /**
    * put your comment there...
    * 
    */
    $.fn.CBVSSimpleShortcodeForm = function(action)
    {
        
        /**
        * put your comment there...
        * 
        */
        var args = arguments;
        
        /**
        * put your comment there...
        * 
        */
        var result = [];
        
        /**
        * 
        */
        this.each(
        
            function()
            {
                
                var jQElement = $(this);
                
                // Construct new Plugin
                if (!jQElement.data('__CBVSSimpleShortcodeForm__'))
                {
                    jQElement.data(
                        '__CBVSSimpleShortcodeForm__',
                        new CBVSSimpleShortcodeForm(jQElement, action)
                    );
                }
                
                plugin = jQElement.data('__CBVSSimpleShortcodeForm__');
                
                // Constructor / method call
                var isMethodCall = (typeof(action) == 'string') ?
                                    true :
                                    false;
                                    
                if (isMethodCall)
                {
                    
                    var callReturns = plugin[action].apply(plugin, $.makeArray(args).slice(1));
                    
                    result.push(callReturns);
                }
                else if (isMethodCall)
                {
                    result.push(jQElement);
                }
            }
        
        );
        
        return $(result);
    };
    
})(jQuery);