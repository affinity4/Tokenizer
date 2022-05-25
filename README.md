# Tokenizer

A zero-dedpendency tokenizer written in PHP. Returns an easily navigatable Stream object of Token objects with public type, value, offset and length properties

Simply pass an associative array [match_pattern => type] (`'\s+' => 'T_WHITESPACE', '[a-zA-Z]\w+' => 'T_STRING'`), and the Tokenizer will return all matches as an array of Token objects

## Installation

### Composer

`composer require affinity4/tokenizer`

## Basic Example

Let's assume we want to create a DSL (Domain Specific Language) for a template engine language that looks more like code, instead of markup

Example template snippet:

```php
$template = <<<TEMPLATE
html(lang="en_IE") {
    // child nodes are inside curly brackets!
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
```

Now we define our "lexicon", which is passed to the tokenizer:

**NOTE:**  
The lexicon must supply all characters and patterns you expect to encounter in your grammar. Currently you cannot skip any characters. Everything must be tokenized, whether you use it later or not.

```php
$lexicon = [
    /*
    It's a good idea to do the punctuation first, or anything you want to remove early on (e.g. comments or whitespace)
    This would be single chars that have meaning in your language. 
    For us, the # means an id attribute, the . is before any classname, 
    and :, ;, (, ), {, } all have their own purpose too
    */
    'T_WHITESPACE' => '\s+', // We might want to remove all whitespace not within quotes ("") to minify our compiled html
    'T_FORWARD_SLASH' => '/',
    'T_DOT' => '\.',
    'T_HASH' => '#',
    'T_COLON' => ':',
    'T_SEMICOLON' => ';',
    'T_EQUALS' => '=',
    'T_DOUBLE_QOUTE' => '"',
    'T_SINGLE_QUOTE' => "'",
    'T_EXCLAIMATION_MARK' => '!',
    'T_OPEN_PARENTHESIS' => '\(',
    'T_CLOSE_PARENTHESIS' => '\)',
    'T_OPEN_CURLY' => '\{',
    'T_CLOSE_CURLY' => '\}',

    // Now we can define some more generic "lexemes"
    
    // Match All words as T_STRING. Our parser can then 
    // check for the first string in each line that is followed by 
    // T_DOT | T_HASH | T_OPENING_PARENTHESIS. This will be the HTML tag name
    'T_STRING' => "\w+"
];
```

We pass the lexicon to the tokenizer...

```php
use Affinity4\Tokenizer\Tokenizer;

$Tokenizer = new Tokenizer($template);
$Tokenizer->registerLexicon($lexicon);
$Stream = $Tokenizer->tokenize(); // Affinity4\Tokenizer\Stream of Affinity4\Tokenizer\Token objects

while ($Stream->hasNext()) {
    $Token = $Stream->nextToken();
    echo $Token->type; // T_HTML_TAG
    echo $Token->value; // html
    echo $Token->linenumber; // 1
    echo $Token->offset[0]; // 0 Start position of match
    echo $Token->offset[1]; // 3 End position of match
    
}

```

From here you just need to write your "finite automata" and or/your parser.

## TIPS

### debug()

The Tokenizer has a debug() method, which will return the compiled regex, for you to examine.

**TIP:**  
A good website for testing PHP regexes is: <https://regexr.com/>

The debug method will by default return the regex as a string, however, you can also echo, var_dump and "dump and die" (or dd() for you Laravel users).

There are constants defined for all of these to help you avoid using the switches for these

* `$Tokenizer->debug(Tokenizer::DEBUG_ECHO)`
* `$Tokenizer->debug(Tokenizer::DEBUG_DUMP)`
* `$Tokenizer->debug(Tokenizer::DEBUG_DUMP_AND_DIE)`

## preg_match_all(): Compilation failed: missing closing parenthesis at offset x

Attempting to match backslashes, or newline chars (e.g. \r|\n|\r\n) is most likely the cause of your troubles here

You will need to double escape backslashes. To help you avoid needing to figure this out I have provided 2 constants which contain the correct regex patterns for T_ESCAPE_CHAR and T_NEWLINE_ALL

```php
$lexicon = [
    // ...
    Tokenize::T_ESCAPE_CHAR => 'T_ESCAPE', // '\\\\'
    Tokenize::T_NEWLINE_ALL => 'T_NEWLINE', // '\n|\r\n|\r'
    // ...
]
```

If you need to match individual newline characters for a specific environment you can use the following constants

```php
$lexicon = [
    // ...
    T_NEWLINE_UNIX  => 'T_NEWLINE_UNIX',  // '\n'
    T_NEWLINE_WIN   => 'T_NEWLINE_WIN',  // '\r\n'
    /**
     * Not only Mac, but everyone thinks it is. 
     * \r is actually a valid newline character on all systems
     */
    T_NEWLINE_MAC   => 'T_NEWLINE_MAC', // '\r'
    // ...
];
```

**TIP:**  
You should clean newlines before running input through the Tokenizer `$input = str_replace(["\r\n", "\r"], "\n", $input)`

See the following section on Matching Backslashes and Special Characters if you want more info

### Matching Backslashes and Special Characters

As mentioned above, backslashes must be double escaped.

So to match a single backslash your must use the regex `'\\\\'` (I know, it sucks, but you have to)

To match special characters (tabs, newlines, cariage returns etc) you MUST NOT USE DOUBLE QUOTES. This is because a double quoted "\n" will just become an actual newline character in PHP.

So always use single quotes for special charachters. In fact, things will work better for you if all your Lexixon patterns are surrounded with single quotes when ever possible. E.g `'\r|\n|\r\\n|\t'`

I will work on some better detection internally for these patterns and attempt to provide better error messages when these errors are encountered *(I'll go real meta and regex the regex before it's ran or something)*
