<?php

namespace githusband\Rule;

use Datetime;
use Exception;
use githusband\Exception\MethodException;

/**
 * Class RuleClassDatetime contains multiple methods to validate the data in date or time format
 * 
 * - Methods are required to be public.
 * - Methods are not required to be static.
 * - You can alse set symbols of the methods.
 */
class RuleClassDatetime
{
    public static $method_symbols = [
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
        if ($data === '' || $data === null) {
            if (empty($format)) {
                return "TAG:{$time_type}";
            } else {
                return "TAG:{$time_type}:{$format_position}";
            }
        }

        if (empty($format)) {
            $is_empty_format = true;
            if ($time_type == 'time') {
                $format = 'H:i:s';
            } else if ($time_type == 'date') {
                $format = 'Y-m-d';
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
            
            // Return false for PHP 8.2.0 or later.
            // Otherwise, return an array
            $date_errors = DateTime::getLastErrors();
            if (
                $date_errors === false
                || ($date_errors['warning_count'] === 0 && $date_errors['error_count'] === 0)
            ) {
                /**
                 * Validate the format:
                 * - date methods: Not supports time part(e.g. "H:i:s")
                 * - time methods: Not supports date part(e.g. "Y-m-d")
                 */
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
            $is_empty_format = true;
            if ($time_type == 'time') {
                $format = 'H:i:s';
            }
        }

        if (empty($format)) {
            try {
                $datetime_obj = new Datetime($param);
                return $datetime_obj;
            } catch (Exception $e) {
                throw MethodException::parameter("Parameter {$param} is not a valid {$time_type}");
            }
        } else {
            if (static::is_relative_date_notation($param)) {
                $param = static::convert_relative_date_notation_to_format($param, $format);
            }

            $format_backup = $format;
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
            
            // Return false for PHP 8.2.0 or later.
            // Otherwise, return an array
            $date_errors = DateTime::getLastErrors();
            if (
                $date_errors === false
                || ($date_errors['warning_count'] === 0 && $date_errors['error_count'] === 0)
            ) {
                /**
                 * I don't want to validate the format here, becuase we should have valiated the format in the field data.
                 * @see static::new_datetime_of_data()
                 */
                return $datetime_obj;
            } else {
                if ($time_type == 'time') {
                    if (!empty($is_empty_format)) {
                        throw MethodException::parameter("Parameter {$param} is not a valid {$time_type}");
                    } else {
                        throw MethodException::parameter("Parameter {$param} is not a valid {$time_type} with format {$format_backup}");
                    }
                } else {
                    if ($is_format_optional) {
                        // Try to get the parameter date without format
                        return static::new_datetime_of_parameter($param, '', false, $time_type);
                    } else {
                        throw MethodException::parameter("Parameter {$param} is not a valid {$time_type} with format {$format_backup}");
                    }
                }
            }
        }
    }

    /**
     * Check if it's a relative date notation
     *
     * @see https://www.php.net/manual/en/datetime.formats.php
     * @param string $notation
     * @return bool
     */
    public static function is_relative_date_notation($notation)
    {
        if (preg_match('/midnight|now|noon|tomorrow|back|front|first|this|last|next|ago|second|minute|hour|day|week|month|year/', $notation)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get Datetime from relative date notation
     * 
     * @see https://www.php.net/manual/en/datetime.formats.php
     * @param string $notation Relative Date Notation
     * @param string $format
     * @return Datetime
     * @throws MethodException
     */
    public static function get_datetime_from_relative_date_notation($notation)
    {
        try {
            return new Datetime($notation);
        } catch (Exception $e) {
            throw MethodException::parameter("Notation {$notation} is invalid");
        }
    }

    /**
     * Convert relative date notation into the specified format
     *
     * @return string
     * @throws MethodException
     */
    public static function convert_relative_date_notation_to_format($notation, $format)
    {
        $datetime_obj = static::get_datetime_from_relative_date_notation($notation);
        return $datetime_obj->format($format);
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

        $param_timestamp = static::get_timestamp_of_parameter($param_datetime, $format, true, $time_type);
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

        $param_timestamp = static::get_timestamp_of_parameter($param_datetime, $format, true, $time_type);
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

        $param_timestamp = static::get_timestamp_of_parameter($param_datetime, $format, true, $time_type);
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

        $param_timestamp = static::get_timestamp_of_parameter($param_datetime, $format, true, $time_type);
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

        $param_timestamp = static::get_timestamp_of_parameter($param_datetime, $format, true, $time_type);
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

        $param_timestamp = static::get_timestamp_of_parameter($param_datetime, $format, true, $time_type);
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

        $start_timestamp = static::get_timestamp_of_parameter($start_datetime, $format, true, $time_type);
        $end_timestamp = static::get_timestamp_of_parameter($end_datetime, $format, true, $time_type);

        if ($time_type !== 'time') {
            return $data_timestamp > $start_timestamp && $data_timestamp < $end_timestamp;
        } else {
            if ($start_timestamp <= $end_timestamp) {
                return $data_timestamp > $start_timestamp && $data_timestamp < $end_timestamp;
            } else {
                /** @example 23:00:00 ~ 01:00:00 */
                if ($data_timestamp > $start_timestamp) return true;
                if ($data_timestamp < $end_timestamp) return true;
                return false;
            }
        }
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

        $start_timestamp = static::get_timestamp_of_parameter($start_datetime, $format, true, $time_type);
        $end_timestamp = static::get_timestamp_of_parameter($end_datetime, $format, true, $time_type);

        if ($time_type !== 'time') {
            return $data_timestamp >= $start_timestamp && $data_timestamp < $end_timestamp;
        } else {
            if ($start_timestamp <= $end_timestamp) {
                return $data_timestamp >= $start_timestamp && $data_timestamp < $end_timestamp;
            } else {
                /** @example 23:00:00 ~ 01:00:00 */
                if ($data_timestamp >= $start_timestamp) return true;
                if ($data_timestamp < $end_timestamp) return true;
                return false;
            }
        }
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

        $start_timestamp = static::get_timestamp_of_parameter($start_datetime, $format, true, $time_type);
        $end_timestamp = static::get_timestamp_of_parameter($end_datetime, $format, true, $time_type);

        if ($time_type !== 'time') {
            return $data_timestamp > $start_timestamp && $data_timestamp <= $end_timestamp;
        } else {
            if ($start_timestamp <= $end_timestamp) {
                return $data_timestamp > $start_timestamp && $data_timestamp <= $end_timestamp;
            } else {
                /** @example 23:00:00 ~ 01:00:00 */
                if ($data_timestamp > $start_timestamp) return true;
                if ($data_timestamp <= $end_timestamp) return true;
                return false;
            }
        }
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

        $start_timestamp = static::get_timestamp_of_parameter($start_datetime, $format, true, $time_type);
        $end_timestamp = static::get_timestamp_of_parameter($end_datetime, $format, true, $time_type);

        if ($time_type !== 'time') {
            return $data_timestamp >= $start_timestamp && $data_timestamp <= $end_timestamp;
        } else {
            if ($start_timestamp <= $end_timestamp) {
                return $data_timestamp >= $start_timestamp && $data_timestamp <= $end_timestamp;
            } else {
                /** @example 23:00:00 ~ 01:00:00 */
                if ($data_timestamp >= $start_timestamp) return true;
                if ($data_timestamp <= $end_timestamp) return true;
                return false;
            }
        }
    }

    /**
     * The field data must be a valid date and equal to the parameter date
     *
     * @param string $data
     * @param string $param_date
     * @param string $format
     * @return bool
     * @throws MethodException
     */
    public static function date_equal($data, $param_date, $format = '')
    {
        return static::datetime_equal($data, $param_date, $format, 'date');
    }

    /**
     * The field data must be a valid date and not equal to the parameter date
     *
     * @param string $data
     * @param string $param_date
     * @param string $format
     * @return bool
     * @throws MethodException
     */
    public static function date_not_equal($data, $param_date, $format = '')
    {
        return static::datetime_not_equal($data, $param_date, $format, 'date');
    }

    /**
     * The field data must be a valid date and greater than the parameter date
     *
     * @param string $data
     * @param string $param_date
     * @param string $format
     * @return bool
     * @throws MethodException
     */
    public static function date_greater_than($data, $param_date, $format = '')
    {
        return static::datetime_greater_than($data, $param_date, $format, 'date');
    }

    /**
     * The field data must be a valid date and greater than or equal to the parameter date
     *
     * @param string $data
     * @param string $param_date
     * @param string $format
     * @return bool
     * @throws MethodException
     */
    public static function date_greater_equal($data, $param_date, $format = '')
    {
        return static::datetime_greater_equal($data, $param_date, $format, 'date');
    }

    /**
     * The field data must be a valid date and greater than the parameter date
     *
     * @param string $data
     * @param string $param_date
     * @param string $format
     * @return bool
     * @throws MethodException
     */
    public static function date_less_than($data, $param_date, $format = '')
    {
        return static::datetime_less_than($data, $param_date, $format, 'date');
    }

    /**
     * The field data must be a valid date and less than or equal to the parameter date
     *
     * @param string $data
     * @param string $param_date
     * @param string $format
     * @return bool
     * @throws MethodException
     */
    public static function date_less_equal($data, $param_date, $format = '')
    {
        return static::datetime_less_equal($data, $param_date, $format, 'date');
    }

    /**
     * The field data must be a valid date and greater than the start date and less than the end date
     *
     * @param string $data
     * @param string $start_date
     * @param string $end_date
     * @param string $format
     * @return bool|string
     * @throws MethodException
     */
    public static function date_greater_less($data, $start_date, $end_date, $format = '')
    {
        return static::datetime_greater_less($data, $start_date, $end_date, $format, 'date');
    }

    /**
     * The field data must be a valid date and greater than or euqal to the start date and less than the end date
     *
     * @param string $data
     * @param string $start_date
     * @param string $end_date
     * @param string $format
     * @return bool|string
     * @throws MethodException
     */
    public static function date_greaterequal_less($data, $start_date, $end_date, $format = '')
    {
        return static::datetime_greaterequal_less($data, $start_date, $end_date, $format, 'date');
    }

    /**
     * The field data must be a valid date and greater than the start date and less than or equal to the end date
     *
     * @param string $data
     * @param string $start_date
     * @param string $end_date
     * @param string $format
     * @return bool|string
     * @throws MethodException
     */
    public static function date_greater_lessequal($data, $start_date, $end_date, $format = '')
    {
        return static::datetime_greater_lessequal($data, $start_date, $end_date, $format, 'date');
    }

    /**
     * The field data must be a valid date and between the start date and the end date
     *
     * @param string $data
     * @param string $start_date
     * @param string $end_date
     * @param string $format
     * @return bool|string
     * @throws MethodException
     */
    public static function date_between($data, $start_date, $end_date, $format = '')
    {
        return static::datetime_between($data, $start_date, $end_date, $format, 'date');
    }

    /**
     * The field data must be a valid time and equal to the parameter time
     *
     * @param string $data
     * @param string $param_time
     * @param string $format
     * @return bool
     * @throws MethodException
     */
    public static function time_equal($data, $param_time, $format = '')
    {
        return static::datetime_equal($data, $param_time, $format, 'time');
    }

    /**
     * The field data must be a valid time and not equal to the parameter time
     *
     * @param string $data
     * @param string $param_time
     * @param string $format
     * @return bool
     * @throws MethodException
     */
    public static function time_not_equal($data, $param_time, $format = '')
    {
        return static::datetime_not_equal($data, $param_time, $format, 'time');
    }

    /**
     * The field data must be a valid time and greater than the parameter time
     *
     * @param string $data
     * @param string $param_time
     * @param string $format
     * @return bool
     * @throws MethodException
     */
    public static function time_greater_than($data, $param_time, $format = '')
    {
        return static::datetime_greater_than($data, $param_time, $format, 'time');
    }

    /**
     * The field data must be a valid time and greater than or equal to the parameter time
     *
     * @param string $data
     * @param string $param_time
     * @param string $format
     * @return bool
     * @throws MethodException
     */
    public static function time_greater_equal($data, $param_time, $format = '')
    {
        return static::datetime_greater_equal($data, $param_time, $format, 'time');
    }

    /**
     * The field data must be a valid time and greater than the parameter time
     *
     * @param string $data
     * @param string $param_time
     * @param string $format
     * @return bool
     * @throws MethodException
     */
    public static function time_less_than($data, $param_time, $format = '')
    {
        return static::datetime_less_than($data, $param_time, $format, 'time');
    }

    /**
     * The field data must be a valid time and less than or equal to the parameter time
     *
     * @param string $data
     * @param string $param_time
     * @param string $format
     * @return bool
     * @throws MethodException
     */
    public static function time_less_equal($data, $param_time, $format = '')
    {
        return static::datetime_less_equal($data, $param_time, $format, 'time');
    }

    /**
     * The field data must be a valid time and greater than the start time and less than the end time
     *
     * @param string $data
     * @param string $start_time
     * @param string $end_time
     * @param string $format
     * @return bool|string
     * @throws MethodException
     */
    public static function time_greater_less($data, $start_time, $end_time, $format = '')
    {
        return static::datetime_greater_less($data, $start_time, $end_time, $format, 'time');
    }

    /**
     * The field data must be a valid time and greater than or euqal to the start time and less than the end time
     *
     * @param string $data
     * @param string $start_time
     * @param string $end_time
     * @param string $format
     * @return bool|string
     * @throws MethodException
     */
    public static function time_greaterequal_less($data, $start_time, $end_time, $format = '')
    {
        return static::datetime_greaterequal_less($data, $start_time, $end_time, $format, 'time');
    }

    /**
     * The field data must be a valid time and greater than the start time and less than or equal to the end time
     *
     * @param string $data
     * @param string $start_time
     * @param string $end_time
     * @param string $format
     * @return bool|string
     * @throws MethodException
     */
    public static function time_greater_lessequal($data, $start_time, $end_time, $format = '')
    {
        return static::datetime_greater_lessequal($data, $start_time, $end_time, $format, 'time');
    }

    /**
     * The field data must be a valid time and between the start time and the end time
     *
     * @param string $data
     * @param string $start_time
     * @param string $end_time
     * @param string $format
     * @return bool|string
     * @throws MethodException
     */
    public static function time_between($data, $start_time, $end_time, $format = '')
    {
        return static::datetime_between($data, $start_time, $end_time, $format, 'time');
    }
}
