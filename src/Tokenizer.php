<?php
declare(strict_types=1);

namespace Affinity4\Tokenizer;

/**
 * Tokenizer
 * 
 * Lexical analyser
 */
class Tokenizer
{
	const DEBUG_ECHO = 'DEBUG_ECHO';

	const DEBUG_RETURN = 'DEBUG_RETURN';

	const DEBUG_DUMP = 'DEBUG_DUMP';

	const DEBUG_DUMP_AND_DIE = 'DEBUG_DUMP_AND_DIE';

	/**
	 * Regular Expresion
	 * 
	 * @var string
	 */
	private $regex;

	/**
	 * Tokens
	 *
	 * @var array
	 */
	private array $tokens;

	/**
	 * @param  array  $patterns  of [(int|string) token type => (string) pattern]
	 * @param  string  $flags  regular expression flags
	 */
	public function __construct(array $lexicon, string $flags = '')
	{
		$patterns = array_keys($lexicon);
		$tokens = array_values($lexicon);
		$patterns = array_map(function($pattern, $token) {
			$skip_token = (is_null($token) || is_bool($token));
			if (!$skip_token && !is_string($token)) {
				throw new \Exception("Token type in lexicon can only be a string or an integer");
			}

			return ($skip_token)
				? sprintf("(?:%s)", $token, $pattern)
				: sprintf("(?P<%s>%s)", $token, $pattern);
		}, $patterns, $tokens);

		$this->regex = '/' . implode('|', $patterns) . '/A' . $flags;
	}

	/**
	 * Debug
	 * 
	 * Used to debug the compiled regex
	 *
	 * @return null|string
	 */
	public function debug($operation = self::DEBUG_RETURN): ?string
	{
		switch ($operation) {
			case self::DEBUG_DUMP:
				var_dump($this->regex);
			break;
			case self::DEBUG_DUMP_AND_DIE:
				var_dump($this->regex);
				exit;
			break;
			case self::DEBUG_ECHO:
				echo $this->regex;
			break;
			default:
				return $this->regex;
			break;
		}
	}

	/**
	 * Tokenizes string
	 * 
	 * @param string $input
	 * 
	 * @return \Affinity\Tokenizer\Stream
	 * 
	 * @throws \Affinity4\Tokenizer\TokenizerException
	 */
	public function tokenize(string $input): Stream
	{
		preg_match_all($this->regex, $input, $tokens, PREG_SET_ORDER);
		if (preg_last_error()) {
			throw new \Exception(array_flip(get_defined_constants(true)['pcre'])[preg_last_error()]);
		}

		$offset = 0;
		foreach ($tokens as $k => $_tokens) {
			foreach ($_tokens as $type => $value) {
				if (is_string($type) && !empty($value)) {
					$length = strlen($value);
					$Token = new Token($value, $type, $offset, $length);
					
					// Move the pointer for the next token
					$offset += $length;

					$this->tokens[] = $Token;
				}
			}
		}

		if ($offset !== strlen($input)) {
			[$line, $col] = $this->getCoordinates($input, $offset);
			$token = str_replace("\n", '\n', substr($input, $offset, 10));
			throw new TokenizerException($token, $line, $col);
		}

		return new Stream($this->tokens);
	}


	/**
	 * Returns position of token in input string.
	 * @return array of [line, column]
	 */
	public static function getCoordinates(string $text, int $offset): array
	{
		$text = substr($text, 0, $offset);
		return [substr_count($text, "\n") + 1, $offset - strrpos("\n" . $text, "\n") + 1];
	}
}
