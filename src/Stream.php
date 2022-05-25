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
	 * Next All
	 * 
	 * @return array
	 */
	public function nextAll(): array
	{
		$tokens = [];
		$token = $this->current();
		while ($token) {
			$tokens[] = $token;
			$token = $this->next();
		}
		
		return $tokens;
	}

	/**
	 * Next Until
	 *
	 * @param string $type
	 * 
	 * @return array
	 */
	public function nextUntil(string $type): array
	{
		$tokens = [];
		$token = $this->current();
		while ($token && $token->type !== $type) {
			$tokens[] = $token;
			
			$token = $this->next();
		}

		return $tokens;
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
	 * Reset
	 *
	 * @return self
	 */
	public function rewind(): self
	{
		$this->position = -1;
		
		return $this;
	}
}
