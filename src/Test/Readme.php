<?php

// namespace githusband\Test;

require_once __DIR__ . '/TestCommon.php';

use githusband\Validation;

/**
 * 1. 推荐用 trait 拓展验证方法
 * 如果需要定义方法标志，将他们放在属性中，属性命名规则：“method_symbol_of_” + 类名（大驼峰转下划线）
 */
trait RuleCustome
{
    protected $method_symbol_of_rule_custome = [
        '=1' => 'euqal_to_1',
    ];

    protected function euqal_to_1($data)
    {
        return $data == 1;
    }
}

/**
 * 2. 拓展类，直接增加验证方法
 * 如果需要定义方法标志，将他们放在属性 method_symbol 中
 */
class MyValidation extends Validation
{
    use RuleCustome;

    protected $method_symbol = [
        ">=1" => "grater_than_or_equal_to_1",
    ];

    protected function grater_than_or_equal_to_1($data)
    {
        return $data >= 1;
    }
}

class Readme extends TestCommon
{

    public function __construct()
    {
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
            "name" => "required|len<=>[3,32]",  // 必要的，且字符串长度必须大于3，小于等于32
            "favorite_animation" => [
                "name" => "required|len<=>[1,64]",          // 必要的，且字符串长度必须大于1，小于等于64
                "release_date" => "optional|len<=>[4,64]",  // 可选的，如果不为空，那么字符串长度必须大于4，小于等于64
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
                // return array(
                //     'error_type' => 'server_error',
                //     'message' => '@this check_id failed',
                //     "extra" => "extra message"
                // );
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
            "height_unit" => "required|(s)[cm,m]",
            // 并联
            "height[or]" => [
                // 若身高单位是厘米 cm, 则身高必须大于等于100，小于等于200 
                "required|=(@height_unit,cm)|<=>=[100,200]",
                // 若身高单位是米 m, 则身高必须大于等于1，小于等于2
                "required|=(@height_unit,m)|<=>=[1,2]",
            ]
        ];

        // 也可以这么写，标志 [||] 可以替换成 [or]
        $rule = [
            // 串联，身高单位是必须的，且必须是 cm 或者 m
            "height_unit" => "required|(s)[cm,m]",
            // 并联
            "height" => [
                "[||]" => [
                    // 若身高单位是厘米 cm, 则身高必须大于等于100，小于等于200 
                    "required|=(@height_unit,cm)|<=>=[100,200]",
                    // 若身高单位是米 m, 则身高必须大于等于1，小于等于2
                    "required|=(@height_unit,m)|<=>=[1,2]",
                ]
            ]
        ];

        return $this->validate($data, $rule);
    }

    public function test_condition_if_yes()
    {
        $data = [
            "attribute" => "weight",
            "centimeter" => ''
        ];

        $rule = [
            // 特征是必要的，且只能是 height(身高) 或 weight(体重)
            "attribute" => "required|(s)[height,weight]",
            // 若属性是 height, 则 centimeter 是必要的，若为 weight，则是可选的。
            // 无论如何，若该值非空，则必须大于 180
            "centimeter" => "if(=(@attribute,height))|required|>[180]",
        ];

        return $this->validate($data, $rule);
    }

    public function test_condition_if_no()
    {
        $data = [
            "attribute" => "weight",
            "centimeter" => ''
        ];

        $rule = [
            // 特征是必要的，且只能是 height(身高) 或 weight(体重)
            "attribute" => "required|(s)[height,weight]",
            // 若属性不是 weight, 则 centimeter 是必要的，若为 weight，则是可选的。
            // 无论如何，若该值非空，则必须大于 180
            "centimeter" => "!if(=(@attribute,weight))|required|>[180]",
        ];

        return $this->validate($data, $rule);
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
            "name" => "required|len>[3]",
            "favourite_fruit" => [
                "name" => "required|len>[3]",
                "color" => "required|len>[3]",
                "shape" => "required|len>[3]"
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
            "name" => "required|len>[3]",
            "favourite_color.*" => "required|len>[3]",
            "favourite_fruits.*" => [
                "name" => "required|len>[3]",
                "color" => "required|len>[3]",
                "shape" => "required|len>[3]"
            ]
        ];

        // 若要验证上述 $data，规则也可以这么写
        $rule = [
            "id" => "required|/^\d+$/",
            "name" => "required|len>[3]",
            "favourite_color" => [
                "*" => "required|len>[3]"
            ],
            "favourite_fruits" => [
                "*" => [
                    "name" => "required|len>[3]",
                    "color" => "required|len>[3]",
                    "shape" => "required|len>[3]"
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
            "gender" => [ "[optional]" => "string" ],
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
            "name" => "!*&&len<=>~3+32",        // 必要的，且字符串长度必须大于3，小于等于32
            "favorite_animation" => [
                "name" => "!*&&len<=>~1+64",                // 必要的，且字符串长度必须大于1，小于等于64
                "release_date" => "o?&&len<=>#@this+4+64",  // 可选的，如果不为空，那么字符串长度必须大于4，小于等于64
            ]
        ];

        $custom_config = [
            // 'language' => 'en-us',                                  // Language, default is en-us
            // 'lang_path' => '',                                      // Customer Language file path
            // 'validation_global' => true,                            // If true, validate all rules; If false, stop validating when one rule was invalid
            // 'auto_field' => "root",                                 // If root data is string or numberic array, add the auto_field to the root data, can validate these kind of data type.
            // 'reg_msg' => '/ >>>(.*)$/',                             // Set special error msg by user 
            'reg_preg' => '/^Reg:(\/.+\/.*)$/',                     // If match this, using regular expression instead of method
            // 'reg_preg_strict' => '/^(\/.+\/[imsxADSUXJun]*)$/',     // Verify if the regular expression is valid
            // 'reg_if' => '/^IF[yn]?\?(.*)$/',                        // If match this, validate this condition first
            // 'reg_if_true' => '/^IFy?\?/',                           // If match this, validate this condition first, if true, then validate the field
            // 'reg_if_false' => '/^IFn\?/',                           // If match this, validate this condition first, if false, then validate the field
            'symbol_rule_separator' => '&&',                        // Rule reqarator for one field
            'symbol_param_this_omitted' => '/^(.*)~(.*)$/',         // If set function by this symbol, will add a @this parameter at first 
            'symbol_param_standard' => '/^(.*)#(.*)$/',             // If set function by this symbol, will not add a @this parameter at first 
            'symbol_param_separator' => '+',                        // Parameters separator, such as @this,@field1,@field2
            'symbol_field_name_separator' => '->',                  // Field name separator, suce as "fruit.apple"
            'symbol_required' => '!*',                              // Symbol of required field, Same as "required"
            'symbol_optional' => 'o?',                              // Symbol of optional field, can be unset or empty, Same as "optional"
            // 'symbol_optional_unset' => 'Ox',                        // Symbol of optional field, can only be unset or not empty, Same as "optional_unset"
            // 'symbol_or' => '[||]',                                  // Symbol of or rule, Same as "[or]"
            // 'symbol_array_optional' => '[o]',                       // Symbol of array optional rule, Same as "[optional]"
            // 'symbol_index_array' => '[N]',                          // Symbol of index array rule
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

        $validation = new Validation();
        $validation->add_method('check_animal', function ($animal) {
            if ($animal == "") {
                return false;
            } else if ($animal == "mouse") {
                return "I don't like mouse";
            } else if ($animal == "snake") {
                return array(
                    "error_type" => "server_error",
                    "message" => "I don't like snake",
                    "extra" => "You scared me"
                );
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

    public function test_full_example()
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
            "name" => "required|len<=>[3,32]",  // name 是必要的，且字符串长度必须大于3，小于等于32
            "favorite_animation" => [
                // favorite_animation.name 是必要的，且字符串长度必须大于1，小于等于64
                "name" => "required|len<=>[1,16]",
                // favorite_animation.release_date 是可选的，如果不为空，那么字符串长度必须大于4，小于等于64
                "release_date" => "optional|len<=>[4,64]",
                // "*" 表示 favorite_animation.series_directed_by 是一个索引数组
                "series_directed_by" => [
                    // favorite_animation.series_directed_by.* 每一个子元素必须满足其规则：不能为空且长度必须大于 3
                    "*" => "required|len>[3]"
                ],
                // [optional] 表示 favorite_animation.series_cast 是可选的
                // ".*"(同上面的“*”) 表示 favorite_animation.series_cast 是一个索引数组，每一个子元素又都是关联数组。
                "series_cast" => [
                    "[optional].*" => [
                        // favorite_animation.series_cast.*.actor 不能为空且长度必须大于 3且必须满足正则
                        "actor" => "required|len>[3]|/^[A-Za-z ]+$/",
                        // favorite_animation.series_cast.*.character 不能为空且长度必须大于 3
                        "character" => "required|len>[3]",
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

    public function test_get_method_and_symbol()
    {
        $language = 'en-us';
        $config = [
            'language' => $language
        ];
        $validation = new Validation($config);

        $config = $validation->get_config();
        $method_symbol = $validation->get_method_symbol();
        $error_template = $validation->get_error_template();

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
        ];

        $header = "";
        if ($language == 'zh-cn') {
            $header = "标志 | 方法 | 含义\n";
        } else if ($language == 'en-us') {
            $header = "Symbol | Method | Desc\n";
        }
        $header .= "---|---|---\n";
        $method_symbol_table = $header;

        foreach ($error_template as $symbol => $method_error_template) {
            // $method = $method_symbol[$symbol] ?? $symbol;
            
            if (isset($built_in_methods[$symbol])) {
                $method = $symbol;
                $symbol = $config[$built_in_methods[$method]] ?? $built_in_methods[$method];
            } else {
                if (isset($method_symbol[$symbol])) {
                    $method = $method_symbol[$symbol];
                } else {
                    $method = $symbol;
                    $symbol = '/';
                }
            }
            if (!empty($symbol) && !in_array($symbol, ['/'])) $symbol = "`{$symbol}`";
            $method_symbol_table .= "{$symbol} | `{$method}` | {$method_error_template}\n";
        }

        echo "\n{$method_symbol_table}";
    }

    protected function validate($data, $rule, $validation_conf = array())
    {
        $validation = new Validation($validation_conf);

        if ($validation->set_rules($rule)->validate($data)) {
            return $validation->get_result();
        } else {
            return $validation->get_error();
        }
    }
}

$method = isset($argv[1]) ? $argv[1] : "error";

$test = new Readme();

if (method_exists($test, $method)) {
    $result = call_user_func_array([$test, $method], []);
} else {
    echo "Error test method {$method}.\n";
    die;
}

echo json_encode($result, JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE) . "\n";
die;
print_r($result);
