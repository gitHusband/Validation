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
        '>=' => 'greater_equal',
        '<=' => 'less_equal',
        '><' => 'greater_less',
        '><=' => 'greater_lessequal',
        '>=<' => 'greaterequal_less',
        '>=<=' => 'between',
        '<number>' => 'in_number_array',
        '!<number>' => 'not_in_number_array',
        '<string>' => 'in_string_array',
        '!<string>' => 'not_in_string_array',
        'length=' => 'length_equal',
        'length!=' => 'length_not_equal',
        'length>' => 'length_greater_than',
        'length<' => 'length_less_than',
        'length>=' => 'length_greater_equal',
        'length<=' => 'length_less_equal',
        'length><' => 'length_greater_less',
        'length><=' => 'length_greater_lessequal',
        'length>=<' => 'length_greaterequal_less',
        'length>=<=' => 'length_between',
        'int' => 'integer',
        'array' => 'is_array',    // native function
        'bool=' => 'bool_equal',
        'bool_string=' => 'bool_string_equal',
        'email' => 'is_email',
        'url' => 'is_url',
        'ip' => 'is_ip',
        'ipv4' => 'is_ipv4',
        'ipv6' => 'is_ipv6',
        'mac' => 'is_mac',
        'uuid' => 'is_uuid',
        'ulid' => 'is_ulid',
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
        // '>=' => 'greater_equal',
        // '<=' => 'less_equal',
        '<>' => 'greater_less',
        '<=>' => 'greater_lessequal',
        '<>=' => 'greaterequal_less',
        '<=>=' => 'between',
        '(n)' => 'in_number_array',
        '!(n)' => 'not_in_number_array',
        '(s)' => 'in_string_array',
        '!(s)' => 'not_in_string_array',
        'len=' => 'length_equal',
        'len!=' => 'length_not_equal',
        'len>' => 'length_greater_than',
        'len<' => 'length_less_than',
        'len>=' => 'length_greater_equal',
        'len<=' => 'length_less_equal',
        'len<>' => 'length_greater_less',
        'len<=>' => 'length_greater_lessequal',
        'len<>=' => 'length_greaterequal_less',
        'len<=>=' => 'length_between',
        // 'int' => 'integer',
        'arr' => 'is_array',    // native function
        // 'bool=' => 'bool_equal',
        'bool_str=' => 'bool_string_equal',
    ];

    /**
     * Get the string length
     *
     * @param mixed $string
     * @return string
     */
    public static function string_length($string)
    {
        if (!static::string($string)) return -1;
        return mb_strlen($string);
    }

    /**
     * The field must be present and its data must not be empty string
     *
     * @param mixed $data
     * @return bool
     */
    public static function required($data)
    {
        return $data === 0 || $data === 0.0 || $data === 0.00 || $data === '0' || $data === '0.0' || $data === '0.00' || $data === false || !empty($data);
    }

    /**
     * The field data must be equal to a given value
     *
     * @param mixed $data
     * @param mixed $param
     * @return bool
     */
    public static function equal($data, $param)
    {
        return $data == $param;
    }

    /**
     * The field data must not be equal to a given value
     *
     * @param string $data
     * @param string $param
     * @return bool
     */
    public static function not_equal($data, $param)
    {
        return $data != $param;
    }

    /**
     * The field data must be strictly equal to a given value
     *
     * @param mixed $data
     * @param mixed $param
     * @return bool
     */
    public static function strictly_equal($data, $param)
    {
        return $data === $param;
    }

    /**
     * The field data must not be strictly equal to a given value
     *
     * @param mixed $data
     * @param mixed $param
     * @return bool
     */
    public static function not_strictly_equal($data, $param)
    {
        return $data !== $param;
    }

    /**
     * The field data must be greater than a given value
     *
     * @param mixed $data
     * @param int|float $param
     * @return bool
     */
    public static function greater_than($data, $param)
    {
        return is_numeric($data) && $data > $param;
    }

    /**
     * The field data must be less than a given value
     *
     * @param mixed $data
     * @param int|float $param
     * @return bool
     */
    public static function less_than($data, $param)
    {
        return is_numeric($data) && $data < $param;
    }

    /**
     * The field data must be greater than or equal to a given value
     *
     * @param mixed $data
     * @param int|float $param
     * @return bool
     */
    public static function greater_equal($data, $param)
    {
        return is_numeric($data) && $data >= $param;
    }

    /**
     * The field data must be less than or equal to a given value
     *
     * @param mixed $data
     * @param int|float $param
     * @return bool
     */
    public static function less_equal($data, $param)
    {
        return is_numeric($data) && $data <= $param;
    }

    /**
     * The field data must be greater than the first given value and less than the second given value
     *
     * @param mixed $data
     * @param int|float $param1
     * @param int|float $param2
     * @return bool
     */
    public static function greater_less($data, $param1, $param2)
    {
        return is_numeric($data) && $data > $param1 && $data < $param2;
    }

    /**
     * The field data must be greater than the first given value and less than or equal to the second given value
     *
     * @param mixed $data
     * @param int|float $param1
     * @param int|float $param2
     * @return bool
     */
    public static function greater_lessequal($data, $param1, $param2)
    {
        return is_numeric($data) && $data > $param1 && $data <= $param2;
    }

    /**
     * The field data must be greater than or equal to the first given value and less than the second given value
     *
     * @param mixed $data
     * @param int|float $param1
     * @param int|float $param2
     * @return bool
     */
    public static function greaterequal_less($data, $param1, $param2)
    {
        return is_numeric($data) && $data >= $param1 && $data < $param2;
    }

    /**
     * The field data must be greater than or equal to the first given value and less than or equal to the second given value
     *
     * @param mixed $data
     * @param int|float $param1
     * @param int|float $param2
     * @return bool
     */
    public static function between($data, $param1, $param2)
    {
        return is_numeric($data) && $data >= $param1 && $data <= $param2;
    }

    /**
     * The field data must be numberic and in a given array
     *
     * @param mixed $data
     * @param array $param
     * @return bool
     */
    public static function in_number_array($data, $param)
    {
        return is_numeric($data) && in_array($data, $param);
    }

    /**
     * The field data must be numberic and not in a given array
     *
     * @param mixed $data
     * @param array $param
     * @return bool
     */
    public static function not_in_number_array($data, $param)
    {
        return is_numeric($data) && !in_array($data, $param);
    }

    /**
     * The field data must be string and in a given array
     *
     * @param mixed $data
     * @param array $param
     * @return bool
     */
    public static function in_string_array($data, $param)
    {
        return is_string($data) && in_array($data, $param);
    }

    /**
     * The field data must be string and not in a given array
     *
     * @param mixed $data
     * @param array $param
     * @return bool
     */
    public static function not_in_string_array($data, $param)
    {
        return is_string($data) && !in_array($data, $param);
    }

    /**
     * The length of the field data must be equal to a given value
     *
     * @param mixed $data
     * @param int $param
     * @return bool
     */
    public static function length_equal($data, $param)
    {
        if (!static::string($data) && is_numeric($data)) $data = (string)$data;

        $data_len = static::string_length($data);
        return $data_len == $param;
    }

    /**
     * The length of the field data must not be equal to a given value
     *
     * @param mixed $data
     * @param int $param
     * @return bool
     */
    public static function length_not_equal($data, $param)
    {
        if (!static::string($data) && is_numeric($data)) $data = (string)$data;

        $data_len = static::string_length($data);
        return $data_len != $param;
    }

    /**
     * The length of the field data must be greater than a given value
     *
     * @param mixed $data
     * @param int $param
     * @return bool
     */
    public static function length_greater_than($data, $param)
    {
        if (!static::string($data) && is_numeric($data)) $data = (string)$data;

        $data_len = static::string_length($data);
        return $data_len > $param;
    }

    /**
     * The length of the field data must be less than a given value
     *
     * @param mixed $data
     * @param int $param
     * @return bool
     */
    public static function length_less_than($data, $param)
    {
        if (!static::string($data) && is_numeric($data)) $data = (string)$data;

        $data_len = static::string_length($data);
        return $data_len < $param;
    }

    /**
     * The length of the field data must be greater than or equal to a given value
     *
     * @param mixed $data
     * @param int $param
     * @return bool
     */
    public static function length_greater_equal($data, $param)
    {
        if (!static::string($data) && is_numeric($data)) $data = (string)$data;

        $data_len = static::string_length($data);
        return $data_len >= $param;
    }

    /**
     * The length of the field data must be less than or equal to a given value
     *
     * @param mixed $data
     * @param int $param
     * @return bool
     */
    public static function length_less_equal($data, $param)
    {
        if (!static::string($data) && is_numeric($data)) $data = (string)$data;

        $data_len = static::string_length($data);
        return $data_len <= $param;
    }

    /**
     * The length of the field data must be greater than the first given value and less than the second given value
     *
     * @param mixed $data
     * @param int $param1
     * @param int $param2
     * @return bool
     */
    public static function length_greater_less($data, $param1, $param2)
    {
        if (!static::string($data) && is_numeric($data)) $data = (string)$data;

        $data_len = static::string_length($data);
        return $data_len > $param1 && $data_len < $param2;
    }

    /**
     * The length of the field data must be greater than the first given value and less than or equal to the second given value
     *
     * @param mixed $data
     * @param int $param1
     * @param int $param2
     * @return bool
     */
    public static function length_greater_lessequal($data, $param1, $param2)
    {
        if (!static::string($data) && is_numeric($data)) $data = (string)$data;

        $data_len = static::string_length($data);
        return $data_len > $param1 && $data_len <= $param2;
    }

    /**
     * The length of the field data must be greater than or equal to the first given value and less than the second given value
     *
     * @param mixed $data
     * @param int $param1
     * @param int $param2
     * @return bool
     */
    public static function length_greaterequal_less($data, $param1, $param2)
    {
        if (!static::string($data) && is_numeric($data)) $data = (string)$data;

        $data_len = static::string_length($data);
        return $data_len >= $param1 && $data_len < $param2;
    }

    /**
     * The length of the field data must be greater than or equal to the first given value and less than or equal to the second given value
     *
     * @param mixed $data
     * @param int $param1
     * @param int $param2
     * @return bool
     */
    public static function length_between($data, $param1, $param2)
    {
        if (!static::string($data) && is_numeric($data)) $data = (string)$data;

        $data_len = static::string_length($data);
        return $data_len >= $param1 && $data_len <= $param2;
    }

    /**
     * The field data must be an integer
     *
     * @param mixed $data
     * @return bool
     */
    public static function integer($data)
    {
        return is_int($data);
    }

    /**
     * The field data must be a float
     *
     * @param mixed $data
     * @return bool
     */
    public static function float($data)
    {
        return is_float($data);
    }

    /**
     * The field data must be a string
     *
     * @param mixed $data
     * @return bool
     */
    public static function string($data)
    {
        return is_string($data);
    }

    /**
     * The field data must be a boolean
     *
     * @param mixed $data
     * @param string $bool
     * @return bool
     */
    public static function bool($data)
    {
        return is_bool($data);
    }

    /**
     * The field data must be a boolean and equal to a given value
     *
     * @param mixed $data
     * @param string $param
     * @return bool
     */
    public static function bool_equal($data, $param)
    {
        $is_bool = static::bool($data);
        if (!$is_bool) return false;

        if (is_bool($param)) return $data === $param;

        $param = strtolower($param);
        if ($data === true && $param === 'true') {
            return true;
        } else if ($data === false && $param === 'false') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * The field data must be a boolean string
     * 
     * @deprecated 2.3.0
     * @param mixed $data
     * @return bool
     */
    public static function bool_str($data)
    {
        return static::bool_string($data);
    }

    /**
     * The field data must be a boolean string
     *
     * @param mixed $data
     * @return bool
     */
    public static function bool_string($data)
    {
        if (!is_string($data)) return false;
        return in_array($data, ['true', 'TRUE', 'false', 'FALSE']);
    }

    /**
     * The field data must be a boolean string and equal to a given value
     *
     * @param mixed $data
     * @param string $param
     * @return bool
     */
    public static function bool_string_equal($data, $param)
    {
        $is_bool_string = static::bool_string($data, $param);
        if (!$is_bool_string) return false;
        return strtolower($data) === strtolower($param);
    }

    /**
     * The field data must be a null string
     *
     * @param mixed $data
     * @return bool
     */
    public static function null_string($data)
    {
        if (!is_string($data)) return false;
        return in_array($data, ['null', 'NULL']);
    }

    /**
     * The field data must be date of birth which is a past date
     *
     * @param mixed $data
     * @return bool
     */
    public static function dob($data)
    {
        if (empty($data) || !is_string($data)) return false;
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $data, $arr)) {
            $datetime = strtotime($data);
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

    /**
     * Get a file size from a base64 encoded string
     *
     * @param string $file_base64
     * @return float
     */
    public static function get_file_base64_size($file_base64)
    {
        $file_base64 = preg_replace('/^(data:\s*(\w+\/\w+);base64,)/', '', $file_base64);
        $file_base64 = str_replace('=', '', $file_base64);
        $file_len = strlen($file_base64);
        $file_size = $file_len - ($file_len / 8) * 2;

        $file_size = round(($file_size / 1024), 2);

        return $file_size;
    }

    /**
     * The field data must be a valid base64 encoded file
     * 
     * e.g. data:image/jpeg;base64,{base64}
     *
     * @param string $file_base64
     * @param string $mime
     * @param int|string $max_size File size, its unit is kb
     * @return bool
     */
    public static function file_base64($file_base64, $mime = '', $max_size = null)
    {
        if (empty($file_base64) || !is_string($file_base64)) return false;
        if (preg_match('/^(data:\s*(\w+\/\w+);base64,)/', $file_base64, $matches)) {
            $file_mime = $matches[2];
            if (!empty($mime) && $mime != $file_mime) {
                return "TAG:file_base64:mime";
            }

            if (!empty($max_size)) {
                $file_base64 = str_replace($matches[1], '', $file_base64);
                $file_size = static::get_file_base64_size($file_base64);
                if ($file_size > $max_size) {
                    return "TAG:file_base64:size";
                }
            }
        } else {
            return false;
        }

        return true;
    }

    /**
     * The field data must be one of the grant types of OAuth2
     *
     * @param string $data
     * @return bool
     */
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

    /**
     * The field data must be an email address
     *
     * @param mixed $data
     * @return bool
     */
    public static function is_email($data)
    {
        if (empty($data) || !is_string($data)) return false;
        if (filter_var($data, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * The field data must be a url
     *
     * @param mixed $data
     * @param string $schemes Supports scheme seperators: + or /. e.g. https/ftp
     * @return bool
     */
    public static function is_url($data, $schemes = '')
    {
        if (empty($data)) return false;
        if (filter_var($data, FILTER_VALIDATE_URL)) {
            if (!empty($schemes)) {
                $schemes = preg_replace('/[\/\+]/', '|', $schemes);
                if (preg_match("/^({$schemes}):\/\//", $data)) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * The field data must be an ip address
     *
     * @param mixed $data
     * @return bool
     */
    public static function is_ip($data)
    {
        if (empty($data) || !is_string($data)) return false;
        if (filter_var($data, FILTER_VALIDATE_IP)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * The field data must be an ipv4 address
     *
     * @param mixed $data
     * @return bool
     */
    public static function is_ipv4($data)
    {
        if (empty($data) || !is_string($data)) return false;
        if (filter_var($data, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * The field data must be an ipv6 address
     *
     * @param mixed $data
     * @return bool
     */
    public static function is_ipv6($data)
    {
        if (empty($data) || !is_string($data)) return false;
        if (filter_var($data, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * The field data must be a mac address
     *
     * @param mixed $data
     * @return bool
     */
    public static function is_mac($data)
    {
        if (empty($data) || !is_string($data)) return false;
        if (filter_var($data, FILTER_VALIDATE_MAC)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * The field data must be a UUID
     *
     * @param string $data
     * @return bool
     */
    public static function is_uuid($data)
    {
        if (empty($data) || !is_string($data)) return false;
        if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/Di', $data)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * The field data must be a ULID
     *
     * @param string $data
     * @return bool
     */
    public static function is_ulid($data)
    {
        if (empty($data) || !is_string($data)) return false;

        $ulid_length = 26;
        if (strlen($data) !== $ulid_length) {
            return false;
        }

        $base32_chars = '0123456789ABCDEFGHJKMNPQRSTVWXYZabcdefghjkmnpqrstvwxyz';
        if ($ulid_length !== strspn($data, $base32_chars)) {
            return false;
        }

        return $data[0] <= '7';
    }
}
