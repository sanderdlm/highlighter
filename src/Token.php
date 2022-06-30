<?php

namespace Highlighter;

class Token
{
    public function __construct(
        public readonly int $type,
        public readonly string $value
    ) {
    }
}
