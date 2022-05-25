<?php
namespace Affinity4\Tokenizer\Test;

use Affinity4\Tokenizer\Token;
use PHPUnit\Framework\TestCase;
use Affinity4\Tokenizer\Tokenizer;

/**
 * @covers \Affinity4\Tokenizer\Token
 * @uses \Affinity4\Tokenizer\Tokenizer
 */
class TokenTest extends TestCase
{
    /*
    | --------------------------------------------------------------------------------
    | LEXICON CONSTANTS TESTS
    | --------------------------------------------------------------------------------
    */

    /**
     * Lexicon Constants
     *
     * @return array
     */
    private function lexiconConstants(): array
    {
        return [
            [Token::T_ESCAPE_CHAR, 'T_ESCAPE_CHAR', '\\\\', '\\'],
            [Token::T_NEWLINE_UNIX, 'T_NEWLINE_UNIX', '\n', "\n"],
            [Token::T_NEWLINE_WIN, 'T_NEWLINE_WIN', '\r\n', "\r\n"],
            [Token::T_NEWLINE_MAC, 'T_NEWLINE_MAC', '\r', "\r"],
            [Token::T_CARRIAGE_RETURN, 'T_CARRIAGE_RETURN', '\r', "\r"],
            [Token::T_RETURN, 'T_RETURN', '\r', "\r"],
            [Token::T_NEWLINE_ALL, 'T_NEWLINE_ALL', '\n|\r\n|\r', "\n"],
            [Token::T_NEWLINE_ALL, 'T_NEWLINE_ALL', '\n|\r\n|\r', "\r\n"],
            [Token::T_NEWLINE_ALL, 'T_NEWLINE_ALL', '\n|\r\n|\r', "\r"],
            [Token::T_TAB, 'T_TAB', '\t', "\t"],
            [Token::T_WHITESPACE, 'T_WHITESPACE', '\s+', '    '],

            // Miscellaneous Symbols
            [Token::T_STAR, 'T_STAR', '\*', '*'],
            [Token::T_SLASH, 'T_SLASH', '\/', '/'],
            [Token::T_HYPHEN, 'T_HYPHEN', '-', '-'],
            [Token::T_PERCENT_SIGN, 'T_PERCENT_SIGN', "%", "%"],
            [Token::T_DOT, 'T_DOT', '\.', '.'],
            [Token::T_HASH, 'T_HASH', '#', '#'],
            [Token::T_AT, 'T_AT', '@', '@'],
            [Token::T_TILDE, 'T_TILDE', '~', '~'],
            [Token::T_COMMA, 'T_COMMA', ',', ','],
            [Token::T_BACKTICK, 'T_BACKTICK', '`', '`'],

            // Currency Symbols
            [Token::T_DOLLAR, 'T_DOLLAR', '\$', '$'],
            [Token::T_EURO, 'T_EURO', '€', '€'],
            [Token::T_POUND, 'T_POUND', '£', '£'],

            // Common Arithmetic Symbols
            [Token::T_DECIMAL_POINT, 'T_DECIMAL_POINT', '\.', '.'],
            [Token::T_EQUALS, 'T_EQUALS', '=', '='],
            [Token::T_MULTIPLY, 'T_MULTIPLY', '\*', '*'],
            [Token::T_DIVIDE, 'T_DIVIDE', '\/', '/'],
            [Token::T_PLUS, 'T_PLUS', '\+', '+'],
            [Token::T_MINUS, 'T_MINUS', '-', '-'],
            [Token::T_MODULOUS, 'T_MODULOUS', '%', '%'],
            [Token::T_MOD, 'T_MOD', '%', '%'],

            // Common Logical Operators
            [Token::T_OR, 'T_OR', '\|\|', '||'],
            [Token::T_AND, 'T_AND', '&&', '&&'],
            [Token::T_NOT, 'T_NOT', '!', '!'],

            // Common programing symbols
            [Token::T_VAR, 'T_VAR', '\$', '$'],
            [Token::T_UNDERSCORE, 'T_UNDERSCORE', '_', '_'],
            [Token::T_COLON, 'T_COLON', ':', ':'],
            [Token::T_SEMICOLON, 'T_SEMICOLON', ';', ';'],
            [Token::T_PIPE, 'T_PIPE', '\|', '|'],
            [Token::T_AMPERSAND, 'T_AMPERSAND', '&', '&'],
            [Token::T_CARET, 'T_CARET', '\^', '^'],
            [Token::T_EXCLAIMATION_MARK, 'T_EXCLAIMATION_MARK', "!", "!"],
            [Token::T_QUESTION_MARK, 'T_QUESTION_MARK', '\?', '?'],
            [Token::T_DOUBLE_QOUTE, 'T_DOUBLE_QOUTE', '"', '"'],
            [Token::T_SINGLE_QUOTE, 'T_SINGLE_QUOTE', "'", "'"],

            [Token::T_OPEN_PARENTHESIS, 'T_OPEN_PARENTHESIS', "\(", "("],
            [Token::T_CLOSE_PARENTHESIS, 'T_CLOSE_PARENTHESIS', "\)", ")"],
            [Token::T_OPEN_CURLY, 'T_OPEN_CURLY', "\{", "{"],
            [Token::T_CLOSE_CURLY, 'T_CLOSE_CURLY', "\}", "}"],
            [Token::T_OPEN_SQUARE, 'T_OPEN_SQUARE', "\[", "["],
            [Token::T_CLOSE_SQUARE, 'T_CLOSE_SQUARE', "\]", "]"],
            [Token::T_STRING, 'T_STRING', "\w+", "Affinity4"],
            [Token::T_NUMBER, 'T_NUMBER', '\d+', "123"],
        ];
    }

    /**
     * @dataProvider lexiconConstants
     *
     * @param string $lexicon_constant
     * @param string $token
     * @param string $expect
     * 
     * @return void
     */
    public function testonstantIsCorrectInFinalRegex(string $lexicon_constant, string $token, string $expect): void
    {
        $lexicon = [
            $lexicon_constant => $token
        ];
        $Tokenizer = new Tokenizer($lexicon);

        $this->assertSame(sprintf("/(?P<%s>%s)/A", $token, $expect), $Tokenizer->debug());
    }

    /**
     * @dataProvider lexiconConstants
     * @depends testonstantIsCorrectInFinalRegex
     *
     * @return void
     */
    public function testRegexMatchesCorrectly(string $lexicon_constant, string $token, string $expect, string $input): void
    {
        $lexicon = [
            $lexicon_constant => $token
        ];

        $Tokenizer = new Tokenizer($lexicon);
        $Stream = $Tokenizer->tokenize($input);

        $this->assertSame(1, $Stream->count());
        $this->assertSame($token, $Stream->current()->type);
    }

    /**
     * @dataProvider lexiconConstants
     * @covers isType
     *
     * @param string $lexicon_constant
     * @param string $token
     * @param string $expect
     * @param string $input
     * 
     * @return void
     */
    public function testIsType(string $lexicon_constant, string $token, string $expect, string $input): void
    {
        $lexicon = [
            $lexicon_constant => $token
        ];

        $Tokenizer = new Tokenizer($lexicon);
        $Stream = $Tokenizer->tokenize($input);

        $this->assertSame(1, $Stream->count());
        $this->assertTrue($Stream->current()->isType($token));
    }
}