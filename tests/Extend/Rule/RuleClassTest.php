<?php

namespace githusband\Tests\Extend\Rule;

/**
 * Rule class 的方式增加验证方法
 * 
 * 如果需要定义方法标志，将他们放在 method_symbols 属性中
 * 
 * @package UnitTests
 */
class RuleClassTest
{
    /**
     * 方法标志：
     * - 如果值为字符串，则表示标志。
     * - 如果值为数组，则支持以下字段:
     *   - `symbols`: 表示标志
     *   - `is_variable_length_argument`: 默认为 false。表示方法第二个参数为可变长度参数，规则集 中的第一个参数之后的所有参数都会被第二个参数的子元素。参考 `githusband\Rule\RuleClassDefault::$method_symbols['in_number_array']`。
     *   - `default_arguments`: 默认为 无。设置方法的默认参数。参考 `githusband\Rule\RuleClassArray::$method_symbols['is_unique']`。
     *     - `default_arguments` 数组的键必须是整形数字，表示第几个默认参数. 例如，`2` 表示第二个参数。
     *     - `default_arguments` 数组的值可以是任意值。对于类似 "@parent" (表示当前字段的父数据)，参考 https://github.com/gitHusband/Validation?tab=readme-ov-file#43-%E6%96%B9%E6%B3%95%E4%BC%A0%E5%8F%82
     * 
     * @example `in_number_array[1,2,3]` 第二个参数是一个数组 `[1,2,3]`
     * @see githusband\Rule\RuleClassDefault::$method_symbols
     * @see githusband\Rule\RuleClassArray::$method_symbols
     * @var array<string, string|array>
     */
    public static $method_symbols = [
        'is_custom_string' => 'cus_str',
        'is_in_custom_list' => [
            'symbols' => '<custom>',
            'is_variable_length_argument' => true,  // 第一个参数之后的所有参数，都被认作第二个参数数组的子元素
        ],
        'is_equal_to_password' => [
            'symbols' => '=pwd',
            'default_arguments' => [
                2 => '@password'    // 第二个参数默认为 password 字段的值
            ]
        ]
    ];

    /**
     * 测试方法 1 - 测试当前字段的格式是否满足要求
     * 
     * 用法：
     * - 'id' => 'is_custom_string'
     * - 'id' => 'cus_str'
     *
     * @param string $data
     * @return bool
     */
    public static function is_custom_string($data)
    {
        return preg_match('/^[\w\d -]{8,32}$/', $data) ? true : false;
    }

    /**
     * 测试方法 2 - 测试当前字段是否存在于列表内
     * 
     * 用法：
     * - 'sequence' => 'is_in_custom_list[1st, First, 2nd, Second]'
     * - 'sequence' => '<custom>[1st, First, 2nd, Second]'
     * 
     * 这是一个第二个参数为可变长度参数的例子。如果不设置可变长度参数，那么它的写法如下，注意第二个参数必须是合法的 JSON Encoded 字符串。例如：
     * - 'sequence' => 'is_in_custom_list[["1st", "First", "2nd", "Second"]]'
     * - 'sequence' => '<custom>[["1st", "First", "2nd", "Second"]]'
     *
     * @param mixed $data
     * @param array $list
     * @return bool
     */
    public static function is_in_custom_list($data, $list)
    {
        return in_array($data, $list);
    }

    /**
     * 测试方法 3 - 验证当前字段是否与 password 字段相等
     * 
     * 用法：
     * - 'confirm_password' => 'is_equal_to_password'
     * - 'confirm_password' => '=pwd'
     * 
     * 这是一个默认参数的例子。用 `euqal` 方法来写效果也一样, 它相当于给 equal 方法加了默认参数 '@password'。例如：
     * - 'confirm_password' => `equal[@password]`，
     * - 'confirm_password' => `=[@password]`，
     *
     * @param string $data
     * @param string $password
     * @return bool
     */
    public static function is_equal_to_password($data, $password)
    {
        return $data == $password;
    }
}
