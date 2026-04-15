<?php
namespace Jankx\Extensions\JankxUX\Structure;

class Header
{
    public static function init()
    {
        add_action('jankx_before_header', [self::class, 'beforeHeader']);
        add_action('jankx_after_header', [self::class, 'afterHeader']);
        add_action('wp_head', [self::class, 'wpHead'], 5);
    }

    public static function beforeHeader()
    {
        do_action('flatsome_before_header');
    }

    public static function afterHeader()
    {
        do_action('flatsome_after_header');
        do_action('flatsome_after_header_bottom');
    }

    public static function wpHead()
    {
        do_action('flatsome_head');
    }
}
