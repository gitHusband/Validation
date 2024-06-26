<?php

namespace githusband\Tests\Extend;

use githusband\Validation;
use githusband\Tests\Extend\Rule\RuleExtendTrait;
use githusband\Tests\Extend\Rule\RuleClassString;

/**
 * 拓展 Validation 的例子：增加验证方法。
 * 
 * 如何增加验证方法（同名方法优先级从高到低）：
 * 1. 拓展 $this->methods 属性。
 *   实例化类后可调用 $this->add_method 增加新的验证方法。这里均未演示。
 * 
 * 2. 拓展 $this->rule_classes 属性。
 *   实例化类后可调用 $this->add_rule_class 增加新的验证规则类。
 * 
 * 3. 拓展 Validation 类，直接增加验证方法
 * 3.1. 拓展类，直接增加验证方法
 *   如果需要定义方法标志，将他们放在属性 method_symbols 中
 * 
 * 3.2. 使用 trait 拓展验证方法
 *   如果需要定义方法标志，将他们放在属性中，属性命名规则：“method_symbols_of_” + 类名（大驼峰转下划线）
 * 
 * 4. 全局函数
 *   如果以上均未找到规则集的验证方法，则从全局函数中查找方法。
 * 
 * @package UnitTests
 */
class MyValidation extends Validation
{
    use RuleExtendTrait;

    protected $rule_classes = [
        RuleClassString::class
    ];

    protected $method_symbols = [
        ">=1" => "grater_than_or_equal_to_1",
    ];

    /**
     * Don't copy this property to the README document
     *
     * @var array
     */
    protected $error_templates = [
        "validate_data_limit" => "@this can not be greater than or equal to 1000",
    ];

    protected function grater_than_or_equal_to_1($data)
    {
        return $data >= 1;
    }

    /**
     * Don't copy this method to the README document
     *
     * @param mixed $data
     * @return mixed
     */
    protected function validate_data_limit($data)
    {
        if (!is_integer($data)) {
            return [
                "error_type" => "data_type",
                "message" => "@this must be integer",
                "extra" => "Data type checks failed"
            ];
        } else if ($data >= 10000) {
            return "@this is out of limited";
        } else if ($data < 10000 && $data >= 1000) {
            return false;
        }

        return true;
    }
}
