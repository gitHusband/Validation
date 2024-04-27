<?php

namespace githusband\Exception;

/**
 * Throw this exception if you define any invalid ruleset
 */
class RuleException extends GhException
{
    const CODE_RULESET = 1;
    const CODE_RULE = 2;
    const CODE_METHOD = 3;
    const CODE_PARAMETER = 4;
    const CODE_PARAMETER_TYPE = 5;

    /**
     * Generate a ruleset exception
     *
     * @param string $message
     * @param array $recurrence_current
     * @param string $auto_field
     * @return static
     */
    public static function ruleset($message, $recurrence_current, $auto_field = 'data')
    {
        $current_details = static::make_current_details($recurrence_current, $auto_field);
        return new static($current_details . " - Invalid Ruleset: {$message}", static::CODE_RULESET, null, $recurrence_current);
    }

    /**
     * Generate a parameter exception
     *
     * @param string $message
     * @param array $recurrence_current
     * @param string $auto_field
     * @return static
     */
    public static function parameter($message, $recurrence_current, $auto_field = 'data')
    {
        $current_details = static::make_current_details($recurrence_current, $auto_field, $previous = null);
        return new static($current_details . " - Invalid Parameter: {$message}", static::CODE_PARAMETER, $previous, $recurrence_current);
    }

    /**
     * Generate a parameter type exception
     *
     * @param string $message
     * @param array $recurrence_current
     * @param string $auto_field
     * @return static
     */
    public static function parameter_type($message, $recurrence_current, $auto_field = 'data')
    {
        $current_details = static::make_current_details($recurrence_current, $auto_field, $previous = null);
        return new static($current_details . " - " . static::make_parameter_type_message($message), static::CODE_PARAMETER_TYPE, $previous, $recurrence_current);
    }

    /**
     * Make a message for parameter type exception
     *
     * @param string $message
     * @return string
     */
    public static function make_parameter_type_message($message)
    {
        return "Invalid Parameter Type: {$message}";
    }
}
