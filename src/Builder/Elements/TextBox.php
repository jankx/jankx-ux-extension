<?php
namespace Jankx\Extensions\JankxUX\Builder\Elements;

/**
 * TextBox Element - [text_box] shortcode
 * Full parity with Flatsome's flatsome_text_box() function
 */
class TextBox extends AbstractElement
{
    protected static $tag = 'text_box';

    protected static function getConfig()
    {
        return [
            'type'        => 'container',
            'name'        => 'Text Box',
            'title'       => __('Text Box', 'jankx'),
            'category'    => __('Content', 'jankx'),
            'description' => __('A positioned text overlay layer for banners.', 'jankx'),
            'wrap'        => true,
            'options'     => [],
            'allow_in'    => ['ux_banner'],
        ];
    }

    public static function render($atts = [], $content = '')
    {
        $atts = shortcode_atts([
            'id'              => 'text-box-' . rand(),
            'style'           => '',
            'res_text'        => 'true',
            'hover'           => '',
            'position_x'      => '50',
            'position_x__sm'  => '',
            'position_x__md'  => '',
            'position_y'      => '50',
            'position_y__sm'  => '',
            'position_y__md'  => '',
            'text_color'      => 'light',
            'bg'              => '',
            'width'           => '60',
            'width__sm'       => '',
            'width__md'       => '',
            'height'          => '',
            'height__sm'      => '',
            'height__md'      => '',
            'scale'           => '100',
            'scale__sm'       => '',
            'scale__md'       => '',
            'text_align'      => 'center',
            'animate'         => '',
            'parallax'        => '',
            'padding'         => '',
            'padding__sm'     => '',
            'padding__md'     => '',
            'margin'          => '',
            'margin__sm'      => '',
            'margin__md'      => '',
            'radius'          => '',
            'rotate'          => '',
            'class'           => '',
            'visibility'      => '',
            'border_radius'   => '',
            // Borders
            'border'          => '',
            'border_color'    => '',
            'border_style'    => '',
            'border_pos'      => '',
            'border_margin'   => '',
            'border_hover'    => '',
            // Depth
            'depth'           => '',
            'depth_hover'     => '',
            // Text depth
            'text_depth'      => '',
        ], $atts);

        extract($atts);

        $classes      = ['text-box', 'banner-layer'];
        $classes_text = ['text-inner'];
        $classes_inner = [];

        // Style variant (circle etc)
        if ($style) $classes[] = 'text-box-' . $style;
        if ($class) $classes[] = $class;
        if ($visibility) $classes[] = $visibility;

        // Position classes — Flatsome uses x{val}/y{val} CSS classes
        $classes[] = self::positionClass('x', $position_x, $position_x__sm, $position_x__md);
        $classes[] = self::positionClass('y', $position_y, $position_y__sm, $position_y__md);

        // Width as inline style (Flatsome uses ux_builder_element_style_tag; we inline)
        $style_parts = [];
        if ($width !== '')   $style_parts[] = 'width:' . intval($width) . '%';
        if ($height !== '')  $style_parts[] = 'height:' . intval($height) . '%';
        if ($margin !== '')  $style_parts[] = 'margin:' . esc_attr($margin);

        // Inner classes
        if ($depth)      $classes_inner[] = 'box-shadow-' . $depth;
        if ($depth_hover) $classes_inner[] = 'box-shadow-' . $depth_hover . '-hover';
        if ($text_color === 'light') $classes_inner[] = 'dark';
        if ($text_depth) $classes_inner[] = 'text-shadow-' . $text_depth;

        // Text classes
        if ($text_align) $classes_text[] = 'text-' . $text_align;

        // Responsive text
        if ($res_text === 'true') $classes[] = 'res-text';

        // Parallax
        $parallax_attr = '';
        if ($parallax) {
            $parallax_attr = 'data-parallax="' . esc_attr($parallax) . '" data-parallax-fade="true"';
        }

        // Inner styles
        $inner_style_parts = [];
        if ($bg) $inner_style_parts[] = 'background-color:' . esc_attr($bg);
        if ($padding) $inner_style_parts[] = 'padding:' . esc_attr($padding);
        if ($scale && $scale !== '100') $inner_style_parts[] = 'font-size:' . intval($scale) . '%';
        if ($radius) $inner_style_parts[] = 'border-radius:' . intval($radius) . 'px';
        if ($rotate) $inner_style_parts[] = 'rotate:' . intval($rotate) . 'deg';

        $class_str       = implode(' ', $classes);
        $class_inner_str = implode(' ', $classes_inner);
        $class_text_str  = implode(' ', $classes_text);
        $inline_style    = $style_parts ? ' style="' . implode(';', $style_parts) . '"' : '';
        $inner_style_str = $inner_style_parts ? ' style="' . implode(';', $inner_style_parts) . '"' : '';

        ob_start();
        ?>
        <div id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($class_str); ?>"<?php echo $inline_style; ?>>
            <?php if ($hover) echo '<div class="hover-' . esc_attr($hover) . '">'; ?>
            <?php if ($parallax_attr) echo '<div ' . $parallax_attr . '>'; ?>
            <?php if ($animate) echo '<div data-animate="' . esc_attr($animate) . '">'; ?>
                <div class="text-box-content text <?php echo esc_attr($class_inner_str); ?>"<?php echo $inner_style_str; ?>>
                    <?php
                    // Border
                    if ($border) {
                        $b_style = '';
                        if ($border_margin) $b_style .= 'margin:' . esc_attr($border_margin) . ';';
                        if ($border_color)  $b_style .= 'border-color:' . esc_attr($border_color) . ';';
                        if ($border_style)  $b_style .= 'border-style:' . esc_attr($border_style) . ';';
                        if ($border_radius) $b_style .= 'border-radius:' . esc_attr($border_radius) . ';';
                        echo '<div class="outline-border border-' . esc_attr($border) . '"' . ($b_style ? ' style="' . $b_style . '"' : '') . '></div>';
                    }
                    ?>
                    <div class="<?php echo esc_attr($class_text_str); ?>">
                        <?php echo do_shortcode($content); ?>
                    </div>
                </div>
            <?php if ($animate) echo '</div>'; ?>
            <?php if ($parallax_attr) echo '</div>'; ?>
            <?php if ($hover) echo '</div>'; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Generate position CSS classes matching Flatsome's flatsome_position_classes()
     * Flatsome uses classes like: x10, x50, x90, y10, y50, y90 etc.
     */
    protected static function positionClass($axis, $val, $val_sm = '', $val_md = '')
    {
        $classes = [];
        if ($val !== '')    $classes[] = $axis . intval($val);
        if ($val_md !== '') $classes[] = 'md-' . $axis . intval($val_md);
        if ($val_sm !== '') $classes[] = 'lg-' . $axis . intval($val);
        return implode(' ', $classes);
    }
}
