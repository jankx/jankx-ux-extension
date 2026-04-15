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
            'text' => 'Text',
            'button' => 'Button',
            'row' => 'Row',
            'col' => 'Column',
            'section' => 'Section',
            'slider' => 'Slider',
            'ux_image' => 'Image',
        ];

        return $names[$tag] ?? ucfirst($tag);
    }
}
