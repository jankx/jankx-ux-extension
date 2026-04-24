<?php
namespace Jankx\Extensions\JankxUX\Builder\Elements;

/**
 * Column Element - [col] shortcode
 * Full parity with Flatsome's ux_col() function
 */
class Column extends AbstractElement
{
    protected static $tag = 'col';

    public static function getConfig()
    {
        return [
            'type'        => 'container',
            'name'        => 'Column',
            'title'       => __('Column', 'jankx'),
            'category'    => __('Layout', 'jankx'),
            'description' => __('Create a column inside a row.', 'jankx'),
            'wrap'        => true,
            'options'     => [
                'span' => [
                    'type'    => 'select',
                    'heading' => __('Span', 'jankx'),
                    'default' => '12',
                    'options' => [
                        '1' => '1/12', '2' => '2/12', '3' => '3/12', '4' => '4/12',
                        '5' => '5/12', '6' => '6/12', '7' => '7/12', '8' => '8/12',
                        '9' => '9/12', '10' => '10/12', '11' => '11/12', '12' => '12/12',
                    ],
                ],
                'padding' => [
                    'type'    => 'textfield',
                    'heading' => __('Padding', 'jankx'),
                    'default' => '',
                ],
                'bg_color' => [
                    'type'    => 'color',
                    'heading' => __('Background Color', 'jankx'),
                    'default' => '',
                ],
                'align' => [
                    'type'    => 'select',
                    'heading' => __('Align', 'jankx'),
                    'default' => '',
                    'options' => [
                        ''       => __('Default', 'jankx'),
                        'left'   => __('Left', 'jankx'),
                        'center' => __('Center', 'jankx'),
                        'right'  => __('Right', 'jankx'),
                    ],
                ],
            ],
            'presets'     => [],
            'allow_in'    => ['row'],
        ];
    }

    public static function render($atts = [], $content = '')
    {
        $atts = shortcode_atts([
            '_id'           => 'col-' . rand(),
            'label'         => '',
            'span'          => '12',
            'span__md'      => isset($atts['span']) ? $atts['span'] : '',
            'span__sm'      => '',
            'small'         => '12',
            'visibility'    => '',
            'divider'       => '',
            'animate'       => '',
            'padding'       => '',
            'padding__md'   => '',
            'padding__sm'   => '',
            'margin'        => '',
            'margin__md'    => '',
            'margin__sm'    => '',
            'tooltip'       => '',
            'max_width'     => '',
            'max_width__md' => '',
            'max_width__sm' => '',
            'hover'         => '',
            'class'         => '',
            'align'         => '',
            'color'         => '',
            'sticky'        => '',
            'sticky_mode'   => '',
            'parallax'      => '',
            'force_first'   => '',
            'bg'            => '',
            'bg_color'      => '',
            'bg_radius'     => '',
            'depth'         => '',
            'depth_hover'   => '',
            'text_depth'    => '',
            // Border Control
            'border'        => '',
            'border_margin' => '',
            'border_style'  => '',
            'border_radius' => '',
            'border_color'  => '',
            'border_hover'  => '',
        ], $atts);

        extract($atts);

        // Hide if visibility is hidden
        if ($visibility === 'hidden') return '';

        $classes       = ['col'];
        $classes_inner = ['col-inner'];

        // Fix old col span fractions
        if (strpos($span, '/') !== false) {
            $parts = explode('/', $span);
            if (count($parts) === 2 && $parts[1] > 0) {
                $span = round(($parts[0] / $parts[1]) * 12);
            }
        }

        // Responsive grid classes
        if ($span)     $classes[] = 'large-' . intval($span);
        if ($span__md) $classes[] = 'medium-' . intval($span__md);
        if ($span__sm) $classes[] = 'small-'  . intval($span__sm);

        // Custom class & visibility
        if ($class)      $classes[] = $class;
        if ($visibility) $classes[] = $visibility;
        if ($border_hover) $classes[] = 'has-hover';

        // Force first position
        if ($force_first) $classes[] = $force_first . '-col-first';

        // Divider
        if ($divider) $classes[] = 'col-divided';

        // Hover
        if ($hover) $classes[] = 'col-hover-' . $hover;

        // Depth on inner
        if ($depth)       $classes_inner[] = 'box-shadow-' . $depth;
        if ($depth_hover) $classes_inner[] = 'box-shadow-' . $depth_hover . '-hover';
        if ($text_depth)  $classes_inner[] = 'text-shadow-' . $text_depth;

        // Text align on inner
        if ($align) $classes_inner[] = 'text-' . $align;

        // Color: "light" = dark text class on inner
        if ($color === 'light') $classes_inner[] = 'dark';

        // Tooltip
        $tooltip_attr = '';
        if ($tooltip) {
            $tooltip_attr = 'title="' . esc_attr($tooltip) . '"';
            $classes[] = 'tip-top';
        }

        // Animation
        $animate_attr = '';
        if ($animate) {
            $animate_attr = 'data-animate="' . esc_attr($animate) . '"';
        }

        // Parallax
        $parallax_attr = '';
        if ($parallax) {
            $parallax_attr = 'data-parallax-fade="true" data-parallax="' . esc_attr($parallax) . '"';
        }

        // Inline styles for inner
        $inner_style_parts = [];
        if ($bg_color)  $inner_style_parts[] = 'background-color:' . esc_attr($bg_color);
        if ($padding)   $inner_style_parts[] = 'padding:' . esc_attr($padding);
        if ($margin)    $inner_style_parts[] = 'margin:' . esc_attr($margin);
        if ($bg_radius) $inner_style_parts[] = 'border-radius:' . intval($bg_radius) . 'px';
        if ($max_width) $inner_style_parts[] = 'max-width:' . esc_attr($max_width);

        // Background image on inner
        if ($bg) {
            if (is_numeric($bg)) {
                $bg_img = wp_get_attachment_image_src($bg, 'large');
                if ($bg_img) $inner_style_parts[] = 'background-image:url(' . esc_url($bg_img[0]) . ')';
            } else {
                $inner_style_parts[] = 'background-image:url(' . esc_url($bg) . ')';
            }
        }

        $inner_style = $inner_style_parts ? ' style="' . implode(';', $inner_style_parts) . '"' : '';

        $class_str       = implode(' ', $classes);
        $class_inner_str = implode(' ', $classes_inner);
        $id              = esc_attr($_id);

        ob_start();
        ?>
        <div id="<?php echo $id; ?>" class="<?php echo esc_attr($class_str); ?>" <?php echo $tooltip_attr; ?> <?php echo $animate_attr; ?>>
            <?php if ($sticky) : ?>
            <div class="is-sticky-column">
                <div class="is-sticky-column__inner">
            <?php endif; ?>
            <div class="<?php echo esc_attr($class_inner_str); ?>"<?php echo $inner_style; ?> <?php echo $parallax_attr; ?>>
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
                $inner_content = do_shortcode($content);
                if (trim($inner_content) === '' && defined('JUX_BUILDER')) {
                    $inner_content = self::renderPlaceholder('col', __('Column', 'jankx'));
                }
                echo $inner_content;
                ?>
            </div>
            <?php if ($sticky) : ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
