<?php
namespace Jankx\Extensions\JankxUX\Builder\Elements;

/**
 * Gap/Spacer element (Flatsome gap compatible)
 */
class Gap extends AbstractElement
{
    public function getTag(): string
    {
        return 'gap';
    }

    public function getName(): string
    {
        return __('Gap', 'jankx');
    }

    public function getInfo(): string
    {
        return __('Add vertical spacing between elements', 'jankx');
    }

    public function getIcon(): string
    {
        return 'minus';
    }

    public function getOptions(): array
    {
        return [
            'height' => [
                'type' => 'text',
                'label' => __('Height', 'jankx'),
                'default' => '30px',
                'help' => __('Gap height, e.g., 30px or 5vh', 'jankx')
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
        return 'layout';
    }

    public static function render($options = [], $content = ''): string
    {
        $height = !empty($options['height']) ? esc_attr($options['height']) : '30px';
        return '<div class="gap-element" style="display:block; height:auto; padding-top:' . $height . ';"></div>';
    }
}
