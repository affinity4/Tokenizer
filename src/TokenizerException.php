<?php
declare(strict_types=1);

namespace Affinity4\Tokenizer;

/**
 * Tokenizer Exception
 */
class TokenizerException extends \Exception
{
    public function __construct(string $token, $line, $col)
    {
        $class = self::class;
        $indicator_position = strlen("$class: Unexpected ");
        $i = 0;
        $indicator = '^';
        while ($i < $indicator_position) {
            $indicator = ' ' . $indicator;
            $i = $i + 1;
        }

        parent::__construct("Unexpected $token on line $line, column $col." . PHP_EOL . "$indicator");
    }
}
