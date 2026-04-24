<?php
namespace Jankx\Extensions\JankxUX\Builder\Elements;

/**
 * Featured Box Element - [featured_box] shortcode
 * Full parity with Flatsome's featured_box() function
 */
class FeaturedBox extends AbstractElement
{
    protected static $tag = 'featured_box';

    public static function getConfig()
    {
        return [
            'type'        => 'container',
            'name'        => 'Featured Box',
            'title'       => __('Featured Box', 'jankx'),
            'category'    => __('Content', 'jankx'),
            'description' => __('Icon and text box with various layouts.', 'jankx'),
            'wrap'        => true,
            'options'     => [],
        ];
    }

    public static function render($atts = [], $content = '')
    {
        $atts = shortcode_atts([
            '_id'             => 'icon-box-' . rand(),
            'img'             => '',
            'img_width'       => '60',
            'pos'             => 'top', // top, left, right, center
            'icon_border'     => '',
            'icon_color'      => '',
            'icon_bg'         => '',
            'icon_radius'     => '',
            'title'           => '',
            'title_small'     => '',
            'title_case'      => '',
            'link'            => '',
            'target'          => '_self',
            'rel'             => '',
            'animate'         => '',
            'class'           => '',
            'visibility'      => '',
            'tooltip'         => '',
            'depth'           => '',
            'depth_hover'     => '',
            // Margin/Padding
            'margin'          => '',
            'padding'         => '',
        ], $atts);

        extract($atts);

        if ($visibility === 'hidden') return '';

        $classes = ['icon-box', 'featured-box'];
        $classes[] = 'icon-box-' . $pos;

        if ($class)         $classes[] = $class;
        if ($visibility)    $classes[] = $visibility;
        if ($depth)         $classes[] = 'box-shadow-' . $depth;
        if ($depth_hover)   $classes[] = 'box-shadow-' . $depth_hover . '-hover';

        // Link start/end
        $link_start = $link_end = '';
        if ($link) {
            $link_start = '<a href="' . esc_url($link) . '" target="' . esc_attr($target) . '" rel="' . esc_attr($rel) . '" class="featured-box-link">';
            $link_end   = '</a>';
        }

        // Icon Style
        $icon_classes = ['icon-box-img'];
        if ($icon_border) $icon_classes[] = 'icon-border-' . $icon_border;
        $icon_style   = $img_width ? ' style="width:' . intval($img_width) . 'px"' : '';

        // Animation
        $animate_attr = $animate ? ' data-animate="' . esc_attr($animate) . '"' : '';

        ob_start();
        ?>
        <div id="<?php echo esc_attr($_id); ?>" class="<?php echo esc_attr(implode(' ', $classes)); ?>"<?php echo $animate_attr; ?>>
            <?php echo $link_start; ?>
            <div class="<?php echo esc_attr(implode(' ', $icon_classes)); ?>"<?php echo $icon_style; ?>>
                <div class="icon-box-img-inner">
                    <?php
                    if ($img) {
                        if (is_numeric($img)) {
                            echo wp_get_attachment_image($img, 'thumbnail');
                        } else {
                            echo '<img src="' . esc_url($img) . '" alt="' . esc_attr($title) . '">';
                        }
                    }
                    ?>
                </div>
            </div>
            <div class="icon-box-text last-reset">
                <?php if ($title) : ?>
                    <h5 class="uppercase"><?php echo wp_kses_post($title); ?></h5>
                <?php endif; ?>
                <?php echo do_shortcode($content); ?>
            </div>
            <?php echo $link_end; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
