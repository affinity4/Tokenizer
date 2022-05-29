<?php
declare(strict_types=1);

namespace Affinity4\Tokenizer;

/**
 * Token Class
 */
class Token
{
    // Special Characters
	const T_ESCAPE_CHAR 	= '\\\\';
	const T_NEWLINE 		= ';T_NEWLINE;';
    const T_TAB 			= ";T_TAB;";
	const T_WHITESPACE 		= '\s+';

    // Miscellaneous Symbols
    const T_STAR 		 	= '\*';
    const T_SLASH 		 	= '\/';
    const T_PERCENT_SIGN 	= '%';
    const T_HYPHEN 	 	 	= '-';
    const T_DOT			 	= '\.';
	const T_HASH 		 	= '#';
	const T_AT 			 	= '@';
	const T_TILDE 		 	= '~';
	const T_COMMA 		 	= ',';
	const T_BACKTICK 	 	= '`';

    // Currency Symbols
	const T_DOLLAR 			= '\$';
	const T_EURO 			= '€';
	const T_POUND 			= '£';

    // Common Arithmetic Symbols
    const T_DECIMAL_POINT	= '\.';
	const T_EQUALS 			= '=';
    const T_MULTIPLY 		= '\*';
    const T_DIVIDE	 		= '\/';
    const T_PLUS 			= '\+';
    const T_MINUS 			= '-';
    const T_MODULOUS 		= '%';
    const T_MOD 			= '%';

    // Common Logical Operators
    const T_OR 				= '\|\|';
    const T_AND 			= '&&';
    const T_NOT 			= '!';

    // Common programing symbols
    const T_VAR 				= '\$';
    const T_UNDERSCORE 			= '_';
    const T_COLON 				= ':';
    const T_SEMICOLON 			= ';';
    const T_PIPE 				= '\|';
    const T_AMPERSAND 			= '&';
    const T_CARET 				= '\^';
    const T_EXCLAIMATION_MARK 	= '!';
    const T_QUESTION_MARK 		= '\?';
    const T_OPEN_PARENTHESIS 	= '\(';
	const T_CLOSE_PARENTHESIS 	= '\)';
	const T_OPEN_CURLY 			= '\{';
	const T_CLOSE_CURLY		 	= '\}';
	const T_OPEN_SQUARE		 	= '\[';
	const T_CLOSE_SQUARE 		= '\]';
	const T_DOUBLE_QUOTE 		= '"';
	const T_SINGLE_QUOTE 		= "'";

	const T_STRING 				= '\w+';
    const T_NUMBER 				= '\d+';

	/**
	 * Value
	 *
	 * @var string
	 */
	public string $value;

	/**
	 * Type
	 *
	 * @var string
	 */
	public string $type;

	/**
	 * Offset
	 *
	 * @var int
	 */
	public int $offset;

	/**
	 * Length
	 *
	 * @var integer
	 */
	public int $length;

	/**
	 * Constructor
	 *
	 * @param string $value
	 * @param string $type
	 * @param int $offset
	 * @param int $length
	 */
	public function __construct(string $value, string $type, int $offset, int $length)
	{
		$this->value = $value;
		$this->type = $type;
		$this->offset = $offset;
		$this->length = $length;
	}

	/**
	 * Is Type
	 *
	 * @param string $type
	 * 
	 * @return boolean
	 */
	public function isType(string $type): bool
	{
		return ($this->type === $type);
	}
}
