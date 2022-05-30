<?php
namespace Affinity4\Tokenizer\Test;

use Affinity4\Tokenizer\Token;
use PHPUnit\Framework\TestCase;
use Affinity4\Tokenizer\Tokenizer;

class StreamTest extends TestCase
{
    /**
     * Template
     *
     * @var string
     */
    private string $template;

    /**
     * Lexicon
     *
     * @var array
     */
    private array $lexicon;

    /**
     * Tokenizer
     *
     * @var \Affinity4\Tokenizer\Tokenizer
     */
    private \Affinity4\Tokenizer\Tokenizer $Tokenizer;

    /**
     * Stream
     *
     * @var \Affinity4\Tokenizer\Stream
     */
    private \Affinity4\Tokenizer\Stream $Stream;

    /**
     * Setup
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->template = <<<TEMPLATE
html(lang="en_IE") {
    // child nodes \ are inside curly brackets!
    head() {
        title(): This is a title;
        link(src="./style.css");
        script(src="./main.js");
    }

    body.app#app() {
        h1.title(): Page title;
    }
}
TEMPLATE;

        $this->lexicon = [
            /*
            It's a good idea to do the punctuation first, or anything you want to remove early on (e.g. comments or whitespace)
            This would be single chars that have meaning in your language. 
            For us, the # means an id attribute, the . is before any classname, 
            and :, ;, (, ), {, } all have their own purpose too
            */
            Token::T_WHITESPACE => 'T_WHITESPACE', // We might want to remove all whitespace not within quotes ("") to minify our compiled html
            Token::T_SLASH => 'T_FORWARD_SLASH',
            Token::T_NEWLINE => 'T_NEWLINE',
            Token::T_ESCAPE_CHAR => 'T_ESCAPE',
            Token::T_DOT => 'T_DOT',
            Token::T_HASH => 'T_HASH',
            Token::T_COLON => 'T_COLON',
            Token::T_SEMICOLON => 'T_SEMICOLON',
            Token::T_EQUALS => 'T_EQUALS',
            Token::T_DOUBLE_QUOTE => 'T_DOUBLE_QUOTE',
            Token::T_SINGLE_QUOTE => 'T_SINGLE_QUOTE',
            Token::T_EXCLAIMATION_MARK => 'T_EXCLAIMATION_MARK',
            Token::T_OPEN_PARENTHESIS => 'T_OPEN_PARENTHESIS',
            Token::T_CLOSE_PARENTHESIS => 'T_CLOSE_PARENTHESIS',
            Token::T_OPEN_CURLY => 'T_OPEN_CURLY',
            Token::T_CLOSE_CURLY => 'T_CLOSE_CURLY',
        
            // Now we can define some more generic "lexemes"
            
            // Match All words as T_STRING. Our parser can then 
            // check for the first string in each line that is followed by 
            // T_DOT | T_HASH | T_OPENING_PARENTHESIS. This will be the HTML tag name
            Token::T_STRING => 'T_STRING'
        ];

        // Newlines must be replaced with a token before they can be handled
        $template = str_replace(["\r\n", "\r"], "\n", $this->template);
		$input_lines = explode("\n", $template); 
		$this->template = implode(";T_NEWLINE;", $input_lines);

        $this->Tokenizer = new Tokenizer($this->lexicon);
        $this->Stream = $this->Tokenizer->tokenize($this->template);
    }

    private function getNexts(): array
    {
        return [
            [
                'type' => 'T_OPEN_PARENTHESIS',
                'value' => '(',
                'offset' => 4,
                'length' => 1
            ],
            [
                'type' => 'T_STRING',
                'value' => 'lang',
                'offset' => 5,
                'length' => 4
            ],
            [
                'type' => 'T_EQUALS',
                'value' => '=',
                'offset' => 9,
                'length' => 1
            ],
            [
                'type' => 'T_DOUBLE_QUOTE',
                'value' => '"',
                'offset' => 10,
                'length' => 1
            ],
            [
                'type' => 'T_STRING',
                'value' => 'en_IE',
                'offset' => 11,
                'length' => 5
            ],
            [
                'type' => 'T_DOUBLE_QUOTE',
                'value' => '"',
                'offset' => 16,
                'length' => 1
            ],
            [
                'type' => 'T_CLOSE_PARENTHESIS',
                'value' => ')',
                'offset' => 17,
                'length' => 1
            ],
            [
                'type' => 'T_WHITESPACE',
                'value' => ' ',
                'offset' => 18,
                'length' => 1
            ],
            [
                'type' => 'T_OPEN_CURLY',
                'value' => '{',
                'offset' => 19,
                'length' => 1
            ],
            [
                'type' => 'T_NEWLINE',
                'value' => ';T_NEWLINE;',
                'offset' => 20,
                'length' => 11
            ],
            [
                'type' => 'T_WHITESPACE',
                'value' => '    ',
                'offset' => 31,
                'length' => 4
            ],
            [
                'type' => 'T_FORWARD_SLASH',
                'value' => '/',
                'offset' => 35,
                'length' => 1
            ],
            [
                'type' => 'T_FORWARD_SLASH',
                'value' => '/',
                'offset' => 36,
                'length' => 1
            ],
            [
                'type' => 'T_WHITESPACE',
                'value' => ' ',
                'offset' => 37,
                'length' => 1
            ],
            [
                'type' => 'T_STRING',
                'value' => 'child',
                'offset' => 38,
                'length' => 5
            ],
            [
                'type' => 'T_WHITESPACE',
                'value' => ' ',
                'offset' => 43,
                'length' => 1
            ],
            [
                'type' => 'T_STRING',
                'value' => 'nodes',
                'offset' => 44,
                'length' => 5
            ],
            [
                'type' => 'T_WHITESPACE',
                'value' => ' ',
                'offset' => 49,
                'length' => 1
            ],
            [
                'type' => 'T_ESCAPE',
                'value' => '\\',
                'offset' => 50,
                'length' => 1
            ],
            [
                'type' => 'T_WHITESPACE',
                'value' => ' ',
                'offset' => 51,
                'length' => 1
            ],
            [
                'type' => 'T_STRING',
                'value' => 'are',
                'offset' => 52,
                'length' => 3
            ],
            [
                'type' => 'T_WHITESPACE',
                'value' => ' ',
                'offset' => 55,
                'length' => 1
            ],
            [
                'type' => 'T_STRING',
                'value' => 'inside',
                'offset' => 56,
                'length' => 6
            ],
            [
                'type' => 'T_WHITESPACE',
                'value' => ' ',
                'offset' => 62,
                'length' => 1
            ],
            [
                'type' => 'T_STRING',
                'value' => 'curly',
                'offset' => 63,
                'length' => 5
            ],
            [
                'type' => 'T_WHITESPACE',
                'value' => ' ',
                'offset' => 68,
                'length' => 1
            ],
            [
                'type' => 'T_STRING',
                'value' => 'brackets',
                'offset' => 69,
                'length' => 8
            ],
            [
                'type' => 'T_EXCLAIMATION_MARK',
                'value' => '!',
                'offset' => 77,
                'length' => 1
            ],
            [
                'type' => 'T_NEWLINE',
                'value' => ';T_NEWLINE;',
                'offset' => 78,
                'length' => 11
            ],
            [
                'type' => 'T_WHITESPACE',
                'value' => '    ',
                'offset' => 89,
                'length' => 4
            ],
            [
                'type' => 'T_STRING',
                'value' => 'head',
                'offset' => 93,
                'length' => 4
            ],
            [
                'type' => 'T_OPEN_PARENTHESIS',
                'value' => '(',
                'offset' => 97,
                'length' => 1
            ],
            [
                'type' => 'T_CLOSE_PARENTHESIS',
                'value' => ')',
                'offset' => 98,
                'length' => 1
            ],
            [
                'type' => 'T_WHITESPACE',
                'value' => ' ',
                'offset' => 99,
                'length' => 1
            ],
            [
                'type' => 'T_OPEN_CURLY',
                'value' => '{',
                'offset' => 100,
                'length' => 1
            ],
            [
                'type' => 'T_NEWLINE',
                'value' => ';T_NEWLINE;',
                'offset' => 101,
                'length' => 11
            ],
        ];
    }

    /**
     * @covers \Affinity4\Tokenizer\Tokenizer::tokenize
     *
     * @return void
     */
    public function testTokeizeReturnsStreamInstance(): void
    {
        $this->assertInstanceOf(\Affinity4\Tokenizer\Stream::class, $this->Stream);
    }

    /**
     * @covers \Affinity4\Tokenizer\Stream
     *
     * @returns void
     */
    public function testStreamTokensAreCorrect(): void
    {
        $this->Stream->rewind();
        $this->assertInstanceOf(Token::class, $this->Stream->current());
        $this->assertSame(114, $this->Stream->count());
        $this->assertSame('T_STRING', $this->Stream->current()->type);
        $this->assertSame('html', $this->Stream->current()->value);
        $this->assertSame(0, $this->Stream->current()->offset);
        $this->assertSame(4, $this->Stream->current()->length);

        foreach ($this->getNexts() as $next) {
            $this->assertSame($next['type'], $this->Stream->next()->type);
            $this->assertSame($next['value'], $this->Stream->current()->value);
            $this->assertSame($next['offset'], $this->Stream->current()->offset);
            $this->assertSame($next['length'], $this->Stream->current()->length);
        }
    }

    /**
     * @covers \Affinity4\Tokenizer\Stream::skipWhile
     *
     * @return void
     */
    public function testSkipWhileMethod(): void
    {
        $template = '    div(class="test")';

        $Tokenizer = new Tokenizer([
            '\s+' => 'T_WHITESPACE',
            '\S+' => 'T_NOT_WHITESPACE'
        ]);

        $Stream = $Tokenizer->tokenize($template);

        $Stream->skipWhile('T_WHITESPACE');

        $this->assertSame('T_NOT_WHITESPACE', $Stream->current()->type);
        $this->assertSame('div(class="test")', $Stream->current()->value);
    }

    /**
     * @covers \Affinity4\Tokenizer\Stream::consumeWhile
     *
     * @return void
     */
    public function testConsumeWhileMethod(): void
    {
        $template = 'div(class="test")';

        $Tokenizer = new Tokenizer([
            '\w+' => 'T_WORD',
            '[^a-zA-Z0-9_]' => 'T_NOT_WORD'
        ]);

        $Stream = $Tokenizer->tokenize($template);

        $tokens = $Stream->consumeWhile('T_WORD');

        $this->assertCount(1, $tokens);
        $this->assertSame('T_WORD', $tokens[0]->type);
        $this->assertSame('div', $tokens[0]->value);
    }

    /**
     * @covers \Affinity4\Tokenizer\Stream::consumeValueWhile
     *
     * @return void
     */
    public function testConsumeValueWhileMethod(): void
    {
        $template = 'div(class="test")';

        $Tokenizer = new Tokenizer([
            '\w+' => 'T_WORD',
            '[^a-zA-Z0-9_]' => 'T_NOT_WORD'
        ]);

        $Stream = $Tokenizer->tokenize($template);

        $value = $Stream->consumeValueWhile('T_WORD');

        $this->assertSame('div', $value);
    }

    /**
     * @covers \Affinity4\Tokenizer\Stream::consumeValueUntil
     *
     * @return void
     */
    public function testConsumeValueUntilMethod(): void
    {
        $template = 'div(class="test")';

        $Tokenizer = new Tokenizer([
            '\w+' => 'T_WORD',
            Token::T_OPEN_PARENTHESIS  => 'T_OPEN_PARENTHESIS',
            Token::T_CLOSE_PARENTHESIS => 'T_CLOSE_PARENTHESIS',
            Token::T_EQUALS            => 'T_EQUALS',
            Token::T_DOUBLE_QUOTE      => 'T_DOUBLE_QUOTE'
        ]);

        $Stream = $Tokenizer->tokenize($template);

        $Stream->skipWhile('T_WORD');

        $this->assertSame('(', $Stream->current()->value);
        $this->assertSame('T_OPEN_PARENTHESIS', $Stream->current()->type);
        $Stream->next();
        $attributes = $Stream->consumeValueUntil('T_CLOSE_PARENTHESIS');
        $this->assertSame('class="test"', $attributes);
        $this->assertSame(')', $Stream->current()->value);
        $this->assertSame('T_CLOSE_PARENTHESIS', $Stream->current()->type);
    }

    /**
     * @covers \Affinity4\Tokenizer\Stream::consumeValueUntil
     *
     * @return void
     */
    public function testConsumeValueUntilMethodWhereNoTokensConsumed(): void
    {
        $template = 'div()'; // testing what happens when the very next token is the 'until'

        $Tokenizer = new Tokenizer([
            '\w+' => 'T_WORD',
            Token::T_OPEN_PARENTHESIS  => 'T_OPEN_PARENTHESIS',
            Token::T_CLOSE_PARENTHESIS => 'T_CLOSE_PARENTHESIS',
        ]);

        $Stream = $Tokenizer->tokenize($template);

        $Stream->skipWhile('T_WORD');

        $this->assertSame('(', $Stream->current()->value);
        $this->assertSame('T_OPEN_PARENTHESIS', $Stream->current()->type);
        $Stream->next();
        $attributes = $Stream->consumeValueUntil('T_CLOSE_PARENTHESIS');
        $this->assertEmpty($attributes);
        $this->assertSame(')', $Stream->current()->value);
        $this->assertSame('T_CLOSE_PARENTHESIS', $Stream->current()->type);
    }
}