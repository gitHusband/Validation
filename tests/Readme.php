<?php

namespace githusband\Tests;

use githusband\Validation;
use githusband\Tests\TestCommon;
use githusband\Tests\Extend\MyValidation;

/**
 * 文档 README 中的相关代码
 * 
 * @package UnitTests
 */
class Readme extends TestCommon
{

    public function __construct()
    {
        parent::__construct();
    }

    public function test_simple_example()
    {
        // 待验证参数的简单示例。实际上无论参数多么复杂，都支持一个验证规则数组完成验证
        $data = [
            "id" => 1,
            "name" => "Devin",
            "age" => 18,
            "favorite_animation" => [
                "name" => "A Record of A Mortal's Journey to Immortality",
                "release_date" => "July 25, 2020 (China)"
            ]
        ];

        // 验证规则数组。规则数组的格式与待验证参数的格式相同。
        $rule = [
            "id" => "required|/^\d+$/",         // 必要的，且只能是数字
            "name" => "required|length><=[3,32]",  // 必要的，且字符串长度必须大于3，小于等于32
            "favorite_animation" => [
                "name" => "required|length><=[1,64]",          // 必要的，且字符串长度必须大于1，小于等于64
                "release_date" => "optional|length><=[4,64]",  // 可选的，如果不为空，那么字符串长度必须大于4，小于等于64
            ]
        ];

        $config = [];
        // 实例化类，接受一个自定义配置数组，但不必要
        $validation = new Validation($config);

        // 设置验证规则并验证数据，成功返回 true，失败返回 false
        if ($validation->set_rules($rule)->validate($data)) {
            // 这里获取验证结果，有被规则{$rule}验证到的参数，成功则修改其值为true，失败则修改其值为错误信息，
            // 没有被验证到的参数，保持原值不变。比如 age 保持 18 不变。
            return $validation->get_result();
        } else {
            // 一共有四种错误信息格式可供选择。默认 Validation::ERROR_FORMAT_DOTTED_GENERAL
            return $validation->get_error();
        }
    }

    public function test_single_string()
    {
        $validation = new Validation();
        if ($validation->set_rules("required|string")->validate("Hello World!")) {
            return $validation->get_result();
        } else {
            return $validation->get_error();
        }
    }

    public function test_regular_expression()
    {
        $data = [
            "id" => "1.00"
        ];

        $rule = [
            // id 是必要的，且必须是数字
            "id" => "required|/^\d+$/",
        ];

        return $this->validate($data, $rule);
    }

    public function test_custom_parameters()
    {
        $data = [
            "age" => 20,
        ];

        $rule = [
            // age 必须等于20。这里的 `@this` 代表当前 age 字段的值。
            "age" => "equal(@this,20)",
            // age 必须等于20。当参数写在中括号`[]`里面时，首个 `@this` 参数可省略不写。
            "age" => "equal[20]",
        ];

        return $this->validate($data, $rule);
    }

    public function test_custom_method_by_add_method()
    {
        $data = [
            "id" => 0,
        ];

        $rule = [
            // 必要的，且只能是数字，且必须大于 0
            "id" => "required|/^\d+$/|check_id",
        ];

        $validation = new Validation();
        $validation->add_method('check_id', function ($id) {
            if (false) define('UNDEFINED_VAR', 1);
            return UNDEFINED_VAR;

            if ($id == 0) {
                return false;
                // return "@this check_id failed";
                // return [
                //     'error_type' => 'server_error',
                //     'message' => '@this check_id failed',
                //     "extra" => "extra message"
                // ];
            }

            return true;
        });

        if ($validation->set_rules($rule)->validate($data)) {
            return $validation->get_result();
        } else {
            return $validation->get_error();
        }
    }

    public function test_custom_method_by_extend()
    {
        $data = [
            "id" => 2,
            "parent_id" => 1,
        ];

        $rule = [
            // id 必要的，且必须大于等于 1
            "id" => "required|>=1",
            // parent_id 可选的，且必须等于 1
            "parent_id" => "optional|euqal_to_1",
        ];

        $validation = new MyValidation();
        if ($validation->set_rules($rule)->validate($data)) {
            return $validation->get_result();
        } else {
            return $validation->get_error();
        }
    }

    public function test_series_parallel()
    {
        $data = [
            "height_unit" => "cm",
            "height" => 180
        ];

        // 规则可以这么写，[or] 可以替换成标志 [||]
        $rule = [
            // 串联，身高单位是必须的，且必须是 cm 或者 m
            "height_unit" => "required|<string>[cm,m]",
            // 并联
            "height[or]" => [
                // 若身高单位是厘米 cm, 则身高必须大于等于100，小于等于200 
                "required|=(@height_unit,cm)|>=<=[100,200]",
                // 若身高单位是米 m, 则身高必须大于等于1，小于等于2
                "required|=(@height_unit,m)|>=<=[1,2]",
            ]
        ];

        // 也可以这么写，标志 [||] 可以替换成 [or]
        $rule = [
            // 串联，身高单位是必须的，且必须是 cm 或者 m
            "height_unit" => "required|<string>[cm,m]",
            // 并联
            "height" => [
                "[||]" => [
                    // 若身高单位是厘米 cm, 则身高必须大于等于100，小于等于200 
                    "required|=(@height_unit,cm)|>=<=[100,200]",
                    // 若身高单位是米 m, 则身高必须大于等于1，小于等于2
                    "required|=(@height_unit,m)|>=<=[1,2]",
                ]
            ]
        ];

        return $this->validate($data, $rule);
    }

    public function test_condition_when()
    {
        $data = [
            "id" => "6",
            "name" => "Devin",
            "age" => "ads"
        ];

        $rule = [
            "id" => "required|><[0,10]",
            // 当 id 小于 5 时，name 只能是数字且长度必须大于 2
            // 当 id 大于等于 5 时，name 可以是任何字符串且长度必须大于 2
            "name" => "/^\d+$/:when(<(@id,5))|length>[2]",
            // 当 id 不小于 5 时，age 必须小于等于 18
            // 当 id 小于 5 时，age 可以是任何数字
            "age" => "int|<=[18]:when_not(<(@id,5))",
        ];

        return $this->validate($data, $rule);
    }

    public function test_condition_required_when()
    {
        $data = [
            "attribute" => "height",
            "centimeter" => ''
        ];

        $rule = [
            // 特征是必要的，且只能是 height(身高) 或 weight(体重)
            "attribute" => "required|<string>[height,weight]",
            // 若属性是 height, 则 centimeter 是必要的，若不是 height，则是可选的。
            // 无论如何，若该值非空，则必须大于 180
            "centimeter" => "required:when(=(@attribute,height))|required|>[180]",
        ];

        return $this->validate($data, $rule);
    }

    public function test_condition_required_when_not()
    {
        $data = [
            "attribute" => "weight",
            "centimeter" => '1'
        ];

        $rule = [
            // 特征是必要的，且只能是 height(身高) 或 weight(体重)
            "attribute" => "required|<string>[height,weight]",
            // 若属性不是 weight, 则 centimeter 是必要的，若是 weight，则是可选的。
            // 无论如何，若该值非空，则必须大于 180
            "centimeter" => "required:when_not(=(@attribute,weight))|required|>[180]",
        ];

        return $this->validate($data, $rule);
    }

    /**
     * @deprecated v2.6.0
     * @return array
     */
    public function test_deprecated_condition_if()
    {
        $data = [
            "attribute" => "height",
            "centimeter" => ''
        ];

        $rule = [
            // 特征是必要的，且只能是 height(身高) 或 weight(体重)
            "attribute" => "required|<string>[height,weight]",
            // 若属性是 height, 则 centimeter 是必要的，且必须大于 180
            // 若不是 height，则不继续验证后续规则，即 centimeter 为任何值都可以。
            "centimeter" => "if(=(@attribute,height))|required|>[180]",
        ];

        return $this->validate($data, $rule);
    }

    /**
     * @deprecated v2.6.0
     * @return array
     */
    public function test_deprecated_condition_if_not()
    {
        $data = [
            "attribute" => "weight",
            "centimeter" => '1'
        ];

        $rule = [
            // 特征是必要的，且只能是 height(身高) 或 weight(体重)
            "attribute" => "required|<string>[height,weight]",
            // 若属性不是 weight, 则 centimeter 是必要的，且必须大于 180
            // 若是 weight，则不继续验证后续规则，即 centimeter 为任何值都可以。
            "centimeter" => "!if(=(@attribute,weight))|required|>[180]",
        ];

        return $this->validate($data, $rule);
    }

    public function test_condition_if()
    {
        $data = [
            "attribute" => "height",
            "centimeter" => ''
        ];

        $rule = [
            // 特征是必要的，且只能是 height(身高) 或 weight(体重)
            "attribute" => "required|<string>[height,weight]",
            // 若属性是 height, 则 centimeter 是必要的，且必须大于 180
            // 若不是 height，则不继续验证后续规则，即 centimeter 为任何值都可以。
            "centimeter" => "if(=(@attribute,height)){required|>[180]}",
        ];

        return $this->validate($data, $rule);
    }

    public function test_condition_if_not()
    {
        $data = [
            "attribute" => "weight",
            "centimeter" => '1'
        ];

        $rule = [
            // 特征是必要的，且只能是 height(身高) 或 weight(体重)
            "attribute" => "required|<string>[height,weight]",
            // 若属性不是 weight, 则 centimeter 是必要的，且必须大于 180
            // 若是 weight，则不继续验证后续规则，即 centimeter 为任何值都可以。
            "centimeter" => "if( !(=(@attribute,weight)) ) { required|>[180] }",
        ];

        return $this->validate($data, $rule);
    }

    public function test_condition_if_complicated()
    {
        $data_lists = [
            "id-50" => [
                "id" => 50,
                "name" => '1(A)'
            ],
            "id-51" => [
                "id" => 51,
                "name" => '12(B)'
            ],
            "id-52" => [
                "id" => 52,
                "name" => '123(C)'
            ],
            "id-53" => [
                "id" => 53,
                "name" => '123(C)'
            ],
            "id-54-1" => [
                "id" => 54,
                "name" => ''
            ],
            "id-54-2" => [
                "id" => 54,
                "name" => 'if-123(C)'
            ],
            "id-54-3" => [
                "id" => 54,
                "name" => '123(C)'
            ],
        ];

        $rule = [
            "id" => "required|><[0,1000]",
            "name" => "if (!<=(@id,49)|<=(@id,51)) {
                if (!!=(@id,50)) {
                    required|string|/^\d{1}[A-Z\)\(]*$/
                } else {
                    required|string|/^\d{2}[A-Z\)\(]*$/
                }
            } else if (!(!=(@id,52)) || =(@id,53)) {
                required|string|/^\d{3}[A-Z\)\(]*$/
            } else {
                optional|string|/^if-\d+[A-Z\)\(]*$/
            }"
        ];

        return $this->validate_data_lists($data_lists, $rule);
    }

    public function test_infinite_nested_associative_array()
    {
        $data = [
            "id" => 1,
            "name" => "Johnny",
            "favourite_fruit" => [
                "name" => "apple",
                "color" => "red",
                "shape" => "circular"
            ]
        ];

        // 若要验证上述 $data，规则可以这么写
        $rule = [
            "id" => "required|/^\d+$/",
            "name" => "required|length>[3]",
            "favourite_fruit" => [
                "name" => "required|length>[3]",
                "color" => "required|length>[3]",
                "shape" => "required|length>[3]"
            ]
        ];

        return $this->validate($data, $rule);
    }

    public function test_infinite_nested_index_array()
    {
        $data = [
            "id" => 1,
            "name" => "Johnny",
            "favourite_color" => [
                "white",
                "red"
            ],
            "favourite_fruits" => [
                [
                    "name" => "apple",
                    "color" => "red",
                    "shape" => "circular"
                ],
                [
                    "name" => "banana",
                    "color" => "yellow",
                    "shape" => "long strip"
                ],
            ]
        ];

        // 若要验证上述 $data，规则可以这么写
        $rule = [
            "id" => "required|/^\d+$/",
            "name" => "required|length>[3]",
            "favourite_color.*" => "required|length>[3]",
            "favourite_fruits.*" => [
                "name" => "required|length>[3]",
                "color" => "required|length>[3]",
                "shape" => "required|length>[3]"
            ]
        ];

        // 若要验证上述 $data，规则也可以这么写
        $rule = [
            "id" => "required|/^\d+$/",
            "name" => "required|length>[3]",
            "favourite_color" => [
                "*" => "required|length>[3]"
            ],
            "favourite_fruits" => [
                "*" => [
                    "name" => "required|length>[3]",
                    "color" => "required|length>[3]",
                    "shape" => "required|length>[3]"
                ]
            ]
        ];

        return $this->validate($data, $rule);
    }

    public function test_optional_field()
    {
        $data = [];

        // 若要验证上述 $data，规则可以这么写
        $rule = [
            // 1. 叶子字段，直接使用 optional 方法，表示该字段是可选的
            "name" => "optional|string",
            // 2. 任意字段，在字段名后面添加 [optional]，表示该字段是可选的
            "favourite_fruit[optional]" => [
                "name" => "required|string",
                "color" => "required|string"
            ],
            // 3. 任意字段，增加唯一子元素 [optional]，表示该字段是可选的
            "gender" => ["[optional]" => "string"],
            "favourite_food" => [
                "[optional]" => [
                    "name" => "required|string",
                    "taste" => "required|string"
                ]
            ],
        ];

        return $this->validate($data, $rule);
    }

    public function test_custom_config()
    {
        $data = [
            "id" => 1,
            "name" => "Devin",
            "age" => 18,
            "favorite_animation" => [
                "name" => "A Record of A Mortal's Journey to Immortality",
                "release_date" => "July 25, 2020 (China)"
            ]
        ];

        // 验证规则数组。规则数组的格式与待验证参数的格式相同。
        $rule = [
            "id" => "!*&&Reg:/^\d+$/",          // 必要的，且只能是数字
            "name" => "!*&&length><=~3+32",        // 必要的，且字符串长度必须大于3，小于等于32
            "favorite_animation" => [
                "name" => "!*&&length><=~1+64",                // 必要的，且字符串长度必须大于1，小于等于64
                "release_date" => "o?&&length><=#@this+4+64",  // 可选的，如果不为空，那么字符串长度必须大于4，小于等于64
            ]
        ];

        $custom_config = [
            'reg_preg' => '/^Reg:(\/.+\/.*)$/',                         // If a rule match reg_preg, indicates it's a regular expression instead of method
            'symbol_rule_separator' => '&&',                            // Serial rules seqarator to split a rule into multiple methods
            'symbol_method_standard' => '/^(.*)#(.*)$/',                // Standard method format, e.g. equal(@this,1)
            'symbol_method_omit_this' => '/^(.*)~(.*)$/',               // @this omitted method format, will add a @this parameter at first. e.g. equal[1]
            'symbol_parameter_separator' => '+',                        // Parameters separator to split the parameter string of a method into multiple parameters, e.g. equal(@this,1)
            'symbol_field_name_separator' => '->',                      // Field name separator of error message, e.g. "fruit.apple"
            'symbol_required' => '!*',                                  // Symbol of required field, Same as the rule "required"
            'symbol_optional' => 'o?',                                  // Symbol of optional field, can be not set or empty, Same as the rule "optional"
        ];
        // 实例化类，接受一个自定义配置数组，但不必要
        $validation = new Validation($custom_config);

        // 设置验证规则并验证数据，成功返回 true，失败返回 false
        if ($validation->set_rules($rule)->validate($data)) {
            // 这里获取验证结果，有被规则{$rule}验证到的参数，成功则修改其值为true，失败则修改其值为错误信息，
            // 没有被验证到的参数，保持原值不变。比如 age 保持 18 不变。
            return $validation->get_result();
        } else {
            // 一共有四种错误信息格式可供选择。默认 Validation::ERROR_FORMAT_DOTTED_GENERAL
            return $validation->get_error();
        }
    }

    public function test_method_return_error_message()
    {
        $data = [
            "animal" => "snake"
        ];

        // 验证规则数组。规则数组的格式与待验证参数的格式相同。
        $rule = [
            "animal" => "check_animal"
        ];

        function is_exclude_animals($animal)
        {
            return false;
        }

        function is_fake_animals($animal)
        {
            return true;
        }

        $validation = new Validation();
        $validation->add_method('check_animal', function ($animal) {
            if ($animal == "") {
                return false;
            } else if ($animal == "mouse") {
                return "I don't like mouse";
            } else if ($animal == "snake") {
                return [
                    "error_type" => "server_error",
                    "message" => "I don't like snake",
                    "extra" => "You scared me"
                ];
            } else if (!is_exclude_animals($animal)) {
                return "TAG:is_exclude_animal";
            } else if (is_fake_animals($animal)) {
                return [
                    "error_type" => "server_error",
                    "message" => "TAG:is_fake_animals",
                    "extra" => "You scared me"
                ];
            }

            return true;
        });


        // 设置验证规则并验证数据，成功返回 true，失败返回 false
        if ($validation->set_rules($rule)->validate($data)) {
            // 这里获取验证结果，有被规则{$rule}验证到的参数，成功则修改其值为true，失败则修改其值为错误信息，
            // 没有被验证到的参数，保持原值不变。比如 age 保持 18 不变。
            return $validation->get_result();
        } else {
            // 一共有四种错误信息格式可供选择。默认 Validation::ERROR_FORMAT_DOTTED_GENERAL
            return $validation->get_error(Validation::ERROR_FORMAT_DOTTED_DETAILED);
        }
    }

    public function test_complete_example()
    {
        $data = [
            "id" => 1,
            "name" => "GH",
            "age" => 18,
            "favorite_animation" => [
                "name" => "A Record of A Mortal's Journey to Immortality",
                "release_date" => "July 25, 2020 (China)",
                "series_directed_by" => [
                    "",
                    "Yuren Wang",
                    "Zhao Xia"
                ],
                "series_cast" => [
                    [
                        "actor" => "Wenqing Qian",
                        "character" => "Han Li",
                    ],
                    [
                        "actor" => "ShiMeng-Li",
                        "character" => "Nan Gong Wan",
                    ],
                ]
            ]
        ];

        $rule = [
            "id" => "required|/^\d+$/",         // id 是必要的，且只能是数字
            "name" => "required|length><=[3,32]",  // name 是必要的，且字符串长度必须大于3，小于等于32
            "favorite_animation" => [
                // favorite_animation.name 是必要的，且字符串长度必须大于1，小于等于64
                "name" => "required|length><=[1,16]",
                // favorite_animation.release_date 是可选的，如果不为空，那么字符串长度必须大于4，小于等于64
                "release_date" => "optional|length><=[4,64]",
                // "*" 表示 favorite_animation.series_directed_by 是一个索引数组
                "series_directed_by" => [
                    // favorite_animation.series_directed_by.* 每一个子元素必须满足其规则：不能为空且长度必须大于 3
                    "*" => "required|length>[3]"
                ],
                // [optional] 表示 favorite_animation.series_cast 是可选的
                // ".*"(同上面的“*”) 表示 favorite_animation.series_cast 是一个索引数组，每一个子元素又都是关联数组。
                "series_cast" => [
                    "[optional].*" => [
                        // favorite_animation.series_cast.*.actor 不能为空且长度必须大于 3且必须满足正则
                        "actor" => "required|length>[3]|/^[A-Za-z ]+$/",
                        // favorite_animation.series_cast.*.character 不能为空且长度必须大于 3
                        "character" => "required|length>[3]",
                    ]
                ]
            ]
        ];

        $config = [
            'language' => 'zh-cn'
        ];
        // 实例化类，接受一个自定义配置数组，但不必要
        $validation = new Validation($config);

        // 设置验证规则并验证数据，成功返回 true，失败返回 false
        if ($validation->set_rules($rule)->validate($data)) {
            // 这里获取验证结果，有被规则{$rule}验证到的参数，成功则修改其值为true，失败则修改其值为错误信息，
            // 没有被验证到的参数，保持原值不变。比如 age 保持 18 不变。
            return $validation->get_result();
        } else {
            // 一共有四种错误信息格式可供选择。默认 Validation::ERROR_FORMAT_DOTTED_GENERAL
            return $validation->get_error();
        }
    }

    public function test_get_method_and_symbol($language = 'en-us', $skip_deprecated = 1)
    {
        if (is_numeric($language)) {
            $skip_deprecated = $language;
            $language = 'zh-cn';    // en-us, zh-cn
        }
        $config = [
            'language' => $language
        ];
        $validation = new Validation($config);

        $validation_config = $validation->get_config();
        $config_default = $validation->get_config_default();
        $method_symbols_reversed = $validation->get_method_symbols(true);
        $deprecated_method_symbols_reversed = $validation->get_deprecated_method_symbols(true);
        $error_templates = $validation->get_error_templates();

        /**
         * Some symbols are allowed to be customized.
         * So their symbol should be got from Validation::config
         * Here is the map:
         * - Error Template Symbol -> Validation Config Field
         */
        $built_in_methods = [
            'default' => '/',
            'index_array' => 'symbol_index_array',
            'required' => 'symbol_required',
            'optional' => 'symbol_optional',
            'optional_unset' => 'symbol_optional_unset',
            // 'preg' => 'reg_preg',
            'preg' => '/',
            // 'preg_format' => 'reg_preg_strict',
            'preg_format' => '/',
            'call_method' => '/',
            'when' => 'symbol_when',
            'when_not' => 'symbol_when_not',
        ];

        $header = "";
        if ($language == 'zh-cn') {
            $header = "标志 | 方法 | 可变长度参数 | 错误消息模板\n";
        } else if ($language == 'en-us') {
            $header = "Symbol | Method | Variable-Length Arguments | Error Message Template\n";
        }
        $header .= "---|---|:---:|---\n";
        $method_symbol_table = $header;

        foreach ($error_templates as $symbol => $method_error_template) {
            $is_deprecated = isset($deprecated_method_symbols_reversed[$symbol]);
            if ($is_deprecated && $skip_deprecated) continue;
            /**
             * Every method supports "when" or "when not" rule.
             * The default symbol of "when" or "when not" rule is ":when" or ":when_not", and they can be customized, default ":?" and ":!?"
             * We use the default symbol in the error templates, such as "required:when" instead of "required:?"
             * So we should convert the default symbol into customized symbol for README.
             */
            if (preg_match("/^(.*)({$config_default['symbol_when']}|{$config_default['symbol_when_not']})$/", $symbol, $matches)) {
                $symbol_1 = $matches[1];
                $method_and_symbol_1 = $this->get_method_and_symbol($symbol_1, $built_in_methods, $validation_config, $method_symbols_reversed);
                // $method_1 = $method_and_symbol_1['method'];
                $symbol_1 = $method_and_symbol_1['symbol'];

                if ($matches[2] == $config_default['symbol_when']) $symbol_when_type = 'when';
                else $symbol_when_type = 'when_not';
                $method_and_symbol_2 = $this->get_method_and_symbol($symbol_when_type, $built_in_methods, $validation_config, $method_symbols_reversed);
                // $method_2 = $method_and_symbol_2['method'];
                $symbol_2 = $method_and_symbol_2['symbol'];
                $method = $symbol;
                $symbol = "{$symbol_1}{$symbol_2}";
            } else {
                $method_and_symbol = $this->get_method_and_symbol($symbol, $built_in_methods, $validation_config, $method_symbols_reversed);
                $method = $method_and_symbol['method'];
                $symbol = $method_and_symbol['symbol'];
            }
            
            if ($is_deprecated) {
                $symbol = "~~{$symbol}~~";
            } else if (!empty($symbol) && !in_array($symbol, ['/'])) {
                $symbol = "`{$symbol}`";
            }

            $is_variable_length_argument = false;
            if (is_array($method)) {
                $is_variable_length_argument = !empty($method['is_variable_length_argument']);
                $method = $method['method'];
            }
            if ($is_variable_length_argument) {
                $ivla_message = $this->translate($language, 'Yes');
                $ivla_message = "**$ivla_message**";
            } else {
                $ivla_message = $this->translate($language, 'No');
            }

            $method_symbol_table .= "{$symbol} | `{$method}` | {$ivla_message} | {$method_error_template}\n";
        }

        $this->write_log(static::LOG_LEVEL_INFO, "\n{$method_symbol_table}");
    }

    protected function get_method_and_symbol($symbol, $built_in_methods, $validation_config, $method_symbol_reversed)
    {
        if (isset($built_in_methods[$symbol])) {
            $method = $symbol;
            $symbol = $validation_config[$built_in_methods[$method]] ?? $built_in_methods[$method];
        } else {
            if (isset($method_symbol_reversed[$symbol])) {
                $method = $method_symbol_reversed[$symbol];
            } else {
                $method = $symbol;
                $symbol = '/';
            }
        }

        return [
            'method' => $method,
            'symbol' => $symbol
        ];
    }

    protected function translate($language, $message)
    {
        if ($language == 'en-us') return $message;

        static $translation = [
            'zh-cn' => [
                'Yes' => '是',
                'No' => '否'
            ]
        ];

        return $translation[$language][$message];
    }

    protected function validate($data, $rule, $validation_conf = [])
    {
        $validation = new Validation($validation_conf);

        if ($validation->set_rules($rule)->validate($data)) {
            return $validation->get_result();
        } else {
            return $validation->get_error();
        }
    }

    protected function validate_data_lists($data_list, $rule, $validation_conf = [])
    {
        $result_lists = [];
        foreach ($data_list as $key => $data) {
            $result_lists['result-of_'.$key] = $this->validate($data, $rule, $validation_conf);
        }

        return $result_lists;
    }
}
