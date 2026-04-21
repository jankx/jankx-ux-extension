<?php
/**
 * Additional Flatsome-compatible Shortcodes for Jankx UX
 * Includes: ux_html, ux_video, divider, title, tab/tabgroup, lightbox, scroll_to, message_box, search
 */

// =============================================
// [ux_html] - Raw HTML block with optional class/visibility
// =============================================
if (!function_exists('jankx_ux_html_shortcode')) {
    function jankx_ux_html_shortcode($atts, $content = '', $tag = 'ux_html')
    {
        $atts = shortcode_atts([
            'visibility' => '',
            'class'      => '',
            'label'      => '',
        ], $atts, $tag);

        if ($atts['visibility'] === 'hidden') return '';

        $classes = [];
        if (!empty($atts['class']))      $classes[] = $atts['class'];
        if (!empty($atts['visibility'])) $classes[] = $atts['visibility'];

        if (empty($classes)) {
            return do_shortcode($content);
        }

        return '<div class="' . esc_attr(implode(' ', $classes)) . '">' . do_shortcode($content) . '</div>';
    }
    add_shortcode('ux_html', 'jankx_ux_html_shortcode');
}

// =============================================
// [ux_video] - Video with aspect ratio wrapper
// =============================================
if (!function_exists('jankx_ux_video_shortcode')) {
    function jankx_ux_video_shortcode($atts)
    {
        extract(shortcode_atts([
            'class'       => '',
            'visibility'  => '',
            'url'         => 'https://www.youtube.com/watch?v=AoPiLg8DZ3A',
            'height'      => '56.25%',
            'depth'       => '',
            'depth_hover' => '',
        ], $atts));

        $classes = ['video', 'video-fit', 'mb'];
        if ($class)       $classes[] = $class;
        if ($visibility)  $classes[] = $visibility;
        if ($depth)       $classes[] = 'box-shadow-' . $depth;
        if ($depth_hover) $classes[] = 'box-shadow-' . $depth_hover . '-hover';

        $video = apply_filters('the_content', esc_url($url));
        $pad_style = $height ? ' style="padding-top:' . esc_attr($height) . '"' : '';

        return '<div class="' . esc_attr(implode(' ', $classes)) . '"' . $pad_style . '>' . $video . '</div>';
    }
    add_shortcode('ux_video', 'jankx_ux_video_shortcode');
}

// =============================================
// [divider] - Horizontal divider line
// =============================================
if (!function_exists('jankx_divider_shortcode')) {
    function jankx_divider_shortcode($atts)
    {
        extract(shortcode_atts([
            'width'  => '',
            'height' => '',
            'margin' => '',
            'align'  => '',
            'color'  => '',
        ], $atts));

        if ($width === 'full') $width = '100%';

        $style_parts = [];
        if ($margin) { $style_parts[] = 'margin-top:' . esc_attr($margin); $style_parts[] = 'margin-bottom:' . esc_attr($margin); }
        if ($width)  $style_parts[] = 'max-width:' . esc_attr($width);
        if ($height) $style_parts[] = 'height:' . esc_attr($height);
        if ($color)  $style_parts[] = 'background-color:' . esc_attr($color);
        $style = $style_parts ? ' style="' . implode(';', $style_parts) . '"' : '';

        $wrap_start = $wrap_end = '';
        if ($align === 'center') { $wrap_start = '<div class="text-center">'; $wrap_end = '</div>'; }
        if ($align === 'right')  { $wrap_start = '<div class="text-right">';  $wrap_end = '</div>'; }

        return $wrap_start . '<div class="is-divider divider clearfix"' . $style . '></div>' . $wrap_end;
    }
    add_shortcode('divider', 'jankx_divider_shortcode');
}

// =============================================
// [title] - Section title with decorators
// =============================================
if (!function_exists('jankx_title_shortcode')) {
    function jankx_title_shortcode($atts)
    {
        extract(shortcode_atts([
            '_id'           => 'title-' . rand(),
            'text'          => '',
            'tag_name'      => 'h3',
            'style'         => 'normal',
            'size'          => '100',
            'margin_top'    => '',
            'margin_bottom' => '',
            'color'         => '',
            'width'         => '',
            'icon'          => '',
            'link_text'     => '',
            'link'          => '',
            'target'        => '',
            'rel'           => '',
            'class'         => '',
            'visibility'    => '',
            'sub_text'      => '',
        ], $atts));

        // Sanitize tag
        if (!preg_match('/^h[1-6]$/', trim($tag_name))) $tag_name = 'h3';
        if ($style === 'bold_center') $style = 'bold-center';

        $classes = ['container', 'section-title-container'];
        if ($class)      $classes[] = $class;
        if ($visibility) $classes[] = $visibility;

        $wrapper_style_parts = [];
        if ($margin_top)    $wrapper_style_parts[] = 'margin-top:'    . esc_attr($margin_top);
        if ($margin_bottom) $wrapper_style_parts[] = 'margin-bottom:' . esc_attr($margin_bottom);
        if ($width)         $wrapper_style_parts[] = 'max-width:'     . esc_attr($width);
        $wrapper_style = $wrapper_style_parts ? ' style="' . implode(';', $wrapper_style_parts) . '"' : '';

        $title_style_parts = [];
        if ($size !== '100') $title_style_parts[] = 'font-size:' . esc_attr($size) . '%';
        if ($color)          $title_style_parts[] = 'color:' . esc_attr($color);
        $title_style = $title_style_parts ? ' style="' . implode(';', $title_style_parts) . '"' : '';

        $icon_html   = $icon ? '<i class="' . esc_attr($icon) . '"></i>' : '';
        $sub_html    = $sub_text ? '<small class="sub-title">' . wp_kses_post($sub_text) . '</small>' : '';
        $link_html   = '';
        if ($link_text) {
            $link_html = '<a href="' . esc_url($link) . '"'
                . ($target ? ' target="' . esc_attr($target) . '"' : '')
                . ($rel    ? ' rel="' . esc_attr($rel) . '"' : '')
                . '>' . wp_kses_post($link_text) . '</a>';
        }

        return '<div class="' . esc_attr(implode(' ', $classes)) . '"' . $wrapper_style . '>'
            . '<' . $tag_name . ' class="section-title section-title-' . esc_attr($style) . '">'
            . '<b aria-hidden="true"></b>'
            . '<span class="section-title-main"' . $title_style . '>' . $icon_html . wp_kses_post($text) . $sub_html . '</span>'
            . '<b aria-hidden="true"></b>'
            . $link_html
            . '</' . $tag_name . '>'
            . '</div>';
    }
    add_shortcode('title', 'jankx_title_shortcode');
}

// =============================================
// [tabgroup] / [tabgroup_vertical] / [tab]
// =============================================
if (!function_exists('jankx_tabgroup_shortcode')) {
    function jankx_tabgroup_shortcode($params, $content = null, $tag = '')
    {
        $GLOBALS['jankx_tabs']       = [];
        $GLOBALS['jankx_tab_count']  = 0;

        extract(shortcode_atts([
            'id'         => 'panel-' . rand(),
            'title'      => '',
            'style'      => 'line',
            'align'      => 'left',
            'class'      => '',
            'visibility' => '',
            'type'       => '',
            'nav_style'  => 'uppercase',
            'nav_size'   => 'normal',
            'history'    => 'false',
            'event'      => '',
        ], $params));

        if ($tag === 'tabgroup_vertical') $type = 'vertical';

        do_shortcode($content); // Execute [tab] shortcodes to populate $GLOBALS['jankx_tabs']

        $wrapper_classes = ['tabbed-content'];
        if ($class)      $wrapper_classes[] = $class;
        if ($visibility) $wrapper_classes[] = $visibility;

        $nav_classes = ['nav'];
        if ($style)                $nav_classes[] = 'nav-' . $style;
        if ($type === 'vertical')  $nav_classes[] = 'nav-vertical';
        if ($nav_style)            $nav_classes[] = 'nav-' . $nav_style;
        if ($nav_size)             $nav_classes[] = 'nav-size-' . $nav_size;
        if ($align)                $nav_classes[] = 'nav-' . $align;
        if ($event)                $nav_classes[] = 'active-on-' . $event;

        $tabs  = [];
        $panes = [];

        foreach ($GLOBALS['jankx_tabs'] as $key => $tab) {
            $tab_id  = $tab['title'] ? sanitize_title($tab['title']) : wp_rand();
            $anchor  = !empty($tab['anchor']) ? rawurlencode($tab['anchor']) : 'tab_' . $tab_id;
            $active  = ($key === 0) ? ' active' : '';
            $tabs[]  = '<li id="tab-' . $tab_id . '" class="tab' . $active . ' has-icon" role="presentation">'
                . '<a href="#' . $anchor . '"' . ($key !== 0 ? ' tabindex="-1"' : '') . ' role="tab" aria-selected="' . ($key === 0 ? 'true' : 'false') . '" aria-controls="tab_' . $tab_id . '">'
                . '<span>' . wp_kses_post($tab['title']) . '</span></a></li>';
            $panes[] = '<div id="tab_' . $tab_id . '" class="panel' . $active . ' entry-content" role="tabpanel" aria-labelledby="tab-' . $tab_id . '">'
                . do_shortcode($tab['content']) . '</div>';
        }

        $title_html = $title ? '<h4 class="uppercase text-' . esc_attr($align) . '">' . wp_kses_post($title) . '</h4>' : '';

        return '<div class="' . esc_attr(implode(' ', $wrapper_classes)) . '">'
            . $title_html
            . '<ul class="' . esc_attr(implode(' ', $nav_classes)) . '" role="tablist">' . implode("\n", $tabs) . '</ul>'
            . '<div class="tab-panels">' . implode("\n", $panes) . '</div>'
            . '</div>';
    }
    add_shortcode('tabgroup', 'jankx_tabgroup_shortcode');
    add_shortcode('tabgroup_vertical', 'jankx_tabgroup_shortcode');
}

if (!function_exists('jankx_tab_shortcode')) {
    function jankx_tab_shortcode($params, $content = null)
    {
        extract(shortcode_atts([
            'title'  => '',
            'anchor' => '',
        ], $params));

        $x = $GLOBALS['jankx_tab_count'] ?? 0;
        $GLOBALS['jankx_tabs'][$x]      = ['title' => $title, 'anchor' => $anchor, 'content' => $content];
        $GLOBALS['jankx_tab_count']     = $x + 1;
        return '';
    }
    add_shortcode('tab', 'jankx_tab_shortcode');
}

// =============================================
// [scroll_to] - Smooth scroll anchor
// =============================================
if (!function_exists('jankx_scroll_to_shortcode')) {
    function jankx_scroll_to_shortcode($atts, $content = '')
    {
        extract(shortcode_atts([
            'id'         => '',
            'text'       => '',
            'icon'       => 'icon-angle-down',
            'class'      => 'scroll-to',
            'visibility' => '',
        ], $atts));

        $classes = [$class];
        if ($visibility) $classes[] = $visibility;
        $href = $id ? '#' . esc_attr($id) : '#';

        return '<a href="' . $href . '" class="' . esc_attr(implode(' ', $classes)) . '">'
            . ($text ? '<span>' . esc_html($text) . '</span>' : '')
            . ($icon ? '<i class="' . esc_attr($icon) . '"></i>' : '')
            . '</a>';
    }
    add_shortcode('scroll_to', 'jankx_scroll_to_shortcode');
}

// =============================================
// [message_box] - Alert/notice message box
// =============================================
if (!function_exists('jankx_message_box_shortcode')) {
    function jankx_message_box_shortcode($atts, $content = '')
    {
        extract(shortcode_atts([
            'type'       => 'info',   // info, success, warning, error
            'class'      => '',
            'visibility' => '',
            'dismissable'=> '',
            'icon'       => '',
        ], $atts));

        $classes = ['message-box', 'message-' . esc_attr($type)];
        if ($class)       $classes[] = $class;
        if ($visibility)  $classes[] = $visibility;
        if ($dismissable) $classes[] = 'is-dismissable';

        $icon_html = $icon ? '<i class="' . esc_attr($icon) . '"></i>' : '';
        $dismiss   = $dismissable ? '<button class="close" aria-label="' . esc_attr__('Dismiss', 'jankx') . '">×</button>' : '';

        return '<div class="' . esc_attr(implode(' ', $classes)) . '">'
            . $icon_html
            . '<div class="message-box-content">' . do_shortcode($content) . '</div>'
            . $dismiss
            . '</div>';
    }
    add_shortcode('message_box', 'jankx_message_box_shortcode');
}

// =============================================
// [search] - Search form
// =============================================
if (!function_exists('jankx_search_shortcode')) {
    function jankx_search_shortcode($atts)
    {
        extract(shortcode_atts([
            'placeholder' => '',
            'class'       => '',
            'visibility'  => '',
        ], $atts));

        $classes = ['search-form-shortcode'];
        if ($class)      $classes[] = $class;
        if ($visibility) $classes[] = $visibility;

        $placeholder = $placeholder ?: __('Search...', 'jankx');

        return '<form role="search" method="get" class="' . esc_attr(implode(' ', $classes)) . '" action="' . esc_url(home_url('/')) . '">'
            . '<div class="flex-row">'
            . '<div class="flex-col flex-grow">'
            . '<input type="search" class="search-field" placeholder="' . esc_attr($placeholder) . '" value="' . esc_attr(get_search_query()) . '" name="s">'
            . '</div>'
            . '<div class="flex-col">'
            . '<button type="submit" class="button icon"><i class="icon-search"></i></button>'
            . '</div>'
            . '</div>'
            . '</form>';
    }
    add_shortcode('search', 'jankx_search_shortcode');
}

// =============================================
// [lightbox] - Lightbox trigger wrapper
// =============================================
if (!function_exists('jankx_lightbox_shortcode')) {
    function jankx_lightbox_shortcode($atts, $content = '')
    {
        extract(shortcode_atts([
            'src'        => '',
            'alt'        => '',
            'title'      => '',
            'class'      => '',
            'visibility' => '',
        ], $atts));

        $classes = ['lightbox-trigger'];
        if ($class)      $classes[] = $class;
        if ($visibility) $classes[] = $visibility;

        if ($src) {
            return '<a href="' . esc_url($src) . '" class="image-lightbox ' . esc_attr(implode(' ', $classes)) . '" title="' . esc_attr($title) . '">'
                . do_shortcode($content)
                . '</a>';
        }

        return '<div class="' . esc_attr(implode(' ', $classes)) . '">' . do_shortcode($content) . '</div>';
    }
    add_shortcode('lightbox', 'jankx_lightbox_shortcode');
}

// =============================================
// [ux_sidebar] - Widget sidebar shortcode
// =============================================
if (!function_exists('jankx_ux_sidebar_shortcode')) {
    function jankx_ux_sidebar_shortcode($atts)
    {
        extract(shortcode_atts([
            'id'    => 'sidebar-1',
            'class' => '',
        ], $atts));

        if (!is_active_sidebar($id)) return '';

        $classes = ['shortcode-sidebar'];
        if ($class) $classes[] = $class;

        ob_start();
        echo '<aside class="' . esc_attr(implode(' ', $classes)) . '">';
        dynamic_sidebar($id);
        echo '</aside>';
        return ob_get_clean();
    }
    add_shortcode('ux_sidebar', 'jankx_ux_sidebar_shortcode');
}

// =============================================
// [logo] - Logo shortcode
// =============================================
if (!function_exists('jankx_logo_shortcode')) {
    function jankx_logo_shortcode($atts)
    {
        extract(shortcode_atts([
            'class'      => '',
            'width'      => '',
            'visibility' => '',
        ], $atts));

        $classes = ['logo-shortcode'];
        if ($class)      $classes[] = $class;
        if ($visibility) $classes[] = $visibility;

        $style = $width ? ' style="max-width:' . esc_attr($width) . '"' : '';

        $logo_html = get_custom_logo() ?: '<span class="site-title"><a href="' . esc_url(home_url('/')) . '">' . get_bloginfo('name') . '</a></span>';

        return '<div class="' . esc_attr(implode(' ', $classes)) . '"' . $style . '>' . $logo_html . '</div>';
    }
    add_shortcode('logo', 'jankx_logo_shortcode');
}

// =============================================
// [ux_accordion] / [accordion-item]
// =============================================
if (!function_exists('jankx_accordion_shortcode')) {
    function jankx_accordion_shortcode($atts, $content = '')
    {
        extract(shortcode_atts([
            'id'         => 'accordion-' . rand(),
            'style'      => '',
            'class'      => '',
            'visibility' => '',
        ], $atts));

        if ($visibility === 'hidden') return '';

        $classes = ['accordion'];
        if ($style)      $classes[] = 'accordion-' . $style;
        if ($class)      $classes[] = $class;
        if ($visibility) $classes[] = $visibility;

        return '<div id="' . esc_attr($id) . '" class="' . esc_attr(implode(' ', $classes)) . '">'
            . do_shortcode($content)
            . '</div>';
    }
    add_shortcode('accordion', 'jankx_accordion_shortcode');
}

if (!function_exists('jankx_accordion_item_shortcode')) {
    function jankx_accordion_item_shortcode($atts, $content = '')
    {
        extract(shortcode_atts([
            'title'  => '',
            'open'   => 'false',
            'icon'   => '',
            'class'  => '',
        ], $atts));

        $open_class  = ($open === 'true') ? ' is-active' : '';
        $icon_html   = $icon ? '<i class="' . esc_attr($icon) . '"></i>' : '';
        $classes     = ['accordion-item' . $open_class];
        if ($class) $classes[] = $class;

        return '<div class="' . esc_attr(implode(' ', $classes)) . '">'
            . '<div class="accordion-title" role="button" tabindex="0">' . $icon_html . wp_kses_post($title) . '</div>'
            . '<div class="accordion-content">' . do_shortcode($content) . '</div>'
            . '</div>';
    }
    add_shortcode('accordion-item', 'jankx_accordion_item_shortcode');
}

// =============================================
// [share] - Social share buttons
// =============================================
if (!function_exists('jankx_share_shortcode')) {
    function jankx_share_shortcode($atts) {
        extract(shortcode_atts([
            'title' => '',
            'class' => '',
            'style' => 'outline', // plain, small, outline, fill
        ], $atts));

        $url = get_permalink();
        $title_text = get_the_title();

        $classes = ['social-icons', 'share-icons', 'relative'];
        if ($class) $classes[] = $class;
        if ($style) $classes[] = 'icon-style-' . $style;

        ob_start();
        ?>
        <div class="<?php echo esc_attr(implode(' ', $classes)); ?>">
            <?php if ($title) : ?><span class="share-title uppercase"><?php echo esc_html($title); ?></span><?php endif; ?>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $url; ?>" target="_blank" class="icon button circle is-outline facebook" title="Share on Facebook"><i class="icon-facebook"></i></a>
            <a href="https://twitter.com/share?url=<?php echo $url; ?>&text=<?php echo $title_text; ?>" target="_blank" class="icon button circle is-outline twitter" title="Share on Twitter"><i class="icon-twitter"></i></a>
            <a href="mailto:?subject=<?php echo $title_text; ?>&body=<?php echo $url; ?>" class="icon button circle is-outline email" title="Email to a Friend"><i class="icon-envelop"></i></a>
            <a href="https://pinterest.com/pin/create/button/?url=<?php echo $url; ?>&description=<?php echo $title_text; ?>" target="_blank" class="icon button circle is-outline pinterest" title="Pin on Pinterest"><i class="icon-pinterest"></i></a>
        </div>
        <?php
        return ob_get_clean();
    }
    add_shortcode('share', 'jankx_share_shortcode');
}

// =============================================
// [follow] - Social follow buttons
// =============================================
if (!function_exists('jankx_follow_shortcode')) {
    function jankx_follow_shortcode($atts) {
        $atts = shortcode_atts([
            'facebook' => '', 'twitter' => '', 'instagram' => '', 'youtube' => '', 'pinterest' => '', 'email' => '',
            'class' => '', 'style' => 'outline', 'size' => '',
        ], $atts);

        $classes = ['social-icons', 'follow-icons'];
        if ($atts['class']) $classes[] = $atts['class'];
        if ($atts['style']) $classes[] = 'icon-style-' . $atts['style'];
        if ($atts['size']) $classes[] = 'is-' . $atts['size'];

        $output = '<div class="' . esc_attr(implode(' ', $classes)) . '">';
        foreach (['facebook', 'twitter', 'instagram', 'youtube', 'pinterest', 'email'] as $network) {
            if ($atts[$network]) {
                $icon = ($network === 'email') ? 'icon-envelop' : 'icon-' . $network;
                $output .= '<a href="' . esc_url($atts[$network]) . '" target="_blank" class="icon button circle is-outline ' . $network . '"><i class="'. $icon .'"></i></a>';
            }
        }
        $output .= '</div>';
        return $output;
    }
    add_shortcode('follow', 'jankx_follow_shortcode');
}

// =============================================
// [testimonial] - Testimonial block
// =============================================
if (!function_exists('jankx_testimonial_shortcode')) {
    function jankx_testimonial_shortcode($atts, $content = '') {
        extract(shortcode_atts([
            'name' => '', 'title' => '', 'image' => '', 'image_width' => '60', 'stars' => '5',
        ], $atts));

        $stars_html = str_repeat('<i class="icon-star"></i>', intval($stars));

        return '<div class="testimonial-box flex-row align-top">'
            . '<div class="flex-col post-item-image">'
            . ($image ? '<div class="testimonial-image circle" style="width:'.intval($image_width).'px">'.wp_get_attachment_image($image, 'thumbnail').'</div>' : '')
            . '</div>'
            . '<div class="flex-col flex-grow">'
            . '<div class="testimonial-text mb-half">' . do_shortcode($content) . '</div>'
            . '<div class="testimonial-meta uppercase"><strong>' . esc_html($name) . '</strong>' . ($title ? ' / <span>' . esc_html($title) . '</span>' : '') . '</div>'
            . '<div class="star-rating">' . $stars_html . '</div>'
            . '</div>'
            . '</div>';
    }
    add_shortcode('testimonial', 'jankx_testimonial_shortcode');
}

// =============================================
// [ux_countdown] - Countdown timer
// =============================================
if (!function_exists('jankx_ux_countdown_shortcode')) {
    function jankx_ux_countdown_shortcode($atts) {
        $atts = shortcode_atts(['year' => '2025', 'month' => '12', 'day' => '31', 'time' => '00:00', 'class' => ''], $atts);
        $date = "{$atts['year']}-{$atts['month']}-{$atts['day']} {$atts['time']}";
        return '<div class="ux-countdown ' . esc_attr($atts['class']) . '" data-date="' . esc_attr($date) . '">'
            . '<div class="ux-countdown-inner flex-row">Phần này yêu cầu JS của Flatsome để chạy...</div>'
            . '</div>';
    }
    add_shortcode('ux_countdown', 'jankx_ux_countdown_shortcode');
}

// =============================================
// [ux_current_year] - Current year utility
// =============================================
add_shortcode('ux_current_year', function() { return date('Y'); });

// =============================================
// [site_url] - Site URL utility
// =============================================
add_shortcode('site_url', function() { return site_url(); });
add_shortcode('site_url_secure', function() { return site_url('', 'https'); });

// =============================================
// [ux_menu] - Custom WP Menu shortcode
// =============================================
if (!function_exists('jankx_ux_menu_shortcode')) {
    function jankx_ux_menu_shortcode($atts) {
        extract(shortcode_atts([
            'nav_id'     => '',
            'class'      => '',
            'divider'    => '', // line, pipe
            'align'      => 'left',
            'icon_size'  => '16',
        ], $atts));

        if (empty($nav_id)) return '';

        $classes = ['ux-menu', 'nav'];
        if ($class)   $classes[] = $class;
        if ($divider) $classes[] = 'nav-divider-' . $divider;
        if ($align)   $classes[] = 'nav-' . $align;

        return wp_nav_menu([
            'menu'        => $nav_id,
            'container'   => false,
            'menu_class'  => implode(' ', $classes),
            'echo'        => false,
            'fallback_cb' => false,
        ]);
    }
    add_shortcode('ux_menu', 'jankx_ux_menu_shortcode');
}
