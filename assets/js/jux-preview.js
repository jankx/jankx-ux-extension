(function($) {
    'use strict';

    var JUXPreview = {
        init: function() {
            this.makeDroppable();
            this.listenToParent();
        },

        makeDroppable: function() {
            var self = this;

            // Make the body or specific containers droppable
            $('body').droppable({
                accept: '.jux-element-item',
                hoverClass: 'jux-drop-hover',
                drop: function(event, ui) {
                    var tag = ui.helper.data('tag');
                    self.addElement(tag, $(this));
                }
            });

            // Handle nested dropping (rows/cols)
            $('.row, .col-inner, section').droppable({
                greedy: true,
                hoverClass: 'jux-drop-hover-nested',
                drop: function(event, ui) {
                    var tag = ui.helper.data('tag');
                    self.addElement(tag, $(this));
                }
            });
        },

        addElement: function(tag, $target) {
            console.log('Adding element:', tag, 'to', $target);
            
            // Temporary placeholder rendering
            var html = '<div class="jux-element-placeholder" data-tag="'+tag+'">['+tag+'] Content goes here...</div>';
            $target.append(html);

            // Notify parent to open settings for this new element
            window.parent.postMessage({
                action: 'jux_element_added',
                tag: tag
            }, '*');
        },

        listenToParent: function() {
            var self = this;
            window.addEventListener('message', function(event) {
                var data = event.data;
                if (!data || !data.action) return;

                // Handle rendered HTML from AJAX
                if (data.action === 'jux-rendered' && data.items) {
                    self.updateRenderedContent(data.items);
                }
            });
        },

        // Update content with rendered HTML from AJAX
        updateRenderedContent: function(items) {
            var self = this;
            items.forEach(function(item) {
                var $el = $('[data-jux-id="' + item.id + '"]');
                if ($el.length) {
                    // Update existing element
                    $el.html(item.html);
                } else {
                    // Create new element wrapper
                    var $wrapper = $('<div class="jux-element-wrapper" data-jux-id="' + item.id + '" data-tag="' + item.tag + '">');
                    $wrapper.html(item.html);
                    
                    // Add click handler to select in builder
                    $wrapper.on('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        window.parent.postMessage({
                            action: 'jux-element-click',
                            id: item.id
                        }, '*');
                    });

                    // Append to content area or find appropriate container
                    $('#jux-builder-content, .entry-content, article, main, body').first().append($wrapper);
                }
            });
        }
    };

    $(document).ready(function() {
        JUXPreview.init();
    });

})(jQuery);
