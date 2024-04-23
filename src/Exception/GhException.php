<?php

namespace githusband\Exception;

use Throwable;

class GhException extends \Exception
{
    /**
     * Current info of the recurrence validation: field path or its rule, etc.
     * 
     * @see githusband\Validation::recurrence_current
     * @var array
     */
    protected $recurrence_current = [];

    /**
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     * @param array $recurrence_current
     */
    public function __construct($message, $code, $previous = null, $recurrence_current = [])
    {
        parent::__construct($message, $code, $previous);

        $this->recurrence_current = $recurrence_current;
    }

    /**
     * Set recurrence_current
     *
     * @param array $recurrence_current
     * @return static
     */
    public function set_recurrence_current($recurrence_current)
    {
        $this->recurrence_current = $recurrence_current;
        return $this;
    }

    /**
     * Get recurrence_current
     *
     * @return array
     */
    public function get_recurrence_current()
    {
        return $this->recurrence_current;
    }

    /**
     * Make current details
     *
     * @param array $recurrence_current
     * @param string $auto_field
     * @return string
     */
    public static function make_current_details($recurrence_current, $auto_field = 'data')
    {
        $current_field_path = $recurrence_current['field_path'];
        $current_field_path = empty($current_field_path) ? $auto_field : $current_field_path;

        if (!empty($recurrence_current['method'])) {
            $field_detail = "@method:{$recurrence_current['method']['method']}";
        } else if (!empty($recurrence_current['rule'])) {
            $field_detail = "@rule:{$recurrence_current['rule']}";
        } else {
            $current_ruleset = $recurrence_current['field_ruleset'];
            $current_ruleset = empty($current_ruleset) ? 'NotSet' : $current_ruleset;
            $field_detail = "@ruleset:{$current_ruleset}";
        }
        
        return "@field:{$current_field_path}, {$field_detail}";
    }

    /**
     * Extend an exception in order to help debugging
     *
     * @param Throwable $previous
     * @param array $recurrence_current
     * @param string $auto_field
     * @return static
     */
    public static function extend_privious($previous, $recurrence_current, $auto_field = 'data')
    {
        $current_details = static::make_current_details($recurrence_current, $auto_field);
        return new GhException($current_details . ' - ' . $previous->getMessage(), $previous->getCode(), $previous, $recurrence_current);
    }
}
