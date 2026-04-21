<?php
namespace Jankx\Extensions\JankxUX\Builder\Elements;

/**
 * Button Element - [button] shortcode
 * Full parity with Flatsome's button_shortcode() function
 */
class Button extends AbstractElement
{
    protected static $tag = 'button';

    protected static function getConfig()
    {
        return [
            'type'        => 'element',
            'name'        => 'Button',
            'title'       => __('Button', 'jankx'),
            'category'    => __('Content', 'jankx'),
            'description' => __('Call to action button.', 'jankx'),
            'wrap'        => false,
            'options'     => [],
        ];
    }

    public static function render($atts = [], $content = '')
    {
        $atts = shortcode_atts([
            'as'          => '',  // 'button' for <button> element
            'text'        => '',
            'style'       => '',
            'color'       => 'primary',
            'size'        => '',
            'animate'     => '',
            'link'        => '',
            'target'      => '_self',
            'rel'         => '',
            'border'      => '',
            'expand'      => '',
            'tooltip'     => '',
            'padding'     => '',
            'radius'      => '',
            'letter_case' => '',
            'mobile_icon' => '',
            'icon'        => '',
            'icon_pos'    => '',
            'icon_reveal' => '',
            'depth'       => '',
            'depth_hover' => '',
            'class'       => '',
            'visibility'  => '',
            'id'          => '',
            'block'       => '',
        ], $atts);

        extract($atts);

        // Old button fallback - color embedded in style
        if (strpos($style, 'primary')   !== false) { $color = 'primary'; }
        elseif (strpos($style, 'secondary') !== false) { $color = 'secondary'; }
        elseif (strpos($style, 'white')     !== false) { $color = 'white'; }
        elseif (strpos($style, 'success')   !== false) { $color = 'success'; }
        elseif (strpos($style, 'alert')     !== false) { $color = 'alert'; }

        // Old alt-button fallback
        if (strpos($style, 'alt-button') !== false) { $style = 'outline'; }

        // Element tag
        $tag  = ($as === 'button') ? 'button' : 'a';
        $link = ($as === 'button') ? '' : $link;

        // Icon positions
        $icon_left  = ($icon && $icon_pos === 'left')  ? self::renderIcon($icon)  : '';
        $icon_right = ($icon && $icon_pos !== 'left')  ? self::renderIcon($icon)  : '';

        // Classes
        $classes = ['button'];
        if ($color)       $classes[] = $color;
        if ($style)       $classes[] = 'is-' . $style;
        if ($size)        $classes[] = 'is-' . $size;
        if ($depth)       $classes[] = 'box-shadow-' . $depth;
        if ($depth_hover) $classes[] = 'box-shadow-' . $depth_hover . '-hover';
        if ($letter_case) $classes[] = $letter_case;
        if ($icon && $icon_reveal) $classes[] = 'reveal-icon';
        if ($expand)      $classes[] = 'expand';
        if ($block)       $classes[] = 'block';
        if ($class)       $classes[] = $class;
        if ($visibility)  $classes[] = $visibility;
        if ($tooltip)     $classes[] = 'has-tooltip';

        // Attributes
        $attrs = [];
        $attrs['class'] = implode(' ', $classes);
        if ($id)      $attrs['id']           = $id;
        if ($animate) $attrs['data-animate'] = $animate;
        if ($tooltip) $attrs['title']        = $tooltip;

        if ($tag === 'a') {
            if ($link)   $attrs['href']   = esc_url($link);
            if ($target) $attrs['target'] = $target;
            if ($rel)    $attrs['rel']    = $rel;
        } else {
            $attrs['type'] = 'button';
        }

        // Inline styles
        $style_parts = [];
        if ($radius)  $style_parts[] = 'border-radius:' . intval($radius) . 'px';
        if ($border)  $style_parts[] = 'border-width:' . intval($border) . 'px';
        if ($padding) $style_parts[] = 'padding:' . esc_attr($padding);
        if ($style_parts) $attrs['style'] = implode(';', $style_parts);

        // Build attr string
        $attr_str = '';
        foreach ($attrs as $k => $v) {
            $attr_str .= ' ' . esc_attr($k) . '="' . esc_attr($v) . '"';
        }

        // Text content
        $btn_text = $text ?: $content;

        return '<' . $tag . $attr_str . '>'
            . $icon_left
            . esc_html($btn_text)
            . $icon_right
            . '</' . $tag . '>';
    }

    /**
     * Render an icon span (Flatsome uses icon font classes like icon-play)
     */
    protected static function renderIcon($icon_name)
    {
        return '<i class="' . esc_attr($icon_name) . '"></i>';
    }
}
