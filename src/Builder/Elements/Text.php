<?php
namespace Jankx\Extensions\JankxUX\Builder\Elements;

/**
 * Text Element - [text] shortcode
 * Simple text/HTML block (used standalone in columns)
 * Note: text_box is the banner-overlay version (see TextBox.php)
 */
class Text extends AbstractElement
{
    protected static $tag = 'text';

    public static function getConfig()
    {
        return [
            'tag'         => static::$tag,
            'type'        => 'element',
            'name'        => 'Text',
            'title'       => __('Text Block', 'jankx'),
            'category'    => __('Content', 'jankx'),
            'description' => __('Simple text/HTML content block.', 'jankx'),
            'wrap'        => false,
            'options'     => [
                'text' => [
                    'type'    => 'textarea',
                    'heading' => __('Content', 'jankx'),
                    'default' => __('Enter your text here...', 'jankx'),
                ],
                'text_align' => [
                    'type'    => 'select',
                    'heading' => __('Text Align', 'jankx'),
                    'default' => 'left',
                    'options' => [
                        'left'   => __('Left', 'jankx'),
                        'center' => __('Center', 'jankx'),
                        'right'  => __('Right', 'jankx'),
                    ],
                ],
                'font_size' => [
                    'type'    => 'select',
                    'heading' => __('Font Size', 'jankx'),
                    'default' => '',
                    'options' => [
                        ''       => __('Normal', 'jankx'),
                        'large'  => __('Large', 'jankx'),
                        'xlarge' => __('X-Large', 'jankx'),
                        'small'  => __('Small', 'jankx'),
                    ],
                ],
                'text_color' => [
                    'type'    => 'color',
                    'heading' => __('Text Color', 'jankx'),
                    'default' => '',
                ],
                'class' => [
                    'type'    => 'textfield',
                    'heading' => __('Custom Class', 'jankx'),
                    'default' => '',
                ],
                '_label' => [
                    'type'    => 'textfield',
                    'heading' => __('Element Label', 'jankx'),
                    'default' => '',
                ],
            ],
            'allow_in'    => ['col', 'section', 'ux_block'],
        ];
    }

    public static function render($atts = [], $content = '')
    {
        $options = shortcode_atts([
            '_id'        => 'text-' . rand(),
            'text_align' => '',
            'font_size'  => '',
            'class'      => '',
            'text_color' => '',
            'visibility' => '',
            'text'       => '',
            'animate'    => '',
        ], $atts);

        if ($options['visibility'] === 'hidden') return '';

        $classes = ['text-block'];
        $styles  = [];

        if (!empty($options['text_align'])) $classes[] = 'text-' . esc_attr($options['text_align']);
        if (!empty($options['font_size']))  $classes[] = 'is-' . esc_attr($options['font_size']);
        if (!empty($options['class']))      $classes[] = esc_attr($options['class']);
        if (!empty($options['visibility'])) $classes[] = esc_attr($options['visibility']);
        if (!empty($options['text_color'])) $styles[]  = 'color:' . esc_attr($options['text_color']);

        $animate_attr = '';
        if (!empty($options['animate'])) {
            $animate_attr = ' data-animate="' . esc_attr($options['animate']) . '"';
        }

        $text = !empty($options['text']) ? $options['text'] : do_shortcode($content);

        $html  = '<div class="' . esc_attr(implode(' ', $classes)) . '"';
        if ($styles) $html .= ' style="' . esc_attr(implode(';', $styles)) . '"';
        $html .= $animate_attr;
        $html .= '>' . wp_kses_post($text) . '</div>';

        return $html;
    }
}
