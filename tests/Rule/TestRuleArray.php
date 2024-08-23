<?php

namespace githusband\Tests\Rule;

/**
 * Test cases of Array
 * 
 * @see githusband\Rule\RuleClassDatetime
 * @package UnitTests
 */
trait TestRuleArray
{
    protected function test_method_require_array_keys()
    {
        $rules = [
            "symbol" => [
                "person" => [
                    "__self__" => "optional|<keys>[id, name]",
                    "id" => "required|int|>=[1]|<=[100]",
                    "name" => "required|string|/^[A-Z]+-\d+/"
                ]
            ],
            "method" => [
                "person" => [
                    "__self__" => "optional|require_array_keys[id, name]",
                    "id" => "required|int|>=[1]|<=[100]",
                    "name" => "required|string|/^[A-Z]+-\d+/"
                ]
            ],
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "person" => [
                        "id" => 1,
                        "name" => "A-1"
                    ]
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "person" => []
                ]
            ],
            "Invalid_data_1" => [
                "data" => [
                    "person" => [
                        "id" => 1,
                        "name" => ""
                    ]
                ],
                "expected_msg" => ["person.name" => "person.name can not be empty"]
            ],
            "Invalid_data_2" => [
                "data" => [
                    "person" => [
                        "id" => 0,
                        "name" => ""
                    ]
                ],
                "expected_msg" => ["person.id" => "person.id must be greater than or equal to 1"]
            ],
            "Invalid_data_3" => [
                "data" => [
                    "person" => [
                        "id" => 101,
                        "name" => ""
                    ]
                ],
                "expected_msg" => ["person.id" => "person.id must be less than or equal to 100"]
            ],
            "Invalid_data_4" => [
                "data" => [
                    "person" => [
                        "id" => 1,
                        "name" => "a1"
                    ]
                ],
                "expected_msg" => ["person.name" => "person.name format is invalid, should be /^[A-Z]+-\\d+/"]
            ],
            "Invalid_data_5" => [
                "data" => [
                    "person" => [
                        "id" => 1,
                    ]
                ],
                "expected_msg" => ["person" => "person must be array and its keys must contain and only contain id,name"]
            ],
            "Invalid_data_6" => [
                "data" => [
                    "person" => [
                        "id" => 1,
                        "name" => "A-1",
                        "age" => 18
                    ]
                ],
                "expected_msg" => ["person" => "person must be array and its keys must contain and only contain id,name"]
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }
}
