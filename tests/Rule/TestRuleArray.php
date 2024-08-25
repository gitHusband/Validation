<?php

namespace githusband\Tests\Rule;

/**
 * Test cases of Array
 * 
 * @see githusband\Rule\RuleClassArray
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

    protected function test_method_is_unique()
    {
        $rules = [
            "symbol" => [
                "*" => "unique[@parent]",
            ],
            "method" => [
                "*" => "is_unique[@parent]",
            ],
            "symbol_by_default_arguments" => [
                "*" => "unique",
            ],
            "method_by_default_arguments" => [
                "*" => "is_unique",
            ],
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    1, 2, 3
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    1, 1.0, 0, 0.0, true, false, null, '', ' '
                ]
            ],
            "Invalid_data_1" => [
                "data" => [
                    1, 2, 1
                ],
                "expected_msg" => ["data.0" => "data.0 is not unique"]
            ],
            "Invalid_data_2" => [
                "data" => [
                    1, 2, 3, 4, 3
                ],
                "expected_msg" => ["data.2" => "data.2 is not unique"]
            ],
            "Invalid_data_3" => [
                "data" => [
                    1, 1.0, 0, 0.0, true, false, null, '', ' ', 0.0
                ],
                "expected_msg" => ["data.3" => "data.3 is not unique"]
            ],
            "Invalid_data_4" => [
                "data" => [
                    1, 1.0, 0, 0.0, true, false, null, '', ' ', TRUE
                ],
                "expected_msg" => ["data.4" => "data.4 is not unique"]
            ],
            "Invalid_data_5" => [
                "data" => [
                    1, 1.0, 0, 0.0, true, false, null, '', ' ', FALSE
                ],
                "expected_msg" => ["data.5" => "data.5 is not unique"]
            ],
            "Invalid_data_6" => [
                "data" => [
                    1, 1.0, 0, 0.0, true, false, null, '', ' ', null
                ],
                "expected_msg" => ["data.6" => "data.6 is not unique"]
            ],
            "Invalid_data_7" => [
                "data" => [
                    1, 1.0, 0, 0.0, true, false, null, '', ' ', ''
                ],
                "expected_msg" => ["data.7" => "data.7 is not unique"]
            ],
            "Invalid_data_8" => [
                "data" => [
                    1, 1.0, 0, 0.0, true, false, null, '', ' ', ' '
                ],
                "expected_msg" => ["data.8" => "data.8 is not unique"]
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
