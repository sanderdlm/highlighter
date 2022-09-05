<?php

declare(strict_types=1);

namespace Highlighter;

use RuntimeException;

final class Highlighter
{
    public const TOKEN_MAP = [
        'identifier' => [
            T_STRING, // identifiers, e.g. keywords like parent and self, function names, class names and more
            T_CALLABLE, // any callable,
            T_NAME_FULLY_QUALIFIED,
            T_NAME_QUALIFIED,
            T_NAME_RELATIVE,
        ],
        'keyword' => [
            T_IF,
            T_ELSEIF,
            T_ELSE,
            T_ENDIF,
            T_DO,
            T_WHILE,
            T_ENDWHILE,
            T_FOR,
            T_ENDFOR,
            T_FOREACH,
            T_ENDFOREACH,
            T_SWITCH,
            T_ENDSWITCH,
            T_CASE,
            T_CONTINUE,
            T_BREAK,
            T_GOTO,
            T_TRY,
            T_CATCH,
            T_FINALLY,
            T_REQUIRE,
            T_REQUIRE_ONCE,
            T_INCLUDE,
            T_INCLUDE_ONCE,
            T_INSTANCEOF,
            T_CLONE,
            T_NEW,
            T_EXIT,
            T_DEFAULT,
            T_YIELD,
            T_YIELD_FROM,
            T_THROW,
            T_GLOBAL,
            T_UNSET,
            T_ISSET,
            T_EMPTY,
            T_PRINT,
            T_ECHO,
            T_PUBLIC,
            T_PROTECTED,
            T_PRIVATE,
            T_STATIC,
            T_ABSTRACT,
            T_FINAL,
            T_CLASS,
            T_INTERFACE,
            T_TRAIT,
            T_FUNCTION,
            T_USE,
            T_NAMESPACE,
            T_EXTENDS,
            T_IMPLEMENTS,
            T_LIST,
            T_INSTEADOF,
            T_AS,
            T_VAR,
            T_CONST,
            T_RETURN,
            T_OPEN_TAG,
            T_OPEN_TAG_WITH_ECHO,
            T_CLOSE_TAG,
            T_EVAL,
            T_DECLARE,
            T_ENDDECLARE,
            T_MATCH,
            T_FN,
            T_READONLY,
            T_HALT_COMPILER,
            T_ENUM,
            T_ARRAY,
        ],
        'cast' => [
            T_UNSET_CAST,
            T_BOOL_CAST,
            T_OBJECT_CAST,
            T_ARRAY_CAST,
            T_STRING_CAST,
            T_DOUBLE_CAST,
            T_INT_CAST,
        ],
        'string' => [
            T_CONSTANT_ENCAPSED_STRING,
            T_ENCAPSED_AND_WHITESPACE,
            T_INLINE_HTML,
            T_START_HEREDOC,
            T_END_HEREDOC,
            T_LNUMBER,
            T_DNUMBER,
            T_NS_SEPARATOR,
            T_ATTRIBUTE,
        ],
        'comment' => [
            T_COMMENT,
            T_DOC_COMMENT,
        ],
        'variable' => [
            T_VARIABLE,
            T_STRING_VARNAME,
            T_NUM_STRING,
            T_DOLLAR_OPEN_CURLY_BRACES,
            T_CURLY_OPEN,
        ],
        'operator' => [
            T_BOOLEAN_AND,
            T_BOOLEAN_OR,
            T_LOGICAL_OR,
            T_LOGICAL_AND,
            T_LOGICAL_XOR,
            T_PLUS_EQUAL,
            T_MINUS_EQUAL,
            T_MUL_EQUAL,
            T_DIV_EQUAL,
            T_CONCAT_EQUAL,
            T_MOD_EQUAL,
            T_AND_EQUAL,
            T_OR_EQUAL,
            T_XOR_EQUAL,
            T_SL_EQUAL,
            T_SR_EQUAL,
            T_COALESCE_EQUAL,
            T_IS_EQUAL,
            T_IS_NOT_EQUAL,
            T_IS_IDENTICAL,
            T_IS_NOT_IDENTICAL,
            T_IS_SMALLER_OR_EQUAL,
            T_IS_GREATER_OR_EQUAL,
            T_SPACESHIP,
            T_SL,
            T_SR,
            T_INC,
            T_DEC,
            T_OBJECT_OPERATOR,
            T_NULLSAFE_OBJECT_OPERATOR,
            T_DOUBLE_ARROW,
            T_DOUBLE_COLON,
            T_ELLIPSIS,
            T_COALESCE,
            T_POW,
            T_POW_EQUAL,
            T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG,
            T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG,
        ],
        'magic' => [
            T_DIR,
            T_FILE,
            T_LINE,
            T_CLASS_C,
            T_TRAIT_C,
            T_METHOD_C,
            T_FUNC_C,
            T_NS_C,
            T_WHITESPACE,
            T_BAD_CHARACTER,
        ],
    ];

    public const STYLE_MAP = [
        'keyword' => 'color: #3A97D4; font-weight: bold;',
        'cast' => 'color: #333',
        'string' => 'color: #333',
        'operator' => 'color: #666',
        'comment' => 'color: #888; font-style: italic;',
        'variable' => 'color: #09814A',
        'magic' => 'color: #09814A; font-weight: bold;',
        'identifier' => 'color: #D87A1A',
    ];

    /** @var array<string, string> */
    public readonly array $styleMap;
    /** @var array<string, array<int>> */
    public readonly array $tokenMap;

    /**
     * @param array<string, string>|null $styleMap
     * @param array<string, array<int>>|null $tokenMap
     */
    public function __construct(?array $styleMap = null, ?array $tokenMap = null)
    {
        $this->styleMap = $styleMap ?? self::STYLE_MAP;
        $this->tokenMap = $tokenMap ?? self::TOKEN_MAP;

        $this->validateMaps();
    }

    public function render(string $input): bool|string
    {
        $tokens = Token::tokenize($input);

        ob_start();

        echo '<pre><code>';
        foreach ($tokens as $token) {
            echo sprintf(
                '<span style="%s">%s</span>',
                self::getStyle($token->id),
                $token->getTextAsSafeHtml()
            );
        }
        echo '</code></pre>';

        return ob_get_clean();
    }

    private function getStyle(int $tokenId): string
    {
        foreach ($this->tokenMap as $group => $tokens) {
            if (in_array($tokenId, $tokens)) {
                return $this->styleMap[$group];
            }
        }

        return 'color: #333;';
    }

    private function validateMaps(): void
    {
        $missingKeys = [];

        foreach ($this->tokenMap as $group => $tokens) {
            if (!array_key_exists($group, $this->styleMap)) {
                $missingKeys[] = $group;
            }
        }

        if (count($missingKeys) > 0) {
            throw new RuntimeException(
                'Your style map is missing the following keys: '
                . implode(', ', $missingKeys)
            );
        }
    }
}
