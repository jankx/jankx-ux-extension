<?php
namespace Jankx\Extensions\JankxUX\Builder\Core;

/**
 * Parse shortcode content into builder nodes
 * Properly handles nested shortcodes by parsing only top-level first
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
        $offset = 0;
        $length = strlen($content);

        while ($offset < $length) {
            // Find next opening bracket
            $openPos = strpos($content, '[', $offset);
            
            if ($openPos === false) {
                break;
            }

            // Check if this is a closing tag
            if (isset($content[$openPos + 1]) && $content[$openPos + 1] === '/') {
                $offset = $openPos + 1;
                continue;
            }

            // Find the tag name
            $closePos = strpos($content, ']', $openPos);
            if ($closePos === false) {
                break;
            }

            $tagContent = substr($content, $openPos + 1, $closePos - $openPos - 1);
            
            // Parse tag and attributes
            $spacePos = strpos($tagContent, ' ');
            if ($spacePos === false) {
                $tag = $tagContent;
                $attsString = '';
            } else {
                $tag = substr($tagContent, 0, $spacePos);
                $attsString = substr($tagContent, $spacePos + 1);
            }

            // Check if it's self-closing
            $isSelfClosing = (substr($tag, -1) === '/');
            if ($isSelfClosing) {
                $tag = substr($tag, 0, -1);
                $tag = trim($tag);
            }

            // Find closing tag for non-self-closing tags
            if (!$isSelfClosing) {
                $endTag = '[/' . $tag . ']';
                $endPos = strpos($content, $endTag, $closePos);
                
                if ($endPos === false) {
                    // No closing tag found, treat as self-closing
                    $innerContent = '';
                    $hasClosing = false;
                } else {
                    // Extract inner content (might contain nested shortcodes)
                    $innerContent = substr($content, $closePos + 1, $endPos - $closePos - 1);
                    $hasClosing = true;
                    $closePos = $endPos + strlen($endTag) - 1;
                }
            } else {
                $innerContent = '';
                $hasClosing = false;
            }

            $node = [
                'id' => 'jux-' . uniqid(),
                'tag' => $tag,
                'name' => self::getElementName($tag),
                'info' => '',
                'options' => self::parseAttributes($attsString),
                'children' => $hasClosing && !empty($innerContent) ? self::parse($innerContent) : null
            ];

            $nodes[] = $node;
            $offset = $closePos + 1;
        }

        return $nodes;
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
