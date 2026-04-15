<?php
namespace Jankx\Extensions\JankxUX\Builder\Elements;

/**
 * Column Element - PSR-4 Static Class
 */
class Column extends AbstractElement
{
    protected static $tag = 'col';

    protected static function getConfig()
    {
        return [
            'type' => 'container',
            'name' => 'Column',
            'title' => __('Column', 'jankx'),
            'category' => __('Layout', 'jankx'),
            'description' => __('Create a column inside a row.', 'jankx'),
            'thumbnail' => '',
            'wrap' => true,
            'options' => [
                'span' => [
                    'type' => 'select',
                    'heading' => __('Column Width', 'jankx'),
                    'default' => '12',
                    'options' => [
                        '1' => '1/12', '2' => '2/12', '3' => '3/12',
                        '4' => '4/12', '5' => '5/12', '6' => '6/12',
                        '7' => '7/12', '8' => '8/12', '9' => '9/12',
                        '10' => '10/12', '11' => '11/12', '12' => '12/12',
                    ],
                ],
                'class' => [
                    'type' => 'text',
                    'heading' => __('Custom Class', 'jankx'),
                    'default' => '',
                ],
            ],
            'presets' => [
                'half' => [
                    'title' => __('1/2 Column', 'jankx'),
                    'options' => ['span' => '6'],
                ],
            ],
            'allow_in' => ['row'],
        ];
    }

    public static function render($atts = [], $content = '')
    {
        // Parse atts - match Flatsome exactly
        $options = shortcode_atts([
            '_id' => 'col-' . rand(),
            '_jux_id' => '',
            'span' => '12',
            'span__md' => '',
            'span__sm' => '',
            'label' => '',
            'class' => '',
            'visibility' => '',
            'animate' => '',
            'parallax' => '',
            'padding' => '',
            'margin' => '',
            'depth' => '',
            'depth_hover' => '',
            'divider' => '',
            'align' => '',
            'hover' => '',
        ], $atts);

        // Hide if visibility is hidden
        if ($options['visibility'] === 'hidden') return '';

        $classes = ['col'];
        $classes_inner = ['col-inner'];

        // Fix span - Flatsome uses large- medium- small-
        $span = $options['span'];
        if (strpos($span, '/') !== false) {
            // Convert fraction to number
            $parts = explode('/', $span);
            if (count($parts) === 2) {
                $span = round(($parts[0] / $parts[1]) * 12);
            }
        }

        // Add size classes - Flatsome order: large, medium, small
        if (!empty($span)) $classes[] = 'large-' . intval($span);
        if (!empty($options['span__md'])) $classes[] = 'medium-' . intval($options['span__md']);
        if (!empty($options['span__sm'])) $classes[] = 'small-' . intval($options['span__sm']);

        // Custom class & visibility
        if (!empty($options['class'])) $classes[] = esc_attr($options['class']);
        if (!empty($options['visibility'])) $classes[] = esc_attr($options['visibility']);

        // Animation attribute
        $animate_attr = '';
        if (!empty($options['animate'])) {
            $animate_attr = 'data-animate="' . esc_attr($options['animate']) . '"';
        }

        // Parallax
        if (!empty($options['parallax'])) {
            $classes[] = 'has-parallax';
        }

        // Divider
        if (!empty($options['divider'])) {
            $classes[] = 'col-divided';
        }

        // Hover
        if (!empty($options['hover'])) {
            $classes[] = 'col-hover-' . esc_attr($options['hover']);
        }

        // Depth
        if (!empty($options['depth'])) $classes[] = 'box-shadow-' . intval($options['depth']);
        if (!empty($options['depth_hover'])) $classes[] = 'box-shadow-' . intval($options['depth_hover']) . '-hover';

        // Inner align
        if (!empty($options['align'])) $classes_inner[] = 'text-' . esc_attr($options['align']);

        $classString = implode(' ', $classes);
        $classInnerString = implode(' ', $classes_inner);
        $id = esc_attr($options['_id']);

        return '<div class="' . esc_attr($classString) . '" id="' . $id . '" ' . $animate_attr . '>'
            . '<div class="' . esc_attr($classInnerString) . '">'
            . do_shortcode($content)
            . '</div>'
            . '</div>';
    }
}
