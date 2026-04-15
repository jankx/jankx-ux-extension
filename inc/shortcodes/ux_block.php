<?php
/**
 * Shortcode: [ux_block] and [block]
 * Parity with Flatsome's block shortcode.
 */

function jux_block_shortcode($atts, $content = null) {
    extract(shortcode_atts(array(
        'id' => '',
    ), $atts));

    if (empty($id)) {
        return '<!-- JUX: No block ID set -->';
    }

    $block_id = flatsome_get_block_id($id);
    $block = get_post($block_id);

    if (!$block || !in_array($block->post_type, array('ux_block', 'blocks'))) {
        return sprintf('<!-- JUX: Block "%s" not found -->', esc_html($id));
    }

    $content = $block->post_content;

    return sprintf(
        '<div class="jux-block-container" data-block-id="%s">%s</div>',
        esc_attr($id),
        do_shortcode($content)
    );
}
add_shortcode('ux_block', 'jux_block_shortcode');
add_shortcode('block', 'jux_block_shortcode');
