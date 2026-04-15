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
                console.log('Preview received message:', data);

                if (!data || !data.action) return;

                // Handle rendered HTML from AJAX
                if (data.action === 'jux-rendered' && data.items) {
                    console.log('Updating rendered content:', data.items);
                    self.updateRenderedContent(data.items);
                }
            });
        },

        // Update content with rendered HTML from AJAX
        // Renders exactly like Flatsome frontend output
        updateRenderedContent: function(items) {
            var self = this;
            var $container = $('#jux-builder-content, .entry-content, .page-content, article, main, body').first();

            // Clear container if first load
            if (!self._initialized) {
                $container.empty();
                self._initialized = true;
            }

            items.forEach(function(item) {
                var $el = $('[data-jux-id="' + item.id + '"]');

                if ($el.length) {
                    // Update existing element - replace inner HTML only
                    $el.html($(item.html).html());
                } else {
                    // Append new HTML (contains wrapper with data-jux-id)
                    $container.append(item.html);
                }
            });

            // Re-initialize droppable and click handlers
            self.makeDroppable();
            self.addClickHandlers();
        },

        // Add click handlers to elements for builder selection
        addClickHandlers: function(id) {
            var self = this;
            // Click on the wrapper itself or its children
            var selector = id ? '[data-jux-id="' + id + '"]' : '[data-jux-id]';

            // Remove existing handlers first to avoid duplicates
            $(selector).off('click.jux');

            $(selector).each(function() {
                var $wrapper = $(this);
                var wrapperId = $wrapper.data('jux-id');
                var tag = $wrapper.data('tag');

                // Add hover effect
                $wrapper.on('mouseenter.jux', function() {
                    $(this).addClass('jux-hover');
                }).on('mouseleave.jux', function() {
                    $(this).removeClass('jux-hover');
                });

                // Click handler on wrapper
                $wrapper.on('click.jux', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    console.log('Preview element clicked:', wrapperId, tag);

                    // Notify parent builder
                    window.parent.postMessage({
                        action: 'jux-element-click',
                        id: wrapperId,
                        tag: tag
                    }, '*');

                    // Highlight this element
                    $('[data-jux-id]').removeClass('jux-selected');
                    $wrapper.addClass('jux-selected');
                });
            });
        }
    };

    $(document).ready(function() {
        JUXPreview.init();
    });

})(jQuery);
