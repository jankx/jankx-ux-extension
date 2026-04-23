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

                    <div id="jux-panel-elements-list">
                        <?php
                        $categories = \Jankx\Extensions\JankxUX\Builder\BuilderManager::getCategorizedElements();
                        if (!empty($categories)) :
                            foreach ($categories as $cat_name => $cat_elements) : ?>
                                <div class="add-shortcode-category">
                                    <h3><?php echo esc_html($cat_name); ?></h3>
                                    <ul>
                                        <?php foreach ($cat_elements as $tag => $el) : ?>
                                            <li class="add-shortcode-box">
                                                <button class="add-shortcode-box-button" type="button" data-tag="<?php echo esc_attr($tag); ?>">
                                                    <?php if (isset($el['thumbnail']) && !empty($el['thumbnail'])) : ?>
                                                        <img src="<?php echo esc_url($el['thumbnail']); ?>" alt="<?php echo esc_attr($el['name']); ?>">
                                                    <?php endif; ?>
                                                    <span class="title"><?php echo esc_html($el['name']); ?></span>
                                                </button>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endforeach;
                        else : ?>
                            <div class="jux-no-elements">
                                <p><?php _e('No elements registered. Shortcodes will appear here.', 'jankx'); ?></p>
                            </div>
                        <?php endif; ?>
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

<style>
/* =============================================
   JUX Builder — Flatsome Behavior Parity
   ============================================= */
:root {
    --jux-topbar-bg: #23282d;
    --jux-sidebar-bg: #fff;
    --jux-border: #e5e5e5;
    --jux-blue: #007cba;
    --jux-green: #5cb85c;
    --jux-text: #333;
    --jux-subtext: #666;
    --jux-sidebar-w: 280px;
}

*, *::before, *::after { box-sizing: border-box; }

#jux-builder-wrapper {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    z-index: 99999;
    background: #555d66;
    display: flex;
    flex-direction: column;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    font-size: 13px;
}

/* ---- TOP BAR ---- */
.jux-topbar {
    height: 46px;
    background: var(--jux-topbar-bg);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 10px;
    color: #fff;
    flex-shrink: 0;
}
.jux-topbar-left, .jux-topbar-right { display: flex; align-items: center; gap: 6px; }
.jux-topbar-center {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
}

.jux-add-button {
    background: transparent;
    border: 1px solid rgba(255,255,255,0.3);
    color: #fff;
    padding: 4px 10px;
    border-radius: 3px;
    cursor: pointer;
    font-size: 12px;
    display: flex;
    align-items: center;
    gap: 4px;
    transition: background 0.15s;
}
.jux-add-button:hover { background: rgba(255,255,255,0.1); }
.jux-add-button .dashicons { font-size: 16px; width: 16px; height: 16px; }

.jux-history { display: flex; gap: 2px; border-left: 1px solid rgba(255,255,255,0.15); padding-left: 8px; margin-left: 2px; }
.jux-btn-icon {
    background: transparent;
    border: none;
    color: rgba(255,255,255,0.7);
    cursor: pointer;
    padding: 5px;
    border-radius: 3px;
    line-height: 1;
    display: flex; align-items: center; justify-content: center;
}
.jux-btn-icon:hover { color: #fff; background: rgba(255,255,255,0.1); }
.jux-btn-icon[disabled] { opacity: 0.3; cursor: not-allowed; }
.jux-btn-icon .dashicons { font-size: 18px; width: 18px; height: 18px; }

/* Device switcher */
.jux-device-switcher { display: flex; gap: 2px; }
.jux-device-switcher button {
    background: transparent;
    border: none;
    color: rgba(255,255,255,0.5);
    padding: 5px 8px;
    cursor: pointer;
    border-radius: 3px;
    display: flex; align-items: center;
    transition: all 0.15s;
}
.jux-device-switcher button .dashicons { font-size: 18px; width: 18px; height: 18px; }
.jux-device-switcher button:hover { color: #fff; }
.jux-device-switcher button.active { color: #fff; background: rgba(255,255,255,0.15); }

/* Save / post info */
.jux-post-status-badge {
    font-size: 10px;
    color: rgba(255,255,255,0.5);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.jux-btn-save {
    background: var(--jux-blue);
    border: none;
    color: #fff;
    padding: 5px 16px;
    border-radius: 3px;
    font-weight: 600;
    font-size: 12px;
    cursor: pointer;
    transition: background 0.15s;
}
.jux-btn-save:hover { background: #005a87; }
.jux-exit-builder { color: rgba(255,255,255,0.6) !important; }

/* ---- MAIN LAYOUT ---- */
.jux-app-actions { flex-shrink: 0; }
.jux-main-area {
    flex: 1;
    display: flex;
    overflow: hidden;
}

/* ---- SIDEBAR ---- */
.jux-sidebar {
    width: var(--jux-sidebar-w);
    background: var(--jux-sidebar-bg);
    border-right: 1px solid var(--jux-border);
    display: flex;
    flex-direction: column;
    flex-shrink: 0;
    transition: width 0.25s;
    overflow: hidden;
}
.jux-sidebar.collapsed { width: 0; border: none; }

/* Title row */
.jux-sidebar-title-row {
    display: flex;
    align-items: center;
    padding: 0 8px;
    height: 45px;
    border-bottom: 1px solid var(--jux-border);
    gap: 8px;
    flex-shrink: 0;
}
.jux-sidebar-title-row .jux-btn-icon { color: #555; }
.jux-sidebar-title-row .jux-btn-icon:hover { color: #000; background: #f1f1f1; }
.jux-sidebar-title-text {
    flex: 1;
    font-weight: 600;
    font-size: 12px;
    color: var(--jux-text);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Sidebar views */
.jux-sidebar-view {
    flex: 1;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
}

/* View header (shortcode/settings views) */
.jux-view-header {
    display: flex;
    align-items: center;
    padding: 0 8px;
    height: 44px;
    border-bottom: 1px solid var(--jux-border);
    flex-shrink: 0;
    gap: 8px;
}
.jux-view-header .jux-btn-icon { color: #555; }
.jux-view-header-title {
    flex: 1;
    font-weight: 600;
    font-size: 13px;
    color: var(--jux-text);
}
.jux-view-body {
    flex: 1;
    overflow-y: auto;
    padding: 15px;
}
.jux-view-footer {
    display: flex;
    gap: 8px;
    padding: 10px 15px;
    border-top: 1px solid var(--jux-border);
    flex-shrink: 0;
}
.jux-btn-discard {
    flex: 1;
    background: transparent;
    border: 1px solid #ccc;
    padding: 7px 10px;
    border-radius: 3px;
    cursor: pointer;
    font-size: 12px;
    display: flex; align-items: center; justify-content: center; gap: 4px;
}
.jux-btn-apply {
    flex: 2;
    background: var(--jux-blue);
    color: #fff;
    border: none;
    padding: 7px 12px;
    border-radius: 3px;
    cursor: pointer;
    font-size: 12px;
    font-weight: 600;
    display: flex; align-items: center; justify-content: center; gap: 4px;
}

/* Hierarchy list */
.jux-hierarchy-root { padding: 10px; }
.jux-hierarchy-item {
    margin-bottom: 4px;
    border-radius: 3px;
    overflow: hidden;
}
.jux-hierarchy-label {
    display: flex;
    align-items: center;
    padding: 7px 10px;
    background: #2d3b45;
    color: #fff;
    font-size: 11px;
    cursor: pointer;
    gap: 6px;
    border-radius: 3px;
}
.jux-hierarchy-label:hover { background: #3a4f5c; }
.jux-hierarchy-label.is-selected { background: var(--jux-blue); }
.jux-hierarchy-toggle { flex-shrink: 0; }
.jux-hierarchy-name { flex: 1; font-weight: 500; }
.jux-hierarchy-info { font-size: 10px; opacity: 0.6; }
.jux-hierarchy-gear { opacity: 0.5; cursor: pointer; }
.jux-hierarchy-gear:hover { opacity: 1; }
.jux-hierarchy-children {
    padding-left: 16px;
    border-left: 2px solid #dde;
    margin-left: 12px;
    margin-top: 3px;
}
.jux-hierarchy-children .jux-hierarchy-label {
    background: #f1f5f7;
    color: #333;
}
.jux-hierarchy-children .jux-hierarchy-label:hover { background: #e1e8ed; }
.jux-hierarchy-add-btn {
    margin-top: 6px;
    width: 100%;
    background: transparent;
    border: 1px dashed #ccc;
    padding: 6px;
    border-radius: 3px;
    color: var(--jux-blue);
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
    text-align: left;
    display: flex; align-items: center; gap: 4px;
}
.jux-hierarchy-add-btn:hover { border-color: var(--jux-blue); background: #f0f8ff; }
.jux-hierarchy-empty { text-align: center; padding: 30px 15px; color: #888; }
.jux-btn-add-inline {
    margin-top: 10px;
    background: var(--jux-blue);
    color: #fff;
    border: none;
    padding: 8px 16px;
    border-radius: 3px;
    cursor: pointer;
    font-size: 12px;
    display: inline-flex; align-items: center; gap: 6px;
}

/* Sidebar Footer */
.jux-sidebar-footer {
    display: flex;
    align-items: center;
    padding: 8px 10px;
    gap: 8px;
    border-top: 1px solid var(--jux-border);
    flex-shrink: 0;
    background: #fafafa;
}
.jux-btn-save-footer {
    flex: 1;
    background: var(--jux-blue);
    color: #fff;
    border: none;
    padding: 8px;
    border-radius: 3px;
    font-weight: 700;
    font-size: 12px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    cursor: pointer;
}
.jux-btn-save-footer:hover { background: #005a87; }
.jux-sidebar-collapse-btn {
    background: #333;
    border: none;
    color: #fff;
    width: 34px;
    height: 34px;
    border-radius: 3px;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.jux-sidebar.collapsed + .jux-preview-area .jux-sidebar-expand-btn { display: flex; }

/* ---- PREVIEW CANVAS ---- */
.jux-preview-area {
    flex: 1;
    background: #555d66;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    position: relative;
}
.jux-preview-container {
    background: #fff;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
}
.jux-preview-container.desktop { width: 100%; height: 100%; }
.jux-preview-container.tablet {
    width: 768px;
    height: calc(100% - 40px);
    border: 10px solid #1a1a1a;
    border-radius: 12px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.5);
}
.jux-preview-container.mobile {
    width: 390px;
    height: calc(100% - 40px);
    border: 10px solid #1a1a1a;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.5);
}
#jux-preview-frame {
    width: 100%;
    height: 100%;
    border: none;
    background: #fff;
    display: block;
}
.jux-iframe-overlay {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    display: none;
    z-index: 10;
}
.jux-iframe-overlay.active { display: block; }

/* ---- APP STACK (Add Element Overlay) ---- */
.jux-app-stack {
    display: none;
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    z-index: 999999;
}
.jux-app-stack.is-visible { display: flex; align-items: stretch; }
.jux-stack-backdrop {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.5);
}
.jux-stack-wrapper {
    position: relative;
    z-index: 1;
    background: #fff;
    width: 320px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    box-shadow: 4px 0 20px rgba(0,0,0,0.3);
}
.jux-stack-close {
    position: absolute;
    top: 10px;
    right: 10px;
    background: transparent;
    border: none;
    font-size: 20px;
    cursor: pointer;
    color: #555;
    z-index: 2;
    line-height: 1;
    width: 30px;
    height: 30px;
    display: flex; align-items: center; justify-content: center;
    border-radius: 50%;
}
.jux-stack-close:hover { background: #f1f1f1; color: #333; }

/* Add panel */
.jux-add-panel {
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: hidden;
}
.add-shortcode-header {
    padding: 15px 15px 0;
    flex-shrink: 0;
}
.add-shortcode-header h2.title {
    margin: 0 0 10px;
    font-size: 15px;
    font-weight: 700;
}
.add-shortcode-types {
    display: flex;
    gap: 0;
    border-bottom: 1px solid var(--jux-border);
}
.add-shortcode-types button {
    background: transparent;
    border: none;
    border-bottom: 2px solid transparent;
    padding: 8px 14px 9px;
    font-size: 12px;
    font-weight: 600;
    color: #888;
    cursor: pointer;
    margin-bottom: -1px;
}
.add-shortcode-types button.active {
    color: var(--jux-blue);
    border-bottom-color: var(--jux-blue);
}
.add-panel-body {
    flex: 1;
    overflow-y: auto;
    padding: 12px 12px 20px;
}
.jux-studio-btn {
    width: 100%;
    background: var(--jux-blue);
    color: #fff;
    border: none;
    padding: 9px 12px;
    border-radius: 3px;
    font-weight: 600;
    font-size: 12px;
    cursor: pointer;
    display: flex; align-items: center; gap: 8px;
    margin-bottom: 10px;
}
.filter-elements {
    width: 100%;
    border: 1px solid #ddd;
    border-radius: 3px;
    padding: 7px 10px;
    font-size: 12px;
    color: #333;
    outline: none;
    margin-bottom: 12px;
}
.filter-elements:focus { border-color: var(--jux-blue); }

/* Element categories and grid */
.add-shortcode-category h3 {
    font-size: 10px;
    font-weight: 700;
    color: #aaa;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin: 12px 0 8px;
}
.add-shortcode-category ul {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6px;
    padding: 0;
    margin: 0;
    list-style: none;
}
.add-shortcode-box-button {
    width: 100%;
    background: #fff;
    border: 1px solid #e8e8e8;
    border-radius: 3px;
    padding: 10px 8px;
    cursor: pointer;
    text-align: center;
    transition: border-color 0.15s, box-shadow 0.15s;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
}
.add-shortcode-box-button:hover {
    border-color: var(--jux-blue);
    box-shadow: 0 0 0 1px var(--jux-blue);
}
.add-shortcode-box-button img {
    width: 100%;
    max-width: 60px;
    height: auto;
}
.add-shortcode-box-button .title { font-size: 10px; font-weight: 600; color: #555; }
.jux-no-elements { text-align: center; padding: 30px 10px; color: #888; font-size: 12px; }
.jux-btn-import {
    width: 100%;
    background: var(--jux-blue);
    color: #fff;
    border: none;
    padding: 10px;
    border-radius: 3px;
    font-weight: 700;
    font-size: 13px;
    cursor: pointer;
}

/* ---- LOADER ---- */
.jux-loader-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: #fff;
    z-index: 100001;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: opacity 0.4s, visibility 0.4s;
}
.jux-builder-ui:not(.jux-loading) .jux-loader-overlay {
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
}
.jux-loader-content { text-align: center; }
.jux-spinner {
    width: 36px; height: 36px;
    border: 3px solid #eee;
    border-top-color: var(--jux-blue);
    border-radius: 50%;
    animation: jux-spin 0.8s linear infinite;
    margin: 0 auto 12px;
}
.jux-loader-text { font-size: 14px; font-weight: 600; color: #444; }
@keyframes jux-spin { to { transform: rotate(360deg); } }

/* Shortcode Options Styles - Flatsome-inspired */
.jux-option-group {
    margin-bottom: 20px;
    background: #fafafa;
    border-radius: 4px;
    padding: 12px;
    border: 1px solid #eee;
}
.jux-option-label {
    display: flex;
    align-items: center;
    font-size: 11px;
    font-weight: 600;
    color: #444;
    text-transform: none;
    letter-spacing: 0;
    margin-bottom: 8px;
    padding-bottom: 8px;
    border-bottom: 1px solid #e0e0e0;
}
.jux-option-label::before {
    content: '';
    display: inline-block;
    width: 3px;
    height: 14px;
    background: var(--jux-blue);
    margin-right: 8px;
    border-radius: 2px;
}
.jux-option-field input[type="text"],
.jux-option-field select,
.jux-option-field textarea {
    width: 100%;
    border: 1px solid #ccc;
    border-radius: 4px;
    padding: 8px 12px;
    font-size: 13px;
    color: #333;
    outline: none;
    background: #fff;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.jux-option-field input:focus,
.jux-option-field select:focus,
.jux-option-field textarea:focus {
    border-color: var(--jux-blue);
    box-shadow: 0 0 0 2px rgba(0,124,186,0.1);
}
.jux-option-field textarea {
    min-height: 80px;
    resize: vertical;
}
.jux-option-field select {
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23666' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 10px center;
    padding-right: 30px;
}
.jux-radio-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}
.jux-radio-buttons label {
    flex: 1;
    min-width: 60px;
    text-align: center;
    border: 1px solid #ccc;
    border-radius: 4px;
    padding: 8px 12px;
    font-size: 12px;
    cursor: pointer;
    background: #fff;
    transition: all 0.2s;
}
.jux-radio-buttons label:hover {
    border-color: var(--jux-blue);
    background: #f0f8ff;
}
.jux-radio-buttons label:has(input:checked) {
    background: var(--jux-blue);
    color: #fff;
    border-color: var(--jux-blue);
}
.jux-radio-buttons input { display: none; }

/* Color picker styling */
.jux-option-field input[type="color"] {
    width: 60px;
    height: 36px;
    padding: 2px;
    border: 1px solid #ccc;
    border-radius: 4px;
    cursor: pointer;
}

/* Checkbox styling */
.jux-option-field input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

/* Element Name field special styling */
.jux-option-group:first-child {
    background: #fff;
    border: 2px solid var(--jux-blue);
}
.jux-option-group:first-child .jux-option-label {
    color: var(--jux-blue);
    font-weight: 700;
}

/* Collapse expand button (when sidebar is collapsed) */
.jux-sidebar-expand {
    position: absolute;
    top: 50%;
    left: 0;
    transform: translateY(-50%);
    background: var(--jux-topbar-bg);
    color: #fff;
    border: none;
    width: 24px;
    height: 60px;
    cursor: pointer;
    border-radius: 0 4px 4px 0;
    display: flex; align-items: center; justify-content: center;
    z-index: 10;
}
</style>
<?php 
$jux_app = \Jankx\Extensions\JankxUX\Builder\Core\Application::getInstance();
wp_print_scripts($jux_app->builder_scripts); 
wp_print_footer_scripts();
?>
</body>
</html>
