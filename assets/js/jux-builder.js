(function($) {
    'use strict';

    var JUXBuilder = {

        currentView: 'home',      // 'home' | 'shortcode' | 'settings'
        selectedShortcode: null,
        history: [],
        historyIndex: -1,
        elements: window.JUX_Builder ? window.JUX_Builder.elements : {},

        init: function() {
            this.bindEvents();
            this.initDeviceSwitcher();
            this.removeLoader();
        },

        // ==========================================
        // Core: View routing (mirrors Flatsome's goto())
        // ==========================================
        goto: function(view, props) {
            var self = this;

            // Deactivate all
            $('.jux-sidebar-view').hide();

            if (view === 'home') {
                $('#jux-view-home').show();
                $('#jux-sidebar-title').text(props && props.title ? props.title : JUXBuilder.postTitle);
                self.currentView = 'home';

            } else if (view === 'shortcode') {
                $('#jux-view-shortcode').show();
                $('#jux-shortcode-title').text(props && props.name ? props.name : '');
                if (props && props.options) {
                    self.renderOptions(props.options, '#jux-shortcode-options');
                } else {
                    $('#jux-shortcode-options').html('<p style="color:#999;font-size:12px;padding:10px">No options available.</p>');
                }
                self.currentView = 'shortcode';

            } else if (view === 'settings') {
                $('#jux-view-settings').show();
                self.currentView = 'settings';
            }
        },

        // ==========================================
        // App-Stack: Add Element Overlay
        // ==========================================
        openStack: function(context) {
            $('#jux-app-stack').addClass('is-visible');
            $('#jux-filter-elements').val('').trigger('input');
        },

        closeStack: function() {
            $('#jux-app-stack').removeClass('is-visible');
        },

        // ==========================================
        // Hierarchy: Render the element tree
        // ==========================================
        renderHierarchy: function(nodes, $container) {
            var self = this;
            $container = $container || $('#jux-hierarchy');
            $container.empty();

            if (!nodes || nodes.length === 0) {
                $container.html(
                    '<div class="jux-hierarchy-empty">' +
                    '<p>No content yet.</p>' +
                    '<button class="jux-btn-add-inline" id="jux-add-first-element">' +
                    '<span class="dashicons dashicons-plus"></span> Add elements' +
                    '</button></div>'
                );
                $container.find('#jux-add-first-element').on('click', function() {
                    self.openStack();
                });
                return;
            }

            nodes.forEach(function(node) {
                $container.append(self.buildHierarchyItem(node));
            });

            // Global add button
            $container.append(
                '<button class="jux-hierarchy-add-btn" data-action="add-global">' +
                '<span class="dashicons dashicons-plus"></span> Add elements' +
                '</button>'
            );
        },

        buildHierarchyItem: function(node) {
            var self = this;
            var hasChildren = node.children && node.children.length > 0;
            var info = node.info || '';

            var $item = $('<div class="jux-hierarchy-item" data-id="' + node.id + '">');

            // Get display name: _label > name > tag
            var displayName = (node.options && node.options._label) ? node.options._label : (node.name || node.tag);

            var $label = $(
                '<div class="jux-hierarchy-label">' +
                (hasChildren ? '<button class="jux-hierarchy-toggle dashicons dashicons-arrow-down-alt2"></button>' : '') +
                '<span class="jux-hierarchy-name">' + displayName + '</span>' +
                (info ? '<span class="jux-hierarchy-info">' + info + '</span>' : '') +
                '<button class="dashicons dashicons-admin-generic jux-hierarchy-gear" title="Settings"></button>' +
                '</div>'
            );

            $label.find('.jux-hierarchy-name').on('click', function() {
                self.selectNode(node, $label);
            });

            $label.find('.jux-hierarchy-gear').on('click', function(e) {
                e.stopPropagation();
                self.configureNode(node);
            });

            $item.append($label);

            if (hasChildren) {
                var $children = $('<div class="jux-hierarchy-children">');
                node.children.forEach(function(child) {
                    $children.append(self.buildHierarchyItem(child));
                });
                $children.append(
                    '<button class="jux-hierarchy-add-btn" data-parent="' + node.id + '">' +
                    '<span class="dashicons dashicons-plus"></span> Add to ' + node.name +
                    '</button>'
                );
                $item.append($children);

                // Toggle
                $label.find('.jux-hierarchy-toggle').on('click', function(e) {
                    e.stopPropagation();
                    $children.toggle();
                    $(this).toggleClass('dashicons-arrow-down-alt2 dashicons-arrow-right-alt2');
                });
            }

            return $item;
        },

        selectNode: function(node, $label) {
            $('.jux-hierarchy-label').removeClass('is-selected');
            $label.addClass('is-selected');
            this.selectedShortcode = node;

            // Scroll to element in iframe
            var frame = document.getElementById('jux-preview-frame');
            if (frame && frame.contentWindow) {
                // postMessage to preview to highlight element
                frame.contentWindow.postMessage({ action: 'jux-select', id: node.id }, '*');
            }
        },

        configureNode: function(node) {
            this.selectedShortcode = node;
            this.goto('shortcode', {
                name: node.name || node.tag,
                options: this.elements[node.tag] ? this.elements[node.tag].options : {}
            });
        },

        // ==========================================
        // Options rendering (mirrors Flatsome ux-options)
        // ==========================================
        renderOptions: function(options, selector) {
            var $container = $(selector).empty();

            if (!options || Object.keys(options).length === 0) {
                $container.html('<p style="font-size:12px;color:#999">No options for this element.</p>');
                return;
            }

            $.each(options, function(key, opt) {
                var $group = $('<div class="jux-option-group">');
                $group.append('<label class="jux-option-label">' + (opt.heading || key) + '</label>');
                var $field = $('<div class="jux-option-field">');

                if (opt.type === 'radio-buttons') {
                    var $rb = $('<div class="jux-radio-buttons">');
                    $.each(opt.options || {}, function(val, data) {
                        var title = typeof data === 'object' ? data.title : data;
                        $rb.append(
                            '<label><input type="radio" name="jux-opt-' + key + '" value="' + val + '"' +
                            (val === (opt.default || '') ? ' checked' : '') + '> ' + title + '</label>'
                        );
                    });
                    $field.append($rb);

                } else if (opt.type === 'select') {
                    var $sel = $('<select>');
                    $.each(opt.options || {}, function(val, label) {
                        var text = typeof label === 'object' ? label.title : label;
                        $sel.append('<option value="' + val + '"' + (val === opt.default ? ' selected' : '') + '>' + text + '</option>');
                    });
                    $field.append($sel);

                } else if (opt.type === 'checkbox') {
                    $field.append('<label><input type="checkbox" name="jux-opt-' + key + '"> ' + (opt.heading || '') + '</label>');

                } else if (opt.type === 'textarea') {
                    $field.append('<textarea rows="4" placeholder="' + (opt.placeholder || '') + '">' + (opt.default || '') + '</textarea>');

                } else {
                    // textfield, scrubfield, etc.
                    $field.append('<input type="text" value="' + (opt.default || '') + '" placeholder="' + (opt.placeholder || '') + '">');
                }

                $group.append($field);
                $container.append($group);
            });
        },

        // ==========================================
        // Event bindings
        // ==========================================
        bindEvents: function() {
            var self = this;

            // Open add-element stack
            $('#jux-open-add-panel, #jux-add-first-element').on('click', function() {
                self.openStack();
            });

            // Close stack
            $('#jux-stack-close, #jux-stack-backdrop').on('click', function() {
                self.closeStack();
            });

            // Add element panel tabs
            $('.add-shortcode-types button').on('click', function() {
                var panel = $(this).data('panel');
                $('.add-shortcode-types button').removeClass('active');
                $(this).addClass('active');
                $('.add-panel-body').hide();
                $('#add-panel-' + panel).show();
            });

            // Add element button click
            $(document).on('click', '.add-shortcode-box-button', function() {
                var tag = $(this).data('tag');
                self.addElement(tag);
                self.closeStack();
            });

            // Filter elements search
            $('#jux-filter-elements').on('input', function() {
                var q = $(this).val().toLowerCase();
                $('.add-shortcode-box').each(function() {
                    var name = $(this).find('.title').text().toLowerCase();
                    $(this).toggle(name.indexOf(q) > -1);
                });
                // Show/hide category headers
                $('.add-shortcode-category').each(function() {
                    var anyVisible = $(this).find('.add-shortcode-box:visible').length > 0;
                    $(this).toggle(anyVisible);
                });
            });

            // Hierarchy "Add elements" buttons
            $(document).on('click', '.jux-hierarchy-add-btn', function() {
                var parentId = $(this).data('parent');
                self.openStack({ parentId: parentId });
            });

            // Back from shortcode settings → home
            $('#jux-back-from-shortcode').on('click', function() {
                self.goto('home');
            });

            // Back from page settings → home
            $('#jux-back-from-settings').on('click', function() {
                self.goto('home');
            });

            // Page settings button
            $('#jux-open-settings').on('click', function() {
                self.goto('settings');
            });

            // Discard shortcode options
            $('#jux-discard-shortcode').on('click', function() {
                self.goto('home');
            });

            // Apply shortcode options
            $('#jux-apply-shortcode').on('click', function() {
                if (!self.selectedShortcode) {
                    self.goto('home');
                    return;
                }

                // Collect option values from form
                var options = {};
                $('#jux-shortcode-options').find('input, select, textarea').each(function() {
                    var $input = $(this);
                    var name = $input.attr('name');
                    if (!name) return;

                    var type = $input.attr('type');
                    if (type === 'checkbox') {
                        options[name] = $input.is(':checked') ? 'yes' : 'no';
                    } else if (type === 'number') {
                        options[name] = $input.val();
                    } else {
                        options[name] = $input.val();
                    }
                });

                // Update the selected node with new options
                self.selectedShortcode.options = $.extend({}, self.selectedShortcode.options, options);

                // Update element name if label changed
                if (options._label) {
                    self.selectedShortcode.name = options._label;
                }

                // Refresh hierarchy and preview
                self.refreshHierarchy();
                self.updatePreview();

                self.goto('home');
            });

            // Save / publish
            $('#jux-save-post, #jux-save-footer').on('click', function() {
                self.savePost();
            });

            // Exit builder
            $('#jux-exit-builder, #jux-sidebar-exit').on('click', function() {
                if (confirm('Exit builder? Unsaved changes will be lost.')) {
                    window.location.href = window.location.href.replace(/page=ux-builder.*$/, 'post=' + self.postId + '&action=edit');
                }
            });

            // Sidebar collapse
            $('#jux-sidebar-collapse').on('click', function() {
                $('#jux-sidebar').toggleClass('collapsed');
                var $icon = $(this).find('.dashicons');
                if ($('#jux-sidebar').hasClass('collapsed')) {
                    $icon.removeClass('dashicons-arrow-left-alt2').addClass('dashicons-arrow-right-alt2');
                } else {
                    $icon.removeClass('dashicons-arrow-right-alt2').addClass('dashicons-arrow-left-alt2');
                }
            });

            // Undo / Redo stubs
            $('#jux-undo').on('click', function() {
                if (!$(this).prop('disabled')) self.undo();
            });
            $('#jux-redo').on('click', function() {
                if (!$(this).prop('disabled')) self.redo();
            });

            // Listen for messages from iframe
            window.addEventListener('message', function(e) {
                if (!e.data || !e.data.action) return;
                self.onIframeMessage(e.data);
            });
        },

        // ==========================================
        // Device switcher
        // ==========================================
        initDeviceSwitcher: function() {
            $('.jux-device-switcher button').on('click', function() {
                var device = $(this).data('device');
                $('.jux-device-switcher button').removeClass('active');
                $(this).addClass('active');
                var $container = $('#jux-preview-container');
                $container.removeClass('desktop tablet mobile').addClass(device);
            });
        },

        // ==========================================
        // Add element to page
        // ==========================================
        addElement: function(tag) {
            var self = this;
            var el = self.elements[tag];
            if (!el) {
                console.error('Element not found:', tag);
                return;
            }

            // Build default options from element config
            var defaultOptions = {};
            if (el.options) {
                Object.keys(el.options).forEach(function(key) {
                    var opt = el.options[key];
                    if (opt && typeof opt.default !== 'undefined') {
                        defaultOptions[key] = opt.default;
                    }
                });
            }

            // Build a new node and add to hierarchy
            var node = {
                id: 'jux-' + Date.now(),
                tag: tag,
                name: el.name || tag,
                info: el.info || '',
                options: defaultOptions,
                children: el.wrap || el.type === 'container' ? [] : null
            };

            self.historyNodes = self.historyNodes || [];
            self.historyNodes.push(node);
            console.log('Added element:', node);

            self.refreshHierarchy();

            // Trigger preview update
            setTimeout(function() {
                self.updatePreview();
            }, 100);
        },

        refreshHierarchy: function() {
            this.renderHierarchy(this.historyNodes || []);
        },

        // ==========================================
        // Save post
        // ==========================================
        savePost: function() {
            var self = this;
            var $btn = $('#jux-save-footer');
            $btn.text('Saving...');

            var data = window.juxBuilderData || {};

            $.post(data.ajaxUrl || window.ajaxurl || '/wp-admin/admin-ajax.php', {
                action: 'jux_save_content',
                post_id: self.postId || data.postId,
                security: data.nonce || '',
                content: self.getShortcodeContent()
            }).done(function(response) {
                if (response.success) {
                    $btn.text('Saved!');
                    setTimeout(function() { $btn.text('Update'); }, 2000);
                } else {
                    $btn.text('Error!');
                    setTimeout(function() { $btn.text('Update'); }, 2000);
                }
            }).fail(function() {
                $btn.text('Failed');
                setTimeout(function() { $btn.text('Update'); }, 2000);
            });
        },

        getShortcodeContent: function() {
            var self = this;
            var content = '';
            
            (self.historyNodes || []).forEach(function(node) {
                content += self.buildShortcodeFromNode(node);
            });
            
            return content;
        },

        buildShortcodeFromNode: function(node) {
            var self = this;
            var tag = node.tag;
            var atts = [];
            
            // Build attributes
            Object.keys(node.options || {}).forEach(function(key) {
                if (key === '_jux_id' || key === '_label') return;
                var val = node.options[key];
                if (val !== '' && val !== undefined && val !== null) {
                    atts.push(key + '="' + val + '"');
                }
            });
            
            // Build content
            var innerContent = '';
            if (node.children && node.children.length > 0) {
                node.children.forEach(function(child) {
                    innerContent += self.buildShortcodeFromNode(child);
                });
            }
            
            // Build shortcode
            var attString = atts.length > 0 ? ' ' + atts.join(' ') : '';
            if (innerContent) {
                return '[' + tag + attString + ']' + innerContent + '[/' + tag + ']';
            } else {
                return '[' + tag + attString + ']';
            }
        },

        // ==========================================
        // Update live preview via AJAX (no page reload)
        // ==========================================
        updatePreview: function() {
            var self = this;
            
            // Get data with fallback defaults
            var data = window.juxBuilderData || {};
            var ajaxUrl = data.ajaxUrl || (window.ajaxurl || '/wp-admin/admin-ajax.php');
            var nonce = data.nonce || '';

            console.log('updatePreview - ajaxUrl:', ajaxUrl, 'nonce:', nonce ? 'set' : 'empty');

            // Build shortcodes array from hierarchy
            var shortcodes = [];
            (self.historyNodes || []).forEach(function(node) {
                shortcodes.push({
                    id: node.id,
                    tag: node.tag,
                    atts: node.options || {},
                    content: node.children ? self.buildContentFromChildren(node.children) : ''
                });
            });

            console.log('Sending shortcodes for render:', shortcodes);

            // AJAX render - must use proper POST with JSON
            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'jux_builder_render_preview',
                    nonce: nonce,
                    shortcodes: shortcodes
                }
            }).done(function(response) {
                console.log('AJAX response:', response);
                if (response.success && response.data.rendered) {
                    self.updateIframeContent(response.data.rendered);
                } else {
                    console.error('AJAX error:', response);
                }
            }).fail(function(xhr, status, error) {
                console.error('AJAX failed:', status, error);
            });
        },

        // Update iframe content with rendered HTML
        updateIframeContent: function(renderedItems) {
            var frame = document.getElementById('jux-preview-frame');
            if (!frame || !frame.contentWindow) return;

            // Send rendered HTML to iframe
            frame.contentWindow.postMessage({
                action: 'jux-rendered',
                items: renderedItems
            }, '*');
        },

        // ==========================================
        // Messages from iframe
        // ==========================================
        onIframeMessage: function(data) {
            var self = this;
            if (data.action === 'jux-element-click') {
                // User clicked element in preview → open its settings
                var node = this.findNode(data.id);
                if (node) self.configureNode(node);
            }
        },

        findNode: function(id) {
            var nodes = this.historyNodes || [];
            for (var i = 0; i < nodes.length; i++) {
                if (nodes[i].id === id) return nodes[i];
                if (nodes[i].children) {
                    for (var j = 0; j < nodes[i].children.length; j++) {
                        if (nodes[i].children[j].id === id) return nodes[i].children[j];
                    }
                }
            }
            return null;
        },

        // ==========================================
        // Undo / Redo (stubs)
        // ==========================================
        undo: function() { /* TODO */ },
        redo: function() { /* TODO */ },

        // ==========================================
        // Remove loader after iframe loads
        // ==========================================
        removeLoader: function() {
            var $wrapper = $('#jux-builder-wrapper');
            var $iframe = $('#jux-preview-frame');

            $iframe.on('load', function() {
                setTimeout(function() {
                    $wrapper.removeClass('jux-loading');
                }, 300);
            });

            // Fallback
            setTimeout(function() {
                $wrapper.removeClass('jux-loading');
            }, 5000);

            // Init hierarchy render from data
            var data = window.JUX_Builder || {};
            JUXBuilder.postId = data.post_id || 0;
            JUXBuilder.postTitle = data.post_title || '';
            JUXBuilder.elements = data.elements || {};

            JUXBuilder.renderHierarchy(data.content_tree || []);
        }
    };

    // Boot on DOM ready
    $(document).ready(function() {
        JUXBuilder.init();
    });

})(jQuery);
