<?php
namespace Jankx\Extensions\JankxUX\Tests;

use PHPUnit\Framework\TestCase;
use Jankx\Extensions\JankxUX\Shortcodes\AbstractShortcode;
use Brain\Monkey;

class ShortcodeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Monkey\setUp();
    }

    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }

    public function testGetWrapperClasses()
    {
        $atts = [
            'class' => 'my-custom-class',
            'visibility' => 'hidden',
            'animate' => 'fadeIn'
        ];

        // We need an implementation of AbstractShortcode to test it
        $stub = new class extends AbstractShortcode {
            public static function render($atts, $content = null) { return ''; }
        };

        $result = $stub::getWrapperClasses($atts);

        $this->assertStringContainsString('my-custom-class', $result);
        $this->assertStringContainsString('hidden', $result);
        $this->assertStringContainsString('wow fadeIn', $result);
    }
}
