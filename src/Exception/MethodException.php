<?php

namespace githusband\Exception;

use Exception;

/**
 * Throw this exception if a pre-defined method needs to throw an exception
 */
class MethodException extends Exception
{
    const CODE_DATA = 1;
    const CODE_PARAMETER = 2;

    public function __construct($message, $code, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function data($message)
    {
        return new static($message, static::CODE_DATA);
    }

    public static function parameter($message)
    {
        return new static($message, static::CODE_PARAMETER);
    }
}
