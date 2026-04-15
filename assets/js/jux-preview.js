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
            window.addEventListener('message', function(event) {
                // Future communication from builder to preview
            });
        }
    };

    $(document).ready(function() {
        JUXPreview.init();
    });

})(jQuery);
