<?php
namespace Jankx\Extensions\JankxUX\Builder\Core;

/**
 * Parse shortcode content into builder nodes
 */
class ShortcodeParser
{
    /**
     * Parse content into nodes array
     */
    public static function parse($content)
    {
        if (empty($content)) {
            return [];
        }

        $nodes = [];
        $pattern = '/\[(\w+)([^\]]*)\](.*?)\[\/\1\]|\[(\w+)([^\]]*)\]/s';

        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $node = self::parseMatch($match);
            if ($node) {
                $nodes[] = $node;
            }
        }

        return $nodes;
    }

    /**
     * Parse a single shortcode match
     */
    protected static function parseMatch($match)
    {
        // Self-closing tag: [tag atts]
        if (!empty($match[4])) {
            $tag = $match[4];
            $attsString = $match[5];
            $innerContent = '';
            $hasClosing = false;
        } else {
            // Enclosing tag: [tag atts]content[/tag]
            $tag = $match[1];
            $attsString = $match[2];
            $innerContent = $match[3] ?? '';
            $hasClosing = true;
        }

        $node = [
            'id' => 'jux-' . uniqid(),
            'tag' => $tag,
            'name' => self::getElementName($tag),
            'info' => '',
            'options' => self::parseAttributes($attsString),
            'children' => $hasClosing && $innerContent ? self::parse($innerContent) : null
        ];

        return $node;
    }

    /**
     * Parse attributes string into array
     */
    protected static function parseAttributes($attsString)
    {
        $atts = [];
        $pattern = '/(\w+)=["\']([^"\']*)["\']|(\w+)=[^\s]*/';

        preg_match_all($pattern, $attsString, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            if (!empty($match[1])) {
                $atts[$match[1]] = $match[2];
            } elseif (!empty($match[3])) {
                $atts[$match[3]] = '';
            }
        }

        return $atts;
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
