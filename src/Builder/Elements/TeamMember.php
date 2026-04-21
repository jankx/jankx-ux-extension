<?php
namespace Jankx\Extensions\JankxUX\Builder\Elements;

/**
 * Team Member Element - [team_member] shortcode
 */
class TeamMember extends AbstractElement
{
    protected static $tag = 'team_member';

    protected static function getConfig()
    {
        return [
            'type'        => 'container',
            'name'        => 'Team Member',
            'title'       => __('Team Member', 'jankx'),
            'category'    => __('Content', 'jankx'),
            'description' => __('Display a team member with social links.', 'jankx'),
            'wrap'        => true,
            'options'     => [],
        ];
    }

    public static function render($atts = [], $content = '')
    {
        $atts = shortcode_atts([
            '_id'        => 'team-member-' . rand(),
            'name'       => 'Member Name',
            'title'      => 'Member Title',
            'img'        => '',
            'img_width'  => '250',
            'facebook'   => '',
            'twitter'    => '',
            'instagram'  => '',
            'email'      => '',
            'style'      => 'normal',
            'text_align' => 'center',
            'class'      => '',
            'animate'    => '',
        ], $atts);

        extract($atts);

        $classes = ['team-member', 'box', 'has-hover'];
        if ($class) $classes[] = $class;

        $social_html = '';
        foreach (['facebook', 'twitter', 'instagram', 'email'] as $network) {
            if ($atts[$network]) {
                $icon = ($network === 'email') ? 'icon-envelop' : 'icon-' . $network;
                $social_html .= '<a href="' . esc_url($atts[$network]) . '" target="_blank" class="icon button circle is-outline small ' . $network . '"><i class="' . $icon . '"></i></a>';
            }
        }

        ob_start();
        ?>
        <div id="<?php echo esc_attr($_id); ?>" class="<?php echo esc_attr(implode(' ', $classes)); ?>"<?php if ($animate) echo ' data-animate="' . esc_attr($animate) . '"'; ?>>
            <div class="box-image" style="width:<?php echo intval($img_width); ?>px; max-width:100%; margin: 0 auto;">
                <div class="image-zoom">
                    <?php echo is_numeric($img) ? wp_get_attachment_image($img, 'large') : '<img src="' . esc_url($img) . '" alt="">'; ?>
                </div>
            </div>
            <div class="box-text <?php echo 'text-' . esc_attr($text_align); ?> last-reset">
                <div class="box-text-inner">
                    <h5 class="uppercase mb-0"><?php echo esc_html($name); ?></h5>
                    <p class="is-xsmall uppercase mb-half"><?php echo esc_html($title); ?></p>
                    <div class="is-divider mb-half"></div>
                    <div class="social-icons follow-icons">
                        <?php echo $social_html; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
