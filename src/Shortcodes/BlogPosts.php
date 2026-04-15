<?php
namespace Jankx\Extensions\JankxUX\Shortcodes;

class BlogPosts extends AbstractShortcode
{
    public static function render($atts, $content = null)
    {
        $atts = shortcode_atts(array(
            'style'         => 'normal', // normal, masha, bounce, pull
            'columns'       => '3',
            'posts'         => '8',
            'category'      => '',
            'image_height'  => '56%',
            'show_date'     => 'true',
            'excerpt'       => 'true',
            'class'         => '',
        ), $atts);

        $query_args = array(
            'post_type'      => 'post',
            'posts_per_page' => intval($atts['posts']),
        );

        if (!empty($atts['category'])) {
            $query_args['category_name'] = $atts['category'];
        }

        $query = new \WP_Query($query_args);
        
        if (!$query->have_posts()) return '';

        ob_start();
        ?>
        <div class="row items-grid <?php echo esc_attr($atts['class']); ?>">
            <?php while ($query->have_posts()) : $query->the_post(); ?>
                <div class="col large-<?php echo 12 / intval($atts['columns']); ?>">
                    <div class="col-inner">
                        <div class="box box-blog post-item">
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="box-image" style="padding-top: <?php echo esc_attr($atts['image_height']); ?>;">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('medium_large'); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            <div class="box-text text-left">
                                <h5 class="post-title uppercase"><?php the_title(); ?></h5>
                                <?php if ($atts['show_date'] === 'true') : ?>
                                    <p class="post-date small uppercase"><?php echo get_the_date(); ?></p>
                                <?php endif; ?>
                                <?php if ($atts['excerpt'] === 'true') : ?>
                                    <p class="from_the_blog_excerpt"><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
