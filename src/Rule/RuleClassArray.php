<?php

namespace githusband\Rule;

use githusband\Exception\MethodException;

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
     * @see githusband\Rule\RuleClassDefault::$method_symbols About its format
     * @var array
     */
    public static $method_symbols = [
        'require_array_keys' => [
            'symbols' => '<keys>',
            'is_variable_length_argument' => true,
        ],
        'is_unique' => [
            'symbols' => 'unique',
            'default_arguments' => [
                2 => '@parent'
            ]
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

    /**
     * Check if the child data is unique within the parent array.
     *
     * @param mixed $data The child data within an index array
     * @param array $parent The parent data of the $data
     * @return bool
     * @throws MethodException
     */
    public static function is_unique($data, $parent)
    {
        $is_unique = true;
        $count = 0;

        if (!is_array($parent)) throw MethodException::parameter("The 2th argument is not an array");

        foreach ($parent as $value) {
            if ($data === $value) {
                $count++;
                if ($count > 1) {
                    $is_unique = false;
                    break;
                }
            }
        }

        return $is_unique;
    }
}
