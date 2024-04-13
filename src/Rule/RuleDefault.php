<?php

namespace githusband\Rule;

trait RuleDefault
{
    protected $method_symbols_of_rule_default = [
        '=' => 'equal',
        '!=' => 'not_equal',
        '==' => 'strictly_equal',
        '!==' => 'not_strictly_equal',
        '>' => 'greater_than',
        '<' => 'less_than',
        '>=' => 'greater_than_equal',
        '<=' => 'less_than_equal',
        '<>' => 'between',
        '<=>' => 'greater_lessequal',
        '<>=' => 'greaterequal_less',
        '<=>=' => 'greaterequal_lessequal',
        '(n)' => 'in_number',
        '!(n)' => 'not_in_number',
        '(s)' => 'in_string',
        '!(s)' => 'not_in_string',
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
        'int' => 'integer',
        'arr' => 'is_array',    // native function
        'bool=' => 'bool_equal',
        'bool_str=' => 'bool_str_equal',
    ];

    public static function string_length($string)
    {
        if (!static::string($string)) return -1;
        return mb_strlen($string);
    }

    public static function parse_strict_data_type($data)
    {
        if (!is_string($data)) return $data;

        if (preg_match('/^[\'"](.*)[\'"]$/', $data, $matches)) {
            $data = $matches[1];
        } else if (preg_match('/^-?\d+$/', $data)) {
            $data = (int) $data;
        } else if (preg_match('/^-?\d+\.\d+$/', $data)) {
            $data = (float) $data;
        } else if (static::bool_str($data)) {
            $data = in_array($data, ['true', 'TRUE']);
        } else if (preg_match('/^[\[\{].*[\]\}]$/', $data)) {
            $data = json_decode($data);
        }
        return $data;
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
        $param = static::parse_strict_data_type($param);
        return $data === $param;
    }

    public static function not_strictly_equal($data, $param)
    {
        $param = static::parse_strict_data_type($param);
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

    public static function in_number($data, $param)
    {
        return is_numeric($data) && in_array($data, $param);
    }

    public static function not_in_number($data, $param)
    {
        return is_numeric($data) && !in_array($data, $param);
    }

    public static function in_string($data, $param)
    {
        return is_string($data) && in_array($data, $param);
    }

    public static function not_in_string($data, $param)
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
            if ($bool === '') return TRUE;
            if ($data === true && $bool === 'true') {
                return TRUE;
            } else if ($data === false && $bool === 'false') {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    public static function bool_equal($data, $bool = '')
    {
        return static::bool($data, $bool);
    }

    public static function bool_str($data, $bool = '')
    {
        $data = strtolower($data);
        if ($data === "true" || $data === "false") {
            if ($bool === '') return TRUE;
            if ($data === $bool) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    public static function bool_str_equal($data, $bool)
    {
        return static::bool_str($data, $bool);
    }

    public static function email($data)
    {
        if (!empty($data) && !preg_match('/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,})$/', $data)) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public static function url($data)
    {
        if (!empty($data) && !preg_match('/^http(s?):\/\/(?:[A-za-z0-9-]+\.)+[A-za-z]{2,8}(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?$/', $data)) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public static function ip($data)
    {
        if (!empty($data) && !preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $data)) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public static function mac($data)
    {
        if (!empty($data) && !preg_match('/^((([a-f0-9]{2}:){5})|(([a-f0-9]{2}-){5})|(([a-f0-9]{2} ){5}))[a-f0-9]{2}$/i', $data)) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    // date of birth
    public static function dob($date)
    {
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

    public static function file_base64($file_base64, $mime = false, $max_size = false)
    {
        if (preg_match('/^(data:\s*(\w+\/\w+);base64,)/', $file_base64, $matches)) {
            $file_mime = $matches[2];
            if ($mime !== false && $mime != $file_mime) {
                return false;
            }

            if ($max_size !== false) {
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
        if (!empty($data) && preg_match('/^[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}$/', $data)) {
            return true;
        } else {
            return false;
        }
    }

    public static function oauth2_grant_type($data)
    {
        $oauth2_grant_types = ['authorization_code', 'password', 'client_credentials'];

        if (in_array($data, $oauth2_grant_types)) {
            return true;
        } else {
            return false;
        }
    }
}
