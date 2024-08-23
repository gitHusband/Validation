<?php

namespace githusband\Rule;

/**
 * Class RuleClassArray contains multiple methods to validate the array
 * 
 * - Methods are required to be public.
 * - Methods are not required to be static.
 * - You can alse set symbols of the methods.
 */
class RuleClassArray
{
    /**
     * The method symbols of rule array.
     *
     * @see githusband\Rule::$method_symbols About validated format
     * @var array
     */
    public static $method_symbols = [
        'require_array_keys' => [
            'symbols' => '<keys>',
            'is_variable_length_argument' => true,
        ],
    ];

    /**
     * The field data must be array and must have the required keys and must have not other keys.
     *
     * @param array $data
     * @param array $required_keys
     * @return bool|string
     */
    public static function require_array_keys($data, $required_keys)
    {
        if (!is_array($data)) return 'TAG:array';

        foreach ($data as $k => $v) {
            if (!in_array($k, $required_keys)) return false;
        }

        foreach ($required_keys as $key) {
            if (!array_key_exists($key, $data)) {
                return false;
            }
        }

        return true;
    }
}
