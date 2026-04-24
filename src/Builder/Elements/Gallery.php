<?php
namespace Jankx\Extensions\JankxUX\Builder\Elements;

/**
 * UX Gallery - [ux_gallery] shortcode
 */
class Gallery extends AbstractElement
{
    protected static $tag = 'ux_gallery';

    public static function getConfig()
    {
        return [
            'type'        => 'element',
            'name'        => 'Gallery',
            'title'       => __('Gallery', 'jankx'),
            'category'    => __('Content', 'jankx'),
            'description' => __('Display image gallery in grid or slider.', 'jankx'),
            'wrap'        => false,
            'options'     => [],
        ];
    }

    public static function render($atts = [], $content = '')
    {
        $atts = shortcode_atts([
            '_id'           => 'gallery-' . rand(),
            'ids'           => '',
            'type'          => 'grid', // grid, slider, masonry
            'columns'       => '4',
            'columns__md'   => '3',
            'columns__sm'   => '2',
            'image_height'  => '100%',
            'image_size'    => 'medium',
            'image_hover'   => '',
            'image_overlay' => '',
            'lightbox'      => 'true',
            'animate'       => '',
            'class'         => '',
            'visibility'    => '',
        ], $atts);

        extract($atts);

        $image_ids = explode(',', $ids);
        if (empty($image_ids)) return '';

        $classes = ['row', 'ux-gallery'];
        if ($type === 'slider')  $classes[] = 'slider';
        if ($type === 'masonry') $classes[] = 'masonry';
        if ($class)              $classes[] = $class;
        if ($visibility)         $classes[] = $visibility;

        ob_start();
        ?>
        <div id="<?php echo esc_attr($_id); ?>" class="<?php echo esc_attr(implode(' ', $classes)); ?>"
             <?php if ($type === 'slider') echo 'data-flickity-options=\'{"cellAlign": "left", "wrapAround": true}\''; ?>>
            <?php
            foreach ($image_ids as $img_id) :
                if (empty($img_id)) continue;
                $col_classes = ['col', 'gallery-col'];
                if ($type !== 'slider') {
                    $col_classes[] = 'large-' . (12 / intval($columns));
                    $col_classes[] = 'medium-' . (12 / intval($columns__md));
                    $col_classes[] = 'small-' . (12 / intval($columns__sm));
                }
                ?>
                <div class="<?php echo esc_attr(implode(' ', $col_classes)); ?>">
                    <div class="col-inner">
                        <?php
                        $img_args = [
                            'id' => $img_id,
                            'image_size' => $image_size,
                            'height' => $image_height,
                            'image_hover' => $image_hover,
                            'image_overlay' => $image_overlay,
                            'lightbox' => $lightbox,
                        ];
                        echo Image::render($img_args);
                        ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
