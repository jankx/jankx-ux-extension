<?php
namespace Jankx\Extensions\JankxUX\Builder\Elements;

/**
 * Section Element - [section] shortcode
 * Flatsome compatible
 */
class Section extends AbstractElement
{
    protected static $tag = 'section';

    protected static function getConfig()
    {
        return [
            'type' => 'container',
            'name' => 'Section',
            'title' => __('Section', 'jankx'),
            'category' => __('Layout', 'jankx'),
            'description' => __('Full-width content section with background options.', 'jankx'),
            'wrap' => true,
            'options' => [
                '_label' => ['type' => 'text', 'heading' => __('Element Name', 'jankx'), 'default' => '', 'placeholder' => __('Custom name for this element', 'jankx')],
                'label' => ['type' => 'text', 'heading' => __('Label', 'jankx'), 'default' => ''],
                'bg_color' => ['type' => 'color', 'heading' => __('Background Color', 'jankx'), 'default' => ''],
                'bg' => ['type' => 'image', 'heading' => __('Background Image', 'jankx'), 'default' => ''],
                'padding' => ['type' => 'select', 'heading' => __('Padding', 'jankx'), 'default' => '',
                    'options' => ['' => __('Default', 'jankx'), 'small' => __('Small', 'jankx'), 'large' => __('Large', 'jankx')]],
                'margin' => ['type' => 'text', 'heading' => __('Margin', 'jankx'), 'default' => ''],
                'border' => ['type' => 'select', 'heading' => __('Border', 'jankx'), 'default' => '',
                    'options' => ['' => __('None', 'jankx'), 'top' => __('Top', 'jankx'), 'bottom' => __('Bottom', 'jankx')]],
                'id' => ['type' => 'text', 'heading' => __('Section ID', 'jankx'), 'default' => ''],
                'class' => ['type' => 'text', 'heading' => __('Custom Class', 'jankx'), 'default' => ''],
            ],
            'presets' => [],
            'allow_in' => [],
        ];
    }

    public static function render($atts = [], $content = '')
    {
        // Parse atts and ignore _jux_id (builder tracking only)
        $options = shortcode_atts([
            '_jux_id' => '',
            'padding' => '',
            'border' => '',
            'class' => '',
            'bg_color' => '',
            'bg' => '',
            'id' => '',
        ], $atts);

        $classes = ['section'];
        $styles = [];
        $id = !empty($options['id']) ? ' id="' . esc_attr($options['id']) . '"' : '';

        if (!empty($options['padding'])) $classes[] = 'section-' . esc_attr($options['padding']);
        if (!empty($options['border'])) $classes[] = 'section-has-border-' . esc_attr($options['border']);
        if (!empty($options['class'])) $classes[] = esc_attr($options['class']);
        if (!empty($options['bg_color'])) $styles[] = 'background-color:' . esc_attr($options['bg_color']) . ';';

        $html = '<section' . $id . ' class="' . esc_attr(implode(' ', $classes)) . '"';
        if (!empty($options['bg'])) $html .= ' data-bg="' . esc_url($options['bg']) . '"';
        if (!empty($styles)) $html .= ' style="' . esc_attr(implode(' ', $styles)) . '"';
        $html .= '><div class="section-content">' . do_shortcode($content) . '</div></section>';

        return $html;
    }
}
