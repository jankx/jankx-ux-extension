<?php
namespace Jankx\Extensions\JankxUX\Builder\Core;

/**
 * Parse shortcode content into builder nodes using WordPress native regex
 */
class ShortcodeParser
{
    /**
     * Parse content into nodes array using WordPress get_shortcode_regex
     */
    public static function parse($content)
    {
        if (empty($content)) {
            return [];
        }

        // Use WordPress native shortcode regex
        $pattern = get_shortcode_regex();
        $nodes = [];

        if (preg_match_all('/' . $pattern . '/s', $content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $node = self::parseMatch($match);
                if ($node) {
                    $nodes[] = $node;
                }
            }
        }

        return $nodes;
    }

    /**
     * Parse a single shortcode match from WordPress regex
     */
    protected static function parseMatch($match)
    {
        // WordPress regex returns:
        // [0] = full shortcode
        // [1] = opening bracket [[ or just [
        // [2] = shortcode name
        // [3] = attributes
        // [4] = self-closing slash
        // [5] = content
        // [6] = closing bracket ]] or just ]

        $tag = $match[2];
        $attsString = $match[3];
        $hasClosing = !empty($match[5]);  // has content
        $content = $match[5] ?? '';

        // Parse attributes using WordPress native function
        $atts = shortcode_parse_atts($attsString);
        if (!is_array($atts)) {
            $atts = [];
        }

        $node = [
            'id' => 'jux-' . uniqid(),
            'tag' => $tag,
            'name' => self::getElementName($tag),
            'info' => '',
            'options' => $atts,
            'children' => $hasClosing && !empty($content) ? self::parse($content) : null
        ];

        return $node;
    }

    /**
     * Get element name from tag
     */
    protected static function getElementName($tag)
    {
        $names = [
            // Core Flatsome
            'text' => 'Text',
            'ux_text' => 'Text',
            'text_box' => 'Text Box',
            'button' => 'Button',
            'ux_button' => 'Button',
            'row' => 'Row',
            'row_inner' => 'Inner Row',
            'col' => 'Column',
            'col_inner' => 'Inner Column',
            'section' => 'Section',
            'slider' => 'Slider',
            'ux_slider' => 'Slider',
            'ux_banner' => 'Banner',
            'banner' => 'Banner',
            'banner_grid' => 'Banner Grid',
            'ux_image' => 'Image',
            'image' => 'Image',
            'ux_image_box' => 'Image Box',
            'gap' => 'Gap',
            'spacer' => 'Spacer',
            'divider' => 'Divider',
            'title' => 'Title',
            'ux_title' => 'Title',
            'video' => 'Video',
            'video_button' => 'Video Button',
            'lightbox' => 'Lightbox',
            'accordion' => 'Accordion',
            'tabs' => 'Tabs',
            'tab' => 'Tab',
            'panel' => 'Panel',
            'featured_box' => 'Featured Box',
            'share' => 'Share',
            'follow' => 'Follow',
            'countdown' => 'Countdown',
            'map' => 'Map',
            'ux_gallery' => 'Gallery',
            'gallery' => 'Gallery',
            'instagram' => 'Instagram',
            'blog_posts' => 'Blog Posts',
            'product' => 'Product',
            'products' => 'Products',
            'product_flip' => 'Product Flip',
            'product_categories' => 'Product Categories',
            'breadcrumbs' => 'Breadcrumbs',
            'search' => 'Search',
            'nav' => 'Navigation',
            'block' => 'Block',
            'ux_html' => 'HTML',
            'raw' => 'Raw HTML',
            'code' => 'Code',
        ];

        return $names[$tag] ?? ucfirst($tag);
    }
}
