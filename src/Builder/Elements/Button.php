<?php
namespace Jankx\Extensions\JankxUX\Builder\Elements;

/**
 * Button Element - [button] shortcode
 * Flatsome compatible
 */
class Button extends AbstractElement
{
    protected static $tag = 'button';

    protected static function getConfig()
    {
        return [
            'type' => 'element',
            'name' => 'Button',
            'title' => __('Button', 'jankx'),
            'category' => __('Content', 'jankx'),
            'description' => __('Call to action button.', 'jankx'),
            'wrap' => false,
            'options' => [
                'text' => ['type' => 'text', 'heading' => __('Button Text', 'jankx'), 'default' => 'Click Me'],
                'link' => ['type' => 'text', 'heading' => __('Link', 'jankx'), 'default' => ''],
                'target' => ['type' => 'select', 'heading' => __('Target', 'jankx'), 'default' => '_self',
                    'options' => ['_self' => __('Same Window', 'jankx'), '_blank' => __('New Window', 'jankx')]],
                'style' => ['type' => 'select', 'heading' => __('Style', 'jankx'), 'default' => 'primary',
                    'options' => ['primary' => __('Primary', 'jankx'), 'secondary' => __('Secondary', 'jankx'), 'success' => __('Success', 'jankx'), 'alert' => __('Alert', 'jankx'), 'outline' => __('Outline', 'jankx')]],
                'size' => ['type' => 'select', 'heading' => __('Size', 'jankx'), 'default' => '',
                    'options' => ['' => __('Default', 'jankx'), 'small' => __('Small', 'jankx'), 'large' => __('Large', 'jankx'), 'expand' => __('Expand', 'jankx')]],
                'radius' => ['type' => 'select', 'heading' => __('Radius', 'jankx'), 'default' => '',
                    'options' => ['' => __('Default', 'jankx'), 'round' => __('Round', 'jankx')]],
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
            'style' => 'primary',
            'size' => '',
            'radius' => '',
            'class' => '',
            'text' => 'Button',
            'link' => '#',
            'target' => '_self',
        ], $atts);

        $classes = ['button'];

        if (!empty($options['style'])) $classes[] = esc_attr($options['style']);
        if (!empty($options['size'])) $classes[] = 'is-' . esc_attr($options['size']);
        if (!empty($options['radius'])) $classes[] = 'is-' . esc_attr($options['radius']);
        if (!empty($options['class'])) $classes[] = esc_attr($options['class']);

        $text = !empty($options['text']) ? esc_html($options['text']) : esc_html($content);
        $link = !empty($options['link']) ? esc_url($options['link']) : '#';
        $target = !empty($options['target']) ? esc_attr($options['target']) : '_self';

        return '<a href="' . $link . '" target="' . $target . '" class="' . esc_attr(implode(' ', $classes)) . '">' . $text . '</a>';
    }
}
