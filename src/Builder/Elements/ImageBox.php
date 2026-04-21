<?php
namespace Jankx\Extensions\JankxUX\Builder\Elements;

/**
 * UX Image Box - [ux_image_box] shortcode
 */
class ImageBox extends AbstractElement
{
    protected static $tag = 'ux_image_box';

    protected static function getConfig()
    {
        return [
            'type'        => 'container',
            'name'        => 'Image Box',
            'title'       => __('Image Box', 'jankx'),
            'category'    => __('Content', 'jankx'),
            'description' => __('An image with a text box underneath.', 'jankx'),
            'wrap'        => true,
            'options'     => [],
        ];
    }

    public static function render($atts = [], $content = '')
    {
        $atts = shortcode_atts([
            '_id'         => 'image-box-' . rand(),
            'img'         => '',
            'img_width'   => '100',
            'link'        => '',
            'target'      => '_self',
            'rel'         => '',
            'animate'     => '',
            'class'       => '',
            'visibility'  => '',
            'depth'       => '',
            'depth_hover' => '',
            'hover'       => '',
            'text_padding'=> '',
            'text_bg'     => '',
            'text_color'  => '',
            'text_align'  => 'center',
            'text_size'   => '',
            'image_size'  => 'large',
            'image_hover' => '',
            'image_overlay' => '',
        ], $atts);

        extract($atts);

        $classes = ['box', 'has-hover'];
        if ($class)       $classes[] = $class;
        if ($visibility)  $classes[] = $visibility;
        if ($hover)       $classes[] = 'box-' . $hover;
        if ($depth)       $classes[] = 'box-shadow-' . $depth;
        if ($depth_hover) $classes[] = 'box-shadow-' . $depth_hover . '-hover';

        $text_classes = ['box-text', 'last-reset'];
        if ($text_align) $text_classes[] = 'text-' . $text_align;
        if ($text_color === 'dark') $text_classes[] = 'dark';
        if ($text_size)  $text_classes[] = 'is-' . $text_size;

        $link_start = $link_end = '';
        if ($link) {
            $link_start = '<a href="' . esc_url($link) . '" target="' . esc_attr($target) . '" rel="' . esc_attr($rel) . '">';
            $link_end   = '</a>';
        }

        ob_start();
        ?>
        <div id="<?php echo esc_attr($_id); ?>" class="<?php echo esc_attr(implode(' ', $classes)); ?>"<?php if ($animate) echo ' data-animate="' . esc_attr($animate) . '"'; ?>>
            <?php echo $link_start; ?>
            <div class="box-image" style="width:<?php echo intval($img_width); ?>%;">
                <div class="image-<?php echo esc_attr($image_hover); ?>">
                    <?php
                    if ($img) {
                        if (is_numeric($img)) {
                            echo wp_get_attachment_image($img, $image_size);
                        } else {
                            echo '<img src="' . esc_url($img) . '" alt="">';
                        }
                    }
                    if ($image_overlay) {
                        echo '<div class="overlay" style="background-color:' . esc_attr($image_overlay) . '"></div>';
                    }
                    ?>
                </div>
            </div>
            <div class="<?php echo esc_attr(implode(' ', $text_classes)); ?>"<?php if ($text_bg || $text_padding) : ?>
                style="<?php if ($text_bg) echo 'background-color:' . esc_attr($text_bg) . ';'; ?> <?php if ($text_padding) echo 'padding:' . esc_attr($text_padding) . ';'; ?>"
            <?php endif; ?>>
                <div class="box-text-inner">
                    <?php echo do_shortcode($content); ?>
                </div>
            </div>
            <?php echo $link_end; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
