<?php
namespace Jankx\Extensions\JankxUX\Builder\Elements;

/**
 * Slider Element - [ux_slider] shortcode
 * Flatsome compatible
 */
class Slider extends AbstractElement
{
    protected static $tag = 'ux_slider';

    protected static function getConfig()
    {
        return [
            'type' => 'container',
            'name' => 'Slider',
            'title' => __('Slider', 'jankx'),
            'category' => __('Content', 'jankx'),
            'description' => __('Image or content slider/carousel.', 'jankx'),
            'wrap' => true,
            'options' => [
                'timer' => ['type' => 'text', 'heading' => __('Autoplay Timer (ms)', 'jankx'), 'default' => '5000'],
                'bullets' => ['type' => 'checkbox', 'heading' => __('Show Bullets', 'jankx'), 'default' => 'true'],
                'arrows' => ['type' => 'checkbox', 'heading' => __('Show Arrows', 'jankx'), 'default' => 'true'],
                'auto_slide' => ['type' => 'checkbox', 'heading' => __('Auto Slide', 'jankx'), 'default' => 'true'],
                'pause_hover' => ['type' => 'checkbox', 'heading' => __('Pause on Hover', 'jankx'), 'default' => 'true'],
                'slide_width' => ['type' => 'text', 'heading' => __('Slide Width', 'jankx'), 'default' => ''],
                'infinitive' => ['type' => 'checkbox', 'heading' => __('Infinite Loop', 'jankx'), 'default' => 'true'],
                'draggable' => ['type' => 'checkbox', 'heading' => __('Draggable', 'jankx'), 'default' => 'true'],
                'class' => ['type' => 'text', 'heading' => __('Custom Class', 'jankx'), 'default' => ''],
            ],
            'presets' => [],
            'allow_in' => ['col', 'section', 'ux_block'],
        ];
    }

    public static function render($options, $content = '')
    {
        $classes = ['slider', 'jux-slider'];
        $data = [];

        if (!empty($options['class'])) $classes[] = esc_attr($options['class']);
        if (!empty($options['timer'])) $data[] = 'data-timer="' . intval($options['timer']) . '"';
        if (isset($options['auto_slide']) && $options['auto_slide'] === 'true') $data[] = 'data-auto="true"';
        if (isset($options['bullets']) && $options['bullets'] === 'true') $data[] = 'data-bullets="true"';
        if (isset($options['arrows']) && $options['arrows'] === 'true') $data[] = 'data-arrows="true"';
        if (isset($options['pause_hover']) && $options['pause_hover'] === 'true') $data[] = 'data-pause-hover="true"';
        if (isset($options['infinitive']) && $options['infinitive'] === 'true') $data[] = 'data-infinite="true"';
        if (isset($options['draggable']) && $options['draggable'] === 'true') $data[] = 'data-draggable="true"';

        $html = '<div class="' . esc_attr(implode(' ', $classes)) . '" ' . implode(' ', $data) . '>';
        $html .= '<div class="slider-wrapper">' . $content . '</div>';
        $html .= '</div>';

        return $html;
    }
}
