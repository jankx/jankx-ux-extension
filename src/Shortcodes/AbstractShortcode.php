<?php
namespace Jankx\Extensions\JankxUX\Shortcodes;

abstract class AbstractShortcode
{
    /**
     * Parse standard Flatsome attributes (visibility, animation, extra classes)
     */
    public static function parseAttributes($atts)
    {
        $defaults = array(
            'class'      => '',
            'visibility' => '', // hidden, visible, etc
            'animate'    => '', // bounceIn, fadeIn, etc
        );

        return shortcode_atts($defaults, $atts);
    }

    /**
     * Generate standard wrapper classes
     */
    public static function getWrapperClasses($atts)
    {
        $classes = [];
        if (!empty($atts['class'])) $classes[] = $atts['class'];
        if (!empty($atts['visibility'])) $classes[] = $atts['visibility'];
        if (!empty($atts['animate'])) $classes[] = 'wow ' . $atts['animate'];
        
        return implode(' ', $classes);
    }

    /**
     * Render the shortcode (to be implemented by children)
     */
    abstract public static function render($atts, $content = null);
}
