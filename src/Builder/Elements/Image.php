<?php
namespace Jankx\Extensions\JankxUX\Builder\Elements;

/**
 * Image element (Flatsome ux_image compatible)
 */
class Image extends AbstractElement
{
    public static function getTag(): string
    {
        return 'ux_image';
    }

    public function getName(): string
    {
        return __('Image', 'jankx');
    }

    public function getInfo(): string
    {
        return __('Add an image with optional link', 'jankx');
    }

    public function getIcon(): string
    {
        return 'format-image';
    }

    public function getOptions(): array
    {
        return [
            'id' => [
                'type' => 'image',
                'label' => __('Image', 'jankx'),
                'default' => ''
            ],
            'image_size' => [
                'type' => 'select',
                'label' => __('Image Size', 'jankx'),
                'default' => 'large',
                'options' => [
                    'thumbnail' => __('Thumbnail', 'jankx'),
                    'medium' => __('Medium', 'jankx'),
                    'large' => __('Large', 'jankx'),
                    'full' => __('Full', 'jankx')
                ]
            ],
            'width' => [
                'type' => 'text',
                'label' => __('Width', 'jankx'),
                'default' => '',
                'help' => __('Custom width, e.g., 300px or 50%', 'jankx')
            ],
            'height' => [
                'type' => 'text',
                'label' => __('Height', 'jankx'),
                'default' => '',
                'help' => __('Custom height, e.g., 200px', 'jankx')
            ],
            'lightbox' => [
                'type' => 'select',
                'label' => __('Lightbox', 'jankx'),
                'default' => 'false',
                'options' => [
                    'false' => __('No', 'jankx'),
                    'true' => __('Yes', 'jankx'),
                    'video' => __('Video', 'jankx')
                ]
            ],
            'caption' => [
                'type' => 'text',
                'label' => __('Caption', 'jankx'),
                'default' => ''
            ],
            'link' => [
                'type' => 'text',
                'label' => __('Link URL', 'jankx'),
                'default' => ''
            ],
            'target' => [
                'type' => 'select',
                'label' => __('Link Target', 'jankx'),
                'default' => '_self',
                'options' => [
                    '_self' => __('Same Window', 'jankx'),
                    '_blank' => __('New Window', 'jankx')
                ]
            ],
            'class' => [
                'type' => 'text',
                'label' => __('CSS Class', 'jankx'),
                'default' => ''
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
        return 'single';
    }

    public function getCategory(): string
    {
        return 'content';
    }

    public static function render($options = [], $content = ''): string
    {
        $imageId = !empty($options['id']) ? intval($options['id']) : 0;
        $imageSize = !empty($options['image_size']) ? $options['image_size'] : 'large';
        $width = !empty($options['width']) ? esc_attr($options['width']) : '';
        $height = !empty($options['height']) ? esc_attr($options['height']) : '';

        $style = '';
        if ($width) $style .= 'width:' . $width . ';';
        if ($height) $style .= 'height:' . $height . ';';

        $classes = ['ux-image'];
        if (!empty($options['class'])) {
            $classes[] = esc_attr($options['class']);
        }

        $imgHtml = '';
        if ($imageId) {
            $imgHtml = wp_get_attachment_image($imageId, $imageSize);
        } else {
            $imgHtml = '<img src="" alt="" />';
        }

        $lightbox = !empty($options['lightbox']) && $options['lightbox'] !== 'false';

        if ($lightbox) {
            $imgHtml = '<a href="' . wp_get_attachment_image_url($imageId, 'full') . '" class="image-lightbox">' . $imgHtml . '</a>';
        } elseif (!empty($options['link'])) {
            $target = !empty($options['target']) ? ' target="' . esc_attr($options['target']) . '"' : '';
            $imgHtml = '<a href="' . esc_url($options['link']) . '"' . $target . '>' . $imgHtml . '</a>';
        }

        $caption = !empty($options['caption']) ? '<div class="caption">' . esc_html($options['caption']) . '</div>' : '';

        $output = '<div class="' . implode(' ', $classes) . '" style="' . $style . '">';
        $output .= '<div class="img">' . $imgHtml . '</div>';
        $output .= $caption;
        $output .= '</div>';

        return $output;
    }
}
