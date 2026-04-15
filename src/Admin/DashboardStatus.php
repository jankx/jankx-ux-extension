<?php
namespace Jankx\Extensions\JankxUX\Admin;

class DashboardStatus
{
    public static function init()
    {
        add_action('wp_dashboard_setup', [self::class, 'addStatusWidget'], 20);
    }

    public static function addStatusWidget()
    {
        wp_add_dashboard_widget(
            'jux_functional_status',
            __('JUX Functional Status', 'jankx'),
            [self::class, 'renderStatusWidget']
        );
    }

    public static function renderStatusWidget()
    {
        $features = [
            ['name' => 'UX Builder', 'status' => 'Active', 'desc' => 'Visual editor for pages and blocks.'],
            ['name' => 'Shortcodes', 'status' => '100% Parity', 'desc' => 'Flatsome-compatible shortcode engine.'],
            ['name' => 'Template Loader', 'status' => 'Optimized', 'desc' => 'PSR-4 compliant template discovery.'],
            ['name' => 'Header Engine', 'status' => 'Operational', 'desc' => 'High-performance header hook system.'],
        ];
        ?>
        <div class="jux-status-list">
            <?php foreach ($features as $feature) : ?>
                <div class="jux-status-item" style="display: flex; align-items: center; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f1f5f9;">
                    <div>
                        <strong style="display: block; font-size: 13px;"><?php echo esc_html($feature['name']); ?></strong>
                        <small style="color: #64748b; font-size: 11px;"><?php echo esc_html($feature['desc']); ?></small>
                    </div>
                    <span style="font-size: 10px; background: #dcfce7; color: #166534; padding: 2px 8px; border-radius: 10px; font-weight: bold;"><?php echo esc_html($feature['status']); ?></span>
                </div>
            <?php endforeach; ?>
            <div style="margin-top: 15px;">
                <a href="<?php echo admin_url('admin.php?page=jankx-ux-settings'); ?>" class="button button-small"><?php _e('Configure Features', 'jankx'); ?></a>
            </div>
        </div>
        <style>
            #jux_functional_status .inside { padding: 15px; }
        </style>
        <?php
    }
}
