<?php

declare(strict_types=1);

namespace Highlighter;

use PhpToken;

class Token extends PhpToken
{
    public function getTextAsSafeHtml()
    {
        /*
         * The usage of htmlspecialchars here is necessary to
         * preserve characters like quotes when the user will
         * display the generated HTML.
         *
         * Replacing PHP_EOL with HTML line breaks (br tag)
         * makes the HTML output more robust and avoids shifting.
         */
        return str_replace(PHP_EOL, '<br>', htmlspecialchars($this->text));
    }
}
