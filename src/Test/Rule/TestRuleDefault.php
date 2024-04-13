<?php

namespace githusband\Test\Rule;

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
            "key_bool_str_true" => "optional|==[\"true\"]|strictly_equal['true']",
            "key_bool_str_FALSE" => "optional|==[\"FALSE\"]|strictly_equal['FALSE']",
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
                    "key_bool_str_true" => "true",
                    "key_bool_str_FALSE" => "FALSE",
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
                    "key_bool_str_true" => "true",
                    "key_bool_str_FALSE" => "FALSE",
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
            "Invalid_key_bool_str_true" => [
                "data" => [
                    "key_bool_str_true" => true,
                ],
                "expected_msg" => ["key_bool_str_true" => "key_bool_str_true must be strictly equal to string(true)"]
            ],
            "Invalid_key_bool_str_true_1" => [
                "data" => [
                    "key_bool_str_true" => "TRUE",
                ],
                "expected_msg" => ["key_bool_str_true" => "key_bool_str_true must be strictly equal to string(true)"]
            ],
            "Invalid_key_bool_str_FALSE" => [
                "data" => [
                    "key_bool_str_FALSE" => false,
                ],
                "expected_msg" => ["key_bool_str_FALSE" => "key_bool_str_FALSE must be strictly equal to string(FALSE)"]
            ],
            "Invalid_key_bool_str_FALSE_1" => [
                "data" => [
                    "key_bool_str_FALSE" => "false",
                ],
                "expected_msg" => ["key_bool_str_FALSE" => "key_bool_str_FALSE must be strictly equal to string(FALSE)"]
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
            "key_bool_str_true" => "optional|!==[\"true\"]|not_strictly_equal['true']",
            "key_bool_str_FALSE" => "optional|!==[\"FALSE\"]|not_strictly_equal['FALSE']",
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
                    "key_bool_str_true" => true,
                    "key_bool_str_FALSE" => FALSE,
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
                    "key_bool_str_true" => 1,
                    "key_bool_str_FALSE" => "",
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

    protected function test_method_greater_than_equal()
    {
        $rule = [
            "id" => ">=[1]|greater_than_equal[1]",
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

    protected function test_method_less_than_equal()
    {
        $rule = [
            "id" => "<=[1]|less_than_equal[1]",
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

    protected function test_method_between()
    {
        $rule = [
            "id" => "<>[1,10]|between[1,10]",
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
            "error_tag" => "<>",
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
    }

    protected function _test_method_greaterequal_less()
    {
    }

    protected function _test_method_greaterequal_lessequal()
    {
    }

    protected function test_method_in_number()
    {
        $rule = [
            "id" => "(n)[1,2,3]|in_number[1,2]",
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

    protected function _test_method_not_in_number()
    {
    }

    protected function _test_method_in_string()
    {
    }

    protected function _test_method_not_in_string()
    {
    }

    protected function _test_method_length_equal()
    {
    }

    protected function _test_method_length_not_equal()
    {
    }

    protected function _test_method_length_greater_than()
    {
    }

    protected function _test_method_length_less_than()
    {
    }

    protected function _test_method_length_greater_than_equal()
    {
    }

    protected function _test_method_length_less_than_equal()
    {
    }

    protected function _test_method_length_between()
    {
    }

    protected function _test_method_length_greater_lessequal()
    {
    }

    protected function _test_method_length_greaterequal_less()
    {
    }

    protected function _test_method_length_greaterequal_lessequal()
    {
    }

    protected function _test_method_integer()
    {
    }

    protected function _test_method_float()
    {
    }

    protected function _test_method_string()
    {
    }

    protected function _test_method_arr()
    {
    }

    protected function _test_method_bool()
    {
    }

    protected function _test_method_bool_str()
    {
    }

    protected function _test_method_email()
    {
    }

    protected function _test_method_url()
    {
    }

    protected function _test_method_ip()
    {
    }

    protected function _test_method_mac()
    {
    }

    // date of birth
    protected function _test_method_dob()
    {
    }

    protected function _test_method_file_base64_size()
    {
    }

    protected function _test_method_file_base64()
    {
    }

    protected function _test_method_uuid()
    {
    }

    protected function _test_method_oauth2_grant_type()
    {
    }
}
