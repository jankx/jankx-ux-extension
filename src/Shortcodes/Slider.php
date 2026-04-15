<?php
namespace Jankx\Extensions\JankxUX\Shortcodes;

class Slider extends AbstractShortcode
{
    public static function render($atts, $content = null)
    {
        $atts = shortcode_atts(array(
            'arrows'    => 'true',
            'bullets'   => 'true',
            'auto_slide'=> 'false',
            'timer'      => '6000',
            'class'     => '',
        ), $atts);

        $classes = ['ux-slider', 'flickity-slider'];
        if ($atts['class']) $classes[] = $atts['class'];

        // Data attributes for JS initialization
        $data = array(
            'prevNextButtons' => $atts['arrows'] === 'true',
            'pageDots'        => $atts['bullets'] === 'true',
            'autoPlay'        => $atts['auto_slide'] === 'true' ? intval($atts['timer']) : false,
        );

        return sprintf(
            '<div class="%s" data-flickity-options="%s">%s</div>',
            esc_attr(implode(' ', $classes)),
            esc_attr(json_encode($data)),
            do_shortcode($content)
        );
    }
}
