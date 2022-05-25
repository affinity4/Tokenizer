<?php
declare(strict_types=1);

namespace Affinity4\Tokenizer;

/**
 * Unexpected Exit Exception
 */
class UnexpectedExitException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Token stream ended unexpectedly');
    }
}