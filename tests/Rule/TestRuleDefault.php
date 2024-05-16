<?php

namespace githusband\Tests\Rule;

trait TestRuleDefault
{
    protected function test_method_equal()
    {
        $rule = [
            "id" => "=[1]|equal[1]",
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "id" => "1",
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "id" => 1,
                ]
            ],
            "Invalid_data_1" => [
                "data" => [
                    "id" => "2",
                ],
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "error_tag" => "=",
            "field_path" => "id",
            "parameters" => [
                "1"
            ],
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_not_equal()
    {
        $rule = [
            "id" => "!=[1]|not_equal[1]",
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "id" => "2",
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "id" => 2,
                ]
            ],
            "Invalid_data_1" => [
                "data" => [
                    "id" => "1",
                ],
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "error_tag" => "!=",
            "field_path" => "id",
            "parameters" => [
                "1"
            ],
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_strictly_equal()
    {
        $rule = [
            "key_int" => "optional|==[1]|strictly_equal[1]",
            "key_int_str" => "optional|==[\"1\"]|strictly_equal['1']",
            "key_float" => "optional|==[1.1]|strictly_equal[1.1]",
            "key_float_str" => "optional|==[\"1.1\"]|strictly_equal['1.1']",
            "key_bool_true" => "optional|==[TRUE]|strictly_equal[true]",
            "key_bool_false" => "optional|==[FALSE]|strictly_equal[false]",
            "key_bool_string_true" => "optional|==[\"true\"]|strictly_equal['true']",
            "key_bool_string_FALSE" => "optional|==[\"FALSE\"]|strictly_equal['FALSE']",
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "key_int" => 1,
                    "key_int_str" => "1",
                    "key_float" => 1.1,
                    "key_float_str" => "1.1",
                    "key_bool_true" => true,
                    "key_bool_false" => FALSE,
                    "key_bool_string_true" => "true",
                    "key_bool_string_FALSE" => "FALSE",
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "key_int" => 1,
                    "key_int_str" => "1",
                    "key_float" => 1.10,
                    "key_float_str" => "1.1",
                    "key_bool_true" => TRUE,
                    "key_bool_false" => FALSE,
                    "key_bool_string_true" => "true",
                    "key_bool_string_FALSE" => "FALSE",
                ]
            ],
            "Invalid_key_int" => [
                "data" => [
                    "key_int" => 1.0,
                ],
                "expected_msg" => ["key_int" => "key_int must be strictly equal to int(1)"]
            ],
            "Invalid_key_int_1" => [
                "data" => [
                    "key_int" => "1",
                ],
                "expected_msg" => ["key_int" => "key_int must be strictly equal to int(1)"]
            ],
            "Invalid_key_int_str" => [
                "data" => [
                    "key_int_str" => 1,
                ],
                "expected_msg" => ["key_int_str" => "key_int_str must be strictly equal to string(1)"]
            ],
            "Invalid_key_int_str_1" => [
                "data" => [
                    "key_int_str" => "2",
                ],
                "expected_msg" => ["key_int_str" => "key_int_str must be strictly equal to string(1)"]
            ],
            "Invalid_key_float" => [
                "data" => [
                    "key_float" => 1.0,
                ],
                "expected_msg" => ["key_float" => "key_float must be strictly equal to float(1.1)"]
            ],
            "Invalid_key_float_1" => [
                "data" => [
                    "key_float" => "1.1",
                ],
                "expected_msg" => ["key_float" => "key_float must be strictly equal to float(1.1)"]
            ],
            "Invalid_key_float_str" => [
                "data" => [
                    "key_float_str" => 1.1,
                ],
                "expected_msg" => ["key_float_str" => "key_float_str must be strictly equal to string(1.1)"]
            ],
            "Invalid_key_float_str_1" => [
                "data" => [
                    "key_float_str" => "1.10",
                ],
                "expected_msg" => ["key_float_str" => "key_float_str must be strictly equal to string(1.1)"]
            ],
            "Invalid_key_bool_true" => [
                "data" => [
                    "key_bool_true" => 1,
                ],
                "expected_msg" => ["key_bool_true" => "key_bool_true must be strictly equal to bool(true)"]
            ],
            "Invalid_key_bool_true_1" => [
                "data" => [
                    "key_bool_true" => "true",
                ],
                "expected_msg" => ["key_bool_true" => "key_bool_true must be strictly equal to bool(true)"]
            ],
            "Invalid_key_bool_false" => [
                "data" => [
                    "key_bool_false" => 0,
                ],
                "expected_msg" => ["key_bool_false" => "key_bool_false must be strictly equal to bool(false)"]
            ],
            "Invalid_key_bool_false_1" => [
                "data" => [
                    "key_bool_false" => "false",
                ],
                "expected_msg" => ["key_bool_false" => "key_bool_false must be strictly equal to bool(false)"]
            ],
            "Invalid_key_bool_string_true" => [
                "data" => [
                    "key_bool_string_true" => true,
                ],
                "expected_msg" => ["key_bool_string_true" => "key_bool_string_true must be strictly equal to string(true)"]
            ],
            "Invalid_key_bool_string_true_1" => [
                "data" => [
                    "key_bool_string_true" => "TRUE",
                ],
                "expected_msg" => ["key_bool_string_true" => "key_bool_string_true must be strictly equal to string(true)"]
            ],
            "Invalid_key_bool_string_FALSE" => [
                "data" => [
                    "key_bool_string_FALSE" => false,
                ],
                "expected_msg" => ["key_bool_string_FALSE" => "key_bool_string_FALSE must be strictly equal to string(FALSE)"]
            ],
            "Invalid_key_bool_string_FALSE_1" => [
                "data" => [
                    "key_bool_string_FALSE" => "false",
                ],
                "expected_msg" => ["key_bool_string_FALSE" => "key_bool_string_FALSE must be strictly equal to string(FALSE)"]
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

    protected function test_method_not_strictly_equal()
    {
        $rule = [
            "key_int" => "optional|!==[1]|not_strictly_equal[1]",
            "key_int_str" => "optional|!==[\"1\"]|not_strictly_equal['1']",
            "key_float" => "optional|!==[1.1]|not_strictly_equal[1.1]",
            "key_float_str" => "optional|!==[\"1.1\"]|not_strictly_equal['1.1']",
            "key_bool_true" => "optional|!==[TRUE]|not_strictly_equal[true]",
            "key_bool_false" => "optional|!==[FALSE]|not_strictly_equal[false]",
            "key_bool_string_true" => "optional|!==[\"true\"]|not_strictly_equal['true']",
            "key_bool_string_FALSE" => "optional|!==[\"FALSE\"]|not_strictly_equal['FALSE']",
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "key_int" => "1",
                    "key_int_str" => 1,
                    "key_float" => "1.1",
                    "key_float_str" => 1.1,
                    "key_bool_true" => "true",
                    "key_bool_false" => "FALSE",
                    "key_bool_string_true" => true,
                    "key_bool_string_FALSE" => FALSE,
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "key_int" => 1.0,
                    "key_int_str" => 1.0,
                    "key_float" => "1.10",
                    "key_float_str" => 1.10,
                    "key_bool_true" => "TRUE",
                    "key_bool_false" => 0,
                    "key_bool_string_true" => 1,
                    "key_bool_string_FALSE" => "",
                ]
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

    protected function test_method_greater_than()
    {
        $rule = [
            "id" => ">[1]|greater_than[1]",
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "id" => 2,
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "id" => "2.2",
                ]
            ],
            "Invalid_data_1" => [
                "data" => [
                    "id" => 0,
                ]
            ],
            "Invalid_data_2" => [
                "data" => [
                    "id" => "1",
                ]
            ],
            "Invalid_data_3" => [
                "data" => [
                    "id" => "a",
                ]
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "error_tag" => ">",
            "field_path" => "id",
            "parameters" => [
                "1"
            ],
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_less_than()
    {
        $rule = [
            "id" => "<[1]|less_than[1]",
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "id" => 0,
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "id" => "0.0",
                ]
            ],
            "Invalid_data_1" => [
                "data" => [
                    "id" => 1,
                ]
            ],
            "Invalid_data_2" => [
                "data" => [
                    "id" => "1",
                ]
            ],
            "Invalid_data_3" => [
                "data" => [
                    "id" => "",
                ]
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "error_tag" => "<",
            "field_path" => "id",
            "parameters" => [
                "1"
            ],
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_greater_equal()
    {
        $rule = [
            "id" => ">=[1]|greater_equal[1]",
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "id" => 1,
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "id" => "1.0",
                ]
            ],
            "Invalid_data_1" => [
                "data" => [
                    "id" => 0,
                ]
            ],
            "Invalid_data_2" => [
                "data" => [
                    "id" => -1,
                ]
            ],
            "Invalid_data_3" => [
                "data" => [
                    "id" => "-1",
                ]
            ],
            "Invalid_data_4" => [
                "data" => [
                    "id" => "",
                ]
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "error_tag" => ">=",
            "field_path" => "id",
            "parameters" => [
                "1"
            ],
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_less_equal()
    {
        $rule = [
            "id" => "<=[1]|less_equal[1]",
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "id" => -1,
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "id" => 0,
                ]
            ],
            "Valid_data_3" => [
                "data" => [
                    "id" => "1.0",
                ]
            ],
            "Invalid_data_1" => [
                "data" => [
                    "id" => 1.1,
                ]
            ],
            "Invalid_data_2" => [
                "data" => [
                    "id" => "2",
                ]
            ],
            "Invalid_data_3" => [
                "data" => [
                    "id" => "",
                ]
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "error_tag" => "<=",
            "field_path" => "id",
            "parameters" => [
                "1"
            ],
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_greater_less()
    {
        $rule = [
            "id" => "><[1,10]|greater_less[1,10]",
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "id" => 1.1,
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "id" => 2,
                ]
            ],
            "Valid_data_3" => [
                "data" => [
                    "id" => "3.1",
                ]
            ],
            "Invalid_data_1" => [
                "data" => [
                    "id" => 0,
                ]
            ],
            "Invalid_data_2" => [
                "data" => [
                    "id" => 1,
                ]
            ],
            "Invalid_data_3" => [
                "data" => [
                    "id" => "1.0",
                ]
            ],
            "Invalid_data_4" => [
                "data" => [
                    "id" => "",
                ]
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "error_tag" => "><",
            "field_path" => "id",
            "parameters" => [
                "1",
                "10",
            ],
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_greater_lessequal()
    {
        $rule = [
            "id" => "><=[1,10]|greater_lessequal[1,10]",
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "id" => 2,
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "id" => "1.1",
                ]
            ],
            "Valid_data_3" => [
                "data" => [
                    "id" => "10",
                ]
            ],
            "Valid_data_4" => [
                "data" => [
                    "id" => 10.0,
                ]
            ],
            "Invalid_data_1" => [
                "data" => [
                    "id" => 1,
                ]
            ],
            "Invalid_data_2" => [
                "data" => [
                    "id" => "1",
                ]
            ],
            "Invalid_data_3" => [
                "data" => [
                    "id" => 10.1,
                ]
            ],
            "Invalid_data_4" => [
                "data" => [
                    "id" => "",
                ]
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "error_tag" => "><=",
            "field_path" => "id",
            "parameters" => [
                "1",
                "10"
            ],
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_greaterequal_less()
    {
        $rule = [
            "id" => ">=<[1,10]|greaterequal_less[1,10]",
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "id" => 1,
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "id" => "1.0",
                ]
            ],
            "Valid_data_3" => [
                "data" => [
                    "id" => "9.9",
                ]
            ],
            "Valid_data_4" => [
                "data" => [
                    "id" => 9,
                ]
            ],
            "Invalid_data_1" => [
                "data" => [
                    "id" => 0.1,
                ]
            ],
            "Invalid_data_2" => [
                "data" => [
                    "id" => "0",
                ]
            ],
            "Invalid_data_3" => [
                "data" => [
                    "id" => 10,
                ]
            ],
            "Invalid_data_4" => [
                "data" => [
                    "id" => "",
                ]
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "error_tag" => ">=<",
            "field_path" => "id",
            "parameters" => [
                "1",
                "10"
            ],
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_between()
    {
        $rule = [
            "id" => ">=<=[1,10]|between[1,10]",
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "id" => 1,
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "id" => "1.0",
                ]
            ],
            "Valid_data_3" => [
                "data" => [
                    "id" => "10.0",
                ]
            ],
            "Valid_data_4" => [
                "data" => [
                    "id" => 10,
                ]
            ],
            "Invalid_data_1" => [
                "data" => [
                    "id" => 0.1,
                ]
            ],
            "Invalid_data_2" => [
                "data" => [
                    "id" => "0",
                ]
            ],
            "Invalid_data_3" => [
                "data" => [
                    "id" => 10.1,
                ]
            ],
            "Invalid_data_4" => [
                "data" => [
                    "id" => "",
                ]
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "error_tag" => ">=<=",
            "field_path" => "id",
            "parameters" => [
                "1",
                "10"
            ],
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_in_number_array()
    {
        $rule = [
            "id" => "<number>[1,2,3]|in_number_array[1,2]",
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "id" => 1,
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "id" => "1",
                ]
            ],
            "Valid_data_3" => [
                "data" => [
                    "id" => 2,
                ]
            ],
            "Valid_data_4" => [
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
                // "expected_msg" => [ "id" => "id must be numeric and in 1,2,3" ]
            ],
            "Invalid_2" => [
                "data" => [
                    "id" => "3",
                ],
                "parameters" => [
                    "1,2"
                ],
                // "expected_msg" => ["id" => "id must be numeric and in 1,2,3"]
            ],
            "Invalid_3" => [
                "data" => [
                    "id" => "a",
                ],
                "parameters" => [
                    "1,2,3"
                ],
                // "expected_msg" => ["id" => "id must be numeric and in 1,2,3"]
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "error_tag" => "<number>",
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

    protected function test_method_not_in_number_array()
    {
        $rule = [
            "id" => "!<number>[1,2]|not_in_number_array[1,2,3]",
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "id" => 0,
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "id" => "0",
                ]
            ],
            "Valid_data_3" => [
                "data" => [
                    "id" => 4,
                ]
            ],
            "Valid_data_4" => [
                "data" => [
                    "id" => "4",
                ]
            ],
            "Invalid_1" => [
                "data" => [
                    "id" => 1,
                ],
                "parameters" => [
                    "1,2"
                ],
            ],
            "Invalid_2" => [
                "data" => [
                    "id" => "1",
                ],
                "parameters" => [
                    "1,2"
                ],
            ],
            "Invalid_3" => [
                "data" => [
                    "id" => 3,
                ],
                "parameters" => [
                    "1,2,3"
                ],
            ],
            "Invalid_4" => [
                "data" => [
                    "id" => "3",
                ],
                "parameters" => [
                    "1,2,3"
                ],
            ],
            "Invalid_5" => [
                "data" => [
                    "id" => "a",
                ],
                "parameters" => [
                    "1,2"
                ],
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "error_tag" => "!<number>",
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

    protected function test_method_in_string_array()
    {
        $rule = [
            "id" => "<string>[1,2,3]|in_string_array[1,2]",
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
                    "id" => 1,
                ],
                "parameters" => [
                    "1,2,3"
                ],
            ],
            "Invalid_2" => [
                "data" => [
                    "id" => "3",
                ],
                "parameters" => [
                    "1,2"
                ],
            ],
            "Invalid_3" => [
                "data" => [
                    "id" => 3,
                ],
                "parameters" => [
                    "1,2,3"
                ],
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "error_tag" => "<string>",
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

    protected function test_method_not_in_string_array()
    {
        $rule = [
            "id" => "!<string>[1,2]|not_in_string_array[1,2,3]",
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "id" => "0",
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "id" => "4",
                ]
            ],
            "Invalid_1" => [
                "data" => [
                    "id" => 1,
                ],
                "parameters" => [
                    "1,2"
                ],
            ],
            "Invalid_2" => [
                "data" => [
                    "id" => "1",
                ],
                "parameters" => [
                    "1,2"
                ],
            ],
            "Invalid_3" => [
                "data" => [
                    "id" => 3,
                ],
                "parameters" => [
                    "1,2"
                ],
            ],
            "Invalid_4" => [
                "data" => [
                    "id" => "3",
                ],
                "parameters" => [
                    "1,2,3"
                ],
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "error_tag" => "!<string>",
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

    protected function test_method_length_equal()
    {
        $rule = [
            "text" => "length=[3]|length_equal[3]",
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "text" => "abc",
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "text" => "   ",
                ]
            ],
            "Valid_data_3" => [
                "data" => [
                    "text" => "1.0",
                ]
            ],
            "Valid_data_4" => [
                "data" => [
                    "text" => "你好吗",
                ]
            ],
            "Valid_data_5" => [
                "data" => [
                    "text" => "no好",
                ]
            ],
            "Invalid_data_1" => [
                "data" => [
                    "text" => "1",
                ],
            ],
            "Invalid_data_2" => [
                "data" => [
                    "text" => "1234",
                ],
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "error_tag" => "length=",
            "field_path" => "text",
            "parameters" => [
                "3"
            ],
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_length_not_equal()
    {
        $rule = [
            "text" => "length!=[3]|length_not_equal[3]",
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "text" => "",
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "text" => "a",
                ]
            ],
            "Valid_data_3" => [
                "data" => [
                    "text" => 1.0,
                ]
            ],
            "Valid_data_4" => [
                "data" => [
                    "text" => "你好吗?",
                ]
            ],
            "Valid_data_5" => [
                "data" => [
                    "text" => "好",
                ]
            ],
            "Invalid_data_1" => [
                "data" => [
                    "text" => "你好吗",
                ],
            ],
            "Invalid_data_2" => [
                "data" => [
                    "text" => "no好",
                ],
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "error_tag" => "length!=",
            "field_path" => "text",
            "parameters" => [
                "3"
            ],
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_length_greater_than()
    {
        $rule = [
            "text" => "length>[3]|length_greater_than[3]",
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "text" => "abcd",
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "text" => "    ",
                ]
            ],
            "Valid_data_3" => [
                "data" => [
                    "text" => 1.01,
                ]
            ],
            "Valid_data_4" => [
                "data" => [
                    "text" => "你好吗?",
                ]
            ],
            "Valid_data_5" => [
                "data" => [
                    "text" => "no好!",
                ]
            ],
            "Invalid_data_1" => [
                "data" => [
                    "text" => "",
                ],
            ],
            "Invalid_data_2" => [
                "data" => [
                    "text" => null,
                ],
            ],
            "Invalid_data_3" => [
                "data" => [
                    "text" => false,
                ],
            ],
            "Invalid_data_4" => [
                "data" => [
                    "text" => 0.100,
                ],
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "error_tag" => "length>",
            "field_path" => "text",
            "parameters" => [
                "3"
            ],
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_length_less_than()
    {
        $rule = [
            "text" => "length<[3]|length_less_than[3]",
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "text" => "",
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "text" => "a",
                ]
            ],
            "Valid_data_3" => [
                "data" => [
                    "text" => 1.00,
                ]
            ],
            "Valid_data_4" => [
                "data" => [
                    "text" => "你好",
                ]
            ],
            "Valid_data_5" => [
                "data" => [
                    "text" => "好a",
                ]
            ],
            "Invalid_data_1" => [
                "data" => [
                    "text" => "abc",
                ],
            ],
            "Invalid_data_2" => [
                "data" => [
                    "text" => 0.1,
                ],
            ],
            "Invalid_data_3" => [
                "data" => [
                    "text" => "false",
                ],
            ],
            "Invalid_data_4" => [
                "data" => [
                    "text" => "0.100",
                ],
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "error_tag" => "length<",
            "field_path" => "text",
            "parameters" => [
                "3"
            ],
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_length_greater_equal()
    {
        $rule = [
            "text" => "length>=[3]|length_greater_equal[3]",
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "text" => "abc",
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "text" => "   ",
                ]
            ],
            "Valid_data_3" => [
                "data" => [
                    "text" => 0.1,
                ]
            ],
            "Valid_data_4" => [
                "data" => [
                    "text" => "你好吗",
                ]
            ],
            "Valid_data_5" => [
                "data" => [
                    "text" => "no好",
                ]
            ],
            "Valid_data_6" => [
                "data" => [
                    "text" => "abcd",
                ]
            ],
            "Invalid_data_1" => [
                "data" => [
                    "text" => "",
                ],
            ],
            "Invalid_data_2" => [
                "data" => [
                    "text" => null,
                ],
            ],
            "Invalid_data_3" => [
                "data" => [
                    "text" => false,
                ],
            ],
            "Invalid_data_4" => [
                "data" => [
                    "text" => 0,
                ],
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "error_tag" => "length>=",
            "field_path" => "text",
            "parameters" => [
                "3"
            ],
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_length_less_equal()
    {
        $rule = [
            "text" => "length<=[3]|length_less_equal[3]",
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "text" => "",
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "text" => "ab",
                ]
            ],
            "Valid_data_3" => [
                "data" => [
                    "text" => 1.00,
                ]
            ],
            "Valid_data_4" => [
                "data" => [
                    "text" => "你好吗",
                ]
            ],
            "Valid_data_5" => [
                "data" => [
                    "text" => "好a",
                ]
            ],
            "Invalid_data_1" => [
                "data" => [
                    "text" => "abcd",
                ],
            ],
            "Invalid_data_2" => [
                "data" => [
                    "text" => 1.01,
                ],
            ],
            "Invalid_data_3" => [
                "data" => [
                    "text" => "false",
                ],
            ],
            "Invalid_data_4" => [
                "data" => [
                    "text" => "0.100",
                ],
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "error_tag" => "length<=",
            "field_path" => "text",
            "parameters" => [
                "3"
            ],
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_length_greater_less()
    {
        $rule = [
            "text" => "length><[1,10]|length_greater_less[1,10]",
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "text" => "12",
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "text" => 123456789,
                ]
            ],
            "Valid_data_3" => [
                "data" => [
                    "text" => "1.1234567",
                ]
            ],
            "Valid_data_4" => [
                "data" => [
                    "text" => "一二",
                ]
            ],
            "Valid_data_5" => [
                "data" => [
                    "text" => "一二三四五六七八九",
                ]
            ],
            "Valid_data_6" => [
                "data" => [
                    "text" => 0.1,
                ]
            ],
            "Invalid_data_1" => [
                "data" => [
                    "text" => "",
                ]
            ],
            "Invalid_data_2" => [
                "data" => [
                    "text" => null,
                ]
            ],
            "Invalid_data_3" => [
                "data" => [
                    "text" => 0,
                ]
            ],
            "Invalid_data_4" => [
                "data" => [
                    "text" => "1",
                ]
            ],
            "Invalid_data_5" => [
                "data" => [
                    "text" => 1234567890,
                ]
            ],
            "Invalid_data_6" => [
                "data" => [
                    "text" => "0123456789",
                ]
            ],
            "Invalid_data_7" => [
                "data" => [
                    "text" => "一二三四五六七八九十",
                ]
            ],
            "Invalid_data_8" => [
                "data" => [
                    "text" => "1234五六七八九十",
                ]
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "error_tag" => "length><",
            "field_path" => "text",
            "parameters" => [
                "1",
                "10",
            ],
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_length_greater_lessequal()
    {
        $rule = [
            "text" => "length><=[1,10]|length_greater_lessequal[1,10]",
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "text" => "12",
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "text" => 123456789,
                ]
            ],
            "Valid_data_3" => [
                "data" => [
                    "text" => "1.12345678",
                ]
            ],
            "Valid_data_4" => [
                "data" => [
                    "text" => "一二",
                ]
            ],
            "Valid_data_5" => [
                "data" => [
                    "text" => "一二三四五六七八九十",
                ]
            ],
            "Valid_data_6" => [
                "data" => [
                    "text" => 0.1,
                ]
            ],
            "Invalid_data_1" => [
                "data" => [
                    "text" => "",
                ]
            ],
            "Invalid_data_2" => [
                "data" => [
                    "text" => null,
                ]
            ],
            "Invalid_data_3" => [
                "data" => [
                    "text" => 0,
                ]
            ],
            "Invalid_data_4" => [
                "data" => [
                    "text" => "1",
                ]
            ],
            "Invalid_data_5" => [
                "data" => [
                    "text" => 12345678901,
                ]
            ],
            "Invalid_data_6" => [
                "data" => [
                    "text" => "01234567891",
                ]
            ],
            "Invalid_data_7" => [
                "data" => [
                    "text" => "一二三四五六七八九十1",
                ]
            ],
            "Invalid_data_8" => [
                "data" => [
                    "text" => "1234五六七八九十1",
                ]
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "error_tag" => "length><=",
            "field_path" => "text",
            "parameters" => [
                "1",
                "10",
            ],
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_length_greaterequal_less()
    {
        $rule = [
            "text" => "length>=<[1,10]|length_greaterequal_less[1,10]",
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "text" => "1",
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "text" => 123456789,
                ]
            ],
            "Valid_data_3" => [
                "data" => [
                    "text" => "1.1234567",
                ]
            ],
            "Valid_data_4" => [
                "data" => [
                    "text" => "一",
                ]
            ],
            "Valid_data_5" => [
                "data" => [
                    "text" => "一二三四五六七八九",
                ]
            ],
            "Valid_data_6" => [
                "data" => [
                    "text" => 0.1,
                ]
            ],
            "Invalid_data_1" => [
                "data" => [
                    "text" => "",
                ]
            ],
            "Invalid_data_2" => [
                "data" => [
                    "text" => null,
                ]
            ],
            // "Invalid_data_3" => [
            //     "data" => [
            //         "text" => 0,
            //     ]
            // ],
            // "Invalid_data_4" => [
            //     "data" => [
            //         "text" => "1",
            //     ]
            // ],
            "Invalid_data_5" => [
                "data" => [
                    "text" => 1234567890,
                ]
            ],
            "Invalid_data_6" => [
                "data" => [
                    "text" => "0123456789",
                ]
            ],
            "Invalid_data_7" => [
                "data" => [
                    "text" => "一二三四五六七八九十",
                ]
            ],
            "Invalid_data_8" => [
                "data" => [
                    "text" => "1234五六七八九十",
                ]
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "error_tag" => "length>=<",
            "field_path" => "text",
            "parameters" => [
                "1",
                "10",
            ],
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_length_between()
    {
        $rule = [
            "text" => "length>=<=[1,10]|length_between[1,10]",
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "text" => "1",
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "text" => 1234567890,
                ]
            ],
            "Valid_data_3" => [
                "data" => [
                    "text" => "1.12345678",
                ]
            ],
            "Valid_data_4" => [
                "data" => [
                    "text" => "一",
                ]
            ],
            "Valid_data_5" => [
                "data" => [
                    "text" => "一二三四五六七八九十",
                ]
            ],
            "Valid_data_6" => [
                "data" => [
                    "text" => 0.1,
                ]
            ],
            "Valid_data_7" => [
                "data" => [
                    "text" => "abc",
                ]
            ],
            "Invalid_data_1" => [
                "data" => [
                    "text" => "",
                ]
            ],
            "Invalid_data_2" => [
                "data" => [
                    "text" => null,
                ]
            ],
            // "Invalid_data_3" => [
            //     "data" => [
            //         "text" => 0,
            //     ]
            // ],
            // "Invalid_data_4" => [
            //     "data" => [
            //         "text" => "1",
            //     ]
            // ],
            "Invalid_data_5" => [
                "data" => [
                    "text" => 12345678901,
                ]
            ],
            "Invalid_data_6" => [
                "data" => [
                    "text" => "01234567891",
                ]
            ],
            "Invalid_data_7" => [
                "data" => [
                    "text" => "一二三四五六七八九十1",
                ]
            ],
            "Invalid_data_8" => [
                "data" => [
                    "text" => "1234五六七八九十1",
                ]
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "error_tag" => "length>=<=",
            "field_path" => "text",
            "parameters" => [
                "1",
                "10",
            ],
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_integer()
    {
        $rule = [
            "id" => "int|integer",
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "id" => 0,
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "id" => 1,
                ]
            ],
            "Invalid_data_1" => [
                "data" => [
                    "id" => "0",
                ],
            ],
            "Invalid_data_2" => [
                "data" => [
                    "id" => "false",
                ],
            ],
            "Invalid_data_3" => [
                "data" => [
                    "id" => 1.0,
                ],
            ],
            "Invalid_data_4" => [
                "data" => [
                    "id" => true,
                ],
            ],
            "Invalid_data_5" => [
                "data" => [
                    "id" => [],
                ],
            ],
            "Invalid_data_6" => [
                "data" => [
                    "id" => "",
                ],
            ],
            "Invalid_data_7" => [
                "data" => [
                    "id" => null,
                ],
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "error_tag" => "int",
            "field_path" => "id",
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_float()
    {
        $rule = [
            "id" => "float",
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "id" => 0.0,
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "id" => 1.0,
                ]
            ],
            "Invalid_data_1" => [
                "data" => [
                    "id" => "0",
                ],
            ],
            "Invalid_data_2" => [
                "data" => [
                    "id" => "false",
                ],
            ],
            "Invalid_data_3" => [
                "data" => [
                    "id" => 1,
                ],
            ],
            "Invalid_data_4" => [
                "data" => [
                    "id" => true,
                ],
            ],
            "Invalid_data_5" => [
                "data" => [
                    "id" => [],
                ],
            ],
            "Invalid_data_6" => [
                "data" => [
                    "id" => "",
                ],
            ],
            "Invalid_data_7" => [
                "data" => [
                    "id" => null,
                ],
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "error_tag" => "float",
            "field_path" => "id",
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_string()
    {
        $rule = [
            "id" => "string",
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "id" => "0",
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "id" => "1.0",
                ]
            ],
            "Valid_data_3" => [
                "data" => [
                    "id" => "false",
                ]
            ],
            "Valid_data_4" => [
                "data" => [
                    "id" => "",
                ]
            ],
            "Invalid_data_3" => [
                "data" => [
                    "id" => 1,
                ],
            ],
            "Invalid_data_4" => [
                "data" => [
                    "id" => true,
                ],
            ],
            "Invalid_data_5" => [
                "data" => [
                    "id" => [],
                ],
            ],
            "Invalid_data_7" => [
                "data" => [
                    "id" => null,
                ],
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "error_tag" => "string",
            "field_path" => "id",
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_is_array()
    {
        $rule = [
            "id" => "array|is_array",
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "id" => [],
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "id" => [ 1, 2, 3 ],
                ]
            ],
            "Valid_data_3" => [
                "data" => [
                    "id" => [ 1, 2, [ 3 ] ],
                ]
            ],
            "Invalid_data_1" => [
                "data" => [
                    "id" => "0",
                ],
            ],
            "Invalid_data_2" => [
                "data" => [
                    "id" => "false",
                ],
            ],
            "Invalid_data_3" => [
                "data" => [
                    "id" => 1.0,
                ],
            ],
            "Invalid_data_4" => [
                "data" => [
                    "id" => true,
                ],
            ],
            "Invalid_data_6" => [
                "data" => [
                    "id" => "",
                ],
            ],
            "Invalid_data_7" => [
                "data" => [
                    "id" => null,
                ],
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "error_tag" => "array",
            "field_path" => "id",
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_bool()
    {
        $rule = [
            "id" => "bool",
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "id" => false,
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "id" => true,
                ]
            ],
            "Invalid_data_1" => [
                "data" => [
                    "id" => "0",
                ],
            ],
            "Invalid_data_2" => [
                "data" => [
                    "id" => "false",
                ],
            ],
            "Invalid_data_3" => [
                "data" => [
                    "id" => 1.0,
                ],
            ],
            "Invalid_data_5" => [
                "data" => [
                    "id" => [],
                ],
            ],
            "Invalid_data_6" => [
                "data" => [
                    "id" => "",
                ],
            ],
            "Invalid_data_7" => [
                "data" => [
                    "id" => null,
                ],
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "error_tag" => "bool",
            "field_path" => "id",
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_bool_string()
    {
        $rule = [
            "id" => "bool_string",
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "id" => "false",
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "id" => "true",
                ]
            ],
            "Valid_data_3" => [
                "data" => [
                    "id" => "FALSE",
                ]
            ],
            "Valid_data_4" => [
                "data" => [
                    "id" => "TRUE",
                ]
            ],
            "Invalid_data_1" => [
                "data" => [
                    "id" => "0",
                ],
            ],
            "Invalid_data_3" => [
                "data" => [
                    "id" => 1.0,
                ],
            ],
            "Invalid_data_4" => [
                "data" => [
                    "id" => true,
                ],
            ],
            "Invalid_data_5" => [
                "data" => [
                    "id" => [],
                ],
            ],
            "Invalid_data_6" => [
                "data" => [
                    "id" => "",
                ],
            ],
            "Invalid_data_7" => [
                "data" => [
                    "id" => null,
                ],
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "error_tag" => "bool_string",
            "field_path" => "id",
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_dob()
    {
        $rule = [
            "text" => "dob",
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "text" => "1990-01-01",
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "text" => "2023-12-12",
                ]
            ],
            "Invalid_data_1" => [
                "data" => [
                    "text" => "",
                ],
            ],
            "Invalid_data_2" => [
                "data" => [
                    "text" => null,
                ],
            ],
            "Invalid_data_3" => [
                "data" => [
                    "text" => "1990-01-1",
                ],
            ],
            "Invalid_data_4" => [
                "data" => [
                    "text" => "1990-1-01",
                ],
            ],
            "Invalid_data_5" => [
                "data" => [
                    "text" => "90-01-01",
                ],
            ],
            "Invalid_data_5" => [
                "data" => [
                    "text" => "2050-01-01",
                ],
            ],
            "Invalid_data_6" => [
                "data" => [
                    "text" => "1990/01/01",
                ],
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "error_tag" => "dob",
            "field_path" => "text",
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_file_base64()
    {
        $rule = [
            // 必须是文件的 base64 且文件尺寸最大是 10kb
            "text" => "file_base64[\"\",10]",
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "text" => "data:image/jpeg;base64,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",
                ]
            ],
            "Invalid_data_1" => [
                "data" => [
                    "text" => "",
                ],
            ],
            "Invalid_data_2" => [
                "data" => [
                    "text" => null,
                ],
            ],
            "Invalid_data_3" => [
                "data" => [
                    "text" => "data:image/jpeg;base64/abc",
                ],
            ],
            // 文件尺寸是 12.13kb
            "Invalid_data_4" => [
                "data" => [
                    "text" => "data:image/png;base64,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",
                ],
                "expected_msg" => ["text" => 'text file size must be less than 10kb'],
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "error_tag" => "file_base64",
            "field_path" => "text",
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_oauth2_grant_type()
    {
        $rule = [
            "text" => "oauth2_grant_type",
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "text" => "authorization_code",
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "text" => "password",
                ]
            ],
            "Valid_data_3" => [
                "data" => [
                    "text" => "client_credentials",
                ]
            ],
            "Invalid_data_1" => [
                "data" => [
                    "text" => "",
                ],
            ],
            "Invalid_data_2" => [
                "data" => [
                    "text" => null,
                ],
            ],
            "Invalid_data_3" => [
                "data" => [
                    "text" => "xxx",
                ],
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "error_tag" => "oauth2_grant_type",
            "field_path" => "text",
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_is_email()
    {
        $rules = [
            "symbol" => [
                "text" => "email",
            ],
            "method" => [
                "text" => "is_email",
            ]
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "text" => "xxx@qq.com",
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "text" => "x-xx@gmail.com",
                ]
            ],
            "Valid_data_3" => [
                "data" => [
                    "text" => "x-xx+xx@gmail.com",
                ]
            ],
            "Valid_data_4" => [
                "data" => [
                    "text" => "123!#$%&'*+-/=?^_`{|}~@qq.com",
                ]
            ],
            "Invalid_data_1" => [
                "data" => [
                    "text" => "",
                ],
            ],
            "Invalid_data_2" => [
                "data" => [
                    "text" => null,
                ],
            ],
            "Invalid_data_3" => [
                "data" => [
                    "text" => "xxx.com",
                ],
            ],
            "Invalid_data_4" => [
                "data" => [
                    "text" => "xxx@com",
                ],
            ],
            "Invalid_data_5" => [
                "data" => [
                    "text" => "xxx@@qq.com",
                ],
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "error_tag" => "email",
            "field_path" => "text",
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_is_url()
    {
        $rules = [
            "symbol" => [
                "text" => "url",
            ],
            "method" => [
                "text" => "is_url",
            ]
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "text" => "http://github.com",
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "text" => "https://github.com/gitHusband/Validation?tab=readme-ov-file",
                ]
            ],
            "Valid_data_3" => [
                "data" => [
                    "text" => "https://xxx.abcdefgh",
                ]
            ],
            "Invalid_data_1" => [
                "data" => [
                    "text" => "",
                ],
            ],
            "Invalid_data_2" => [
                "data" => [
                    "text" => null,
                ],
            ],
            "Invalid_data_3" => [
                "data" => [
                    "text" => "xxx.com",
                ],
            ],
            "Invalid_data_4" => [
                "data" => [
                    "text" => "http:xxx.com",
                ],
            ],
            "Invalid_data_5" => [
                "data" => [
                    "text" => "https://xxx.abcdefghi",
                ],
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "error_tag" => "url",
            "field_path" => "text",
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_is_ip()
    {
        $rules = [
            "symbol" => [
                "text" => "ip",
            ],
            "method" => [
                "text" => "is_ip",
            ]
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "text" => "1.1.1.1",
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "text" => "255.255.255.255",
                ]
            ],
            "Valid_data_3" => [
                "data" => [
                    "text" => "0.0.0.0",
                ]
            ],
            "Invalid_data_1" => [
                "data" => [
                    "text" => "",
                ],
            ],
            "Invalid_data_2" => [
                "data" => [
                    "text" => null,
                ],
            ],
            "Invalid_data_3" => [
                "data" => [
                    "text" => "1.1.1.257",
                ],
            ],
            "Invalid_data_4" => [
                "data" => [
                    "text" => "1.1.1",
                ],
            ],
            "Invalid_data_5" => [
                "data" => [
                    "text" => "1.1.1.1000",
                ],
            ],
            "Invalid_data_5" => [
                "data" => [
                    "text" => "1.1.1.1.1",
                ],
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "error_tag" => "ip",
            "field_path" => "text",
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_is_mac()
    {
        $rules = [
            "symbol" => [
                "text" => "mac",
            ],
            "method" => [
                "text" => "is_mac",
            ]
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "text" => "00-00-00-00-00-00",
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "text" => "FF-FF-FF-FF-FF-FF",
                ]
            ],
            "Invalid_data_1" => [
                "data" => [
                    "text" => "",
                ],
            ],
            "Invalid_data_2" => [
                "data" => [
                    "text" => null,
                ],
            ],
            "Invalid_data_3" => [
                "data" => [
                    "text" => "FF-FF-FF-FF-FF",
                ],
            ],
            "Invalid_data_4" => [
                "data" => [
                    "text" => "FF-FF-FF-FF-FF-FF-00",
                ],
            ],
            "Invalid_data_5" => [
                "data" => [
                    "text" => "FF-FF-FF-FF-FF-FH",
                ],
            ],
            "Invalid_data_5" => [
                "data" => [
                    "text" => "FF-FF-FF-FF-FF-F",
                ],
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "error_tag" => "mac",
            "field_path" => "text",
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_is_uuid()
    {
        $rules = [
            "symbol" => [
                "text" => "uuid",
            ],
            "method" => [
                "text" => "is_uuid",
            ]
        ];

        $cases = [
            "Valid_uuidV1_1" => [
                "data" => [
                    "text" => "e1d66bd0-fa4d-11ee-a06a-0242ac120003",
                ]
            ],
            "Valid_uuidV2_1" => [
                "data" => [
                    "text" => "00000000-fa4d-21ee-8800-0242ac120003",
                ]
            ],
            "Valid_uuidV3_1" => [
                "data" => [
                    "text" => "3f703955-aaba-3e70-a3cb-baff6aa3b28f",
                ]
            ],
            "Valid_uuidV4_1" => [
                "data" => [
                    "text" => "197f8315-72d8-4beb-b004-c64db12fead5",
                ]
            ],
            "Valid_uuidV5_1" => [
                "data" => [
                    "text" => "a8f6ae40-d8a7-58f0-be05-a22f94eca9ec",
                ]
            ],
            "Valid_uuidV6_1" => [
                "data" => [
                    "text" => "1eefa4de-1e0e-6722-851d-0242ac120003",
                ]
            ],
            "Valid_uuidV7_1" => [
                "data" => [
                    "text" => "018edc41-e76b-71af-84fc-a34311b97cd3",
                ]
            ],
            "Valid_uuidV8_1" => [
                "data" => [
                    "text" => "00112233-4455-8677-8899-aabbccddeeff",
                ]
            ],
            "Invalid_data_1" => [
                "data" => [
                    "text" => "",
                ],
            ],
            "Invalid_data_2" => [
                "data" => [
                    "text" => null,
                ],
            ],
            "Invalid_data_3" => [
                "data" => [
                    "text" => "018edc41-e76b-71af-84fc-a34311b97cdH",
                ],
            ],
            "Invalid_data_4" => [
                "data" => [
                    "text" => "018edc41-e76b-71af-84fc-a34311b97cd3-",
                ],
            ],
            "Invalid_data_5" => [
                "data" => [
                    "text" => "-018edc41-e76b-71af-84fc-a34311b97cd3",
                ],
            ],
            "Invalid_data_5" => [
                "data" => [
                    "text" => "018edc41-e76b-71af-84fc",
                ],
            ],
            "Invalid_data_6" => [
                "data" => [
                    "text" => "018edc41-e76b-71af-a34311b97cd3",
                ],
            ],
        ];

        if (version_compare(PHP_VERSION, '7', '>=')) {
            $cases["Valid_uuidV1_2"] = [
                "data" => [
                    "text" => (string) \Ramsey\Uuid\Uuid::uuid1(),
                ]
            ];
            if (method_exists('\Ramsey\Uuid\Uuid', 'uuid2')) $cases["Valid_uuidV2_2"] = [
                "data" => [
                    "text" => (string) \Ramsey\Uuid\Uuid::uuid2(\Ramsey\Uuid\Uuid::DCE_DOMAIN_PERSON),
                ]
            ];
            $cases["Valid_uuidV3_2"] = [
                "data" => [
                    "text" => (string) \Ramsey\Uuid\Uuid::uuid3(\Ramsey\Uuid\Uuid::NAMESPACE_URL, 'https://github.com/gitHusband/Validation'),
                ]
            ];
            $cases["Valid_uuidV4_2"] = [
                "data" => [
                    "text" => (string) \Ramsey\Uuid\Uuid::uuid4(),
                ]
            ];
            $cases["Valid_uuidV5_2"] = [
                "data" => [
                    "text" => (string) \Ramsey\Uuid\Uuid::uuid5(\Ramsey\Uuid\Uuid::NAMESPACE_URL, 'https://github.com/gitHusband/Validation'),
                ]
            ];
            if (method_exists('\Ramsey\Uuid\Uuid', 'uuid6')) $cases["Valid_uuidV6_2"] = [
                "data" => [
                    "text" => (string) \Ramsey\Uuid\Uuid::uuid6(),
                ]
            ];
            if (method_exists('\Ramsey\Uuid\Uuid', 'uuid7')) $cases["Valid_uuidV7_2"] = [
                "data" => [
                    "text" => (string) \Ramsey\Uuid\Uuid::uuid7(),
                ]
            ];
            if (method_exists('\Ramsey\Uuid\Uuid', 'uuid8')) $cases["Valid_uuidV8_2"] = [
                "data" => [
                    "text" => (string) \Ramsey\Uuid\Uuid::uuid8("\x00\x11\x22\x33\x44\x55\x66\x77\x88\x99\xaa\xbb\xcc\xdd\xee\xff"),
                ]
            ];
        }

        $extra = [
            "method_name" => __METHOD__,
            "error_tag" => "uuid",
            "field_path" => "text",
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_is_ulid()
    {
        $rules = [
            "symbol" => [
                "text" => "ulid",
            ],
            "method" => [
                "text" => "is_ulid",
            ]
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "text" => "01HXTZVYTF6CKBXYRFWBGDD9BB",
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "text" => "01hxtzvytf6ckbxyrfwbgdd9bc",
                ]
            ],
            "Valid_data_3" => [
                "data" => [
                    "text" => "01HXTZZ2DA2X3MJSNY01JWHBCS",
                ]
            ],
            "Valid_data_4" => [
                "data" => [
                    "text" => "01hxtzz2dbn89agrsjwexzdm0c",
                ]
            ],
            "Valid_data_5" => [
                "data" => [
                    "text" => "01HXTZZN67HXB9FM2QXTR1QTND",
                ]
            ],
            "Valid_data_6" => [
                "data" => [
                    "text" => "01hxtzzn67hxb9fm2qxtr1qtne",
                ]
            ],
            "Invalid_data_1" => [
                "data" => [
                    "text" => "",
                ],
            ],
            "Invalid_data_2" => [
                "data" => [
                    "text" => null,
                ],
            ],
            "Invalid_data_3" => [
                "data" => [
                    "text" => "01HXTZVYTF6CKBXYRFWBGDD9BO",
                ],
            ],
            "Invalid_data_4" => [
                "data" => [
                    "text" => "01HXTZVYTF6CKBXYRFWBGDD9B",
                ],
            ],
            "Invalid_data_5" => [
                "data" => [
                    "text" => "01hxtzvytf6ckbxyrfwbgdd9bi",
                ],
            ],
            "Invalid_data_6" => [
                "data" => [
                    "text" => "01hxtzvytf6ckbxyrfwbgdd9b",
                ],
            ],
        ];

        if (version_compare(PHP_VERSION, '7', '>=')) {
            // echo 123;die;
            $dynamic_cases = [
                "Valid_data_7" => [
                    "data" => [
                        "text" => (string) \Ulid\Ulid::generate(),
                    ]
                ],
                "Valid_data_8" => [
                    "data" => [
                        "text" => (string) \Ulid\Ulid::generate(true),
                    ]
                ],
            ];

            $cases = array_merge($cases, $dynamic_cases);
        }

        $extra = [
            "method_name" => __METHOD__,
            "error_tag" => "ulid",
            "field_path" => "text",
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }
}
