<?php

namespace githusband\Test\Extend;

use githusband\Validation;
use githusband\Test\Extend\Rule\RuleCustom;

/**
 * 2. 拓展类，直接增加验证方法
 * 如果需要定义方法标志，将他们放在属性 method_symbol 中
 */
class MyValidation extends Validation
{
    use RuleCustom;

    protected $method_symbol = [
        ">=1" => "grater_than_or_equal_to_1",
    ];

    /**
     * Don't copy this property to the README document
     *
     * @var array
     */
    protected $error_template = [
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
