<?php

namespace githusband\Test\Extend;

require_once __DIR__ . '/../../../vendor/autoload.php';

use githusband\Validation;
use githusband\Test\Extend\Rule\RuleDate;

class MyValidation extends Validation
{
    use RuleDate;

    protected $method_symbol = [
        "date<=>=" => "date_greaterequal_lessequal",
    ];

    protected function date_greaterequal_lessequal($date, $start_date, $end_date)
    {
        $is_date = $this->is_date($date);
        if ($is_date !== true) return $is_date;

        $datetime = strtotime($date);
        $start_datetime = strtotime($start_date);
        $end_datetime = strtotime($end_date);
        $is_between = $datetime >= $start_datetime && $datetime <= $end_datetime;
        if (!$is_between) return '@this must be greater than or equal to @p1 and less than or equal to @p2';
        return true;
    }

    protected function get_self_method_symbol()
    {
        return self::$method_symbol;
    }
}
