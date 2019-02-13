<?php
declare(strict_types=1);

namespace Ueef\Machina\Exceptions;

use Throwable;
use Exception;

abstract class AbstractError extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        if (is_array($message)) {
            foreach ($message as &$value) {
                if (is_array($value) || is_object($value)) {
                    $value = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                }
            }

            $message = sprintf(...$message);
        }

        parent::__construct($message, $code, $previous);
    }
}