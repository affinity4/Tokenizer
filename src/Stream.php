<?php
declare(strict_types=1);

namespace Affinity4\Tokenizer;

/**
 * Stream of tokens
 */
class Stream
{
	/**
	 * Tokens
	 *
	 * @var array
	 */
	public array $tokens;

	/**
	 * Position
	 *
	 * @var integer
	 */
	public int $position = 0;

	/**
	 * Constructor
	 * 
	 * @param  array  $tokens
	 */
	public function __construct(array $tokens)
	{
		$this->tokens = $tokens;
	}

	/**
	 * Count
	 *
	 * @return integer
	 */
	public function count(): int
	{
		return count($this->tokens);
	}

	/**
	 * Current Token
	 *
	 * @return \Affinity4\Tokenizer\Token
	 */
	public function current(): \Affinity4\Tokenizer\Token
	{
		if (!isset($this->tokens[$this->position])) {
			throw new UnexpectedExitException;
		}

		return $this->tokens[$this->position];
	}

	/**
	 * Skip While
	 * 
	 * Skip each token that is a match in ...$args
	 * Stops when a token does not match ...$args list
	 *
	 * @param string $type
	 *
	 * @return void
	 */
	public function skipWhile(...$args): void
	{
		$token = $this->current();
		while ($token && in_array($token->type, $args)) {
			$token = $this->next();
		}
	}

	/**
	 * Consume While
	 * 
	 * Moves pointer ahead while  type is one of ...$args
	 * 
	 * Returns array of tokens which where 'consumed'
	 *
	 * @return array
	 */
	public function consumeWhile(... $args): array
	{
		$token = $this->current();
		$tokens = [];
		while ($token && in_array($token->type, $args)) {
			$tokens[] = $token;
			$token = $this->next();
		}

		return $tokens;
	}

	/**
	 * consumeValueWhile
	 *
	 * @param arglist $args
	 *
	 * @return string
	 */
	public function consumeValueWhile(... $args): string
	{
		$tokens = $this->consumeWhile(...$args);
		$i = 0;
		$value = '';
		while ($tokens[$i]) {
			$value .= $tokens[$i]->value;
		}

		return $value;
	}

	/**
	 * Consume Value Until
	 * 
	 * Returns value up until one of the provided token types is matched
	 *
	 * @param arglist $args
	 * 
	 * @return string
	 */
	public function consumeValueUntil(...$args): string
	{
		$Token = $this->current();
		$value = '';
		while ($Token && !in_array($Token->type, $args)) {
			$value .= $Token->value;
			$Token = $this->next();
		}

		return $value;
	}

	/**
	 * Next Token
	 * 
	 * @return false|\Affinity4\Tokenizer\Token
	 */
	public function next(): false|\Affinity4\Tokenizer\Token
	{
		$this->position = $this->position + 1;
		$token = $this->tokens[$this->position] ?? false;

		return $token;
	}

	/**
	 * Is Current
	 *
	 * @param mixed ...$args
	 * 
	 * @return boolean
	 */
	public function isCurrent(string $type): bool
	{
		$token = $this->tokens[$this->position] ?? false;

		return ($token && $token->isType($type));
	}

	/**
	 * Is Next
	 *
	 * @param string $type
	 * 
	 * @return boolean
	 */
	public function isNext(string $type): bool
	{
		$position = $this->position + 1;
		$token = $this->tokens[$position] ?? false;

		return ($token && $token->isType($type));
	}

	/**
	 * Is Prev
	 *
	 * @param string $type
	 * 
	 * @return boolean
	 */
	public function isPrev(string $type): bool
	{
		$position = $this->position - 1;
		$token = $this->tokens[$position] ?? false;

		return ($token && $token->isType($type));
	}

	/**
	 * Has Next
	 *
	 * @return boolean
	 */
	public function hasNext(): bool
	{
		$pos = $this->position + 1;

		return isset($this->tokens[$pos]);
	}
	

	/**
	 * Rewind
	 *
	 * @return self
	 */
	public function rewind(): self
	{
		$this->position = 0;
		
		return $this;
	}
}
