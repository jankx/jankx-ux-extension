<?php
namespace Jankx\Extensions\JankxUX\Builder\Elements;

/**
 * Blog Posts Element - [blog_posts] shortcode
 */
class BlogPosts extends AbstractElement
{
    protected static $tag = 'blog_posts';

    protected static function getConfig()
    {
        return [
            'type'        => 'element',
            'name'        => 'Blog Posts',
            'title'       => __('Blog Posts', 'jankx'),
            'category'    => __('Content', 'jankx'),
            'description' => __('Display latest blog posts in grid or slider.', 'jankx'),
            'wrap'        => false,
            'options'     => [],
        ];
    }

    public static function render($atts = [], $content = '')
    {
        $atts = shortcode_atts([
            '_id'           => 'blog-posts-' . rand(),
            'style'         => 'normal', // normal, bounce, slide, overlay
            'type'          => 'grid',   // grid, slider, row
            'columns'       => '4',
            'columns__md'   => '3',
            'columns__sm'   => '1',
            'posts'         => '8',
            'category'      => '',
            'excerpt'       => 'true',
            'comments'      => 'false',
            'date'          => 'true',
            'readmost'      => 'false',
            'image_height'  => '56.25%',
            'image_size'    => 'medium',
            'image_hover'   => '',
            'image_overlay' => '',
            'text_align'    => 'center',
            'text_pos'      => 'bottom',
            'animate'       => '',
            'class'         => '',
            'visibility'    => '',
            'depth'         => '',
            'depth_hover'   => '',
        ], $atts);

        extract($atts);

        $query_args = [
            'post_type'      => 'post',
            'posts_per_page' => intval($posts),
            'post_status'    => 'publish',
        ];

        if ($category) {
            $query_args['category_name'] = $category;
        }

        $query = new \WP_Query($query_args);
        if (!$query->have_posts()) return '';

        $classes = ['row', 'blog-posts'];
        if ($class)      $classes[] = $class;
        if ($visibility) $classes[] = $visibility;

        // Grid/Slider setup
        $is_slider = ($type === 'slider');
        if ($is_slider) {
            $classes[] = 'slider';
            // Placeholder: thực tế sẽ cần Flickity init JSON như class Slider
        }

        ob_start();
        ?>
        <div id="<?php echo esc_attr($_id); ?>" class="<?php echo esc_attr(implode(' ', $classes)); ?>"
             data-columns="<?php echo esc_attr($columns); ?>"
             <?php if ($is_slider) echo 'data-flickity-options=\'{"cellAlign": "left", "wrapAround": true, "autoPlay": false}\''; ?>>
            <?php
            while ($query->have_posts()) : $query->the_post();
                $col_classes = ['col', 'post-item'];
                if (!$is_slider) {
                    $col_classes[] = 'large-' . (12 / intval($columns));
                    $col_classes[] = 'medium-' . (12 / intval($columns__md));
                    $col_classes[] = 'small-' . (12 / intval($columns__sm));
                }
                ?>
                <div class="<?php echo esc_attr(implode(' ', $col_classes)); ?>">
                    <div class="col-inner">
                        <div class="box <?php echo $depth ? 'box-shadow-' . $depth : ''; ?> <?php echo $depth_hover ? 'box-shadow-' . $depth_hover . '-hover' : ''; ?> has-hover">
                            <div class="box-image">
                                <a href="<?php the_permalink(); ?>">
                                    <div class="image-<?php echo esc_attr($image_hover); ?>" style="padding-top:<?php echo esc_attr($image_height); ?>;">
                                        <?php the_post_thumbnail($image_size); ?>
                                        <?php if ($image_overlay) : ?>
                                            <div class="overlay" style="background-color:<?php echo esc_attr($image_overlay); ?>"></div>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            </div>
                            <div class="box-text <?php echo 'text-' . esc_attr($text_align); ?> last-reset">
                                <div class="box-text-inner">
                                    <?php if ($date === 'true') : ?>
                                        <p class="post-date"><?php echo get_the_date(); ?></p>
                                    <?php endif; ?>
                                    <h5 class="post-title is-large"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
                                    <div class="is-divider"></div>
                                    <?php if ($excerpt === 'true') : ?>
                                        <p class="from_the_blog_excerpt"><?php echo wp_trim_words(get_the_excerpt(), 15); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
            endwhile;
            wp_reset_postdata();
            ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
