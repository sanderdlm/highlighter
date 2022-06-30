<?php

namespace Highlighter\Test;

use Highlighter\Highlighter;
use PHPUnit\Framework\TestCase;

class HighlighterTest extends TestCase
{
    public function testParse(): void
    {
        $foo = '<?= "Hello world" ?>';

        $highlighter = new Highlighter();

        $parsedTree = $highlighter->parse($foo);

        $this->assertCount(5, $parsedTree);
        $this->assertEquals(390, $parsedTree[0]->type);
        $this->assertEquals(391, $parsedTree[4]->type);
        $this->assertEquals('&quot;Hello world&quot;', $parsedTree[2]->value);
    }

    public function testRender(): void
    {
        $foo = '<?= // This is just a comment! ?>';

        $highlighter = new Highlighter([
            'keyword' => 'color: #3A97D4; font-weight: bold;',
            'cast' => 'color: #333',
            'string' => 'color: #333',
            'operator' => 'color: #666',
            'comment' => 'color: pink;',
            'variable' => 'color: #09814A',
            'magic' => 'color: #09814A; font-weight: bold;',
            'identifier' => 'color: #D87A1A',
        ]);

        $highlightedHtml = $highlighter->render($foo);

        $this->assertStringStartsWith('<pre><code>', $highlightedHtml);
        $this->assertStringEndsWith('</code></pre>', $highlightedHtml);
        $this->assertStringContainsString('color: pink;', $highlightedHtml);
    }
}
