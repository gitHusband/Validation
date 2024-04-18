<?php

namespace githusband\Exception;

class RuleException extends GhException
{
    const CODE_RULE_SET = 1;
    const CODE_RULE = 2;
    const CODE_METHOD = 3;
    const CODE_PARAMETER = 4;

    /**
     * Generate a rule set exception
     *
     * @param string $message
     * @param array $recurrence_current
     * @param string $auto_field
     * @return static
     */
    public static function rule_set($message, $recurrence_current, $auto_field = 'data')
    {
        $current_details = static::make_current_details($recurrence_current, $auto_field);
        return new static($current_details . ' - ' . $message, static::CODE_RULE_SET, null, $recurrence_current);
    }
}
