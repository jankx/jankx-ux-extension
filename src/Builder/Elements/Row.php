<?php
namespace Jankx\Extensions\JankxUX\Builder\Elements;

/**
 * Row Element - [row] / [row_inner] shortcode
 * Full parity with Flatsome's ux_row() function
 */
class Row extends AbstractElement
{
    protected static $tag = 'row';

    protected static function getConfig()
    {
        return [
            'type'        => 'container',
            'name'        => 'Row',
            'title'       => __('Row', 'jankx'),
            'category'    => __('Layout', 'jankx'),
            'description' => __('Create a row container for columns.', 'jankx'),
            'wrap'        => true,
            'options'     => [
                'style' => [
                    'type'    => 'select',
                    'heading' => __('Style', 'jankx'),
                    'default' => '',
                    'options' => [
                        ''         => __('Default', 'jankx'),
                        'collapse' => __('Collapse', 'jankx'),
                        'full-width' => __('Full Width', 'jankx'),
                    ],
                ],
                'width' => [
                    'type'    => 'select',
                    'heading' => __('Width', 'jankx'),
                    'default' => '',
                    'options' => [
                        ''           => __('Container', 'jankx'),
                        'full-width' => __('Full Width', 'jankx'),
                        'custom'     => __('Custom', 'jankx'),
                    ],
                ],
                'v_align' => [
                    'type'    => 'select',
                    'heading' => __('Vertical Align', 'jankx'),
                    'default' => '',
                    'options' => [
                        ''       => __('Top', 'jankx'),
                        'middle' => __('Middle', 'jankx'),
                        'bottom' => __('Bottom', 'jankx'),
                    ],
                ],
                'class' => [
                    'type'    => 'textfield',
                    'heading' => __('Custom Class', 'jankx'),
                    'default' => '',
                ],
            ],
            'allow_in'    => ['section', 'ux_block'],
        ];
    }

    public static function render($atts = [], $content = '')
    {
        $atts = shortcode_atts([
            '_id'          => 'row-' . rand(),
            'style'        => '',
            'col_style'    => '',
            'label'        => '',
            'border_color' => '',
            'width'        => '',
            'custom_width' => '',
            'class'        => '',
            'visibility'   => '',
            'v_align'      => '',
            'h_align'      => '',
            'depth'        => '',
            'depth_hover'  => '',
            // Paddings
            'padding'      => '',
            'col_bg'       => '',
            'col_bg_radius' => '',
        ], $atts);

        extract($atts);

        // Stop if visibility is hidden
        if ($visibility === 'hidden') return '';

        $classes = ['row'];

        if ($style)                      $classes[] = 'row-' . $style;
        if ($width === 'full-width')     $classes[] = 'row-full-width';
        if ($v_align)                    $classes[] = 'align-' . $v_align;
        if ($h_align)                    $classes[] = 'align-' . $h_align;
        if ($col_style)                  $classes[] = 'row-' . $col_style;
        if ($class)                      $classes[] = $class;
        if ($visibility)                 $classes[] = $visibility;
        if ($depth)                      $classes[] = 'row-box-shadow-' . $depth;
        if ($depth_hover)                $classes[] = 'row-box-shadow-' . $depth_hover . '-hover';

        // Custom width style attr
        $custom_width_attr = '';
        if ($width === 'custom' && $custom_width) {
            $custom_width_attr = ' style="max-width:' . esc_attr($custom_width) . '"';
        }

        $class_str = implode(' ', $classes);
        $id        = esc_attr($_id);

        // Inline CSS for col padding/bg applied via <style>
        $col_css = '';
        if ($padding) {
            $col_css .= '#' . $id . ' > .col > .col-inner { padding:' . esc_attr($padding) . '; }';
        }
        if ($col_bg) {
            $col_css .= '#' . $id . ' > .col > .col-inner { background-color:' . esc_attr($col_bg) . '; }';
        }
        if ($col_bg_radius) {
            $col_css .= '#' . $id . ' > .col > .col-inner { border-radius:' . intval($col_bg_radius) . 'px; }';
        }

        $output  = '<div class="' . esc_attr($class_str) . '"' . $custom_width_attr . ' id="' . $id . '">';
        $output .= do_shortcode($content);
        $output .= '</div>';
        if ($col_css) {
            $output .= '<style>' . $col_css . '</style>';
        }

        return $output;
    }
}
