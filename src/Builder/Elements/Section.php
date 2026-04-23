<?php
namespace Jankx\Extensions\JankxUX\Builder\Elements;

/**
 * Section Element - [section] / [ux_section] shortcode
 * Full parity with Flatsome's ux_section() function
 */
class Section extends AbstractElement
{
    protected static $tag = 'section';

    protected static function getConfig()
    {
        return [
            'type'        => 'container',
            'name'        => 'Section',
            'title'       => __('Section', 'jankx'),
            'category'    => __('Layout', 'jankx'),
            'description' => __('Full-width content section with background options.', 'jankx'),
            'wrap'        => true,
            'options'     => [
                'bg_color' => [
                    'type'    => 'color',
                    'heading' => __('Background Color', 'jankx'),
                    'default' => '',
                ],
                'bg' => [
                    'type'    => 'image',
                    'heading' => __('Background Image', 'jankx'),
                ],
                'bg_overlay' => [
                    'type'    => 'color',
                    'heading' => __('Overlay Color', 'jankx'),
                ],
                'padding' => [
                    'type'    => 'textfield',
                    'heading' => __('Padding', 'jankx'),
                    'default' => '30px',
                    'placeholder' => '30px, 50px, 5%',
                ],
                'height' => [
                    'type'    => 'textfield',
                    'heading' => __('Min Height', 'jankx'),
                    'default' => '',
                    'placeholder' => 'auto, 500px, 100vh',
                ],
                'dark' => [
                    'type'    => 'checkbox',
                    'heading' => __('Dark Text', 'jankx'),
                    'default' => 'false',
                ],
            ],
            'presets'     => [],
            'allow_in'    => [],
        ];
    }

    public static function render($atts = [], $content = '')
    {
        $atts = shortcode_atts([
            '_id'              => 'section_' . rand(),
            'class'            => '',
            'label'            => '',
            'visibility'       => '',
            'sticky'           => '',
            // Background
            'bg'               => '',
            'bg_size'          => '',
            'bg_color'         => '',
            'bg_overlay'       => '',
            'bg_overlay__sm'   => '',
            'bg_overlay__md'   => '',
            'bg_pos'           => '',
            'parallax'         => '',
            'effect'           => '',
            // Video
            'video_mp4'        => '',
            'video_ogg'        => '',
            'video_webm'       => '',
            'video_sound'      => 'false',
            'video_loop'       => 'true',
            'youtube'          => '',
            'video_visibility' => 'hide-for-medium',
            // Layout
            'dark'             => 'false',
            'mask'             => '',
            'padding'          => '30px',
            'padding__sm'      => '',
            'padding__md'      => '',
            'height'           => '',
            'height__sm'       => '',
            'height__md'       => '',
            'margin'           => '',
            'loading'          => '',
            'scroll_for_more'  => '',
            // Shape divider
            'divider_top'            => '',
            'divider_top_height'     => '150px',
            'divider_top_height__sm' => null,
            'divider_top_height__md' => null,
            'divider_top_width'      => '100',
            'divider_top_width__sm'  => null,
            'divider_top_width__md'  => null,
            'divider_top_fill'       => '',
            'divider_top_flip'       => 'false',
            'divider_top_to_front'   => 'false',
            'divider'                => '',
            'divider_height'         => '150px',
            'divider_height__sm'     => null,
            'divider_height__md'     => null,
            'divider_width'          => '100',
            'divider_width__sm'      => null,
            'divider_width__md'      => null,
            'divider_fill'           => '',
            'divider_flip'           => 'false',
            'divider_to_front'       => 'false',
            // Border Control
            'border'           => '',
            'border_hover'     => '',
            'border_color'     => '',
            'border_margin'    => '',
            'border_radius'    => '',
            'border_style'     => '',
        ], $atts);

        extract($atts);

        // Hide if visibility is hidden
        if ($visibility === 'hidden') {
            return '';
        }

        $classes    = ['section'];
        $classes_bg = ['section-bg', 'fill'];

        // Fix old bg fallback
        if (strpos($bg, '#') !== false) {
            $atts['bg_color'] = $bg;
            $atts['bg']       = false;
            $bg = false;
        }

        if ($class)      $classes[] = $class;
        if ($dark === 'true') $classes[] = 'dark';
        if ($sticky)     $classes[] = 'sticky-section';
        if ($mask)       $classes[] = 'has-mask mask-' . $mask;
        if ($visibility) $classes[] = $visibility;
        if ($border_hover) $classes[] = 'has-hover';
        if ($scroll_for_more) $classes[] = 'has-scroll-for-more';

        // Parallax
        $parallax_attr = '';
        if ($parallax) {
            $classes[]     = 'has-parallax';
            $parallax_attr = 'data-parallax-container=".section" data-parallax-background data-parallax="-' . esc_attr($parallax) . '"';
        }

        // Full height
        if ($height === '100vh') {
            $classes[] = 'is-full-height';
        }

        // Build inline styles
        $style_parts = [];
        if ($padding)   $style_parts[] = 'padding-top:' . esc_attr($padding) . ';padding-bottom:' . esc_attr($padding);
        if ($margin)    $style_parts[] = 'margin-bottom:' . esc_attr($margin);
        if ($height && $height !== '100vh') $style_parts[] = 'min-height:' . esc_attr($height);
        if (!empty($atts['bg_color'])) $style_parts[] = 'background-color:' . esc_attr($atts['bg_color']);
        $inline_style = $style_parts ? ' style="' . implode(';', $style_parts) . '"' : '';

        $class_str    = implode(' ', $classes);
        $class_bg_str = implode(' ', $classes_bg);

        ob_start();
        ?>
        <section class="<?php echo esc_attr($class_str); ?>" id="<?php echo esc_attr($_id); ?>"<?php echo $inline_style; ?>>
            <div class="<?php echo esc_attr($class_bg_str); ?>" <?php echo $parallax_attr; ?>>
                <?php
                // Background image
                if ($bg) {
                    if (is_numeric($bg)) {
                        $size   = $bg_size ?: 'large';
                        $img    = wp_get_attachment_image_src($bg, $size);
                        $src    = $img ? $img[0] : '';
                    } else {
                        $src = $bg;
                    }
                    if (!empty($src)) {
                        echo '<img src="' . esc_url($src) . '" class="bg-image" alt="" loading="lazy">';
                    }
                }
                // Background position
                if ($bg_pos) {
                    echo '<style>#' . esc_attr($_id) . ' .section-bg img { object-position: ' . esc_attr($bg_pos) . '; }</style>';
                }
                // Video
                if ($video_mp4 || $video_webm || $video_ogg) {
                    $muted   = ($video_sound === 'false') ? 'muted' : '';
                    $loop    = ($video_loop === 'true') ? 'loop' : '';
                    $v_class = $video_visibility ? esc_attr($video_visibility) : '';
                    echo '<video class="bg-video ' . $v_class . '" autoplay ' . $muted . ' playsinline ' . $loop . '>';
                    if ($video_mp4)  echo '<source src="' . esc_url($video_mp4)  . '" type="video/mp4">';
                    if ($video_webm) echo '<source src="' . esc_url($video_webm) . '" type="video/webm">';
                    if ($video_ogg)  echo '<source src="' . esc_url($video_ogg)  . '" type="video/ogg">';
                    echo '</video>';
                }
                // Overlay
                if ($bg_overlay) {
                    echo '<div class="section-bg-overlay absolute fill" style="background-color:' . esc_attr($bg_overlay) . ';"></div>';
                }
                // Loading spinner
                if ($loading) {
                    echo '<div class="loading-spin centered"></div>';
                }
                // Scroll for more
                if ($scroll_for_more) {
                    echo '<button class="scroll-for-more z-5 icon absolute bottom h-center" aria-label="' . esc_attr__('Scroll for more', 'jankx') . '">'
                        . '<i class="icon-angle-down"></i>'
                        . '</button>';
                }
                // Effects
                if ($effect) {
                    echo '<div class="effect-' . esc_attr($effect) . ' bg-effect fill no-click"></div>';
                }
                // Border
                if ($border) {
                    $b_style = '';
                    if ($border_margin) $b_style .= 'margin:' . esc_attr($border_margin) . ';';
                    if ($border_color)  $b_style .= 'border-color:' . esc_attr($border_color) . ';';
                    if ($border_style)  $b_style .= 'border-style:' . esc_attr($border_style) . ';';
                    if ($border_radius) $b_style .= 'border-radius:' . esc_attr($border_radius) . ';';
                    echo '<div class="outline-border border-' . esc_attr($border) . '"' . ($b_style ? ' style="' . $b_style . '"' : '') . '></div>';
                }
                ?>
            </div>

            <?php
            // Shape dividers
            if ($divider_top) {
                Banner::renderShapeDividerPublic('top', $divider_top, $atts);
            }
            if ($divider) {
                Banner::renderShapeDividerPublic('bottom', $divider, $atts);
            }
            ?>

            <div class="section-content relative">
                <?php echo do_shortcode($content); ?>
            </div>
        </section>
        <?php
        return ob_get_clean();
    }
}
