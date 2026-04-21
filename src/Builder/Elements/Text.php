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

    protected static function getConfig()
    {
        return [
            'type'        => 'element',
            'name'        => 'Text',
            'title'       => __('Text Block', 'jankx'),
            'category'    => __('Content', 'jankx'),
            'description' => __('Simple text/HTML content block.', 'jankx'),
            'wrap'        => false,
            'options'     => [],
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

        $classes = ['jux-text'];
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
