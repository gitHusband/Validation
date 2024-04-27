<?php

namespace githusband\Rule;

use Datetime;
use Exception;
use githusband\Exception\MethodException;

trait RuleDate
{
    protected $method_symbols_of_rule_date = [
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
    ];

    /**
     * Get a Datetime instance of field data
     * The format is optional, but if it's present:
     * - The data must be in the format
     *
     * @param string $data
     * @param string $format
     * @param string $format_position The position of format parameter. Default to '@p1'
     * @return Datetime|string
     */
    public static function new_datetime_of_data($data, $format = '', $format_position = '@p1')
    {
        if (empty($data)) return 'TAG:date';

        if (empty($format)) {
            try {
                $datetime_obj = new Datetime($data);
                return $datetime_obj;
            } catch (Exception $e) {
                return 'TAG:date';
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
            if ($date_errors === false) return $datetime_obj;

            if ($date_errors['warning_count'] === 0 && $date_errors['error_count'] === 0) return $datetime_obj;

            return "TAG:date:format:{$format_position}";
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
     * @return Datetime|string
     * @throws MethodException
     */
    public static function new_datetime_of_parameter($param, $format = '', $is_format_optional = true)
    {
        if (empty($format)) {
            try {
                $datetime_obj = new Datetime($param);
                return $datetime_obj;
            } catch (Exception $e) {
                throw MethodException::parameter("Parameter {$param} is not a valid date");
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
                return static::new_datetime_of_parameter($param, '');
            } else {
                throw MethodException::parameter("Parameter {$param} is not a valid date with format {$format}");
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
     * @return int
     * @throws MethodException
     */
    public static function get_timestamp_of_parameter($param, $format = '', $is_format_optional = true)
    {
        return static::new_datetime_of_parameter($param, $format, $is_format_optional)->getTimestamp();
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
        $datetime_obj = static::new_datetime_of_data($data, $format);
        if ($datetime_obj instanceof Datetime) return true;
        else return $datetime_obj;
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
        $data_datetime_obj = static::new_datetime_of_data($data, $format, '@p2');
        if (!($data_datetime_obj instanceof Datetime)) return $data_datetime_obj;
        $data_timestamp = $data_datetime_obj->getTimestamp();

        $param_timestamp = static::get_timestamp_of_parameter($param_date, $format);
        return $data_timestamp == $param_timestamp;
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
        $data_datetime_obj = static::new_datetime_of_data($data, $format, '@p2');
        if (!($data_datetime_obj instanceof Datetime)) return $data_datetime_obj;
        $data_timestamp = $data_datetime_obj->getTimestamp();

        $param_timestamp = static::get_timestamp_of_parameter($param_date, $format);
        return $data_timestamp != $param_timestamp;
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
        $data_datetime_obj = static::new_datetime_of_data($data, $format, '@p2');
        if (!($data_datetime_obj instanceof Datetime)) return $data_datetime_obj;
        $data_timestamp = $data_datetime_obj->getTimestamp();

        $param_timestamp = static::get_timestamp_of_parameter($param_date, $format);
        return $data_timestamp > $param_timestamp;
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
        $data_datetime_obj = static::new_datetime_of_data($data, $format, '@p2');
        if (!($data_datetime_obj instanceof Datetime)) return $data_datetime_obj;
        $data_timestamp = $data_datetime_obj->getTimestamp();

        $param_timestamp = static::get_timestamp_of_parameter($param_date, $format);
        return $data_timestamp >= $param_timestamp;
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
        $data_datetime_obj = static::new_datetime_of_data($data, $format, '@p2');
        if (!($data_datetime_obj instanceof Datetime)) return $data_datetime_obj;
        $data_timestamp = $data_datetime_obj->getTimestamp();

        $param_timestamp = static::get_timestamp_of_parameter($param_date, $format);
        return $data_timestamp < $param_timestamp;
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
        $data_datetime_obj = static::new_datetime_of_data($data, $format, '@p2');
        if (!($data_datetime_obj instanceof Datetime)) return $data_datetime_obj;
        $data_timestamp = $data_datetime_obj->getTimestamp();

        $param_timestamp = static::get_timestamp_of_parameter($param_date, $format);
        return $data_timestamp <= $param_timestamp;
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
        $data_datetime_obj = static::new_datetime_of_data($data, $format, '@p3');
        if (!($data_datetime_obj instanceof Datetime)) return $data_datetime_obj;
        $data_timestamp = $data_datetime_obj->getTimestamp();

        $start_timestamp = static::get_timestamp_of_parameter($start_date, $format);
        $end_timestamp = static::get_timestamp_of_parameter($end_date, $format);
        return $data_timestamp > $start_timestamp && $data_timestamp < $end_timestamp;
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
        $data_datetime_obj = static::new_datetime_of_data($data, $format, '@p3');
        if (!($data_datetime_obj instanceof Datetime)) return $data_datetime_obj;
        $data_timestamp = $data_datetime_obj->getTimestamp();

        $start_timestamp = static::get_timestamp_of_parameter($start_date, $format);
        $end_timestamp = static::get_timestamp_of_parameter($end_date, $format);
        return $data_timestamp >= $start_timestamp && $data_timestamp < $end_timestamp;
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
        $data_datetime_obj = static::new_datetime_of_data($data, $format, '@p3');
        if (!($data_datetime_obj instanceof Datetime)) return $data_datetime_obj;
        $data_timestamp = $data_datetime_obj->getTimestamp();

        $start_timestamp = static::get_timestamp_of_parameter($start_date, $format);
        $end_timestamp = static::get_timestamp_of_parameter($end_date, $format);
        return $data_timestamp > $start_timestamp && $data_timestamp <= $end_timestamp;
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
        $data_datetime_obj = static::new_datetime_of_data($data, $format, '@p3');
        if (!($data_datetime_obj instanceof Datetime)) return $data_datetime_obj;
        $data_timestamp = $data_datetime_obj->getTimestamp();

        $start_timestamp = static::get_timestamp_of_parameter($start_date, $format);
        $end_timestamp = static::get_timestamp_of_parameter($end_date, $format);
        return $data_timestamp >= $start_timestamp && $data_timestamp <= $end_timestamp;
    }
}
