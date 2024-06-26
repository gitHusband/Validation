<?php

namespace githusband\Tests\Extend\Rule;

/**
 * Trait 的方式增加验证方法
 * 
 * 如果需要定义方法标志，将他们放在属性中，属性命名规则：“method_symbols_of_” + 类名（大驼峰转下划线）
 * 
 * @package UnitTests
 */
trait RuleExtendTrait
{
    // 方法标志
    protected $method_symbols_of_rule_extend_trait = [
        '=1' => 'euqal_to_1',
    ];

    // 方法
    protected function euqal_to_1($data)
    {
        return $data == 1;
    }
}
