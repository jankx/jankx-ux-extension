<?php
namespace Jankx\Extensions\JankxUX\Builder\Elements;

/**
 * Banner element (Flatsome ux_banner compatible)
 * Full parity with Flatsome's flatsome_ux_banner() function
 */
class Banner extends AbstractElement
{
    protected static $tag = 'ux_banner';

    protected static function getConfig()
    {
        return [
            'type'     => 'container',
            'name'     => 'Banner',
            'title'    => __('Banner', 'jankx'),
            'category' => __('Content', 'jankx'),
            'wrap'     => false,
            'options'  => [],
        ];
    }

    public static function render($atts = [], $content = ''): string
    {
        $atts = shortcode_atts([
            '_id'                => 'banner-' . rand(),
            'visibility'         => '',
            // Layout
            'hover'              => '',
            'hover_alt'          => '',
            'alt'                => '',
            'class'              => '',
            'sticky'             => '',
            'height'             => '',
            'height__sm'         => '',
            'height__md'         => '',
            'container_width'    => '',
            'mob_height'         => '', // Deprecated
            'tablet_height'      => '', // Deprecated
            // Background
            'bg'                 => '',
            'parallax'           => '',
            'parallax_style'     => '',
            'slide_effect'       => '',
            'bg_size'            => 'large',
            'bg_color'           => '',
            'bg_overlay'         => '',
            'bg_overlay__sm'     => '',
            'bg_overlay__md'     => '',
            'bg_pos'             => '',
            'effect'             => '',
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
            // Video
            'video_mp4'          => '',
            'video_ogg'          => '',
            'video_webm'         => '',
            'video_sound'        => 'false',
            'video_loop'         => 'true',
            'youtube'            => '',
            'video_visibility'   => 'hide-for-medium',
            // Border Control
            'border'             => '',
            'border_color'       => '',
            'border_margin'      => '',
            'border_radius'      => '',
            'border_style'       => '',
            'border_hover'       => '',
            // Deprecated text options
            'animation'          => 'fadeIn',
            'animate'            => '',
            'loading'            => '',
            'animated'           => '',
            'animation_duration' => '',
            'text_width'         => '60%',
            'text_align'         => 'center',
            'text_color'         => 'light',
            'text_pos'           => 'center',
            'parallax_text'      => '',
            'text_bg'            => '',
            'padding'            => '',
            // Link
            'link'               => '',
            'link_aria_label'    => '',
            'target'             => '',
            'rel'                => '',
        ], $atts);

        extract($atts);

        // Stop if visibility is hidden
        if ($visibility === 'hidden') return '';

        $classes = ['has-hover'];

        // Custom Class
        if ($class) $classes[] = $class;

        if ($animate) { $animation = $animate; }
        if ($animated) { $animation = $animated; }

        // Hover Class
        if ($hover) $classes[] = 'bg-' . $hover;
        if ($hover_alt) $classes[] = 'bg-' . $hover_alt;

        // Has video
        if ($video_mp4 || $video_webm || $video_ogg) { $classes[] = 'has-video'; }

        // Sticky
        if ($sticky) $classes[] = 'sticky-section';

        // Old bg fallback
        $atts['bg_color'] = $bg_color;
        if (strpos($bg, '#') !== false) {
            $atts['bg_color'] = $bg;
            $bg = false;
        }

        // Mute if video_sound is 0
        if ($video_sound === '0') $video_sound = 'false';

        if ($bg_overlay && strpos($bg_overlay, '#') !== false) {
            $atts['bg_overlay'] = self::hex2rgba($bg_overlay, '0.15');
            $bg_overlay = $atts['bg_overlay'];
        }

        // Full height banner
        if (strpos($height, '100%') !== false) {
            $classes[] = 'is-full-height';
        }

        // Slide Effects
        if ($slide_effect) $classes[] = 'has-slide-effect slide-' . $slide_effect;

        // Visibility
        if ($visibility) $classes[] = $visibility;

        // Links
        $start_link = '';
        $end_link   = '';
        if ($link) {
            $link_attr = 'href="' . esc_url($link) . '" class="fill"';
            if ($link_aria_label) $link_attr .= ' aria-label="' . esc_attr($link_aria_label) . '"';
            if ($target) $link_attr .= ' target="' . esc_attr($target) . '"';
            if ($rel) $link_attr .= ' rel="' . esc_attr($rel) . '"';
            $start_link = '<a ' . $link_attr . '>';
            $end_link   = '</a>';
        }

        // Parallax
        $parallax_attr = '';
        if ($parallax) {
            $classes[] = 'has-parallax';
            $parallax_attr = 'data-parallax="-' . esc_attr($parallax) . '" data-parallax-container=".banner" data-parallax-background';
        }

        $classList = implode(' ', $classes);

        // Build inline style via CSS vars
        $style_parts = [];
        if ($height) {
            $style_parts[] = 'padding-top:' . esc_attr($height);
        }
        if (!empty($atts['bg_color'])) {
            $style_parts[] = 'background-color:' . esc_attr($atts['bg_color']);
        }
        $inline_style = $style_parts ? ' style="' . implode(';', $style_parts) . '"' : '';

        ob_start();
        ?>
        <div class="banner <?php echo esc_attr($classList); ?>" id="<?php echo esc_attr($_id); ?>"<?php echo $inline_style; ?>>
            <?php if ($loading) echo '<div class="loading-spin dark centered"></div>'; ?>
            <div class="banner-inner fill">
                <div class="banner-bg fill" <?php echo $parallax_attr; ?>>
                    <?php
                    // Background image
                    if ($bg) {
                        if (is_numeric($bg)) {
                            $img_src = wp_get_attachment_image_src($bg, $bg_size);
                            if ($img_src) {
                                echo '<img src="' . esc_url($img_src[0]) . '" class="bg-image" alt="' . esc_attr($alt) . '" loading="lazy">';
                            }
                        } else {
                            echo '<img src="' . esc_url($bg) . '" class="bg-image" alt="' . esc_attr($alt) . '" loading="lazy">';
                        }
                    }
                    // Background position
                    if ($bg_pos) {
                        echo '<style>#' . esc_attr($_id) . ' .banner-bg img { object-position: ' . esc_attr($bg_pos) . '; }</style>';
                    }
                    // Video background
                    if ($video_mp4 || $video_webm || $video_ogg) {
                        $muted  = ($video_sound === 'false') ? 'muted' : '';
                        $loop   = ($video_loop === 'true') ? 'loop' : '';
                        $v_class = $video_visibility ? esc_attr($video_visibility) : '';
                        echo '<video class="bg-video ' . $v_class . '" autoplay ' . $muted . ' playsinline ' . $loop . '>';
                        if ($video_mp4)  echo '<source src="' . esc_url($video_mp4)  . '" type="video/mp4">';
                        if ($video_webm) echo '<source src="' . esc_url($video_webm) . '" type="video/webm">';
                        if ($video_ogg)  echo '<source src="' . esc_url($video_ogg)  . '" type="video/ogg">';
                        echo '</video>';
                    }
                    // Overlay
                    if ($bg_overlay) {
                        echo '<div class="overlay" style="background-color:' . esc_attr($bg_overlay) . ';"></div>';
                    }
                    // Border
                    if ($border) {
                        $border_style_inline = '';
                        if ($border_margin) $border_style_inline .= 'margin:' . esc_attr($border_margin) . ';';
                        if ($border_color)  $border_style_inline .= 'border-color:' . esc_attr($border_color) . ';';
                        if ($border_style)  $border_style_inline .= 'border-style:' . esc_attr($border_style) . ';';
                        if ($border_radius) $border_style_inline .= 'border-radius:' . esc_attr($border_radius) . ';';
                        echo '<div class="outline-border border-' . esc_attr($border) . '"' . ($border_style_inline ? ' style="' . $border_style_inline . '"' : '') . '></div>';
                    }
                    // Effects
                    if ($effect) {
                        echo '<div class="effect-' . esc_attr($effect) . ' bg-effect fill no-click"></div>';
                    }
                    ?>
                </div>

                <?php
                // Shape dividers
                if ($divider_top) {
                    self::renderShapeDivider('top', $divider_top, $atts);
                }
                if ($divider) {
                    self::renderShapeDivider('bottom', $divider, $atts);
                }
                ?>

                <div class="banner-layers <?php if ($container_width !== 'full-width') echo 'container'; ?>">
                    <?php echo $start_link; ?><div class="fill banner-link"></div><?php echo $end_link; ?>
                    <?php
                    // Get layers
                    if (has_shortcode($content, 'text_box') || has_shortcode($content, 'ux_hotspot') || has_shortcode($content, 'ux_image')) {
                        echo do_shortcode($content);
                    } else {
                        // Legacy: build a text_box from banner atts
                        $x = '50'; $y = '50';
                        if ($text_pos !== 'center') {
                            $values = explode(' ', $text_pos);
                            if (in_array('left', $values))      { $x = '10'; }
                            if (in_array('right', $values))     { $x = '90'; }
                            if (in_array('far-left', $values))  { $x = '0'; }
                            if (in_array('far-right', $values)) { $x = '100'; }
                            if (in_array('top', $values))       { $y = '10'; }
                            if (in_array('bottom', $values))    { $y = '90'; }
                        }
                        if ($text_bg && !$padding) $padding = '30px 30px 30px 30px';
                        $depth = $text_bg ? '1' : '';

                        // Render via text_box shortcode if registered
                        $attrs_str = 'text_align="' . esc_attr($text_align) . '"'
                            . ' parallax="' . esc_attr($parallax_text) . '"'
                            . ' animate="' . esc_attr($animation) . '"'
                            . ' depth="' . esc_attr($depth) . '"'
                            . ' padding="' . esc_attr($padding) . '"'
                            . ' bg="' . esc_attr($text_bg) . '"'
                            . ' text_color="' . esc_attr($text_color) . '"'
                            . ' width="' . intval($text_width) . '"'
                            . ' width__sm="60%"'
                            . ' position_y="' . esc_attr($y) . '"'
                            . ' position_x="' . esc_attr($x) . '"';
                        echo do_shortcode('[text_box ' . $attrs_str . ']' . $content . '[/text_box]');
                    }
                    ?>
                </div>
            </div>

            <?php
            // Invisible height-fix image when no explicit height set
            if (!$height && $bg) {
                $fix_src = '';
                if (is_numeric($bg)) {
                    $fix_img = wp_get_attachment_image_src($bg, $bg_size);
                    $fix_src = $fix_img ? $fix_img[0] : '';
                } else {
                    $fix_src = $bg;
                }
                if ($fix_src) {
                    echo '<div class="height-fix is-invisible"><img src="' . esc_url($fix_src) . '" alt="' . esc_attr($alt) . '"></div>';
                }
            }
            ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Render shape divider (public alias for cross-class use)
     */
    public static function renderShapeDividerPublic($position, $type, $atts)
    {
        self::renderShapeDivider($position, $type, $atts);
    }

    /**
     * Render shape divider
     */
    protected static function renderShapeDivider($position, $type, $atts)
    {
        $prefix  = ($position === 'top') ? 'divider_top_' : 'divider_';
        $flip    = !empty($atts[$prefix . 'flip']) && $atts[$prefix . 'flip'] === 'true';
        $to_front = !empty($atts[$prefix . 'to_front']) && $atts[$prefix . 'to_front'] === 'true';
        $fill    = !empty($atts[$prefix . 'fill']) ? $atts[$prefix . 'fill'] : '';

        $cls  = 'ux-shape-divider ux-shape-divider--' . $position;
        if ($flip) $cls .= ' is-flipped';
        if ($to_front) $cls .= ' is-front';

        $height  = !empty($atts[$prefix . 'height']) ? $atts[$prefix . 'height'] : '150px';
        $width   = !empty($atts[$prefix . 'width'])  ? $atts[$prefix . 'width']  : '100';

        $fill_style = $fill ? ' fill="' . esc_attr($fill) . '"' : '';
        $svg_style  = 'height:' . esc_attr($height) . ';' . '--divider-width:' . esc_attr($width) . '%;';

        echo '<div class="' . esc_attr($cls) . '">';
        echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none" style="' . esc_attr($svg_style) . '">';
        self::renderDividerShape($type, $fill_style);
        echo '</svg>';
        echo '</div>';
    }

    /**
     * Render inline SVG shape for divider type
     */
    protected static function renderDividerShape($type, $fill_attr)
    {
        $shapes = [
            'curve'       => '<path class="ux-shape-fill"' . $fill_attr . ' d="M0,0 C300,120 900,120 1200,0 L1200,120 L0,120 Z"/>',
            'curve-asym'  => '<path class="ux-shape-fill"' . $fill_attr . ' d="M0,0 C600,120 1000,60 1200,100 L1200,120 L0,120 Z"/>',
            'wave'        => '<path class="ux-shape-fill"' . $fill_attr . ' d="M0,60 C150,100 350,20 500,60 C650,100 850,20 1200,60 L1200,120 L0,120 Z"/>',
            'wave-small'  => '<path class="ux-shape-fill"' . $fill_attr . ' d="M0,80 C200,100 300,60 500,80 C700,100 900,60 1200,80 L1200,120 L0,120 Z"/>',
            'arrow'       => '<path class="ux-shape-fill"' . $fill_attr . ' d="M0,0 L600,120 L1200,0 L1200,120 L0,120 Z"/>',
            'clouds'      => '<path class="ux-shape-fill"' . $fill_attr . ' d="M0,80 C80,60 160,100 240,80 C320,60 400,100 480,80 C560,60 640,100 720,80 C800,60 880,100 960,80 C1040,60 1120,100 1200,80 L1200,120 L0,120 Z"/>',
            'split'       => '<path class="ux-shape-fill"' . $fill_attr . ' d="M0,0 L0,120 L600,60 L1200,120 L1200,0 Z"/>',
            'triangle'    => '<path class="ux-shape-fill"' . $fill_attr . ' d="M0,120 L600,0 L1200,120 Z"/>',
        ];

        echo isset($shapes[$type]) ? $shapes[$type] : '<path class="ux-shape-fill"' . $fill_attr . ' d="M0,0 L1200,0 L1200,120 L0,120 Z"/>';
    }

    /**
     * Convert hex color to rgba
     */
    protected static function hex2rgba($hex, $alpha = '1')
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        return "rgba($r, $g, $b, $alpha)";
    }
}
