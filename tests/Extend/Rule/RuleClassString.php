<?php

namespace githusband\Tests\Extend\Rule;

/**
 * Rule class 的方式增加验证方法
 * 
 * 如果需要定义方法标志，将他们放在 method_symbols 属性中
 * 
 * @package UnitTests
 */
class RuleClassString
{
    /**
     * 方法标志：
     * - 如果值为字符串，则表示标志。
     * - 如果值为数组，则支持一下字段:
     *   - 'symbols': 表示标志
     *   - ‘is_variable_length_argument’: 表示方法第二个参数为可变长度参数，规则集 中的第一个参数之后的所有参数都会被第二个参数的子元素。参考 `githusband\Validation\RuleClassDefault::$method_symbols`
     * 
     * @example `in_number_array[1,2,3]` 第二个参数是一个数组 `[1,2,3]`
     * @var array<string, string|array>
     */
    public static $method_symbols = [
        'is_custom_string' => 'cus_str',
    ];

    // 方法
    public static function is_custom_string($data)
    {
        return preg_match('/^[\w\d -]{8,32}$/', $data) ? true : false;
    }
}
