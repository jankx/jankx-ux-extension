(function(wp) {
    'use strict';

    if (!wp || !wp.editPost) return;

    var el = wp.element.createElement;
    var __ = wp.i18n.__;
    var registerPlugin = wp.plugins.registerPlugin;
    var PluginPostStatusInfo = wp.editPost.PluginPostStatusInfo;
    var compose = wp.compose.compose;
    var withSelect = wp.data.withSelect;

    // We can use PluginMoreMenuItem or PluginPostStatusInfo
    // But for the TOP BAR, we might need a custom approach or PluginPostStatusInfo (sidebar)
    
    var UXBuilderButton = function(props) {
        if (!props.isOpen) return null;

        var url = jankx_ux_gutenberg.builder_url;

        // Create the button element
        return el(
            'a',
            {
                href: url,
                className: 'button button-primary jux-gutenberg-button',
                style: {
                    marginLeft: '10px',
                    backgroundColor: '#007cba',
                    color: '#fff',
                    borderRadius: '4px',
                    fontWeight: '600',
                    display: 'flex',
                    alignItems: center
                }
            },
            __('Edit with UX Builder', 'jankx')
        );
    };

    // To get it into the TOP BAR, WP doesn't have a public stable slot yet for the primary toolbar.
    // However, many themes just inject it using DOM ready.
    
    wp.domReady(function() {
        var toolbar = document.querySelector('.edit-post-header__toolbar');
        if (toolbar) {
            var btn = document.createElement('a');
            btn.href = jankx_ux_gutenberg.builder_url;
            btn.innerText = jankx_ux_gutenberg.button_text;
            btn.className = 'button button-primary jux-gutenberg-button';
            btn.style.marginLeft = '10px';
            btn.style.backgroundColor = '#d26e4b'; // Flatsome orange-ish color
            btn.style.borderColor = '#d26e4b';
            btn.style.color = '#fff';
            
            toolbar.appendChild(btn);
        }
    });

})(window.wp);
