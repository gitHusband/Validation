<?php

namespace githusband\Exception;

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
}
