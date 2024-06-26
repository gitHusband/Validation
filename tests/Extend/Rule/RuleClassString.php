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
    // 方法标志
    public static $method_symbols = [
        'cus_str' => 'is_custom_string',
    ];

    // 方法
    public static function is_custom_string($data)
    {
        return preg_match('/^[\w\d -]{8,32}$/', $data) ? true : false;
    }
}
