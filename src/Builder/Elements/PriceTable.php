<?php
namespace Jankx\Extensions\JankxUX\Builder\Elements;

/**
 * Price Table - [ux_price_table] shortcode
 */
class PriceTable extends AbstractElement
{
    protected static $tag = 'ux_price_table';

    public static function getConfig()
    {
        return [
            'type'        => 'container',
            'name'        => 'Price Table',
            'title'       => __('Price Table', 'jankx'),
            'category'    => __('Content', 'jankx'),
            'description' => __('A table for displaying prices.', 'jankx'),
            'wrap'        => true,
            'options'     => [],
        ];
    }

    public static function render($atts = [], $content = '')
    {
        $atts = shortcode_atts([
            'title'      => 'Service Name',
            'price'      => '$99',
            'description'=> 'Per Month',
            'featured'   => 'false',
            'button_text'=> 'Get Started',
            'button_link'=> '',
            'class'      => '',
        ], $atts);

        extract($atts);

        $classes = ['pricing-table'];
        if ($featured === 'true') $classes[] = 'featured';
        if ($class)               $classes[] = $class;

        ob_start();
        ?>
        <div class="<?php echo esc_attr(implode(' ', $classes)); ?>">
            <div class="pricing-table-inner box-shadow-1 box-shadow-2-hover">
                <div class="pricing-table-header uppercase">
                    <h5 class="mb-0"><?php echo esc_html($title); ?></h5>
                </div>
                <div class="pricing-table-price">
                    <span class="price"><?php echo esc_html($price); ?></span>
                    <span class="description is-xsmall"><?php echo esc_html($description); ?></span>
                </div>
                <div class="pricing-table-content">
                    <?php echo do_shortcode($content); ?>
                </div>
                <?php if ($button_text) : ?>
                <div class="pricing-table-footer">
                    <?php echo Button::render(['text' => $button_text, 'link' => $button_link, 'expand' => 'true']); ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
