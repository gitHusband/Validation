<?php

namespace githusband\Tests\Rule;

use githusband\Tests\Extend\MyValidation;

/**
 * Test cases of RuleClassTest
 * 
 * @see githusband\Tests\Extend\Rule\RuleClassTest
 * @package UnitTests
 */
trait TestRuleTest
{
    protected function test_method_is_custom_string()
    {
        $rules = [
            "symbol" => [
                "text" => "required|cus_str",
            ],
            "method" => [
                "text" => "required|is_custom_string",
            ],
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "text" => "Hello World",
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "text" => "Hello at 2024-06-24",
                ]
            ],
            "Invalid_data_1" => [
                "data" => [
                    "text" => "Hello World!",
                ],
                "expected_msg" => ["text" => "text validation failed"]
            ],
            "Invalid_data_2" => [
                "data" => [
                    "text" => "Hello",
                ],
                "expected_msg" => ["text" => "text validation failed"]
            ],
        ];

        $extra = [
            "validation_class" => new MyValidation([
                "validation_global" => false,
            ]),
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_is_in_custom_list()
    {
        $rules = [
            "symbol" => [
                "text" => 'required|<custom>["1st", "First", "2nd", "Second"]',
            ],
            "method" => [
                "text" => 'required|is_in_custom_list["1st", "First", "2nd", "Second"]',
            ],
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "text" => "1st",
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "text" => "Second",
                ]
            ],
            "Invalid_data_1" => [
                "data" => [
                    "text" => "1",
                ],
                "expected_msg" => ["text" => "text validation failed"]
            ],
            "Invalid_data_2" => [
                "data" => [
                    "text" => "二",
                ],
                "expected_msg" => ["text" => "text validation failed"]
            ],
        ];

        $extra = [
            "validation_class" => new MyValidation([
                "validation_global" => false,
            ]),
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_is_equal_to_password()
    {
        $rules = [
            "symbol" => [
                "password" => 'required|string',
                "confirm_password" => 'required|is_equal_to_password >> The password confirms failed',
                "compare_password" => 'required|=[@password] >> The password compares failed',
            ],
            "method" => [
                "password" => 'required|string',
                "confirm_password" => 'required|=pwd >> The password confirms failed',
                "compare_password" => 'required|equal[@password] >> The password compares failed',
            ],
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "password" => 'a12345678',
                    "confirm_password" => 'a12345678',
                    "compare_password" => 'a12345678',
                ]
            ],
            "Invalid_data_1" => [
                "data" => [
                    "password" => 'a12345678',
                    "confirm_password" => 'A12345678',
                    "compare_password" => 'a12345678',
                ],
                "expected_msg" => ["confirm_password" => "The password confirms failed"]
            ],
            "Invalid_data_2" => [
                "data" => [
                    "password" => 'a12345678',
                    "confirm_password" => 'a12345678',
                    "compare_password" => 'A12345678',
                ],
                "expected_msg" => ["compare_password" => "The password compares failed"]
            ],
        ];

        $extra = [
            "validation_class" => new MyValidation([
                "validation_global" => false,
            ]),
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }
}
