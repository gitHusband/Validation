<?php

namespace githusband\Rule;

trait RuleDefault
{
    /**
     * The method symbols of rule default.
     *
     * @var array
     */
    protected $method_symbols_of_rule_default = [
        '=' => 'equal',
        '!=' => 'not_equal',
        '==' => 'strictly_equal',
        '!==' => 'not_strictly_equal',
        '>' => 'greater_than',
        '<' => 'less_than',
        '>=' => 'greater_than_equal',
        '<=' => 'less_than_equal',
        '><' => 'between',
        '><=' => 'greater_lessequal',
        '>=<' => 'greaterequal_less',
        '>=<=' => 'greaterequal_lessequal',
        '<number>' => 'in_number_array',
        '!<number>' => 'not_in_number_array',
        '<string>' => 'in_string_array',
        '!<string>' => 'not_in_string_array',
        'length=' => 'length_equal',
        'length!=' => 'length_not_equal',
        'length>' => 'length_greater_than',
        'length<' => 'length_less_than',
        'length>=' => 'length_greater_than_equal',
        'length<=' => 'length_less_than_equal',
        'length><' => 'length_between',
        'length><=' => 'length_greater_lessequal',
        'length>=<' => 'length_greaterequal_less',
        'length>=<=' => 'length_greaterequal_lessequal',
        'int' => 'integer',
        'array' => 'is_array',    // native function
        'bool=' => 'bool_equal',
        'bool_string=' => 'bool_string_equal',
    ];

    /**
     * The old method symbols of rule default that are deprecated.
     * 
     * @deprecated 2.3.0
     * @var array
     */
    protected $deprecated_method_symbols_of_rule_default = [
        // '=' => 'equal',
        // '!=' => 'not_equal',
        // '==' => 'strictly_equal',
        // '!==' => 'not_strictly_equal',
        // '>' => 'greater_than',
        // '<' => 'less_than',
        // '>=' => 'greater_than_equal',
        // '<=' => 'less_than_equal',
        '<>' => 'between',
        '<=>' => 'greater_lessequal',
        '<>=' => 'greaterequal_less',
        '<=>=' => 'greaterequal_lessequal',
        '(n)' => 'in_number_array',
        '!(n)' => 'not_in_number_array',
        '(s)' => 'in_string_array',
        '!(s)' => 'not_in_string_array',
        'len=' => 'length_equal',
        'len!=' => 'length_not_equal',
        'len>' => 'length_greater_than',
        'len<' => 'length_less_than',
        'len>=' => 'length_greater_than_equal',
        'len<=' => 'length_less_than_equal',
        'len<>' => 'length_between',
        'len<=>' => 'length_greater_lessequal',
        'len<>=' => 'length_greaterequal_less',
        'len<=>=' => 'length_greaterequal_lessequal',
        // 'int' => 'integer',
        'arr' => 'is_array',    // native function
        // 'bool=' => 'bool_equal',
        'bool_str=' => 'bool_string_equal',
    ];

    /**
     * When is_strict_parameter_type = false, and the methods require strict parameters type
     * 
     * @deprecated 2.4.0
     * @see static::config['is_strict_parameter_type']
     * @var array
     */
    protected $strict_methods_of_rule_default = [
        'strictly_equal',
        'not_strictly_equal'
    ];

    public static function string_length($string)
    {
        if (!static::string($string)) return -1;
        return mb_strlen($string);
    }

    public static function required($data)
    {
        return $data === 0 || $data === 0.0 || $data === 0.00 || $data === '0' || $data === '0.0' || $data === '0.00' || $data === false || !empty($data);
    }

    public static function equal($data, $param)
    {
        return $data == $param;
    }

    public static function not_equal($data, $param)
    {
        return $data != $param;
    }

    public static function strictly_equal($data, $param)
    {
        return $data === $param;
    }

    public static function not_strictly_equal($data, $param)
    {
        return $data !== $param;
    }

    public static function greater_than($data, $param)
    {
        return is_numeric($data) && $data > $param;
    }

    public static function less_than($data, $param)
    {
        return is_numeric($data) && $data < $param;
    }

    public static function greater_than_equal($data, $param)
    {
        return is_numeric($data) && $data >= $param;
    }

    public static function less_than_equal($data, $param)
    {
        return is_numeric($data) && $data <= $param;
    }

    public static function between($data, $param1, $param2)
    {
        return is_numeric($data) && $data > $param1 && $data < $param2;
    }

    public static function greater_lessequal($data, $param1, $param2)
    {
        return is_numeric($data) && $data > $param1 && $data <= $param2;
    }

    public static function greaterequal_less($data, $param1, $param2)
    {
        return is_numeric($data) && $data >= $param1 && $data < $param2;
    }

    public static function greaterequal_lessequal($data, $param1, $param2)
    {
        return is_numeric($data) && $data >= $param1 && $data <= $param2;
    }

    public static function in_number_array($data, $param)
    {
        return is_numeric($data) && in_array($data, $param);
    }

    public static function not_in_number_array($data, $param)
    {
        return is_numeric($data) && !in_array($data, $param);
    }

    public static function in_string_array($data, $param)
    {
        return is_string($data) && in_array($data, $param);
    }

    public static function not_in_string_array($data, $param)
    {
        return is_string($data) && !in_array($data, $param);
    }

    public static function length_equal($data, $param)
    {
        if (!static::string($data) && is_numeric($data)) $data = (string)$data;

        $data_len = static::string_length($data);
        return $data_len == $param;
    }

    public static function length_not_equal($data, $param)
    {
        if (!static::string($data) && is_numeric($data)) $data = (string)$data;

        $data_len = static::string_length($data);
        return $data_len != $param;
    }

    public static function length_greater_than($data, $param)
    {
        if (!static::string($data) && is_numeric($data)) $data = (string)$data;

        $data_len = static::string_length($data);
        return $data_len > $param;
    }

    public static function length_less_than($data, $param)
    {
        if (!static::string($data) && is_numeric($data)) $data = (string)$data;

        $data_len = static::string_length($data);
        return $data_len < $param;
    }

    public static function length_greater_than_equal($data, $param)
    {
        if (!static::string($data) && is_numeric($data)) $data = (string)$data;

        $data_len = static::string_length($data);
        return $data_len >= $param;
    }

    public static function length_less_than_equal($data, $param)
    {
        if (!static::string($data) && is_numeric($data)) $data = (string)$data;

        $data_len = static::string_length($data);
        return $data_len <= $param;
    }

    public static function length_between($data, $param1, $param2)
    {
        if (!static::string($data) && is_numeric($data)) $data = (string)$data;

        $data_len = static::string_length($data);
        return $data_len > $param1 && $data_len < $param2;
    }

    public static function length_greater_lessequal($data, $param1, $param2)
    {
        if (!static::string($data) && is_numeric($data)) $data = (string)$data;

        $data_len = static::string_length($data);
        return $data_len > $param1 && $data_len <= $param2;
    }

    public static function length_greaterequal_less($data, $param1, $param2)
    {
        if (!static::string($data) && is_numeric($data)) $data = (string)$data;

        $data_len = static::string_length($data);
        return $data_len >= $param1 && $data_len < $param2;
    }

    public static function length_greaterequal_lessequal($data, $param1, $param2)
    {
        if (!static::string($data) && is_numeric($data)) $data = (string)$data;

        $data_len = static::string_length($data);
        return $data_len >= $param1 && $data_len <= $param2;
    }

    public static function integer($data)
    {
        return is_int($data);
    }

    public static function float($data)
    {
        return is_float($data);
    }

    public static function string($data)
    {
        return is_string($data);
    }

    public static function bool($data, $bool = '')
    {
        $bool = strtolower($bool);
        if ($data === true || $data === false) {
            if ($bool === '') return true;
            if ($data === true && $bool === 'true') {
                return true;
            } else if ($data === false && $bool === 'false') {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function bool_equal($data, $bool = '')
    {
        return static::bool($data, $bool);
    }

    /**
     * Check if it's a bool string
     * 
     * @deprecated 2.3.0
     * @param mixed $data
     * @param string $bool
     * @return bool
     */
    public static function bool_str($data, $bool = '')
    {
        return static::bool_string($data, $bool);
    }

    public static function bool_string($data, $bool = '')
    {
        if (!is_string($data)) return false;
        $data = strtolower($data);
        if ($data === "true" || $data === "false") {
            if ($bool === '') return true;
            if ($data === $bool) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function bool_string_equal($data, $bool)
    {
        return static::bool_string($data, $bool);
    }

    public static function email($data)
    {
        if (empty($data)) return false;
        if (!preg_match('/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,})$/', $data)) {
            return false;
        } else {
            return true;
        }
    }

    public static function url($data)
    {
        if (empty($data)) return false;
        if (!preg_match('/^http(s?):\/\/(?:[A-za-z0-9-]+\.)+[A-za-z]{2,8}(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?$/', $data)) {
            return false;
        } else {
            return true;
        }
    }

    public static function ip($data)
    {
        if (empty($data) || !is_string($data)) return false;
        if (filter_var($data, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return true;
        } else {
            return false;
        }
    }

    public static function mac($data)
    {
        if (empty($data) || !is_string($data)) return false;
        if (filter_var($data, FILTER_VALIDATE_MAC)) {
            return true;
        } else {
            return false;
        }
    }

    // date of birth
    public static function dob($date)
    {
        if (empty($date) || !is_string($date)) return false;
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $arr)) {
            $datetime = strtotime($date);
            $now = time();
            if (checkdate($arr[2], $arr[3], $arr[1]) && $datetime < $now) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function file_base64_size($file_base64)
    {
        $file_base64 = preg_replace('/^(data:\s*(\w+\/\w+);base64,)/', '', $file_base64);
        $file_base64 = str_replace('=', '', $file_base64);
        $file_len = strlen($file_base64);
        $file_size = $file_len - ($file_len / 8) * 2;

        $file_size = round(($file_size / 1024), 2);

        return $file_size;
    }

    /**
     * Check if the data is a file base64 string
     * e.g. data:image/jpeg;base64,{base64}
     *
     * @param string $file_base64
     * @param string $mime
     * @param int|string $max_size File size, its unit is kb
     * @return void
     */
    public static function file_base64($file_base64, $mime = '', $max_size = null)
    {
        if (empty($file_base64) || !is_string($file_base64)) return false;
        if (preg_match('/^(data:\s*(\w+\/\w+);base64,)/', $file_base64, $matches)) {
            $file_mime = $matches[2];
            if (!empty($mime) && $mime != $file_mime) {
                return false;
            }

            if (!empty($max_size)) {
                $file_base64 = str_replace($matches[1], '', $file_base64);
                $file_size = static::file_base64_size($file_base64);
                if ($file_size > $max_size) {
                    return false;
                }
            }
        } else {
            return false;
        }

        return true;
    }

    public static function uuid($data)
    {
        if (empty($data) || !is_string($data)) return false;
        if (preg_match('/^[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}$/', $data)) {
            return true;
        } else {
            return false;
        }
    }

    public static function oauth2_grant_type($data)
    {
        if (empty($data) || !is_string($data)) return false;

        $oauth2_grant_types = ['authorization_code', 'password', 'client_credentials'];
        if (in_array($data, $oauth2_grant_types)) {
            return true;
        } else {
            return false;
        }
    }
}
