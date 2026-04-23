<?php
/**
 * UX Builder Canvas Template
 * Structure mirrors Flatsome's actual UX Builder behavior:
 *  - Sidebar default = Hierarchy list (home view)
 *  - "+" opens app-stack overlay with element picker
 *  - Clicking gear on element = shortcode settings view in sidebar
 *
 * NOTE: juxBuilderData is already injected by Application::enqueueEditorAssets()
 * via wp_add_inline_script() before jux-builder-core loads. Do NOT redefine it here.
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php _e('JUX Builder', 'jankx'); ?></title>
    <script type="text/javascript">
        /* Early initialization to prevent ReferenceError: wp is not defined */
        window.wp = window.wp || {};
        window.wp.i18n = window.wp.i18n || { setLocaleData: function() {} };
        window.moment = window.moment || function() { return { format: function() { return ""; } }; };
    </script>
    <?php 
    $jux_app = \Jankx\Extensions\JankxUX\Builder\Core\Application::getInstance();
    
    // Enqueue core scripts
    wp_enqueue_script('wp-polyfill');
    wp_enqueue_script('wp-i18n');
    wp_enqueue_script('wp-api-fetch');
    wp_enqueue_script('wp-data');
    wp_enqueue_script('wp-date');
    
    wp_print_styles($jux_app->builder_styles); 
    ?>
</head>
<body <?php body_class('wp-admin wp-core-ui jux-builder-body'); ?>>

<div id="jux-builder-wrapper" class="jux-builder-ui jux-loading">

    <!-- Loader Overlay -->
    <div class="jux-loader-overlay">
        <div class="jux-loader-content">
            <div class="jux-spinner"></div>
            <div class="jux-loader-text"><?php _e('Loading UX Builder', 'jankx'); ?></div>
        </div>
    </div>

    <!-- App Actions bar (Left vertical, like Flatsome) -->
    <div class="jux-app-actions">
        <div class="jux-topbar">
            <div class="jux-topbar-left">
                <button class="jux-add-button" id="jux-open-add-panel" title="<?php _e('Add Element', 'jankx'); ?>">
                    <span class="dashicons dashicons-plus-alt2"></span>
                    <?php _e('Add', 'jankx'); ?>
                </button>

                <div class="jux-history">
                    <button class="jux-btn-icon" id="jux-undo" title="Undo" disabled>
                        <span class="dashicons dashicons-undo"></span>
                    </button>
                    <button class="jux-btn-icon" id="jux-redo" title="Redo" disabled>
                        <span class="dashicons dashicons-redo"></span>
                    </button>
                </div>
            </div>

            <div class="jux-topbar-center">
                <div class="jux-device-switcher">
                    <button class="active" data-device="desktop" title="Desktop">
                        <span class="dashicons dashicons-desktop"></span>
                    </button>
                    <button data-device="tablet" title="Tablet">
                        <span class="dashicons dashicons-tablet"></span>
                    </button>
                    <button data-device="mobile" title="Mobile">
                        <span class="dashicons dashicons-smartphone"></span>
                    </button>
                </div>
            </div>

            <div class="jux-topbar-right">
                <span class="jux-post-status-badge"><?php echo esc_html(ucfirst($post->post_status ?? 'draft')); ?></span>
                <button class="jux-btn-save" id="jux-save-post">
                    <?php echo ($post->post_status === 'publish') ? __('Update', 'jankx') : __('Publish', 'jankx'); ?>
                </button>
                <button class="jux-btn-icon jux-exit-builder" id="jux-exit-builder" title="<?php _e('Exit Builder', 'jankx'); ?>">
                    <span class="dashicons dashicons-no-alt"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Main layout -->
    <div class="jux-main-area">

        <!-- LEFT SIDEBAR -->
        <div class="jux-sidebar" id="jux-sidebar">

            <!-- SIDEBAR HEADER (title row) -->
            <div class="jux-sidebar-title-row">
                <div class="jux-sidebar-title-icon">
                    <button class="jux-btn-icon" id="jux-sidebar-exit" title="<?php _e('Exit Builder', 'jankx'); ?>">
                        <span class="dashicons dashicons-no-alt"></span>
                    </button>
                </div>
                <div class="jux-sidebar-title-text" id="jux-sidebar-title">
                    <?php echo esc_html($post->post_title ?? __('(no title)', 'jankx')); ?>
                </div>
                <div class="jux-sidebar-title-actions">
                    <button class="jux-btn-icon" id="jux-open-settings" title="<?php _e('Page Settings', 'jankx'); ?>">
                        <span class="dashicons dashicons-admin-generic"></span>
                    </button>
                </div>
            </div>

            <!-- HOME VIEW: Hierarchy List -->
            <div class="jux-sidebar-view" id="jux-view-home">
                <div class="jux-hierarchy-root" id="jux-hierarchy">
                    <div class="jux-hierarchy-empty">
                        <p><?php _e('No content yet.', 'jankx'); ?></p>
                        <button class="jux-btn-add-inline" id="jux-add-first-element">
                            <span class="dashicons dashicons-plus"></span>
                            <?php _e('Add elements', 'jankx'); ?>
                        </button>
                    </div>
                </div>
            </div>

            <!-- SHORTCODE / ELEMENT SETTINGS VIEW -->
            <div class="jux-sidebar-view" id="jux-view-shortcode" style="display:none;">
                <div class="jux-view-header">
                    <div class="jux-view-header-icon">
                        <button class="jux-btn-icon" id="jux-back-from-shortcode" title="<?php _e('Back', 'jankx'); ?>">
                            <span class="dashicons dashicons-arrow-left-alt2"></span>
                        </button>
                    </div>
                    <div class="jux-view-header-title" id="jux-shortcode-title">Row</div>
                    <div class="jux-view-header-actions"></div>
                </div>
                <div class="jux-view-body" id="jux-shortcode-options">
                    <!-- Dynamic options rendered by JS -->
                </div>
                <div class="jux-view-footer">
                    <button class="jux-btn-discard" id="jux-discard-shortcode"><?php _e('Discard', 'jankx'); ?></button>
                    <button class="jux-btn-apply" id="jux-apply-shortcode"><?php _e('Apply', 'jankx'); ?></button>
                </div>
            </div>

            <!-- PAGE SETTINGS VIEW -->
            <div class="jux-sidebar-view" id="jux-view-settings" style="display:none;">
                <div class="jux-view-header">
                    <div class="jux-view-header-icon">
                        <button class="jux-btn-icon" id="jux-back-from-settings">
                            <span class="dashicons dashicons-arrow-left-alt2"></span>
                        </button>
                    </div>
                    <div class="jux-view-header-title"><?php _e('Page Settings', 'jankx'); ?></div>
                </div>
                <div class="jux-view-body" id="jux-settings-options">
                    <!-- Page-level settings -->
                </div>
                <div class="jux-view-footer">
                    <button class="jux-btn-discard" id="jux-discard-settings">
                        <span class="dashicons dashicons-no-alt"></span>
                        <?php _e('Discard', 'jankx'); ?>
                    </button>
                    <button class="jux-btn-apply" id="jux-apply-settings">
                        <span class="dashicons dashicons-yes"></span>
                        <?php _e('Apply', 'jankx'); ?>
                    </button>
                </div>
            </div>

            <!-- SIDEBAR FOOTER -->
            <div class="jux-sidebar-footer">
                <button class="jux-btn-save-footer" id="jux-save-footer">
                    <?php echo ($post->post_status === 'publish') ? __('Update', 'jankx') : __('Save Draft', 'jankx'); ?>
                </button>
                <button class="jux-sidebar-collapse-btn" id="jux-sidebar-collapse" title="<?php _e('Collapse Sidebar', 'jankx'); ?>">
                    <span class="dashicons dashicons-arrow-left-alt2"></span>
                </button>
            </div>
        </div>

        <!-- PREVIEW CANVAS -->
        <div class="jux-preview-area" id="jux-preview-area">
            <div class="jux-preview-container desktop" id="jux-preview-container">
                <?php
                $preview_url = $post_id ? get_permalink($post_id) : home_url('/');
                $preview_url = add_query_arg('jux-preview', 'true', $preview_url);
                ?>
                <iframe id="jux-preview-frame" src="<?php echo esc_url($preview_url); ?>"></iframe>
                <div class="jux-iframe-overlay" id="jux-iframe-overlay"></div>
            </div>
        </div>
    </div>

    <!-- ADD ELEMENT OVERLAY (app-stack) - mimics Flatsome's add-shortcode panel -->
    <div class="jux-app-stack" id="jux-app-stack">
        <div class="jux-stack-backdrop" id="jux-stack-backdrop"></div>
        <div class="jux-stack-wrapper">
            <button class="jux-stack-close" id="jux-stack-close">&times;</button>

            <div class="jux-add-panel">
                <!-- Header -->
                <div class="add-shortcode-header">
                    <h2 class="title"><?php _e('Add Content', 'jankx'); ?></h2>
                    <nav class="add-shortcode-types">
                        <button class="active" data-panel="elements"><?php _e('Elements', 'jankx'); ?></button>
                        <button data-panel="import"><?php _e('Import', 'jankx'); ?></button>
                    </nav>
                </div>

                <!-- Elements panel -->
                <div class="add-panel-body" id="add-panel-elements">
                    <?php if (true): // Show Flatsome Studio button if applicable ?>
                    <div class="flatsome-studio-button">
                        <button type="button" class="jux-studio-btn">
                            <span class="dashicons dashicons-screenoptions"></span>
                            <?php _e('Flatsome Studio', 'jankx'); ?>
                        </button>
                        <hr/>
                    </div>
                    <?php endif; ?>

                    <input class="filter-elements" type="text" placeholder="<?php _e('Search&hellip;', 'jankx'); ?>" id="jux-filter-elements">

                    <div id="jux-panel-elements-list" class="add-shortcode-list">
                        <!-- Elements are rendered dynamically via JavaScript (AddPanelView.ts) -->
                        <div class="jux-loader-small"></div>
                    </div>
                </div>

                <!-- Import panel -->
                <div class="add-panel-body" id="add-panel-import" style="display:none;">
                    <div class="template-importer">
                        <textarea style="height: calc(100vh - 185px); margin-top: 20px; margin-bottom: 15px; width: 100%; font-size: .9rem"
                            id="jux-import-content"
                            placeholder="<?php _e('Insert exported code or shortcodes here', 'jankx'); ?>"></textarea>
                        <button type="button" class="jux-btn-import" id="jux-import-submit">
                            <?php _e('Import', 'jankx'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div><!-- #jux-builder-wrapper -->


<?php 
$jux_app = \Jankx\Extensions\JankxUX\Builder\Core\Application::getInstance();
wp_print_scripts($jux_app->builder_scripts); 
wp_print_footer_scripts();
?>
</body>
</html>
