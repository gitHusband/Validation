<?php

namespace githusband\Test\Extend\Rule;

trait RuleDate
{
    protected $method_symbol_of_rule_date = array(
        'date<>' => 'date_between',
    );

    protected function is_date($date, $format = '')
    {
        $time = strtotime($date);
        if (!$time) return '@this is not a date';

        if (!empty($format)) {
            if (date($format, $time) != $date) return '@this is not a date2';
        }

        return true;
    }

    protected function date_between($date, $start_date, $end_date)
    {
        $is_date = $this->is_date($date);
        if ($is_date !== true) return $is_date;

        $datetime = strtotime($date);
        $start_datetime = strtotime($start_date);
        $end_datetime = strtotime($end_date);
        $is_between = $datetime > $start_datetime && $datetime < $end_datetime;
        if (!$is_between) return '@this is not between @p1 and @p2';
        return true;
    }
}
