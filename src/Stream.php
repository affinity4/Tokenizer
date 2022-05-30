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
	 * @since 0.0.3
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
	 * skip Until
	 * 
	 * Moves forward until one of the provided token types is matched
	 * 
	 * @since 0.0.4
	 *
	 * @param array $args
	 */
	public function skipUntil(...$args)
	{
		$Token = $this->current();
		while ($Token && !in_array($Token->type, $args)) {
			$Token = $this->next();
		}
	}

	/**
	 * Consume While
	 * 
	 * Moves pointer ahead while  type is one of ...$args
	 * 
	 * Returns array of tokens which where 'consumed'
	 * 
	 * @since 0.0.3
	 * 
	 * @param array $args
	 *
	 * @return array
	 */
	public function consumeWhile(...$args): array
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
	 * @since 0.0.3
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	public function consumeValueWhile(...$args): string
	{
		$tokens = $this->consumeWhile(...$args);
		$i = 0;
		$value = '';
		while (!empty($tokens) && isset($tokens[$i])) {
			$value .= $tokens[$i]->value;
			++$i;
		}

		return $value;
	}

	/**
	 * Consume Value Until
	 * 
	 * Returns value up until one of the provided token types is matched
	 * 
	 * @since 0.0.3
	 *
	 * @param array $args
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
	 * Consume Until
	 * 
	 * Returns tokens array up until one of the provided token types is matched
	 * 
	 * @since 0.0.4
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public function consumeUntil(...$args): array
	{
		$Token = $this->current();
		$tokens = [];
		while ($Token && !in_array($Token->type, $args)) {
			$tokens[] = $Token;
			$Token = $this->next();
		}

		return $tokens;
	}
	
	/**
	 * Copy Stream Until
	 * 
	 * @since 0.0.4
	 *
	 * @param array $args
	 * 
	 * @return \Affinity4\Tokenizer\Stream
	 */
	public function copyStreamUntil(...$args): \Affinity4\Tokenizer\Stream
	{
		$tokens = $this->consumeUntil(...$args);

		return new Stream($tokens);
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
	 * @param string $type
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
