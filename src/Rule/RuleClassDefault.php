<?php

namespace githusband\Rule;

use githusband\Validation;
use githusband\Exception\MethodException;

class RuleClassDefault
{
    /**
     * The method symbols of rule default.
     *
     * @var array
     */
    public static $method_symbols = [
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
        'alpha' => 'is_alpha',
        'alpha_ext' => 'is_alpha_ext',
        'alphanumeric' => 'is_alphanumeric',
        'alphanumeric_ext' => 'is_alphanumeric_ext',
    ];

    /**
     * The old method symbols of rule default that are deprecated.
     * 
     * @deprecated 2.3.0
     * @var array
     */
    public static $deprecated_method_symbols = [
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

    public static $unicode_alpha_preg_of_letter = [
        'letter' => '\pL',
        'letter_lowercase' => '\p{Ll}',    // a-z, µßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ and more
        'letter_uppercase' => '\p{Lu}',    // A-Z, ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞ and more
        'letter_modifier' => '\p{Lm}',     // Letter-like characters that are usually combined with others, but here they stand alone: ʰʱʲʳʴʵʶʷʸʹʺʻʼʽʾʿˀˁˆˇˈˉˊˋˌˍˎˏːˑˠˡˢˣˤˬˮʹͺՙ and more
        'letter_other' => '\p{Lo}',        // ªºƻǀǁǂǃʔ and many more ideographs and letters from unicase alphabets
        'letter_title' => '\p{Lt}',        // ǅǈǋǲᾈᾉᾊᾋᾌᾍᾎᾏᾘᾙᾚᾛᾜᾝᾞᾟᾨᾩᾪᾫᾬᾭᾮᾯᾼῌῼ
    ];

    public static $ascii_alpha_preg_of_letter = [
        'letter' => 'a-zA-Z',
        'letter_lowercase' => 'a-z',
        'letter_uppercase' => 'A-Z',
    ];

    public static $unicode_alpha_preg_of_mark = [
        'mark' => '\pM',
        'mark_spacing' => '\p{Mc}',          // None in latin scripts
        'mark_non-spacing' => '\p{Mn}',      // Combining enclosing square (U+20DE) like in a⃞ , combining enclosing circle backslash (U+20E0) like in a⃠
        'mark_enclosing' => '\p{Me}',        // Combining diacritical marks U+0300-U+036f, like the accents on this letter a: áâãāa̅ăȧäảåa̋ǎa̍a̎ȁa̐ȃ
    ];

    public static $ascii_alpha_preg_of_mark = [
        'mark' => '',
        'mark_spacing' => '',
        'mark_non-spacing' => '',
        'mark_enclosing' => '',
    ];

    public static $unicode_alpha_preg_of_numeric = [
        'numeric' => '\pN',
        'numeric_decimal' => '\p{Nd}',  // 0123456789, ٠١٢٣٤٥٦٧٨٩ and digits in many other scripts.
        'numeric_letter' => '\p{Nl}',   // ⅠⅡⅢⅣⅤⅥⅦⅧⅨⅩⅪⅫⅬⅭⅮⅯⅰⅱⅲⅳⅴⅵⅶⅷⅸⅹⅺⅻⅼⅽⅾⅿ and some more
        'numeric_other' => '\p{No}',    // ⁰¹²³⁴⁵⁶⁷⁸⁹ ₀₁₂₃₄₅₆₇₈₉ ½⅓⅔¼¾⅕⅖⅗⅘⅙⅚⅐⅛⅜⅝⅞⅑⅒ ①②③④⑤⑥⑦⑧⑨⑩⑪⑫⑬⑭⑮⑯⑰⑱⑲⑳, etc.
    ];

    public static $ascii_alpha_preg_of_numeric = [
        'numeric' => '0-9',
        // 'numeric_decimal' => '0-9',
        // 'numeric_letter' => '0-9',
        // 'numeric_other' => '0-9',
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
        return Validation::required($data);
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
     * The field data must be numeric and in a given array
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
     * The field data must be numeric and not in a given array
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
        return Validation::bool_string($data);
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
        return Validation::null_string($data);
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

    /**
     * Get the regular expression of Alphabet or Alphanumeric
     *
     * @see https://www.php.net/manual/en/regexp.reference.unicode.php
     * @param string $sub_types For example, type "letter" have sub-type "lowercase" or "uppercase"
     * @param string $charset UNICODE / ASCII
     * @return string
     * @throws MethodException
     */
    public static function get_alpha_preg($types, $sub_types = 'default', $charset = 'UNICODE')
    {
        $charset = strtolower($charset);

        $types_array = explode('/', $types);

        $expression = '';
        $invalid_sub_types = [];
        foreach ($types_array as $type) {
            $prototype_name = "{$charset}_alpha_preg_of_{$type}";
            if (!property_exists(static::class, $prototype_name)) throw MethodException::parameter("Invalid charset {$charset} {$type}");
            $alpha_preg = static::${$prototype_name};

            if (
                $sub_types == 'default'
                ||$sub_types == $type
            ) {
                $expression .= $alpha_preg[$type];
            } else {
                $sub_types_array = explode('/', $sub_types);
                foreach ($sub_types_array as $sub_type) {
                    $full_sub_type = "{$type}_{$sub_type}";
                    if (!isset($alpha_preg[$full_sub_type])) {
                        if (in_array($sub_type, $invalid_sub_types)) $invalid_sub_types[] = $sub_type;
                        break;
                    }
                    $expression .= $alpha_preg[$full_sub_type];
                }
            }
        }

        if (!empty($invalid_sub_types)) {
            throw MethodException::parameter("Invalid alpha {$type}:{$invalid_sub_types[0]} type of {$charset}");
        }

        return $expression;
    }

    /**
     * Get the regular expression of Alphabet
     *
     * @param string $sub_types 
     * @param string $charset UNICODE / ASCII
     * @return string
     * @throws MethodException
     */
    public static function get_alpha_preg_letter($sub_types = 'default', $charset = 'UNICODE')
    {
        return static::get_alpha_preg('letter', $sub_types, $charset);
    }

    /**
     * Get the regular expression of Mark
     *
     * @param string $sub_types
     * @param string $charset UNICODE / ASCII
     * @return string
     * @throws MethodException
     */
    public static function get_alpha_preg_mark($sub_types = 'default', $charset = 'UNICODE')
    {
        return static::get_alpha_preg('mark', $sub_types, $charset);
    }

    /**
     * Get the regular expression of Numeric
     *
     * @param string $sub_types
     * @param string $charset UNICODE / ASCII
     * @return string
     * @throws MethodException
     */
    public static function get_alpha_preg_numeric($sub_types = 'default', $charset = 'UNICODE')
    {
        return static::get_alpha_preg('numeric', $sub_types, $charset);
    }

    /**
     * Get the regular expression of Letter and Numeric
     *
     * @param string $sub_types
     * @param string $charset UNICODE / ASCII
     * @return string
     * @throws MethodException
     */
    public static function get_alpha_preg_letter_numeric($sub_types = 'default', $charset = 'UNICODE')
    {
        return static::get_alpha_preg('letter/numeric', $sub_types, $charset);
    }

    /**
     * The field data must only contain letters
     *
     * @param string $data
     * @param string $charset
     * @param string $extensions
     * @return bool|string
     * @throws MethodException
     */
    public static function is_alpha($data, $charset = 'UNICODE', $extensions = '')
    {
        if (empty($data) || !is_string($data)) return false;

        $letter_expression = static::get_alpha_preg_letter('default', $charset);
        $mark_expression = static::get_alpha_preg_mark('default', $charset);
        $alpha_expression = $letter_expression . $mark_expression . $extensions;

        /**
         * @example Unicode /^[\pL\pM]+$/u
         * @example ASCII /^[a-zA-Z]+$/u
         */
        if (preg_match("/^[{$alpha_expression}]+$/u", $data)) {
            return true;
        } else {
            if (empty($extensions)) {
                return false;
            } else {
                return 'TAG:alpha_ext:@p2';
            }
        }
    }

    /**
     * The field data must only contain letters and numbers
     *
     * @param string $data
     * @param string $charset
     * @param string $extensions
     * @return bool|string
     * @throws MethodException
     */
    public static function is_alphanumeric($data, $charset = 'UNICODE', $extensions = '')
    {
        if (empty($data) || !is_string($data)) return false;

        $letter_numeric_expression = static::get_alpha_preg_letter_numeric('default', $charset);
        $mark_expression = static::get_alpha_preg_mark('default', $charset);
        $alphanumeric_expression = $letter_numeric_expression . $mark_expression . $extensions;

        /**
         * @example Unicode /^[\pL\pM\pN]+$/u
         * @example ASCII /^[a-zA-Z0-9]+$/u
         */
        if (preg_match("/^[{$alphanumeric_expression}]+$/u", $data)) {
            return true;
        } else {
            if (empty($extensions)) {
                return false;
            } else {
                return 'TAG:alphanumeric_ext:@p2';
            }
        }
    }

    /**
     * The field data must only contain letters and extensions
     *
     * @param string $data
     * @param string $charset
     * @param string $extensions Default to "_-"
     * @return bool|string
     * @throws MethodException
     */
    public static function is_alpha_ext($data, $charset = 'UNICODE', $extensions = '_-')
    {
        if(static::is_alpha($data, $charset, $extensions) === true) {
            return true;
        } else {
            if ($extensions === '_-') {
                return false;
            } else {
                return 'TAG:alpha_ext:@p2';
            }
        }
    }

    /**
     * The field data must only contain letters and numbers and extensions
     *
     * @param string $data
     * @param string $charset
     * @param string $extensions Default to "_-"
     * @return bool|string
     * @throws MethodException
     */
    public static function is_alphanumeric_ext($data, $charset = 'UNICODE', $extensions = '_-')
    {
        if(static::is_alphanumeric($data, $charset, $extensions) === true) {
            return true;
        } else {
            if ($extensions === '_-') {
                return false;
            } else {
                return 'TAG:alphanumeric_ext:@p2';
            }
        }
    }
}
