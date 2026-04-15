<?php
namespace Jankx\Extensions\JankxUX\Builder\Elements;

/**
 * Banner element (Flatsome ux_banner compatible)
 */
class Banner extends AbstractElement
{
    public function getTag(): string
    {
        return 'ux_banner';
    }

    public function getName(): string
    {
        return __('Banner', 'jankx');
    }

    public function getInfo(): string
    {
        return __('Add a banner with image, text overlay and link', 'jankx');
    }

    public function getIcon(): string
    {
        return 'picture';
    }

    public function getOptions(): array
    {
        return [
            'bg' => [
                'type' => 'image',
                'label' => __('Background Image', 'jankx'),
                'default' => ''
            ],
            'height' => [
                'type' => 'text',
                'label' => __('Height', 'jankx'),
                'default' => '300px',
                'help' => __('Banner height, e.g., 300px or 50vh', 'jankx')
            ],
            'hover' => [
                'type' => 'select',
                'label' => __('Hover Effect', 'jankx'),
                'default' => 'zoom',
                'options' => [
                    'none' => __('None', 'jankx'),
                    'zoom' => __('Zoom', 'jankx'),
                    'fade' => __('Fade', 'jankx'),
                    'color' => __('Color', 'jankx')
                ]
            ],
            'link' => [
                'type' => 'text',
                'label' => __('Link URL', 'jankx'),
                'default' => ''
            ],
            'link_target' => [
                'type' => 'select',
                'label' => __('Link Target', 'jankx'),
                'default' => '_self',
                'options' => [
                    '_self' => __('Same Window', 'jankx'),
                    '_blank' => __('New Window', 'jankx')
                ]
            ],
            'text_color' => [
                'type' => 'color',
                'label' => __('Text Color', 'jankx'),
                'default' => '#ffffff'
            ],
            'text_pos' => [
                'type' => 'select',
                'label' => __('Text Position', 'jankx'),
                'default' => 'center',
                'options' => [
                    'top' => __('Top', 'jankx'),
                    'center' => __('Center', 'jankx'),
                    'bottom' => __('Bottom', 'jankx'),
                    'left' => __('Left', 'jankx'),
                    'right' => __('Right', 'jankx'),
                    'top-left' => __('Top Left', 'jankx'),
                    'top-right' => __('Top Right', 'jankx'),
                    'bottom-left' => __('Bottom Left', 'jankx'),
                    'bottom-right' => __('Bottom Right', 'jankx')
                ]
            ],
            '_label' => [
                'type' => 'text',
                'label' => __('Label', 'jankx'),
                'default' => ''
            ]
        ];
    }

    public function getType(): string
    {
        return 'container';
    }

    public function getCategory(): string
    {
        return 'content';
    }

    public function render(array $options, string $content = ''): string
    {
        $classes = ['banner', 'has-hover'];
        $style = '';

        if (!empty($options['height'])) {
            $style .= 'height:' . esc_attr($options['height']) . ';';
        }

        if (!empty($options['hover'])) {
            $classes[] = 'hover-' . esc_attr($options['hover']);
        }

        $textColor = !empty($options['text_color']) ? 'color:' . esc_attr($options['text_color']) . ';' : '';
        $textPos = !empty($options['text_pos']) ? 'text-' . str_replace('-', ' ', esc_attr($options['text_pos'])) : '';

        $output = '<div class="' . implode(' ', $classes) . '" style="' . $style . '">';

        if (!empty($options['bg'])) {
            $output .= '<div class="banner-bg fill" style="background-image:url(' . esc_url($options['bg']) . ');"></div>';
        }

        if (!empty($options['link'])) {
            $target = !empty($options['link_target']) ? ' target="' . esc_attr($options['link_target']) . '"' : '';
            $output .= '<a href="' . esc_url($options['link']) . '" class="fill"' . $target . '></a>';
        }

        $output .= '<div class="banner-layers container">';
        $output .= '<div class="fill banner-link">';
        $output .= '<div class="banner-text ' . $textPos . '" style="' . $textColor . '">';
        $output .= do_shortcode($content);
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</div>';

        return $output;
    }
}
