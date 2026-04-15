<?php
namespace Jankx\Extensions\JankxUX\Tests;

use PHPUnit\Framework\TestCase;
use Jankx\Extensions\JankxUX\Builder\ElementRegistry;

/**
 * Test suite for Element Registry
 */
class ElementRegistryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Reset registry state
        ElementRegistry::clear();
    }

    protected function tearDown(): void
    {
        ElementRegistry::clear();
        parent::tearDown();
    }

    /**
     * Test registering an element
     */
    public function testCanRegisterElement()
    {
        ElementRegistry::register('text', [
            'type' => 'element',
            'name' => 'Text',
            'category' => 'Content'
        ]);

        $this->assertTrue(ElementRegistry::has('text'));
        $this->assertEquals('Text', ElementRegistry::get('text')['name']);
    }

    /**
     * Test getting all elements
     */
    public function testCanGetAllElements()
    {
        ElementRegistry::register('text', ['name' => 'Text']);
        ElementRegistry::register('button', ['name' => 'Button']);
        ElementRegistry::register('row', ['name' => 'Row']);

        $all = ElementRegistry::all();
        
        $this->assertCount(3, $all);
        $this->assertArrayHasKey('text', $all);
        $this->assertArrayHasKey('button', $all);
        $this->assertArrayHasKey('row', $all);
    }

    /**
     * Test element count
     */
    public function testCanGetElementCount()
    {
        $this->assertEquals(0, ElementRegistry::count());

        ElementRegistry::register('text', ['name' => 'Text']);
        $this->assertEquals(1, ElementRegistry::count());

        ElementRegistry::register('button', ['name' => 'Button']);
        $this->assertEquals(2, ElementRegistry::count());
    }

    /**
     * Test getting categorized elements
     */
    public function testCanGetCategorizedElements()
    {
        ElementRegistry::register('text', [
            'name' => 'Text',
            'category' => 'Content'
        ]);
        ElementRegistry::register('button', [
            'name' => 'Button',
            'category' => 'Content'
        ]);
        ElementRegistry::register('row', [
            'name' => 'Row',
            'category' => 'Layout'
        ]);

        $categorized = ElementRegistry::getCategorizedElements();

        $this->assertArrayHasKey('Content', $categorized);
        $this->assertArrayHasKey('Layout', $categorized);
        $this->assertCount(2, $categorized['Content']);
        $this->assertCount(1, $categorized['Layout']);
    }

    /**
     * Test searching elements
     */
    public function testCanSearchElements()
    {
        ElementRegistry::register('text', [
            'name' => 'Text Block',
            'category' => 'Content'
        ]);
        ElementRegistry::register('button', [
            'name' => 'Call to Action',
            'category' => 'Content'
        ]);

        $results = ElementRegistry::search('text');
        
        $this->assertCount(1, $results);
        $this->assertArrayHasKey('text', $results);
    }

    /**
     * Test getting elements by category
     */
    public function testCanGetElementsByCategory()
    {
        ElementRegistry::register('text', [
            'name' => 'Text',
            'category' => 'Content'
        ]);
        ElementRegistry::register('button', [
            'name' => 'Button',
            'category' => 'Content'
        ]);
        ElementRegistry::register('row', [
            'name' => 'Row',
            'category' => 'Layout'
        ]);

        $contentElements = ElementRegistry::getByCategory('Content');
        
        $this->assertCount(2, $contentElements);
        $this->assertArrayHasKey('text', $contentElements);
        $this->assertArrayHasKey('button', $contentElements);
    }
}
