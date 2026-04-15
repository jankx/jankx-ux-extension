<?php
namespace Jankx\Extensions\JankxUX\Builder\Elements;

/**
 * Text Element - [text] shortcode
 * Flatsome compatible
 */
class Text extends AbstractElement
{
    protected static $tag = 'text';

    protected static function getConfig()
    {
        return [
            'type' => 'element',
            'name' => 'Text',
            'title' => __('Text Block', 'jankx'),
            'category' => __('Content', 'jankx'),
            'description' => __('Simple text content block.', 'jankx'),
            'wrap' => false,
            'options' => [
                'text' => ['type' => 'textarea', 'heading' => __('Content', 'jankx'), 'default' => 'Enter your text here...'],
                'text_align' => ['type' => 'select', 'heading' => __('Text Align', 'jankx'), 'default' => '',
                    'options' => ['' => __('Left', 'jankx'), 'center' => __('Center', 'jankx'), 'right' => __('Right', 'jankx')]],
                'text_color' => ['type' => 'color', 'heading' => __('Text Color', 'jankx'), 'default' => ''],
                'font_size' => ['type' => 'select', 'heading' => __('Font Size', 'jankx'), 'default' => '',
                    'options' => ['' => __('Default', 'jankx'), 'small' => __('Small', 'jankx'), 'medium' => __('Medium', 'jankx'), 'large' => __('Large', 'jankx')]],
                'class' => ['type' => 'text', 'heading' => __('Custom Class', 'jankx'), 'default' => ''],
            ],
            'presets' => [],
            'allow_in' => ['col', 'section', 'ux_block'],
        ];
    }

    public static function render($atts = [], $content = '')
    {
        // Parse atts and ignore _jux_id (builder tracking only)
        $options = shortcode_atts([
            '_jux_id' => '',
            'text_align' => '',
            'font_size' => '',
            'class' => '',
            'text_color' => '',
            'text' => '',
        ], $atts);

        $classes = ['jux-text'];
        $styles = [];

        if (!empty($options['text_align'])) $classes[] = 'text-' . esc_attr($options['text_align']);
        if (!empty($options['font_size'])) $classes[] = 'is-' . esc_attr($options['font_size']);
        if (!empty($options['class'])) $classes[] = esc_attr($options['class']);
        if (!empty($options['text_color'])) $styles[] = 'color:' . esc_attr($options['text_color']) . ';';

        $text = !empty($options['text']) ? $options['text'] : do_shortcode($content);

        $html = '<div class="' . esc_attr(implode(' ', $classes)) . '"';
        if (!empty($styles)) $html .= ' style="' . esc_attr(implode(' ', $styles)) . '"';
        $html .= '>' . wp_kses_post($text) . '</div>';

        return $html;
    }
}
