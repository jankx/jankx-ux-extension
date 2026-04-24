<?php
namespace Jankx\Extensions\JankxUX\Builder\Elements;

/**
 * Image element - [ux_image] shortcode
 * Full parity with Flatsome's ux_image() function
 */
class Image extends AbstractElement
{
    protected static $tag = 'ux_image';

    public static function getConfig()
    {
        return [
            'type'        => 'element',
            'name'        => 'Image',
            'title'       => __('Image', 'jankx'),
            'category'    => __('Content', 'jankx'),
            'description' => __('Add an image with optional link, lightbox, overlay.', 'jankx'),
            'wrap'        => false,
            'options'     => [],
        ];
    }

    public static function render($atts = [], $content = '')
    {
        $atts = shortcode_atts([
            '_id'                 => 'image_' . rand(),
            'class'               => '',
            'visibility'          => '',
            'id'                  => '',
            'org_img'             => '',
            'caption'             => '',
            'animate'             => '',
            'animate_delay'       => '',
            'lightbox'            => '',
            'lightbox_image_size' => 'large',
            'lightbox_caption'    => '',
            'image_title'         => '',
            'height'              => '',
            'image_overlay'       => '',
            'image_hover'         => '',
            'image_hover_alt'     => '',
            'image_size'          => 'large',
            'icon'                => '',
            'width'               => '',
            'margin'              => '',
            'position_x'          => '',
            'position_x__sm'      => '',
            'position_x__md'      => '',
            'position_y'          => '',
            'position_y__sm'      => '',
            'position_y__md'      => '',
            'depth'               => '',
            'parallax'            => '',
            'depth_hover'         => '',
            'link'                => '',
            'target'              => '_self',
            'rel'                 => '',
        ], $atts);

        extract($atts);

        // No image - builder placeholder
        if (empty($id)) {
            return '<div class="uxb-no-content uxb-image">Upload Image...</div>';
        }

        $classes       = ['img', 'has-hover'];
        $classes_inner = ['img-inner'];

        // Custom class & visibility
        if ($class)      $classes[] = $class;
        if ($visibility) $classes[] = $visibility;

        // Resolve original image src for lightbox
        if (is_numeric($id)) {
            if (!$org_img) {
                $org_img_data = wp_get_attachment_image_src($id, $lightbox_image_size);
                $org_img      = $org_img_data ? $org_img_data[0] : '';
            }
            if ($caption === 'true') {
                $image_meta = wp_prepare_attachment_for_js($id);
                $caption    = is_array($image_meta) ? $image_meta['caption'] : '';
            }
        } else {
            if (!$org_img) $org_img = $id;
        }

        // Inner classes
        if ($image_hover)     $classes_inner[] = 'image-' . $image_hover;
        if ($image_hover_alt) $classes_inner[] = 'image-' . $image_hover_alt;
        if ($height)          $classes_inner[] = 'image-cover';
        if ($depth)           $classes_inner[] = 'box-shadow-' . $depth;
        if ($depth_hover)     $classes_inner[] = 'box-shadow-' . $depth_hover . '-hover';

        // Link setup
        $link_start = '';
        $link_end   = '';
        $link_class = '';

        if ($link) {
            if (strpos($link, 'watch?v=') !== false) {
                $icon       = 'icon-play';
                $link_class = 'open-video';
                if (!$image_overlay) $image_overlay = 'rgba(0,0,0,.2)';
            }
            $target_attr = ($target && $target !== '_self') ? ' target="' . esc_attr($target) . '"' : '';
            $rel_attr    = $rel ? ' rel="' . esc_attr($rel) . '"' : '';
            $link_start  = '<a class="' . esc_attr($link_class) . '" href="' . esc_url($link) . '"' . $target_attr . $rel_attr . '>';
            $link_end    = '</a>';
        } elseif ($lightbox) {
            $image_meta = is_numeric($id) ? wp_prepare_attachment_for_js($id) : [];
            $lb_title   = ($lightbox_caption && is_array($image_meta)) ? $image_meta['caption'] : '';
            $link_start = '<a class="image-lightbox lightbox-gallery" title="' . esc_attr($lb_title) . '" href="' . esc_url($org_img) . '">';
            $link_end   = '</a>';
        }

        // Position classes
        if ($position_x !== '') $classes[] = 'x' . intval($position_x);
        if ($position_y !== '') $classes[] = 'y' . intval($position_y);

        // Inner inline styles
        $inner_css_parts = [];
        if ($height) $inner_css_parts[] = 'padding-top:' . esc_attr($height);
        if ($margin) $inner_css_parts[] = 'margin:' . esc_attr($margin);
        $inner_style = $inner_css_parts ? ' style="' . implode(';', $inner_css_parts) . '"' : '';

        // Outer inline styles
        $outer_style_parts = [];
        if ($width)  $outer_style_parts[] = 'width:' . esc_attr($width) . '%';
        $outer_style = $outer_style_parts ? ' style="' . implode(';', $outer_style_parts) . '"' : '';

        // Parallax
        $parallax_attr = '';
        if ($parallax) {
            $parallax_attr = 'data-parallax-fade="true" data-parallax="' . esc_attr($parallax) . '"';
        }

        // Get image HTML
        if (is_numeric($id)) {
            $img_html = wp_get_attachment_image($id, $image_size, false, [
                'title' => filter_var($image_title, FILTER_VALIDATE_BOOLEAN) ? '' : esc_attr($image_title),
            ]);
        } else {
            $img_html = '<img src="' . esc_url($id) . '" alt="' . esc_attr($caption) . '">';
        }

        $class_str       = implode(' ', $classes);
        $class_inner_str = implode(' ', $classes_inner);

        ob_start();
        ?>
        <div class="<?php echo esc_attr($class_str); ?>" id="<?php echo esc_attr($_id); ?>"<?php echo $outer_style; ?>>
            <?php echo $link_start; ?>
            <?php if ($parallax_attr) echo '<div ' . $parallax_attr . '>'; ?>
            <?php if ($animate) echo '<div data-animate="' . esc_attr($animate) . '">'; ?>
            <div class="<?php echo esc_attr($class_inner_str); ?> dark"<?php echo $inner_style; ?>>
                <?php echo $img_html; ?>
                <?php if ($image_overlay) : ?>
                    <div class="overlay" style="background-color:<?php echo esc_attr($image_overlay); ?>"></div>
                <?php endif; ?>
                <?php if ($icon) : ?>
                    <div class="absolute no-click x50 y50 md-x50 md-y50 lg-x50 lg-y50 text-shadow-2">
                        <div class="overlay-icon">
                            <i class="<?php echo esc_attr($icon); ?>"></i>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($caption) : ?>
                    <div class="caption"><?php echo wp_kses_post($caption); ?></div>
                <?php endif; ?>
            </div>
            <?php if ($animate) echo '</div>'; ?>
            <?php if ($parallax_attr) echo '</div>'; ?>
            <?php echo $link_end; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
