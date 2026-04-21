<?php
namespace Jankx\Extensions\JankxUX\Builder\Elements;

/**
 * Slider Element - [ux_slider] shortcode
 * Full parity with Flatsome's shortcode_ux_slider() function (Flickity-based)
 */
class Slider extends AbstractElement
{
    protected static $tag = 'ux_slider';

    protected static function getConfig()
    {
        return [
            'type'        => 'container',
            'name'        => 'Slider',
            'title'       => __('Slider', 'jankx'),
            'category'    => __('Content', 'jankx'),
            'description' => __('Image or content slider/carousel (Flickity).', 'jankx'),
            'wrap'        => true,
            'options'     => [],
        ];
    }

    public static function render($atts = [], $content = '')
    {
        $atts = shortcode_atts([
            '_id'                => 'slider-' . rand(),
            'timer'              => '6000',
            'bullets'            => 'true',
            'visibility'         => '',
            'class'              => '',
            'type'               => 'slide',
            'bullet_style'       => '',
            'auto_slide'         => 'true',
            'auto_height'        => 'true',
            'bg_color'           => '',
            'slide_align'        => 'center',
            'style'              => 'normal',
            'slide_width'        => '',
            'slide_width__md'    => null,
            'slide_width__sm'    => null,
            'arrows'             => 'true',
            'pause_hover'        => 'true',
            'hide_nav'           => '',
            'nav_style'          => 'circle',
            'nav_color'          => 'light',
            'nav_size'           => 'large',
            'nav_pos'            => '',
            'infinitive'         => 'true',
            'freescroll'         => 'false',
            'parallax'           => '0',
            'margin'             => '',
            'margin__md'         => '',
            'margin__sm'         => '',
            'columns'            => '1',
            'height'             => '',
            'rtl'                => 'false',
            'draggable'          => 'true',
            'friction'           => '0.6',
            'selectedattraction' => '0.1',
            'threshold'          => '10',
            // Deprecated
            'mobile'             => 'true',
        ], $atts);

        extract($atts);

        // Stop if visibility is hidden
        if ($visibility === 'hidden') return '';
        if ($mobile !== 'true' && !$visibility) { $visibility = 'hide-for-small'; }

        // Wrapper classes
        $wrapper_classes = ['slider-wrapper', 'relative'];
        if ($class)      $wrapper_classes[] = $class;
        if ($visibility) $wrapper_classes[] = $visibility;
        $wrapper_class_str = implode(' ', $wrapper_classes);

        // Slider classes
        $slider_classes = ['slider'];
        if ($type === 'fade')  $slider_classes[] = 'slider-type-' . $type;
        if ($bullet_style)     $slider_classes[] = 'slider-nav-dots-' . $bullet_style;
        if ($nav_style)        $slider_classes[] = 'slider-nav-' . $nav_style;
        if ($nav_size)         $slider_classes[] = 'slider-nav-' . $nav_size;
        if ($nav_color)        $slider_classes[] = 'slider-nav-' . $nav_color;
        if ($nav_pos)          $slider_classes[] = 'slider-nav-' . $nav_pos;
        if ($style)            $slider_classes[] = 'slider-style-' . $style;
        if ($hide_nav === 'true') $slider_classes[] = 'slider-show-nav';
        $slider_class_str = implode(' ', $slider_classes);

        // Auto slide value
        $auto_slide_val = ($auto_slide === 'true') ? $timer : 'false';

        // Nav flags
        $is_arrows  = ($arrows  === 'false') ? 'false' : 'true';
        $is_bullets = ($bullets === 'false') ? 'false' : 'true';

        if (is_rtl()) $rtl = 'true';

        // Wrapper inline styles
        $wrapper_style = '';
        if ($bg_color) {
            $wrapper_style = ' style="background-color:' . esc_attr($bg_color) . '"';
        }

        // Flickity options JSON
        $flickity = json_encode([
            'cellAlign'             => $slide_align,
            'imagesLoaded'          => true,
            'lazyLoad'              => 1,
            'freeScroll'            => $freescroll === 'true',
            'wrapAround'            => $infinitive === 'true',
            'autoPlay'              => ($auto_slide_val === 'false') ? false : intval($auto_slide_val),
            'pauseAutoPlayOnHover'  => $pause_hover === 'true',
            'prevNextButtons'       => $is_arrows === 'true',
            'contain'               => true,
            'adaptiveHeight'        => $auto_height === 'true',
            'dragThreshold'         => intval($threshold),
            'percentPosition'       => true,
            'pageDots'              => $is_bullets === 'true',
            'rightToLeft'           => $rtl === 'true',
            'draggable'             => $draggable === 'true',
            'selectedAttraction'    => floatval($selectedattraction),
            'parallax'              => intval($parallax),
            'friction'              => floatval($friction),
        ]);

        ob_start();
        ?>
        <div class="<?php echo esc_attr($wrapper_class_str); ?>" id="<?php echo esc_attr($_id); ?>"<?php echo $wrapper_style; ?>>
            <div class="<?php echo esc_attr($slider_class_str); ?>"
                 data-flickity-options='<?php echo esc_attr($flickity); ?>'>
                <?php echo do_shortcode($content); ?>
            </div>
            <div class="loading-spin dark large centered"></div>
        </div>
        <?php
        return ob_get_clean();
    }
}
