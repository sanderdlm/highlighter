<?php

namespace Highlighter\Test;

use Highlighter\Highlighter;
use Highlighter\Token;
use PHPUnit\Framework\TestCase;

class HighlighterTest extends TestCase
{
    public function testHtmlEntitiesInTokenText(): void
    {
        $input = '<?= "Hello world" ?>';

        $tokens = Token::tokenize($input);

        $this->assertCount(5, $tokens);
        $this->assertEquals(390, $tokens[0]->id);
        $this->assertEquals(391, $tokens[4]->id);
        $this->assertEquals('&quot;Hello world&quot;', $tokens[2]->getTextAsSafeHtml());
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

    public function testVegetableExample(): void
    {
        $foo = file_get_contents('tests/vegetable.php');

        $highlighter = new Highlighter();

        $html = $highlighter->render($foo);

        file_put_contents('tests/vegetable.html', $html);

        $this->assertFileExists('tests/vegetable.html');
    }
}
