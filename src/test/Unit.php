<?php
/**
 * php Unit.php run [method_name]
 *
 * method_name - optional. If empty, run all test case. if is not empty, only run this test case.
 */


function check_id($data, $min, $max) {
    return $data >= $min && $data <= $max;
}

function check_age($data, $gender, $param) {
    if($gender == "male") {
        if($data > $param) return false;
    }else {
        if($data < $param) return false;
    }

    return true;
}

require_once(__DIR__."/../Validation.php");
// require_once(__DIR__."/vendor/autoload.php");
use githusband\Validation;

class Unit {
    // Validation Instance
    protected $validation;

    // Unit Testing error message
    protected $error_message = array();
    // If true, means unit testing is error
    protected $is_error = false;

    private $_symbol_me = "@me";

    public function __construct()
    {
        $validation_conf = [
            "validation_global" => false,
        ];
        $this->validation = new Validation($validation_conf);
    }

    /**
     * Run uint testing.
     * Auto-executing two kind of methods:
     *  1. Method name starting with "test_" - This kind of method only contains validation config, suce as test cases
     *  2. Method name starting with "test_" and end with "execute" - This kind of method only contains validation config, suce as test cases, and will execute validation.
     * If unit testing is error, stop test and return error message immediatelly
     *   
     * @Author   Devin
     * @return   [type]                   [description]
     */
    public function run($method_name='') {
        echo "Start run {$method_name}\n";

        if(!empty($method_name)) {
            $this->execute_tests($method_name);
        }else {
            $class_methods = get_class_methods($this);

            foreach ($class_methods as $method_name) {
                if(preg_match('/^test_.*/', $method_name)) {


                    $result = $this->execute_tests($method_name);

                    if(!$result) break;
                }
            }
        }

        return $this->get_unit_result();
    }

    /**
     * Auto-executing two kind of methods:
     *  1. Method name starting with "test_" - This kind of method only contains validation config, suce as test cases
     *  2. Method name starting with "test_" and end with "execute" - This kind of method only contains validation config, suce as test cases, and will execute validation.
     * @Author   Devin
     * @param    [type]                     $method_name [description]
     * @param    mixed                      $error_data  If is array, will get config data of method
     * @return   [type]                                [description]
     */
    protected function execute_tests($method_name, $error_data=false) {
        echo "Testing method - {$method_name}\n";

        if(preg_match('/^.*_execute$/', $method_name)) {
            return $this->{$method_name}($error_data);
        }

        $method_info = $this->{$method_name}();

        if($error_data !== false) return $this->get_method_info($method_info['rule'], $method_info['cases'], $method_info['extra'], $error_data);

        return $this->valid_cases($method_info['rule'], $method_info['cases'], $method_info['extra']);
    }

    /**
     * Auto-Validate two kind of cases:
     *  1. Start with "Valid" - If validation result is not true, means this case is error. 
     *  2. Start with "Invalid" - If validation result is not true, then check the validation error message is expected(expected_msg field of a case array), if not, means this case is error.
     * Each case contains:
     *  1. data - case data 
     *  2. expected_msg - expected validation error message
     *  3. err_format => [
     *      'standard' => is standard error format
     *      'simple' => is simple error format
     *  ],
     *  4. error_tag - get error message of the error_tag
     *  5. field_path - parse error message of @me
     * extra contains:
     *  1. method_name
     *  2. error_tag - use this if case do not contains error_tag
     *  3. field_path - use this if case do not contains field_path
     * @Author   Devin
     * @param    [type]                   $rule   validation rule
     * @param    [type]                   $cases  test cases, has many test case
     * @param    [type]                   $extra  extra message, such as method name
     * @return   [type]                           [description]
     */
    protected function valid_cases($rule, $cases, $extra) {
        $this->validation->set_rules($rule);

        $result = true;

        foreach($cases as $c_field => $case) {
            $standard = isset($case['err_format']['standard'])? $case['err_format']['standard'] : false;
            $simple = isset($case['err_format']['simple'])? $case['err_format']['simple'] : true;

            // Check valid cases
            if(strpos($c_field, "Valid") !== false) {
                $valid_alert = isset($case['valid_alert'])? $case['valid_alert'] : "Validation error. It should be valid.";

                if(!$this->validation->validate($case['data'])) {
                    $this->set_unit_error($extra['method_name'], $c_field, [
                        "valid_alert" => $valid_alert,
                        "error_msg" => $this->validation->get_error($standard, $simple)
                    ], $rule, $cases);
                    $result = false;
                }

            // Check invalid cases
            }else if(strpos($c_field, "Invalid") !== false) {
                $valid_alert = isset($case['valid_alert'])? $case['valid_alert'] : "Validation error. It should be invalid.";

                if($this->validation->validate($case['data'])) {
                    $this->set_unit_error($extra['method_name'], $c_field, $valid_alert, $rule, $cases);
                    $result = false;
                }else {
                    if(isset($case["check_error_msg"]) && $case["check_error_msg"] == false) continue;

                    // If invalid, check error massage if it's expected.
                    if(isset($case["expected_msg"])) {
                        $expected_msg = $case["expected_msg"];
                    }else {
                        $error_tag = isset($extra['error_tag'])? $extra['error_tag'] : 'default';
                        $error_tag = isset($case['error_tag'])? $case['error_tag'] : $error_tag;
                        $field_path = isset($extra['field_path'])? $extra['field_path'] : 'Unknown field path';
                        $field_path = isset($case['field_path'])? $case['field_path'] : $field_path;
                        $params = isset($extra['parameters'])? $extra['parameters'] : [];
                        $params = isset($case['parameters'])? $case['parameters'] : $params;
                        $expected_msg = $this->parse_error_message($error_tag, $field_path, $params);
                    }

                    $error_msg = $this->validation->get_error($standard, $simple);
                    if($expected_msg !== $error_msg) {
                        $this->set_unit_error($extra['method_name'], $c_field, [
                            "Error msg is unexpected.", 
                            [
                                "expected" => $expected_msg,
                                "current" => $error_msg
                            ]
                        ], $rule, $cases);
                        $result = false;
                    }
                }
            }
        }

        return $result;
    }

    protected function set_unit_error($method, $cases_field, $error_message, $rule, $cases) {
        $this->is_error = true;
        $this->error_message[$method]["rule"] = $rule;
        $this->error_message[$method]["cases"][$cases_field] = $cases[$cases_field]["data"];

        $this->error_message[$method]["error"][$cases_field] = $error_message;
    }

    protected function parse_error_message($tag, $field_path, $params=array()) {
        $error_template = $this->validation->get_error_template($tag);
        $error_template = str_replace($this->_symbol_me, $field_path, $error_template);

        foreach($params as $key => $value) {
            $error_template = str_replace('@p'.($key+1), $value, $error_template);
        }

        return $error_template;
    }

    protected function get_unit_result() {
        if($this->is_error) {
            return $this->error_message;
        }else {
            return "*******************************\nUnit test success!\n*******************************\n";
        }
    }

    protected function set_method_info() {
        foreach($this->error_message as $unit_method => $um_value) {
            $method = str_replace(__CLASS__."::", "", $unit_method);
            $method_info = $this->execute_tests($method, $um_value);

            $this->error_message[$unit_method]["rule"] = $method_info["rule"];
            $this->error_message[$unit_method]["cases"] = $method_info["cases"];
        }
    }

    protected function get_method_info($rule, $cases, $extra, $error_data=array()) {
        $method_info = [
            "rule" => $rule,
            "cases" => [],
            "extra" => $extra
        ];

        if(empty($error_data)) {
            $method_info["cases"] = $cases;
        }else {
            foreach($error_data as $field => $value) {
                $method_info["cases"][$field] = isset($method_info[$field])? $method_info[$field]["data"] : "Unset";
            }
        }
        
        return $method_info;
    }

    protected function test_series_rule() {
        $rule = [
            "name" => "*|string|/^\d+.*/"
        ];

        $cases = [
            "Valid_data" => [
                "data" => [
                    "name" => "123ABC"
                ]
            ],
            "Invalid_empty" => [
                "data" => [
                    "name" => ""
                ],
                "expected_msg" => "name can not be empty"
            ],
            "Invalid_not_string" => [
                "data" => [
                    "name" => 123
                ],
                "expected_msg" => "name must be string"
            ],
            "Invalid_not_start_num" => [
                "data" => [
                    "name" => "abcABC"
                ],
                "expected_msg" => "name format is invalid, should be /^\d+.*/"
            ]
        ];

        $extra = [
            "method_name" => __METHOD__,
            "field_path" => "name",
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_if_rule() {
        $rule = [
            "id" => "*|<>:0,10",
            "name" => "if?<::@id,5|*|string|/^\d+.*/",
            "name1" => "if1?<::@id,5|*|string|/^\d+.*/",
            "name0" => "if0?<::@id,5|*|string|/^\d+.*/",
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "id" => 1,
                    "name" => "123ABC",
                    "name1" => "123ABC",
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "id" => 8,
                    "name" => "",
                    "name0" => "123ABC"
                ]
            ],
            "Invalid_data_1" => [
                "data" => [
                    "id" => 1,
                    "name" => ""
                ],
                "expected_msg" => "name can not be empty"
            ],
            "Invalid_data_2" => [
                "data" => [
                    "id" => 1,
                    "name" => "abc"
                ],
                "expected_msg" => "name format is invalid, should be /^\d+.*/"
            ],
            "Invalid_data_3" => [
                "data" => [
                    "id" => 1,
                    "name" => "123ABC",
                    "name1" => "abc",
                ],
                "expected_msg" => "name1 format is invalid, should be /^\d+.*/"
            ],
            "Invalid_data_4" => [
                "data" => [
                    "id" => 8,
                    "name0" => "abc"
                ],
                "expected_msg" => "name0 format is invalid, should be /^\d+.*/"
            ],
            "Invalid_data_5" => [
                "data" => [
                    "id" => 8,
                    "name1" => "abc",
                    "name0" => "abc"
                ],
                "expected_msg" => "name1 format is invalid, should be /^\d+.*/"
            ]
        ];

        $extra = [
            "method_name" => __METHOD__,
            "field_path" => "name",
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_optional() {
        $rule = [
            "name" => "O|string",
            "gender" => [ "[O]" => "string"],
            "favourite_fruit[O]" => [
                "name" => "*|string",
                "color" => "*|string"
            ],
            "favourite_meat" => [
                "[O]" => [
                    "name" => "*|string",
                    "from" => "*|string"
                ]
            ],
        ];

        $cases = [
            "Valid_set" => [
                "data" => [
                    "name" => "",
                    "gender" => "",
                    "favourite_fruit" => [],
                    "favourite_meat" => [],
                ]
            ],
            "Valid_unset" => [
                "data" => [
                ]
            ],
            "Valid_data" => [
                "data" => [
                    "name" => "Devin",
                    "gender" => "male",
                    "favourite_fruit" => [
                        "name" => "Apple",
                        "color" => "red"
                    ],
                    "favourite_meat" => [
                        "name" => "Beef",
                        "from" => "Cattle"
                    ],
                ]
            ],
            "Invalid_data_1" => [
                "data" => [
                    "name" => 1,
                    "gender" => "male",
                ],
                "expected_msg" => "name must be string"
            ],
            "Invalid_data_2" => [
                "data" => [
                    "name" => "Devin",
                    "gender" => 1,
                ],
                "expected_msg" => "gender must be string"
            ],
            "Invalid_data_3" => [
                "data" => [
                    "name" => "Devin",
                    "gender" => "male",
                    "favourite_fruit" => [
                        "name" => "Apple",
                        "color" => 1
                    ],
                ],
                "expected_msg" => "favourite_fruit.color must be string"
            ],
            "Invalid_data_4" => [
                "data" => [
                    "name" => "Devin",
                    "gender" => "male",
                    "favourite_fruit" => [
                        "name" => "Apple",
                        "color" => "red"
                    ],
                    "favourite_meat" => [
                        "name" => 1,
                        "from" => "Cattle"
                    ],
                ],
                "expected_msg" => "favourite_meat.name must be string"
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "error_tag" => "O",
            "field_path" => "name",
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_or_rule() {
        $rule = [
            "name[||]" => [
                "*|bool",
                "*|bool_str",
            ],
            "height" => [
                "[||]" => [
                    "*|int|>:100",
                    "*|string",
                ]
            ]
        ];

        $cases = [
            "Valid_data" => [
                "data" => [
                    "name" => false,
                    "height" => 170
                ]
            ],
            "Valid_data2" => [
                "data" => [
                    "name" => "false",
                    "height" => "1.70m"
                ]
            ],
            "Invalid_empty" => [
                "data" => [
                    "name" => ""
                ],
                "expected_msg" => "name can not be empty"
            ],
            "Invalid_0" => [
                "data" => [
                    "name" => 0
                ],
                "expected_msg" => "name must be boolean or name must be boolean string"
            ],
            "Invalid_1" => [
                "data" => [
                    "name" => "false",
                    "height" => 50,
                ],
                "expected_msg" => "height must be greater than 100 or height must be string"
            ]
        ];

        $extra = [
            "method_name" => __METHOD__,
            "field_path" => "name",
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_assoc_array() {
        $rule = [
            "person" => [
                "name" => "*|string|/^\d+.*/"
            ]
        ];

        $cases = [
            "Valid_data" => [
                "data" => [
                    "person" => [
                        "name" => "123ABC"
                    ]
                ]
            ],
            "Invalid_empty" => [
                "data" => [
                    "person" => [
                        "name" => ""
                    ]
                ],
                "expected_msg" => "person.name can not be empty"
            ],
            "Invalid_not_string" => [
                "data" => [
                    "person" => [
                        "name" => 123
                    ]
                ],
                "expected_msg" => "person.name must be string"
            ],
            "Invalid_not_start_num" => [
                "data" => [
                    "person" => [
                        "name" => "abcABC"
                    ]
                ],
                "expected_msg" => "person.name format is invalid, should be /^\d+.*/"
            ]
        ];

        $extra = [
            "method_name" => __METHOD__,
            "field_path" => "person.name",
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_numberic_array() {
        $rule = [
            "person[n]" => [
                "name" => "*|string|/^\d+.*/",
                "relation" => [
                    "father" => "*|string",
                    "mother" => "O|string",
                ]
            ],
            "pet" => [
                "*|string",
                "*|string",
                [
                    "*|string",
                    "*|string",
                ]
            ],
            "flower[n]" => "*|string",
            "clothes[O]" => [
                [
                    "*|string",
                ]
            ],
            "shoes" => [
                "[O],[n]" => "*|string"
            ],
        ];

        $cases = [
            "Valid_data" => [
                "data" => [
                    "person" => [
                        ["name" => "123", "relation" => ["father" => "f123", "mother" => "m123"]],
                        ["name" => "123ABC", "relation" => ["father" => "f123ABC", "mother" => ""]],
                    ],
                    "pet" => [
                        "cat",
                        "dog",
                        ["cat", "dog"],
                    ],
                    "flower" => [
                        "Rose",
                        "Narcissu",
                        "Peony",
                    ],
                    "clothes" => [
                        ["cat", "dog"],
                        ["", "dog"],
                    ],
                ]
            ],
            "Invalid_item_0-0(assoc_arr)" => [
                "data" => [
                    "person" => [
                        [],
                        ["name" => ""],
                    ]
                ],
                "expected_msg" => "person.0.name can not be empty"
            ],
            "Invalid_item_0-1(assoc_arr)" => [
                "data" => [
                    "person" => [
                        ["name" => "123", "relation" => ["father" => "f123", "mother" => "m123"]],
                        ["name" => "abcABC"],
                    ]
                ],
                "expected_msg" => "person.1.name format is invalid, should be /^\d+.*/"
            ],
            "Invalid_item_0-2(assoc_arr)" => [
                "data" => [
                    "person" => [
                        ["name" => "123", "relation" => ["father" => "f123", "mother" => "m123"]],
                        ["name" => "123ABC", "relation" => ["father" => "", "mother" => "m123ABC"]],
                    ]
                ],
                "expected_msg" => "person.1.relation.father can not be empty"
            ],
            "Invalid_item_1-0(static,string)" => [
                "data" => [
                    "person" => [
                        ["name" => "123", "relation" => ["father" => "f123", "mother" => "m123"]],
                        ["name" => "123ABC", "relation" => ["father" => "f123ABC", "mother" => "m123ABC"]],
                    ],
                    "pet" => [
                        "",
                        "ABC",
                    ]
                ],
                "expected_msg" => "pet.0 can not be empty"
            ],
            "Invalid_item_1-1(static,string)" => [
                "data" => [
                    "person" => [
                        ["name" => "123", "relation" => ["father" => "f123", "mother" => "m123"]],
                        ["name" => "123ABC", "relation" => ["father" => "f123ABC", "mother" => "m123ABC"]],
                    ],
                    "pet" => [
                        "123",
                        123,
                    ]
                ],
                "expected_msg" => "pet.1 must be string"
            ],
            "Invalid_item_1-2(static,string)" => [
                "data" => [
                    "person" => [
                        ["name" => "123", "relation" => ["father" => "f123", "mother" => "m123"]],
                        ["name" => "123ABC", "relation" => ["father" => "f123ABC", "mother" => "m123ABC"]],
                    ],
                    "pet" => [
                        "cat",
                        "dog",
                        ["cat", ""],
                    ]
                ],
                "expected_msg" => "pet.2.1 can not be empty"
            ],
            "Invalid_item_2-0(dynamic,string)" => [
                "data" => [
                    "person" => [
                        ["name" => "123", "relation" => ["father" => "f123", "mother" => "m123"]],
                        ["name" => "123ABC", "relation" => ["father" => "f123ABC", "mother" => "m123ABC"]],
                    ],
                    "pet" => [
                        "cat",
                        "dog",
                        ["cat", "dog"],
                    ],
                    "flower" => [
                        "",
                        "Narcissu",
                        "Peony",
                    ],
                ],
                "expected_msg" => "flower.0 can not be empty"
            ],
            "Invalid_item_2-1(dynamic,string)" => [
                "data" => [
                    "person" => [
                        ["name" => "123", "relation" => ["father" => "f123", "mother" => "m123"]],
                        ["name" => "123ABC", "relation" => ["father" => "f123ABC", "mother" => "m123ABC"]],
                    ],
                    "pet" => [
                        "cat",
                        "dog",
                        ["cat", "dog"],
                    ],
                    "flower" => [
                        "Rose",
                        "Narcissu",
                        123,
                    ],
                ],
                "expected_msg" => "flower.2 must be string"
            ],
            "Invalid_item_3-0" => [
                "data" => [
                    "person" => [
                        ["name" => "123", "relation" => ["father" => "f123", "mother" => "m123"]],
                        ["name" => "123ABC", "relation" => ["father" => "f123ABC", "mother" => ""]],
                    ],
                    "flower" => [
                        "Rose",
                        "Narcissu",
                        "Peony",
                    ],
                    "pet" => [
                        "cat",
                        "dog",
                        ["cat", "dog"],
                    ],
                    "clothes" => [
                        "",
                        ["cat", "dog"],
                        ["", "dog"],
                    ],
                ],
                "expected_msg" => "clothes.0.0 can not be empty"
            ],
            "Invalid_item_4-0" => [
                "data" => [
                    "person" => [
                        ["name" => "123", "relation" => ["father" => "f123", "mother" => "m123"]],
                        ["name" => "123ABC", "relation" => ["father" => "f123ABC", "mother" => ""]],
                    ],
                    "flower" => [
                        "Rose",
                        "Narcissu",
                        "Peony",
                    ],
                    "pet" => [
                        "cat",
                        "dog",
                        ["cat", "dog"],
                    ],
                    "clothes" => [
                        ["cat", "dog"],
                        ["", "dog"],
                    ],
                    "shoes" => [
                        "Nike",
                        "",
                        // ["cat", "dog"],
                        // ["", "dog"],
                    ],
                ],
                "expected_msg" => "shoes.1 can not be empty"
            ],
            "Invalid_item_4-1" => [
                "data" => [
                    "person" => [
                        ["name" => "123", "relation" => ["father" => "f123", "mother" => "m123"]],
                        ["name" => "123ABC", "relation" => ["father" => "f123ABC", "mother" => ""]],
                    ],
                    "flower" => [
                        "Rose",
                        "Narcissu",
                        "Peony",
                    ],
                    "pet" => [
                        "cat",
                        "dog",
                        ["cat", "dog"],
                    ],
                    "clothes" => [
                        ["cat", "dog"],
                        ["", "dog"],
                    ],
                    "shoes" => [
                        ["cat", "dog"],
                    ],
                ],
                "expected_msg" => "shoes.0 must be string"
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_numberic_assoc_array() {
        $rule = [
            // "person[n]" => [
            //     "name" => "*|string",
            //     "relation" => [
            //         "father" => "*|string",
            //         "mother" => "O|string",
            //         "brother" => [
            //             "[O],[n]" => [
            //                 "[n]" => [
            //                     "name" => "*|string",
            //                     "level" => [
            //                         "[||]" => [
            //                             "*|int",
            //                             "*|string",
            //                         ]
            //                     ]
            //                 ]
            //             ]
            //         ]
            //     ],
            //     "fruit" => [
            //         "[n]" => [
            //             "[n]" => [
            //                 "name" => "*|string",
            //                 "color" => "O|string",
            //             ]
                        
            //         ]
            //     ],
            // ],
            "person" => [
                "[n]" => [
                    "name" => "*|string",
                    "relation" => [
                        "father" => "*|string",
                        "mother" => "O|string",
                        "brother" => [
                            "[O],[n]" => [
                                "[n]" => [
                                    "name" => "*|string",
                                    "level" => [
                                        "[||]" => [
                                            "*|int",
                                            "*|string",
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    "fruit" => [
                        "[n]" => [
                            "[n]" => [
                                "name" => "*|string",
                                "color" => "O|string",
                            ]

                        ]
                    ],
                ]
            ],
        ];

        $cases = [
            "Valid_data" => [
                "data" => [
                    "person" => [
                        [
                            "name" => "Devin", 
                            "relation" => [
                                "father" => "fDevin", 
                                "mother" => "mDevin",
                                "brother" => [
                                    [
                                        ["name" => "Tom", "level" => 1],
                                        ["name" => "Mike", "level" => "Second"],
                                    ]
                                ]
                            ],
                            "fruit" => [
                                [
                                    ["name" => "Apple", "color" => "Red"],
                                    ["name" => "Banana", "color" => "Yellow"],
                                ],
                                [
                                    ["name" => "Cherry", "color" => "Red"],
                                    ["name" => "Orange", "color" => "Yellow"],
                                ]
                            ]
                        ],
                        [
                            "name" => "Johnny", 
                            "relation" => ["father" => "fJohnny", "mother" => "mJohnny"],
                            "fruit" => [
                                [
                                    ["name" => "Apple", "color" => "Red"],
                                    ["name" => "Banana", "color" => "Yellow"],
                                ],
                                [
                                    ["name" => "Cherry", "color" => "Red"],
                                    ["name" => "Orange", "color" => "Yellow"],
                                ]
                            ]
                        ],
                    ],
                ]
            ],
            "Invalid_item_0-0" => [
                "data" => [
                    "person" => [
                        [
                            "name" => "Devin", 
                            "relation" => ["father" => "fDevin", "mother" => "mDevin"],
                            "fruit" => [
                                [
                                    ["name" => "", "color" => "Red"],
                                    ["name" => "Banana", "color" => "Yellow"],
                                ],
                                [
                                    ["name" => "Cherry", "color" => "Red"],
                                    ["name" => "Orange", "color" => "Yellow"],
                                ]
                            ]
                        ],
                    ],
                ],
                "expected_msg" => "person.0.fruit.0.0.name can not be empty"
            ],
            "Invalid_item_0-1" => [
                "data" => [
                    "person" => [
                        [
                            "name" => "Devin", 
                            "relation" => ["father" => "fDevin", "mother" => "mDevin"],
                            "fruit" => [
                                [
                                    ["name" => "Apple", "color" => "Red"],
                                    ["name" => "Orange", "color" => "Yellow"],
                                ],
                                [
                                    ["name" => "Cherry", "color" => "Red"],
                                    ["name" => "", "color" => "Yellow"],
                                ]
                            ]
                        ],
                    ],
                ],
                "expected_msg" => "person.0.fruit.1.1.name can not be empty"
            ],
            "Invalid_item_0-2" => [
                "data" => [
                    "person" => [
                        [
                            "name" => "Devin", 
                            "relation" => ["father" => "fDevin", "mother" => "mDevin"],
                            "fruit" => [
                                [
                                    ["name" => "Apple", "color" => "Red"],
                                    ["name" => "Orange", "color" => "Yellow"],
                                ]
                            ]
                        ],
                        [
                            "name" => "Devin", 
                            "relation" => ["father" => "fDevin", "mother" => "mDevin"],
                            "fruit" => [
                                [
                                    ["name" => "Apple", "color" => "Red"],
                                    ["name" => "", "color" => "Yellow"],
                                ]
                            ]
                        ],
                    ],
                ],
                "expected_msg" => "person.1.fruit.0.1.name can not be empty"
            ],
            "Invalid_item_1-0" => [
                "data" => [
                    "person" => [
                        [
                            "name" => "Devin", 
                            "relation" => [
                                "father" => "fDevin", 
                                "mother" => "mDevin",
                                "brother" => [
                                    [
                                        ["name" => "Tom", "level" => false],
                                        ["name" => "Mike", "level" => "Second"],
                                    ]
                                ]
                            ],
                            "fruit" => [
                                [
                                    ["name" => "Apple", "color" => "Red"],
                                    ["name" => "Orange", "color" => "Yellow"],
                                ]
                            ]
                        ],
                    ],
                ],
                "expected_msg" => "person.0.relation.brother.0.0.level must be integer or person.0.relation.brother.0.0.level must be string"
            ],
            "Invalid_item_1-1" => [
                "data" => [
                    "person" => [
                        [
                            "name" => "Devin", 
                            "relation" => [
                                "father" => "fDevin", 
                                "mother" => "mDevin",
                                "brother" => [
                                    [
                                        ["name" => "Tom", "level" => 1],
                                        ["name" => "Mike", "level" => "Second"],
                                    ],
                                    [
                                        ["name" => "Tom", "level" => 1],
                                        ["name" => "Mike", "level" => false],
                                    ]
                                ]
                            ],
                            "fruit" => [
                                [
                                    ["name" => "Apple", "color" => "Red"],
                                    ["name" => "Orange", "color" => "Yellow"],
                                ]
                            ]
                        ],
                    ],
                ],
                "expected_msg" => "person.0.relation.brother.1.1.level must be integer or person.0.relation.brother.1.1.level must be string"
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_root_data_rule_1() {
        $rule = "*|string";

        $cases = [
            "Valid_data" => [
                "data" => "Hello World!",
            ],
            "Invalid_empty" => [
                "data" => "",
                "expected_msg" => "data can not be empty"
            ],
            "Invalid_0" => [
                "data" => 0,
                "expected_msg" => "data must be string"
            ],
            "Invalid_1" => [
                "data" => ["name" => "false"],
                "expected_msg" => "data must be string"
            ]
        ];

        $extra = [
            "method_name" => __METHOD__,
            "field_path" => "data",
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_root_data_rule_2() {
        $rule = [
            "[||]" => [
                "*|string",
                "*|int"
            ]
        ];

        $cases = [
            "Valid_data" => [
                "data" => "Hello World!",
            ],
            "Invalid_empty" => [
                "data" => "",
                "expected_msg" => "data can not be empty"
            ],
            "Invalid_0" => [
                "data" => false,
                "expected_msg" => "data must be string or data must be integer"
            ],
            "Invalid_1" => [
                "data" => ["name" => "false"],
                "expected_msg" => "data must be string or data must be integer"
            ]
        ];

        $extra = [
            "method_name" => __METHOD__,
            "field_path" => "data",
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_root_data_rule_3() {
        $rule = [
            "[O]" => [
                "*|string",
                "*|int"
            ]
        ];

        $cases = [
            "Valid_data" => [
                "data" => ["Hello World!", 1],
            ],
            "Valid_data_empty" => [
                "data" => "",
            ],
            "Invalid_0" => [
                "data" => ["", 1],
                "expected_msg" => "data.0 can not be empty"
            ],
            "Invalid_1" => [
                "data" => ["Hello World!", false],
                "expected_msg" => "data.1 must be integer"
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "field_path" => "data",
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_root_data_rule_4() {
        $rule = [
            "[n]" => "*|string"
        ];

        $cases = [
            "Valid_data" => [
                "data" => ["Hello World!", "1"],
            ],
            "Invalid_data_empty" => [
                "data" => "",
                "expected_msg" => "data must be a numeric array"
            ],
            "Invalid_0" => [
                "data" => ["", 1],
                "expected_msg" => "data.0 can not be empty"
            ],
            "Invalid_1" => [
                "data" => ["Hello World!", false],
                "expected_msg" => "data.1 must be string"
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "field_path" => "data",
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_dynamic_err_msg_simple() {
        $rule = [
            "id" => "*|<=>=:1,100 >> Users define - @me should not be >= @p1 and <= @p2",
            "name" => "*|string >> Users define - @me should not be empty and must be string",
        ];

        $cases = [
            "Invalid_empty" => [
                "data" => [
                    "id" => 101
                ],
                "expected_msg" => "Users define - id should not be >= 1 and <= 100"
            ],
            "Invalid_unset" => [
                "data" => [
                    "id" => 1,
                ],
                "expected_msg" => "Users define - name should not be empty and must be string"
            ]
        ];

        $extra = [
            "method_name" => __METHOD__,
            "field_path" => "name",
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_and_parameter() {
        $rule = [
            "id" => "*|string|check_id:1,100",
            "name" => "*|string|check_name:@id",
            "favourite_fruit" => [
                "fruit_id" => "O|check_fruit_id::@root",
                "fruit_name" => "O|check_fruit_name::@parent",
                "fruit_color" => "O|check_fruit_color:@fruit_name,@me >> fruit name(@p0) and color(@p1) is not matched",
            ]
        ];

        $cases = [
            "Valid_data" => [
                "data" => [
                    "id" => "1",
                    "name" => "Admin",
                    "favourite_fruit" => [
                        "fruit_id" => "1",
                        "fruit_name" => "Admin Fruit",
                        "fruit_color" => "",
                    ]
                ]
            ],
            "Valid_data_1" => [
                "data" => [
                    "id" => "10",
                    "name" => "Tom",
                    "favourite_fruit" => [
                        "fruit_id" => "10",
                        "fruit_name" => "apple",
                        "fruit_color" => "red",
                    ]
                ]
            ],
            "Invalid_global_method" => [
                "data" => [
                    "id" => "101",
                ],
                "expected_msg" => "id validation failed"
            ],
            "Invalid_add_1" => [
                "data" => [
                    "id" => "1",
                    "name" => "Tom"
                ],
                "expected_msg" => "name validation failed"
            ],
            "Invalid_add_2" => [
                "data" => [
                    "id" => "1",
                    "name" => "Admin",
                    "favourite_fruit" => [
                        "fruit_id" => "51",
                    ]
                ],
                "expected_msg" => "favourite_fruit.fruit_id validation failed"
            ],
            "Invalid_add_3" => [
                "data" => [
                    "id" => "1",
                    "name" => "Admin",
                    "favourite_fruit" => [
                        "fruit_id" => "1",
                        "fruit_name" => "apple",
                        "fruit_color" => "red",
                    ]
                ],
                "expected_msg" => "favourite_fruit.fruit_name validation failed"
            ],
            "Invalid_add_4" => [
                "data" => [
                    "id" => "1",
                    "name" => "Admin",
                    "favourite_fruit" => [
                        "fruit_id" => "10",
                        "fruit_name" => "apple",
                        "fruit_color" => "yellow",
                    ]
                ],
                "expected_msg" => "fruit name(apple) and color(yellow) is not matched"
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        $this->validation->add_method("check_name", function($name, $id) {
            if($id == 1) {
                if($name != "Admin") {
                    return false;
                }
            }

            return true;
        });

        $this->validation->add_method("check_fruit_id", function($data) {
            if($data["id"] < 50) {
                if($data["favourite_fruit"]["fruit_id"] >= 50) {
                    return false;
                }
            }else {
                if($data["favourite_fruit"]["fruit_id"] < 50) {
                    return false;
                }
            }

            return true;
        });

        $this->validation->add_method("check_fruit_name", function($favourite_fruit) {
            if($favourite_fruit['fruit_id'] == 1) {
                if($favourite_fruit['fruit_name'] != "Admin Fruit") {
                    return false;
                }
            }

            return true;
        });

        $this->validation->add_method("check_fruit_color", function($fruit_name, $fruit_color) {
            $fruit_color_arr = [
                "Admin Fruit" => "",
                "apple" => "red",
                "banana" => "yellow",
                "watermelon" => "green",
            ];

            if(isset($fruit_color_arr[$fruit_name]) && $fruit_color_arr[$fruit_name] == $fruit_color) {
                return true;
            }

            return false;
        });

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_validation_global_execute($error_data=false) {
        $rule = [
            "id" => "*|int",
            "name" => "*|string",
        ];

        $cases = [
            "Valid_data" => [
                "data" => [
                    "id" => 123,
                    "name" => "Tom",
                ]
            ],
            "Invalid_1" => [
                "data" => [
                    "id" => '123',
                    "name" => "Tom",
                ],
                "expected_msg" => [
                    "id" => "id must be integer"
                ]
            ],
            "Invalid_2" => [
                "data" => [
                    "id" => 123,
                    "name" => "",
                ],
                "expected_msg" => [
                    "name" => "name can not be empty"
                ]
            ],
            "Invalid_all" => [
                "data" => [
                    "id" => "123",
                    "name" => "",
                ],
                "expected_msg" => [
                    "id" => "id must be integer",
                    "name" => "name can not be empty",
                ]
            ]
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        if($error_data !== false) return $this->get_method_info($rule, $cases, $extra, $error_data);

        $this->validation->set_validation_global(true);
        $result = $this->valid_cases($rule, $cases, $extra);
        $this->validation->set_validation_global(false);

        return $result;
    }

    protected function test_err_format_execute($error_data=false) {
        $rule = [
            "id" => "*|int",
            "name" => "*|string",
            "favourite_fruit" => [
                "fruit_id" => "*|int",
                "fruit_name" => "*|string",
            ]
        ];

        $cases = [
            "Valid_data" => [
                "data" => [
                    "id" => 123,
                    "name" => "Tom",
                    "favourite_fruit" => [
                        "fruit_id" => 1,
                        "fruit_name" => "apple",
                    ]
                ]
            ],
            "Invalid_1" => [
                "data" => [
                    "id" => '123',
                    "name" => 1,
                    "favourite_fruit" => [
                        "fruit_id" => "1",
                        "fruit_name" => 1,
                    ]
                ],
                "expected_msg" => [
                    "id" => "id must be integer",
                    "name" => "name must be string",
                    "favourite_fruit.fruit_id" => "favourite_fruit.fruit_id must be integer",
                    "favourite_fruit.fruit_name" => "favourite_fruit.fruit_name must be string",
                ],
                "err_format" => [
                    "standard" => false,
                    "simple" => true,
                ]
            ],
            "Invalid_2" => [
                "data" => [
                    "id" => '',
                    "name" => 1,
                    "favourite_fruit" => [
                        "fruit_id" => "1",
                        "fruit_name" => 1,
                    ]
                ],
                "expected_msg" => [
                    "id" => [
                        "error_type" => "required_field",
                        "message" => "id can not be empty"
                    ],
                    "name" => [
                        "error_type" => "validation",
                        "message" => "name must be string",
                    ],
                    "favourite_fruit.fruit_id" => [
                        "error_type" => "validation",
                        "message" => "favourite_fruit.fruit_id must be integer",
                    ],
                    "favourite_fruit.fruit_name" => [
                        "error_type" => "validation",
                        "message" => "favourite_fruit.fruit_name must be string",
                    ],
                ],
                "err_format" => [
                    "standard" => false,
                    "simple" => false,
                ]
            ],
            "Invalid_3" => [
                "data" => [
                    "id" => '123',
                    "name" => 1,
                    "favourite_fruit" => [
                        "fruit_id" => "1",
                        "fruit_name" => 1,
                    ]
                ],
                "expected_msg" => [
                    "id" => "id must be integer",
                    "name" => "name must be string",
                    "favourite_fruit" => [
                        "fruit_id" => "favourite_fruit.fruit_id must be integer",
                        "fruit_name" => "favourite_fruit.fruit_name must be string"
                    ],
                ],
                "err_format" => [
                    "standard" => true,
                    "simple" => true,
                ]
            ],
            "Invalid_4" => [
                "data" => [
                    "id" => '',
                    "name" => 1,
                    "favourite_fruit" => [
                        "fruit_id" => "1",
                        "fruit_name" => 1,
                    ]
                ],
                "expected_msg" => [
                    "id" => [
                        "error_type" => "required_field",
                        "message" => "id can not be empty"
                    ],
                    "name" => [
                        "error_type" => "validation",
                        "message" => "name must be string",
                    ],
                    "favourite_fruit" => [
                        "fruit_id" => [
                            "error_type" => "validation",
                            "message" => "favourite_fruit.fruit_id must be integer",
                        ],
                        "fruit_name" => [
                            "error_type" => "validation",
                            "message" => "favourite_fruit.fruit_name must be string",
                        ],
                    ],
                ],
                "err_format" => [
                    "standard" => true,
                    "simple" => false,
                ]
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        if($error_data !== false) return $this->get_method_info($rule, $cases, $extra, $error_data);

        $this->validation->set_validation_global(true);
        $result = $this->valid_cases($rule, $cases, $extra);
        $this->validation->set_validation_global(false);

        return $result;
    }

    protected function test_dynamic_err_msg_complex() {
        $rule = [
            "id" => "*|check_err_field",
            "number" => "*|check_err_field >> @me error!",
        ];

        $cases = [
            "Valid_data" => [
                "data" => [
                    "id" => 100,
                    "number" => 100,
                ]
            ],
            "Invalid_data_1" => [
                "data" => [
                    "id" => 1
                ],
                "expected_msg" => [
                    "error_type" => "validation",
                    "message" => "id validation failed",
                ],
                "err_format" => [
                    "standard" => true,
                    "simple" => false,
                ]
            ],
            "Invalid_data_2" => [
                "data" => [
                    "id" => 11
                ],
                "expected_msg" => [
                    "error_type" => "validation",
                    "message" => "id: check_err_field error. [10, 20]",
                ],
                "err_format" => [
                    "standard" => true,
                    "simple" => false,
                ]
            ],
            "Invalid_data_3" => [
                "data" => [
                    "id" => 21
                ],
                "expected_msg" => [
                    "error_type" => "3",
                    "message" => "id: check_err_field error. [20, 30]",
                ],
                "err_format" => [
                    "standard" => true,
                    "simple" => false,
                ]
            ],
            "Invalid_data_4" => [
                "data" => [
                    "id" => 31
                ],
                "expected_msg" => [
                    "error_type" => "4",
                    "message" => "id: check_err_field error. [30, 40]",
                    "extra" => "It should be greater than 40"
                ],
                "err_format" => [
                    "standard" => true,
                    "simple" => false,
                ]
            ],
            "Invalid_data_5" => [
                "data" => [
                    "id" => 41,
                    "number" => 11
                ],
                "expected_msg" => [
                    "error_type" => "validation",
                    "message" => "number error!",
                ],
                "err_format" => [
                    "standard" => true,
                    "simple" => false,
                ]
            ],
            "Invalid_data_6" => [
                "data" => [
                    "id" => 41,
                    "number" => 31
                ],
                "expected_msg" => [
                    "error_type" => "4",
                    "message" => "number error!",
                    "extra" => "It should be greater than 40"
                ],
                "err_format" => [
                    "standard" => true,
                    "simple" => false,
                ]
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "field_path" => "name",
        ];

        $this->validation->add_method("check_err_field", function($data) {
            if($data < 10) {
                return false;
            }else if($data < 20) {
                return "id: check_err_field error. [10, 20]";
            }else if($data < 30) {
                return [
                    "error_type" => "3",
                    "message" => "@me: check_err_field error. [20, 30]",
                ];
            }else if($data <= 40) {
                return [
                    "error_type" => "4",
                    "message" => "@me: check_err_field error. [30, 40]",
                    "extra" => "It should be greater than 40"
                ];
            }

            return true;
        });

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_language_execute($error_data=false) {
        $rule = [
            "name" => "*|string"
        ];

        $cases = [
            "Invalid_empty" => [
                "data" => [
                    "name" => ""
                ],
                "expected_msg" => "name 不能为空"
            ],
            "Invalid_unset" => [
                "data" => [],
                "expected_msg" => "name 不能为空"
            ],
            "Invalid_num" => [
                "data" => [
                    "name" => 123,
                ],
                "error_tag" => "string",
                "field_path" => "name",
            ]
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        if($error_data !== false) return $this->get_method_info($rule, $cases, $extra, $error_data);

        $this->validation->set_language("zh-cn");
        $result = $this->valid_cases($rule, $cases, $extra);
        $this->validation->set_language("en-us");

        return $result;
    }

    protected function test_default_config_execute($error_data=false) {
        $rule = [
            "id" => "*|int|/^\d+$/",
            "name" => "*|string|len<=>:8,32",
            "gender" => "*|(s):male,female",
            "dob" => "*|dob",
            "age" => "*|check_age:@gender,30 >> @me is wrong",
            "height_unit" => "*|(s):cm,m",
            "height[||]" => [
                "*|=::@height_unit,cm|<=>=:100,200 >> @me should be in [100,200] when height_unit is cm",
                "*|=::@height_unit,m|<=>=:1,2 >> @me should be in [1,2] when height_unit is m",
            ],
            "education" => [
                "primary_school" => "*|=:Qiankeng Xiaoxue",
                "junior_middle_school" => "*|!=:Foshan Zhongxue",
                "high_school" => "if?=::@junior_middle_school,Mianhu Zhongxue|*|len>:10",
                "university" => "if0?=::@junior_middle_school,Qiankeng Zhongxue|*|len>:10",
            ],
            "company" => [
                "name" => "*|len<=>:8,64",
                "country" => "O|len>=:3",
                "addr" => "*|len>:16",
                "colleagues[n]" => [
                    "name" => "*|string|len<=>:3,32",
                    "position" => "*|(s):Reception,Financial,PHP,JAVA"
                ],
                "boss" => [
                    "*|=:Mike",
                    "*|(s):Johnny,David",
                    "O|(s):Johnny,David"
                ]
            ],
            "favourite_food[O][n]" => [
                "name" => "*|string",
                "place_name" => "O|string" 
            ]
        ];

        $cases = [
            "Valid_data" => [
                "data" => [
                    "id" => 12,
                    "name" => "LinJunjie",
                    "gender" => "female",
                    "dob" => "2000-01-01",
                    "age" => 30,
                    "height_unit" => "cm",
                    "height" => 165,
                    "weight_unit" => "kg",
                    "weight" => 80,
                    "education" => [
                        "primary_school" => "Qiankeng Xiaoxue",
                        "junior_middle_school" => "Qiankeng Zhongxue",
                        "high_school" => "Mianhu Gaozhong",
                        "university" => "Foshan University",
                    ],
                    "company" => [
                        "name" => "Qiankeng Company",
                        "country" => "China",
                        "addr" => "Foshan Nanhai Guicheng",
                        "colleagues" => [
                            [
                                "name" => "Judy",
                                "position" => "Reception"
                            ],
                            [
                                "name" => "Sian",
                                "position" => "Financial"
                            ],
                            [
                                "name" => "Brook",
                                "position" => "JAVA"
                            ],
                            [
                                "name" => "Kurt",
                                "position" => "PHP"
                            ],
                        ],
                        "boss" => [
                            "Mike",
                            "David",
                            "Johnny",
                            "Extra",
                        ]
                    ]
                ],
            ],
            "Invalid_data" => [
                "data" => [
                    "id" => "ABBC",
                    "name" => "12",
                    "gender" => "female2",
                    "dob" => "2000-01-01",
                    "age" => 11,
                    "height_unit" => "cm",
                    "height" => 1.65,
                    "weight_unit" => "lb1",
                    "weight" => 80,
                    "education" => [
                        "primary_school" => "???Qiankeng Xiaoxue",
                        "junior_middle_school" => "Foshan Zhongxue",
                        "high_school" => "Mianhu",
                        "university" => "Mianhu",
                    ],
                    "company" => [
                        "name" => "Qianken",
                        "country" => "US",
                        "addr" => "Foshan Nanhai",
                        "colleagues" => [
                            [
                                "name" => 1,
                                "position" => "Reception"
                            ],
                            [
                                "name" => 2,
                                "position" => "Financial1"
                            ],
                            [
                                "name" => 3,
                                "position" => "JAVA"
                            ],
                            [
                                "name" => "Kurt",
                                "position" => "PHP1"
                            ],
                        ],
                        "boss" => [
                            "Mike1",
                            "David",
                            "Johnny2",
                            "Extra",
                        ]
                    ],
                    "favourite_food" => [
                        [
                            "name" => "HuoGuo",
                            "place_name" => "SiChuan" 
                        ],
                        [
                            "name" => "Beijing Kaoya",
                            "place_name" => "Beijing"
                        ],
                    ]
                ],
                "expected_msg" => [
                    "id" => "id must be integer",
                    "name" => "name length must be greater than 8 and less than or equal to 32",
                    "gender" => "gender must be string and in male,female",
                    "age" => "age is wrong",
                    "height" => "height should be in [100,200] when height_unit is cm or height should be in [1,2] when height_unit is m",
                    "education" => [
                        "primary_school" => "education.primary_school must be equal to Qiankeng Xiaoxue",
                        "junior_middle_school" => "education.junior_middle_school must be not equal to Foshan Zhongxue",
                        "high_school" => "education.high_school length must be greater than 10",
                        "university" => "education.university length must be greater than 10",
                    ],
                    "company" => [
                        "name" => "company.name length must be greater than 8 and less than or equal to 64",
                        "country" => "company.country length must be greater than or equal to 3",
                        "addr" => "company.addr length must be greater than 16",
                        "colleagues" => [
                            "0" => [
                                "name" => "company.colleagues.0.name must be string",
                            ],
                            "1" => [
                                "name" => "company.colleagues.1.name must be string",
                                "position" => "company.colleagues.1.position must be string and in Reception,Financial,PHP,JAVA",
                            ],
                            "2" => [
                                "name" => "company.colleagues.2.name must be string",
                            ],
                            "3" => [
                                "position" => "company.colleagues.3.position must be string and in Reception,Financial,PHP,JAVA",
                            ],
                        ],
                        "boss" => [
                            "0" => "company.boss.0 must be equal to Mike",
                            "2" => "company.boss.2 must be string and in Johnny,David",
                        ],
                    ]
                ],
                "err_format" => [
                    "standard" => true,
                    "simple" => true,
                ]

            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        if($error_data !== false) return $this->get_method_info($rule, $cases, $extra, $error_data);

        $this->validation->set_validation_global(true);
        $result = $this->valid_cases($rule, $cases, $extra);
        $this->validation->set_validation_global(false);

        return $result;
    }

    protected function test_user_config_execute($error_data=false) {
        $validation_conf = array(
            'language' => 'en-us',                  // Language, default is en-us
            'validation_global' => true,            // If true, validate all rules; If false, stop validating when one rule was invalid.
            'reg_msg' => '/ >>>(.*)$/',             // Set special error msg by user 
            'reg_preg' => '/^Reg:(\/.+\/.*)$/',     // If match this, using regular expression instead of method
            'reg_if' => '/^IF[yn]?\?/',             // If match this, validate this condition first
            'reg_if_true' => '/^IFy?\?/',           // If match this, validate this condition first, if true, then validate the field
            'reg_if_true' => '/^IFn\?/',            // If match this, validate this condition first, if false, then validate the field
            'symbol_or' => '[or]',                  // Symbol of or rule
            'symbol_rule_separator' => '&&',        // Rule reqarator for one field
            'symbol_param_classic' => '~',          // If set function by this symbol, will add a @me parameter at first 
            'symbol_param_force' => '~~',           // If set function by this symbol, will not add a @me parameter at first 
            'symbol_param_separator' => ',',        // Parameters separator, such as @me,@field1,@field2
            'symbol_field_name_separator' => '->',  // Field name separator, suce as "fruit.apple"
            'symbol_required' => '!*',              // Symbol of required field
            'symbol_optional' => 'o',               // Symbol of optional field
            'symbol_numeric_array' => '[!n]',       // Symbol of association array
            'symbol_array_optional' => '[o]',       // Symbol of array optional
        );

        $rule = [
            "id" => "!*&&int&&Reg:/^\d+$/i",
            "name" => "!*&&string&&len<=>~8,32",
            "gender" => "!*&&(s)~male,female",
            "dob" => "!*&&dob",
            "age" => "!*&&check_age~@gender,30 >>>@me is wrong",
            "height_unit" => "!*&&(s)~cm,m",
            "height[or]" => [
                "!*&&=~~@height_unit,cm&&<=>=~100,200 >>>@me should be in [100,200] when height_unit is cm",
                "!*&&=~~@height_unit,m&&<=>=~1,2 >>>@me should be in [1,2] when height_unit is m",
            ],
            "education" => [
                "primary_school" => "!*&&=~Qiankeng Xiaoxue",
                "junior_middle_school" => "!*&&!=~Foshan Zhongxue",
                "high_school" => "IF?=~~@junior_middle_school,Mianhu Zhongxue&&!*&&len>~10",
                "university" => "IFn?=~~@junior_middle_school,Qiankeng Zhongxue&&!*&&len>~10",
            ],
            "company" => [
                "name" => "!*&&len<=>~8,64",
                "country" => "o&&len>=~3",
                "addr" => "!*&&len>~16",
                "colleagues[!n]" => [
                    "name" => "!*&&string&&len<=>~3,32",
                    "position" => "!*&&(s)~Reception,Financial,PHP,JAVA"
                ],
                "boss" => [
                    "!*&&=~Mike",
                    "!*&&(s)~Johnny,David",
                    "o&&(s)~Johnny,David"
                ]
            ],
            "favourite_food[o][!n]" => [
                "name" => "!*&&string",
                "place_name" => "o&&string" 
            ]
        ];

        $cases = [
            "Valid_data" => [
                "data" => [
                    "id" => 12,
                    "name" => "LinJunjie",
                    "gender" => "female",
                    "dob" => "2000-01-01",
                    "age" => 30,
                    "height_unit" => "cm",
                    "height" => 165,
                    "weight_unit" => "kg",
                    "weight" => 80,
                    "education" => [
                        "primary_school" => "Qiankeng Xiaoxue",
                        "junior_middle_school" => "Qiankeng Zhongxue",
                        "high_school" => "Mianhu Gaozhong",
                        "university" => "Foshan University",
                    ],
                    "company" => [
                        "name" => "Qiankeng Company",
                        "country" => "China",
                        "addr" => "Foshan Nanhai Guicheng",
                        "colleagues" => [
                            [
                                "name" => "Judy",
                                "position" => "Reception"
                            ],
                            [
                                "name" => "Sian",
                                "position" => "Financial"
                            ],
                            [
                                "name" => "Brook",
                                "position" => "JAVA"
                            ],
                            [
                                "name" => "Kurt",
                                "position" => "PHP"
                            ],
                        ],
                        "boss" => [
                            "Mike",
                            "David",
                            "Johnny",
                            "Extra",
                        ]
                    ]
                ],
            ],
            "Invalid_data" => [
                "data" => [
                    "id" => "ABBC",
                    "name" => "12",
                    "gender" => "female2",
                    "dob" => "2000-01-01",
                    "age" => 11,
                    "height_unit" => "cm",
                    "height" => 1.65,
                    "weight_unit" => "lb1",
                    "weight" => 80,
                    "education" => [
                        "primary_school" => "???Qiankeng Xiaoxue",
                        "junior_middle_school" => "Foshan Zhongxue",
                        "high_school" => "Mianhu",
                        "university" => "Mianhu",
                    ],
                    "company" => [
                        "name" => "Qianken",
                        "country" => "US",
                        "addr" => "Foshan Nanhai",
                        "colleagues" => [
                            [
                                "name" => 1,
                                "position" => "Reception"
                            ],
                            [
                                "name" => 2,
                                "position" => "Financial1"
                            ],
                            [
                                "name" => 3,
                                "position" => "JAVA"
                            ],
                            [
                                "name" => "Kurt",
                                "position" => "PHP1"
                            ],
                        ],
                        "boss" => [
                            "Mike1",
                            "David",
                            "Johnny2",
                            "Extra",
                        ]
                    ],
                    "favourite_food" => [
                        [
                            "name" => "HuoGuo",
                            "place_name" => "SiChuan" 
                        ],
                        [
                            "name" => "Beijing Kaoya",
                            "place_name" => "Beijing"
                        ],
                    ]
                ],
                "expected_msg" => [
                    "id" => "id must be integer",
                    "name" => "name length must be greater than 8 and less than or equal to 32",
                    "gender" => "gender must be string and in male,female",
                    "age" => "age is wrong",
                    "height" => "height should be in [100,200] when height_unit is cm or height should be in [1,2] when height_unit is m",
                    "education" => [
                        "primary_school" => "education->primary_school must be equal to Qiankeng Xiaoxue",
                        "junior_middle_school" => "education->junior_middle_school must be not equal to Foshan Zhongxue",
                        "high_school" => "education->high_school length must be greater than 10",
                        "university" => "education->university length must be greater than 10",
                    ],
                    "company" => [
                        "name" => "company->name length must be greater than 8 and less than or equal to 64",
                        "country" => "company->country length must be greater than or equal to 3",
                        "addr" => "company->addr length must be greater than 16",
                        "colleagues" => [
                            "0" => [
                                "name" => "company->colleagues->0->name must be string",
                            ],
                            "1" => [
                                "name" => "company->colleagues->1->name must be string",
                                "position" => "company->colleagues->1->position must be string and in Reception,Financial,PHP,JAVA",
                            ],
                            "2" => [
                                "name" => "company->colleagues->2->name must be string",
                            ],
                            "3" => [
                                "position" => "company->colleagues->3->position must be string and in Reception,Financial,PHP,JAVA",
                            ],
                        ],
                        "boss" => [
                            "0" => "company->boss->0 must be equal to Mike",
                            "2" => "company->boss->2 must be string and in Johnny,David",
                        ],
                    ]
                ],
                "err_format" => [
                    "standard" => true,
                    "simple" => true,
                ]
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        if($error_data !== false) return $this->get_method_info($rule, $cases, $extra, $error_data);

        $this->validation->set_config($validation_conf);
        $result = $this->valid_cases($rule, $cases, $extra);
        $this->validation->reset_config();
        $this->validation->set_validation_global(false);

        return $result;
    }

    protected function test_require() {
        $rule = [
            "name" => "*"
        ];

        $cases = [
            "Valid_data" => [
                "data" => [
                    "name" => "a"
                ]
            ],
            "Invalid_empty" => [
                "data" => [
                    "name" => ""
                ]
            ],
            "Invalid_unset" => [
                "data" => []
            ]
        ];

        $extra = [
            "method_name" => __METHOD__,
            "error_tag" => "required",
            "field_path" => "name",
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_regular_expression() {
        $rule = [
            "id" => "*|/^\d+$/",
            "name" => "*|/Tom/i|string"
        ];

        $rule1 = [
            "id" => "*|Reg>/^\d+$/",
            "name" => "*|Reg>/Tom/i|string"
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "id" => "1",
                    "name" => "Tom",
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "id" => "123",
                    "name" => "tom",
                ]
            ],
            "Invalid_1" => [
                "data" => [
                    "id" => "1a",
                    "name" => "John",
                ],
                "expected_msg" => "id format is invalid, should be /^\d+$/"
            ],
            "Invalid_2" => [
                "data" => [
                    "id" => "1",
                    "name" => "John",
                ],
                "expected_msg" => "name format is invalid, should be /Tom/i"
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_in_method() {
        $rule = [
            "id" => "*|(n):1,2,3",
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "id" => "1",
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "id" => "2",
                ]
            ],
            "Invalid_1" => [
                "data" => [
                    "id" => "4",
                ],
                "parameters" => [
                    "1,2,3"
                ],
                // "expected_msg" => "id must be numeric and in 1,2,3"
            ],
            "Invalid_2" => [
                "data" => [
                    "id" => "12",
                ],
                "expected_msg" => "id must be numeric and in 1,2,3"
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "error_tag" => "(n)",
            "field_path" => "id",
            "parameters" => [
                "1,2,3"
            ],
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }
}


$arguments = $argv;
$method = isset($arguments[1])? $arguments[1] : "error";
unset($arguments[0], $arguments[1]);

$test = new Unit();

if(method_exists($test, $method)) {
    $result = call_user_func_array([$test, $method], $arguments);
}else {
    echo "Error test method {$method}.\n";
    die;
}

print_r($result);