<?php

namespace githusband\Test\Extend\Rule;

/**
 * 1. 推荐用 trait 拓展验证方法
 * 如果需要定义方法标志，将他们放在属性中，属性命名规则：“method_symbols_of_” + 类名（大驼峰转下划线）
 */
trait RuleCustom
{
    protected $method_symbols_of_rule_custom = [
        '=1' => 'euqal_to_1',
    ];

    protected function euqal_to_1($data)
    {
        return $data == 1;
    }
}
