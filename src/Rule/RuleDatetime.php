<?php

namespace githusband\Rule;

use Datetime;
use Exception;
use githusband\Exception\MethodException;

trait RuleDatetime
{
    protected $method_symbols_of_rule_datetime = [
        // Datetime
        'datetime' => 'is_datetime',
        'datetime=' => 'datetime_equal',
        'datetime!=' => 'datetime_not_equal',
        'datetime>' => 'datetime_greater_than',
        'datetime>=' => 'datetime_greater_equal',
        'datetime<' => 'datetime_less_than',
        'datetime<=' => 'datetime_less_equal',
        'datetime><' => 'datetime_greater_less',
        'datetime><=' => 'datetime_greater_lessequal',
        'datetime>=<' => 'datetime_greaterequal_less',
        'datetime>=<=' => 'datetime_between',
        // Date
        'date' => 'is_date',
        'date=' => 'date_equal',
        'date!=' => 'date_not_equal',
        'date>' => 'date_greater_than',
        'date>=' => 'date_greater_equal',
        'date<' => 'date_less_than',
        'date<=' => 'date_less_equal',
        'date><' => 'date_greater_less',
        'date><=' => 'date_greater_lessequal',
        'date>=<' => 'date_greaterequal_less',
        'date>=<=' => 'date_between',
        // Time
        'time' => 'is_time',
        'time=' => 'time_equal',
        'time!=' => 'time_not_equal',
        'time>' => 'time_greater_than',
        'time>=' => 'time_greater_equal',
        'time<' => 'time_less_than',
        'time<=' => 'time_less_equal',
        'time><' => 'time_greater_less',
        'time><=' => 'time_greater_lessequal',
        'time>=<' => 'time_greaterequal_less',
        'time>=<=' => 'time_between',
    ];

    /**
     * Get a Datetime instance of field data
     * The format is optional, but if it's present:
     * - The data must be in the format
     *
     * @param string $data
     * @param string $format
     * @param string $format_position The position of format parameter. Default to '@p1'
     * @param string $time_type The time type that helps to validate the format. e.g. date, time, datetime
     * @return Datetime|string
     */
    public static function new_datetime_of_data($data, $format = '', $format_position = '@p1', $time_type = 'datetime')
    {
        if (empty($data)) return "TAG:{$time_type}";

        if (empty($format)) {
            $is_empty_format = true;
            if ($time_type == 'time') {
                $format = 'H:i:s';
            }
        }

        if (empty($format)) {
            try {
                $datetime_obj = new Datetime($data);
                return $datetime_obj;
            } catch (Exception $e) {
                return "TAG:{$time_type}";
            }
        } else {
            if (defined('DateTime::' . $format)) {
                $format = constant('DateTime::' . $format);
            } else {
                /**
                 * "!": Resets all fields (year, month, day, hour, minute, second, fraction and timezone information) to zero-like values ( 0 for hour, minute, second and fraction, 1 for month and day, 1970 for year and UTC for timezone information)
                 * Without !, all fields will be set to the current date and time.
                 * @link https://www.php.net/manual/en/datetimeimmutable.createfromformat.php
                 */
                $format = '!' . $format;
            }
            $datetime_obj = Datetime::createFromFormat($format, $data);
            
            $date_errors = DateTime::getLastErrors();
            // Return false for PHP 8.2.0 or later.
            if (
                $date_errors === false
                || ($date_errors['warning_count'] === 0 && $date_errors['error_count'] === 0)
            ) {
                if ($time_type == 'date') {
                    $time = $datetime_obj->format('H:i:s');
                    if ($time != '00:00:00') return "TAG:{$time_type}:invalid_format:{$format_position}";
                } else if ($time_type == 'time') {
                    $date = $datetime_obj->format('Y-m-d');
                    if ($date != '1970-01-01') {
                        if (!empty($is_empty_format)) {
                            return "TAG:{$time_type}";
                        } else {
                            return "TAG:{$time_type}:invalid_format:{$format_position}";
                        }
                    }
                }
                
                return $datetime_obj;
            } else {
                if (!empty($is_empty_format)) {
                    return "TAG:{$time_type}";
                } else {
                    return "TAG:{$time_type}:format:{$format_position}";
                }
            }
        }
    }

    /**
     * Get a Datetime instance of parameter of rule
     * The format is optional, but if it's present:
     * - The parameter must be in the format
     * - Or the parameter must be a valid date without any format
     *
     * @param string $param parameter defined in the ruleset
     * @param string $format Generally, the format is the format of field data.
     * @param bool $is_format_optional If true, we don't require the parameters are defined in the format of field data.
     * @param string $time_type The time type that helps to validate the format. e.g. date, time, datetime
     * @return Datetime|string
     * @throws MethodException
     */
    public static function new_datetime_of_parameter($param, $format = '', $is_format_optional = true, $time_type = 'datetime')
    {
        if (empty($format)) {
            try {
                $datetime_obj = new Datetime($param);
                return $datetime_obj;
            } catch (Exception $e) {
                throw MethodException::parameter("Parameter {$param} is not a valid {$time_type}");
            }
        } else {
            if (defined('DateTime::' . $format)) {
                $format = constant('DateTime::' . $format);
            } else {
                /**
                 * "!": Resets all fields (year, month, day, hour, minute, second, fraction and timezone information) to zero-like values ( 0 for hour, minute, second and fraction, 1 for month and day, 1970 for year and UTC for timezone information)
                 * Without !, all fields will be set to the current date and time.
                 * @link https://www.php.net/manual/en/datetimeimmutable.createfromformat.php
                 */
                $format = '!' . $format;
            }
            $datetime_obj = Datetime::createFromFormat($format, $param);
            
            $date_errors = DateTime::getLastErrors();
            // Return false for PHP 8.2.0 or later.
            if ($date_errors === false) return $datetime_obj;
            // Otherwise, return array
            if ($date_errors['warning_count'] === 0 && $date_errors['error_count'] === 0) return $datetime_obj;

            if ($is_format_optional) {
                // Try to get the parameter date without format
                return static::new_datetime_of_parameter($param, '', false, $time_type);
            } else {
                throw MethodException::parameter("Parameter {$param} is not a valid {$time_type} with format {$format}");
            }
        }
    }

    /**
     * Get timestamp of parameter of rule
     *
     * @see static::new_datetime_of_parameter
     * @param string $param
     * @param string $format
     * @param bool $is_format_optional
     * @param string $time_type
     * @return int
     * @throws MethodException
     */
    public static function get_timestamp_of_parameter($param, $format = '', $is_format_optional = true, $time_type = 'datetime')
    {
        return static::new_datetime_of_parameter($param, $format, $is_format_optional, $time_type)->getTimestamp();
    }

    /**
     * The field data must be a valid datetime
     *
     * @param string $data
     * @param string $format
     * @param string $time_type
     * @return bool|string
     */
    public static function is_datetime($data, $format = '', $time_type = 'datetime')
    {
        $datetime_obj = static::new_datetime_of_data($data, $format, '@p1', $time_type);
        if ($datetime_obj instanceof Datetime) return true;
        else return $datetime_obj;
    }

    /**
     * The field data must be a valid date
     *
     * @param string $data
     * @param string $format
     * @return bool|string
     */
    public static function is_date($data, $format = '')
    {
        return static::is_datetime($data, $format, 'date');
    }

    /**
     * The field data must be a valid time
     *
     * @param string $data
     * @param string $format
     * @return bool|string
     */
    public static function is_time($data, $format = '')
    {
        return static::is_datetime($data, $format, 'time');
    }

    /**
     * The field data must be a valid datetime and equal to the parameter datetime
     *
     * @param string $data
     * @param string $param_datetime
     * @param string $format
     * @param string $time_type
     * @return bool
     * @throws MethodException
     */
    public static function datetime_equal($data, $param_datetime, $format = '', $time_type = 'datetime')
    {
        $data_datetime_obj = static::new_datetime_of_data($data, $format, '@p2', $time_type);
        if (!($data_datetime_obj instanceof Datetime)) return $data_datetime_obj;
        $data_timestamp = $data_datetime_obj->getTimestamp();

        $param_timestamp = static::get_timestamp_of_parameter($param_datetime, $format, $time_type);
        return $data_timestamp == $param_timestamp;
    }

    /**
     * The field data must be a valid datetime and not equal to the parameter datetime
     *
     * @param string $data
     * @param string $param_datetime
     * @param string $format
     * @param string $time_type
     * @return bool
     * @throws MethodException
     */
    public static function datetime_not_equal($data, $param_datetime, $format = '', $time_type = 'datetime')
    {
        $data_datetime_obj = static::new_datetime_of_data($data, $format, '@p2', $time_type);
        if (!($data_datetime_obj instanceof Datetime)) return $data_datetime_obj;
        $data_timestamp = $data_datetime_obj->getTimestamp();

        $param_timestamp = static::get_timestamp_of_parameter($param_datetime, $format, $time_type);
        return $data_timestamp != $param_timestamp;
    }

    /**
     * The field data must be a valid datetime and greater than the parameter datetime
     *
     * @param string $data
     * @param string $param_datetime
     * @param string $format
     * @param string $time_type
     * @return bool
     * @throws MethodException
     */
    public static function datetime_greater_than($data, $param_datetime, $format = '', $time_type = 'datetime')
    {
        $data_datetime_obj = static::new_datetime_of_data($data, $format, '@p2', $time_type);
        if (!($data_datetime_obj instanceof Datetime)) return $data_datetime_obj;
        $data_timestamp = $data_datetime_obj->getTimestamp();

        $param_timestamp = static::get_timestamp_of_parameter($param_datetime, $format, $time_type);
        return $data_timestamp > $param_timestamp;
    }

    /**
     * The field data must be a valid datetime and greater than or equal to the parameter datetime
     *
     * @param string $data
     * @param string $param_datetime
     * @param string $format
     * @param string $time_type
     * @return bool
     * @throws MethodException
     */
    public static function datetime_greater_equal($data, $param_datetime, $format = '', $time_type = 'datetime')
    {
        $data_datetime_obj = static::new_datetime_of_data($data, $format, '@p2', $time_type);
        if (!($data_datetime_obj instanceof Datetime)) return $data_datetime_obj;
        $data_timestamp = $data_datetime_obj->getTimestamp();

        $param_timestamp = static::get_timestamp_of_parameter($param_datetime, $format, $time_type);
        return $data_timestamp >= $param_timestamp;
    }

    /**
     * The field data must be a valid datetime and greater than the parameter datetime
     *
     * @param string $data
     * @param string $param_datetime
     * @param string $format
     * @param string $time_type
     * @return bool
     * @throws MethodException
     */
    public static function datetime_less_than($data, $param_datetime, $format = '', $time_type = 'datetime')
    {
        $data_datetime_obj = static::new_datetime_of_data($data, $format, '@p2', $time_type);
        if (!($data_datetime_obj instanceof Datetime)) return $data_datetime_obj;
        $data_timestamp = $data_datetime_obj->getTimestamp();

        $param_timestamp = static::get_timestamp_of_parameter($param_datetime, $format, $time_type);
        return $data_timestamp < $param_timestamp;
    }

    /**
     * The field data must be a valid datetime and less than or equal to the parameter datetime
     *
     * @param string $data
     * @param string $param_datetime
     * @param string $format
     * @param string $time_type
     * @return bool
     * @throws MethodException
     */
    public static function datetime_less_equal($data, $param_datetime, $format = '', $time_type = 'datetime')
    {
        $data_datetime_obj = static::new_datetime_of_data($data, $format, '@p2', $time_type);
        if (!($data_datetime_obj instanceof Datetime)) return $data_datetime_obj;
        $data_timestamp = $data_datetime_obj->getTimestamp();

        $param_timestamp = static::get_timestamp_of_parameter($param_datetime, $format, $time_type);
        return $data_timestamp <= $param_timestamp;
    }

    /**
     * The field data must be a valid datetime and greater than the start datetime and less than the end datetime
     *
     * @param string $data
     * @param string $start_datetime
     * @param string $end_datetime
     * @param string $format
     * @param string $time_type
     * @return bool|string
     * @throws MethodException
     */
    public static function datetime_greater_less($data, $start_datetime, $end_datetime, $format = '', $time_type = 'datetime')
    {
        $data_datetime_obj = static::new_datetime_of_data($data, $format, '@p3', $time_type);
        if (!($data_datetime_obj instanceof Datetime)) return $data_datetime_obj;
        $data_timestamp = $data_datetime_obj->getTimestamp();

        $start_timestamp = static::get_timestamp_of_parameter($start_datetime, $format, $time_type);
        $end_timestamp = static::get_timestamp_of_parameter($end_datetime, $format, $time_type);
        return $data_timestamp > $start_timestamp && $data_timestamp < $end_timestamp;
    }

    /**
     * The field data must be a valid datetime and greater than or euqal to the start datetime and less than the end datetime
     *
     * @param string $data
     * @param string $start_datetime
     * @param string $end_datetime
     * @param string $format
     * @param string $time_type
     * @return bool|string
     * @throws MethodException
     */
    public static function datetime_greaterequal_less($data, $start_datetime, $end_datetime, $format = '', $time_type = 'datetime')
    {
        $data_datetime_obj = static::new_datetime_of_data($data, $format, '@p3', $time_type);
        if (!($data_datetime_obj instanceof Datetime)) return $data_datetime_obj;
        $data_timestamp = $data_datetime_obj->getTimestamp();

        $start_timestamp = static::get_timestamp_of_parameter($start_datetime, $format, $time_type);
        $end_timestamp = static::get_timestamp_of_parameter($end_datetime, $format, $time_type);
        return $data_timestamp >= $start_timestamp && $data_timestamp < $end_timestamp;
    }

    /**
     * The field data must be a valid datetime and greater than the start datetime and less than or equal to the end datetime
     *
     * @param string $data
     * @param string $start_datetime
     * @param string $end_datetime
     * @param string $format
     * @param string $time_type
     * @return bool|string
     * @throws MethodException
     */
    public static function datetime_greater_lessequal($data, $start_datetime, $end_datetime, $format = '', $time_type = 'datetime')
    {
        $data_datetime_obj = static::new_datetime_of_data($data, $format, '@p3', $time_type);
        if (!($data_datetime_obj instanceof Datetime)) return $data_datetime_obj;
        $data_timestamp = $data_datetime_obj->getTimestamp();

        $start_timestamp = static::get_timestamp_of_parameter($start_datetime, $format, $time_type);
        $end_timestamp = static::get_timestamp_of_parameter($end_datetime, $format, $time_type);
        return $data_timestamp > $start_timestamp && $data_timestamp <= $end_timestamp;
    }

    /**
     * The field data must be a valid datetime and between the start datetime and the end datetime
     *
     * @param string $data
     * @param string $start_datetime
     * @param string $end_datetime
     * @param string $format
     * @param string $time_type
     * @return bool|string
     * @throws MethodException
     */
    public static function datetime_between($data, $start_datetime, $end_datetime, $format = '', $time_type = 'datetime')
    {
        $data_datetime_obj = static::new_datetime_of_data($data, $format, '@p3', $time_type);
        if (!($data_datetime_obj instanceof Datetime)) return $data_datetime_obj;
        $data_timestamp = $data_datetime_obj->getTimestamp();

        $start_timestamp = static::get_timestamp_of_parameter($start_datetime, $format, $time_type);
        $end_timestamp = static::get_timestamp_of_parameter($end_datetime, $format, $time_type);
        return $data_timestamp >= $start_timestamp && $data_timestamp <= $end_timestamp;
    }
}
