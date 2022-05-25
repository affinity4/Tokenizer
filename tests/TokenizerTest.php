<?php
namespace Affinity4\Tokenizer\Test;

use Affinity4\Tokenizer\Token;
use PHPUnit\Framework\TestCase;
use Affinity4\Tokenizer\Tokenizer;

/**
 * Tokenizer Tests
 * 
 * @covers \Affinity4\Tokenizer\Tokenizer
 * @uses \Affinity4\Tokenizer\Stream
 * @uses \Affinity4\Tokenizer\Token
 */
class TokenizerTest extends TestCase
{
    /**
     * Template
     *
     * @var string
     */
    private string $template;

    /**
     * Tokenizer
     *
     * @var Affinity4\Tokenizer\Tokenizer
     */
    private Tokenizer $Tokenizer;

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

        $lexicon = [
            /*
            It's a good idea to do the punctuation first, or anything you want to remove early on (e.g. comments or whitespace)
            This would be single chars that have meaning in your language. 
            For us, the # means an id attribute, the . is before any classname, 
            and :, ;, (, ), {, } all have their own purpose too
            */
            Token::T_WHITESPACE => 'T_WHITESPACE', // We might want to remove all whitespace not within quotes ("") to minify our compiled html
            Token::T_SLASH => 'T_FORWARD_SLASH',
            Token::T_ESCAPE_CHAR => 'T_ESCAPE',
            Token::T_NEWLINE_ALL => 'T_NEWLINE',
            Token::T_DOT => 'T_DOT',
            Token::T_HASH => 'T_HASH',
            Token::T_COLON => 'T_COLON',
            Token::T_SEMICOLON => 'T_SEMICOLON',
            Token::T_EQUALS => 'T_EQUALS',
            Token::T_DOUBLE_QOUTE => 'T_DOUBLE_QOUTE',
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

        $this->Tokenizer = new Tokenizer($lexicon);
    }

    /**
     * Test Token Returns Stream
     * 
     * @covers tokenize
     *
     * @return void
     */
    public function testTokenizeReturnsStream(): void
    {
        $this->assertInstanceOf('Affinity4\Tokenizer\Stream', $this->Tokenizer->tokenize($this->template));
    }
}