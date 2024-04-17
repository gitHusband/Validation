<?php

namespace githusband\Test;

use githusband\Test\Rule\TestRuleDefaultDeprecated;

/**
 * 1. How to add a new unit test case?
 *  To add a new method for Unit class. The method name must be starting with "test_".
 *  The method must return an array which is a unit test case.
 *  - @see Unit::execute_tests() for the logic.
 *  - @see Unit::test_series_rule() for an example.
 * 
 * 2. What is a unit test case?
 *  - @see Unit::validate_cases() for the logic.
 *  For example:
 *  {
 *      "rule": {
 *          "id": "required|/^\d+$/"
 *      },
 *      "cases": {
 *          "Valid_data_1": {
 *              "data": {
 *                  "id": 1
 *              }
 *          },
 *          "Invalid_data_1": {
 *              "data": {
 *                  "id": "abc"
 *              },
 *              "expected_msg": {
 *                  "id": "id format is invalid, should be /^\d+$/"
 *              }
 *          }
 *      },
 *      "extra": {
 *          "method_name": "githusband\Test\Unit::test_xxx",
 *      }
 *  }
 */

use githusband\Validation;
use githusband\Test\TestCommon;
use githusband\Test\Extend\MyValidation;

/**
 * @deprecated 2.3.1
 */
class UnitDeprecated extends TestCommon
{
    /**
     * @var Validation
     */
    protected $validation;

    /**
     * Unit Testing error message
     *
     * @var array
     */
    protected $error_message;
    /**
     * If true, means unit testing is error
     *
     * @var bool
     */
    protected $is_error = false;

    private $_symbol_me = "@this";

    public function __construct()
    {
        parent::__construct();

        $validation_conf = [
            "validation_global" => false,
        ];
        $this->validation = new Validation($validation_conf);
    }

    /**
     * Run uint testing.
     * If any of unit testing case is failed, stop continuing to test other cases and return an error message immediatelly
     *
     * @param string $method_name
     * @param string $sub_method_name
     * @return array|string
     */
    public function run($method_name = '')
    {
        if ($method_name == 'help') return $this->help();
        if ($method_name == 'performance') return $this->performance();

        $result = $this->run_method($method_name);

        if ($result) {
            $this->write_log(static::LOG_LEVEL_INFO, "===================================\n= Re-run for strict parameter mode\n===================================\n");
            $this->validation->set_config([
                "is_strict_parameter_type" => true
            ]);
            $this->run_method($method_name);
        }

        return $this->get_unit_result();
    }

    protected function run_method($method_name = '', $is_performance = false)
    {
        $this->write_log(static::LOG_LEVEL_INFO, "# Test PHP v" . PHP_VERSION . "\n");
        if (!empty($method_name)) {
            $this->write_log(static::LOG_LEVEL_DEBUG, "Start execute test case of method {$method_name}:\n");
            $result = $this->execute_tests($method_name);
        } else {
            $this->write_log(static::LOG_LEVEL_DEBUG, "Start execute all the test cases:\n");
            $class_methods = get_class_methods($this);

            foreach ($class_methods as $method_name) {
                if (preg_match('/^test_.*/', $method_name)) {
                    if ($is_performance && $method_name == 'test_exception') continue;

                    $result = $this->execute_tests($method_name);
                    if (!$result) break;
                }
            }
        }

        return $result;
    }

    /**
     * Performance testing
     *
     * @param int $times
     * @param string $method_name
     * @return void
     */
    public function performance($times = 500, $method_name = '')
    {
        if (!is_numeric($times)) {
            $method_name = $times;
            $times = 500;
        }

        $log_level = getenv('COMPOSER_LOG_LEVEL_OPTION');
        if (empty($log_level) || !is_numeric($log_level)) $this->set_log_level(static::LOG_LEVEL_WARN);

        $this->validation->set_config([
            'is_strict_parameter_separator' => true,                    // @deprecated 2.4.0. 1. true - Parse multiple parameters, support "," and array; 2. false - Simple way to parse parameters but not support "," as part of a parameter;
            'is_strict_parameter_type' => true,                         // @deprecated 2.4.0. 1. true - Detect the parameters type, e.g. 123 is int, "123" is string; 2. false - All the parameters type is string;
        ]);
        $step_1_spent_time = $this->run_performance("1 Strict mode -", $times, $method_name);
        if ($step_1_spent_time == false) return false;

        $this->validation->set_config([
            'is_strict_parameter_separator' => false,                   // @deprecated 2.4.0. 1. true - Parse multiple parameters, support "," and array; 2. false - Simple way to parse parameters but not support "," as part of a parameter;
            'is_strict_parameter_type' => false,                        // @deprecated 2.4.0. 1. true - Detect the parameters type, e.g. 123 is int, "123" is string; 2. false - All the parameters type is string;
        ]);
        $step_2_spent_time = $this->run_performance("2 Simple mode -", $times, $method_name);
        if ($step_2_spent_time == false) return false;

        $this->write_log(static::LOG_LEVEL_WARN, "#######################################################\n");
        if ($step_2_spent_time > $step_1_spent_time) {
            $big_time = $step_2_spent_time;
            $small_time = $step_1_spent_time;
            $this->write_log(static::LOG_LEVEL_WARN, "# Simple mode(2) costed more time.\n");
        } else {
            $big_time = $step_1_spent_time;
            $small_time = $step_2_spent_time;
            $this->write_log(static::LOG_LEVEL_WARN, "# Strict mode(1) costed more time.\n");
        }
        $diff_time = $big_time - $small_time;
        $diff_human_time = $this->seconds_to_human_time($diff_time);
        $this->write_log(static::LOG_LEVEL_WARN, "# Total time diff: {$diff_time} Seconds({$diff_human_time})\n");
        $this->write_log(static::LOG_LEVEL_WARN, "#######################################################\n");

        return $this->get_unit_result();
    }

    protected function run_performance($step, $times = 100, $method_name = '') {
        $start_time = (int) (microtime(true) * 1000);
        $start_datetime = date('Y-m-d H:i:s', floor($start_time / 1000));
        $this->write_log(static::LOG_LEVEL_WARN, "#{$step} Start performance testing at {$start_datetime}\n");

        $i = 0;
        for ($i = 0; $i < $times; $i++) {
            $result = $this->run_method($method_name, true);
            if ($this->is_error) return $this->get_unit_result();
        }

        $end_time = (int) (microtime(true) * 1000);
        $end_datetime = date('Y-m-d H:i:s', floor($end_time / 1000));
        $spent_time = ($end_time - $start_time) / 1000;
        $spent_human_time = $this->seconds_to_human_time($spent_time);
        $this->write_log(static::LOG_LEVEL_WARN, "#{$step} End performance testing at {$end_datetime}. Total time spent: {$spent_time} Seconds({$spent_human_time})\n");

        return $spent_time;
    }

    /**
     * Auto-executing two kind of methods:
     *  1. Method name starting with "test_" - This kind of method only contains validation config, suce as test cases
     *  2. Method name starting with "test_" and end with "_execute" - Not just contains validation config, it will call static::validate_cases() inside the method
     *
     * @param string $method_name
     * @param bool $error_data If is array, will get config data of method
     * @return bool
     */
    protected function execute_tests($method_name, $error_data = false)
    {
        $this->write_log(static::LOG_LEVEL_DEBUG, " - {$method_name}\n");

        if (preg_match('/^.*_execute$/', $method_name)) {
            return $this->{$method_name}($error_data);
        }

        $method_info = $this->{$method_name}();

        if ($method_info === null) {
            $this->is_error = true;
            return false;
        }

        if ($error_data !== false) return $this->get_method_info($method_info['rule'], $method_info['cases'], $method_info['extra'], $error_data);

        return $this->validate_cases($method_info['rule'], $method_info['cases'], $method_info['extra']);
    }

    /**
     * 1. Auto-Validate two kind of cases:
     *  1.1. Start with "Valid" - If validation result is not true, means this case is error. 
     *  1.2. Start with "Invalid" - If validation result is not true, then check the validation error message is expected(expected_msg field of a case array), if not, means this case is error.
     * 2. Each "Invalid Case" contains fields:
     *  2.1. data - Required. Case data 
     *  2.2. expected_msg - Optional. Expected the validation error message
     *  2.3. error_msg_format - Optional. Indicates the error message format, you can set it to:
     *      [
     *          'format' => One of the four error format. e.g. Validation::ERROR_FORMAT_DOTTED_GENERAL
     *          'nested' => is nested error format (@deprecated)
     *          'general' => is general error format (@deprecated)
     *      ],
     *  2.4. error_tag - Optional. If you don't set expected_msg directly, get error message of the error_tag
     *  2.5. field_path - Optional. In place of `@this` in the error message getting from the error_tag
     * 3. Extra data contains fields:
     *  3.1. validation_class - Optional. The validation class. If not set, then use {$this->validation}
     *  3.2. method_name - Required. The name of test method. e.g. test_series_rule
     *  3.3. error_tag - Optional. Use this if an invalid case does not contains expected_msg and error_tag
     *  3.4. field_path - Optional. Use this if an invalid case does not contains expected_msg and field_path
     *
     * @param array $rule Validation rule
     * @param array $cases Test cases, more than 1 case in it
     * @param array $extra Extra message, such as method name
     * @return bool
     */
    protected function validate_cases($rule, $cases, $extra)
    {
        $validation = isset($extra['validation_class']) ? $extra['validation_class'] : $this->validation;
        $validation->set_rules($rule);

        $stop_if_failed = true;
        $result = true;

        foreach ($cases as $c_field => $case) {
            $is_nested = isset($case['error_msg_format']['nested']) ? $case['error_msg_format']['nested'] : false;
            $is_general = isset($case['error_msg_format']['general']) ? $case['error_msg_format']['general'] : true;
            $error_format = '';
            if ($is_nested) {
                if ($is_general) $error_format = Validation::ERROR_FORMAT_NESTED_GENERAL;
                else $error_format = Validation::ERROR_FORMAT_NESTED_DETAILED;
            } else {
                if ($is_general) $error_format = Validation::ERROR_FORMAT_DOTTED_GENERAL;
                else $error_format = Validation::ERROR_FORMAT_DOTTED_DETAILED;
            }
            $error_format = isset($case['error_msg_format']['format']) ? $case['error_msg_format']['format'] : $error_format;

            // Check valid cases
            if (strpos($c_field, "Valid") !== false) {
                $valid_alert = isset($case['valid_alert']) ? $case['valid_alert'] : "Validation error. It should be valid.";

                if (!$validation->validate($case['data'])) {
                    $this->set_unit_error($extra['method_name'], $c_field, [
                        "valid_alert" => $valid_alert,
                        "error_msg" => $validation->get_error($error_format)
                    ], $rule, $cases);
                    $result = false;
                }
            }
            // Check invalid cases
            else if (strpos($c_field, "Invalid") !== false) {
                $valid_alert = isset($case['valid_alert']) ? $case['valid_alert'] : "Validation error. It should be invalid.";

                if ($validation->validate($case['data'])) {
                    $this->set_unit_error($extra['method_name'], $c_field, $valid_alert, $rule, $cases);
                    $result = false;
                } else {
                    if (isset($case["check_error_msg"]) && $case["check_error_msg"] == false) continue;

                    // If invalid, check error massage if it's expected.
                    if (isset($case["expected_msg"])) {
                        $expected_msg = $case["expected_msg"];
                    } else {
                        $error_tag = isset($extra['error_tag']) ? $extra['error_tag'] : 'default';
                        $error_tag = isset($case['error_tag']) ? $case['error_tag'] : $error_tag;
                        $field_path = isset($extra['field_path']) ? $extra['field_path'] : 'Unknown field path';
                        $field_path = isset($case['field_path']) ? $case['field_path'] : $field_path;
                        $params = isset($extra['parameters']) ? $extra['parameters'] : [];
                        $params = isset($case['parameters']) ? $case['parameters'] : $params;
                        $expected_msg = $this->parse_error_message($validation, $error_tag, $field_path, $params);
                        if (!$validation->get_validation_global() && !is_array($expected_msg)) {
                            $expected_msg = [$field_path => $expected_msg];
                        }
                    }

                    $error_msg = $validation->get_error($error_format);
                    if ($expected_msg !== $error_msg) {
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
            // Check exception cases
            else if (strpos($c_field, "Exception") !== false) {
                try {
                    if (!$validation->validate($case['data'])) {
                        $this->set_unit_error($extra['method_name'], $c_field, [
                            "exception_alert" => "It should throw an exception but it was validated failed",
                            "error_msg" => $validation->get_error($error_format)
                        ], $rule, $cases);
                        $result = false;
                    } else {
                        $this->set_unit_error($extra['method_name'], $c_field, [
                            "exception_alert" => "It should throw an exception but it was validated passed",
                        ], $rule, $cases);
                        $result = false;
                    }
                } catch (\Throwable $t) {
                    $exception_message = $t->getMessage();
                    $expected_msg = isset($case["expected_msg"]) ? $case["expected_msg"] : '';

                    if ($exception_message != $expected_msg) {
                        $this->set_unit_error($extra['method_name'], $c_field, [
                            "exception_alert" => "Can not match the expected exception message.",
                            "expected_msg" => $expected_msg,
                            "exception_msg" => $exception_message
                        ], $rule, $cases);
                        return $result = false;
                    }
                }
                // For the PHP version < 7
                catch (\Exception $t) {
                    $exception_message = $t->getMessage();
                    $expected_msg = isset($case["expected_msg"]) ? $case["expected_msg"] : '';

                    if ($exception_message != $expected_msg) {
                        $this->set_unit_error($extra['method_name'], $c_field, [
                            "exception_alert" => "Can not match the expected exception message.",
                            "expected_msg" => $expected_msg,
                            "exception_msg" => $exception_message
                        ], $rule, $cases);
                        return $result = false;
                    }
                }
            }

            if ($stop_if_failed && !$result) break;
        }

        return $result;
    }

    protected function set_unit_error($method, $cases_field, $error_message, $rule, $cases)
    {
        $this->is_error = true;
        $this->error_message[$method]["rule"] = $rule;
        $this->error_message[$method]["cases"][$cases_field] = $cases[$cases_field]["data"];

        $this->error_message[$method]["error"][$cases_field] = $error_message;
    }

    protected function parse_error_message($validation, $tag, $field_path, $params = [])
    {
        $error_template = $validation->get_error_template($tag);
        $error_template = str_replace($this->_symbol_me, $field_path, $error_template);

        foreach ($params as $key => $value) {
            $p = $value;
            if (!isset($value)) {
                $p = "NULL";
            } else if (is_bool($value)) {
                $p = $value ? 'true' : 'false';
            }
            $error_template = str_replace('@p' . ($key + 1), $p, $error_template);
            $error_template = str_replace('@t' . $key, $this->validation->get_parameter_type($value), $error_template);
        }

        return $error_template;
    }

    protected function get_unit_result()
    {
        if ($this->is_error) {
            $this->write_log(static::LOG_LEVEL_ERROR, "***************************************\nTest failed: " . "\n");
            $this->write_log(static::LOG_LEVEL_ERROR, json_encode($this->error_message, JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE) . "\n");
            if ($this->log_level >= static::LOG_LEVEL_INFO) {
                // @see https://www.gnu.org/software/bash/manual/bash.html#Environment
                $this->write_log(static::LOG_LEVEL_ERROR, "NOTE: you can set ENV by prefixing a command with 'VALIDATION_LOG_LEVEL=1' to print more debug message. e.g. VALIDATION_LOG_LEVEL=1 php Test.php run\n");
            }
            $this->write_log(static::LOG_LEVEL_ERROR, "***************************************\n");
            return false;
        } else {
            $php_data = "Test PHP v" . PHP_VERSION . " -";
            $this->write_log(static::LOG_LEVEL_INFO, "***************************************\n* {$php_data} Unit test success!\n***************************************\n\n");
            return true;
        }
    }

    protected function set_method_info()
    {
        foreach ($this->error_message as $unit_method => $um_value) {
            $method = str_replace(__CLASS__ . "::", "", $unit_method);
            $method_info = $this->execute_tests($method, $um_value);

            $this->error_message[$unit_method]["rule"] = $method_info["rule"];
            $this->error_message[$unit_method]["cases"] = $method_info["cases"];
        }
    }

    protected function get_method_info($rule, $cases, $extra, $error_data = [])
    {
        $method_info = [
            "rule" => $rule,
            "cases" => [],
            "extra" => $extra
        ];

        if (empty($error_data)) {
            $method_info["cases"] = $cases;
        } else {
            foreach ($error_data as $field => $value) {
                $method_info["cases"][$field] = isset($method_info[$field]) ? $method_info[$field]["data"] : "Unset";
            }
        }

        return $method_info;
    }

    protected function test_series_rule()
    {
        $rule = [
            "name" => "required|string|/^\d+.*/"
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
                "expected_msg" => ["name" => "name can not be empty"]
            ],
            "Invalid_not_string" => [
                "data" => [
                    "name" => 123
                ],
                "expected_msg" => ["name" => "name must be string"]
            ],
            "Invalid_not_start_num" => [
                "data" => [
                    "name" => "abcABC"
                ],
                "expected_msg" => ["name" => "name format is invalid, should be /^\d+.*/"]
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

    protected function test_required()
    {
        $rule = [
            "name" => "required"
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

    protected function test_require_symbol()
    {
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

    protected function test_optional()
    {
        $rule = [
            "name" => "optional|string",
            "gender" => ["[optional]" => "string"],
            "favourite_fruit[optional]" => [
                "name" => "required|string",
                "color" => "required|string"
            ],
            "favourite_meat" => [
                "[optional]" => [
                    "name" => "required|string",
                    "from" => "required|string"
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
                "data" => []
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
                "expected_msg" => ["name" => "name must be string"]
            ],
            "Invalid_data_2" => [
                "data" => [
                    "name" => "Devin",
                    "gender" => 1,
                ],
                "expected_msg" => ["gender" => "gender must be string"]
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
                "expected_msg" => ["favourite_fruit.color" => "favourite_fruit.color must be string"]
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
                "expected_msg" => ["favourite_meat.name" => "favourite_meat.name must be string"]
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

    protected function test_optional_symbol()
    {
        $rule = [
            "name" => "O|string",
            "gender" => ["[O]" => "string"],
            "favourite_fruit[O]" => [
                "name" => "required|string",
                "color" => "required|string"
            ],
            "favourite_meat" => [
                "[O]" => [
                    "name" => "required|string",
                    "from" => "required|string"
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
                "data" => []
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
                "expected_msg" => ["name" => "name must be string"]
            ],
            "Invalid_data_2" => [
                "data" => [
                    "name" => "Devin",
                    "gender" => 1,
                ],
                "expected_msg" => ["gender" => "gender must be string"]
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
                "expected_msg" => ["favourite_fruit.color" => "favourite_fruit.color must be string"]
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
                "expected_msg" => ["favourite_meat.name" => "favourite_meat.name must be string"]
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

    protected function test_optional_unset()
    {
        $rule = [
            "name" => "optional_unset|string"
        ];

        $cases = [
            "Valid_data" => [
                "data" => [
                    "name" => "David"
                ]
            ],
            "Valid_unset" => [
                "data" => []
            ],
            "Invalid_empty" => [
                "data" => [
                    "name" => ""
                ],
                "expected_msg" => ["name" => "name must be unset or must not be empty if it's set"]
            ]
        ];

        $extra = [
            "method_name" => __METHOD__,
            "error_tag" => "unset",
            "field_path" => "name",
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_optional_unset_symbol()
    {
        $rule = [
            "name" => "O!|string"
        ];

        $cases = [
            "Valid_data" => [
                "data" => [
                    "name" => "David"
                ]
            ],
            "Valid_unset" => [
                "data" => []
            ],
            "Invalid_empty" => [
                "data" => [
                    "name" => ""
                ],
                "expected_msg" => ["name" => "name must be unset or must not be empty if it's set"]
            ]
        ];

        $extra = [
            "method_name" => __METHOD__,
            "error_tag" => "unset",
            "field_path" => "name",
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_if_rule()
    {
        $rule = [
            "id" => "required|<>[0,10]",
            "name_1" => "if(<(@id,5))|required|string|/^\d+.*/",
            "name_0" => "!if(<(@id,5))|required|string|/^\d+.*/",
        ];

        $cases = [
            "Valid_data_1_1" => [
                "data" => [
                    "id" => 1,
                    "name_1" => "123ABC",
                ]
            ],
            "Valid_data_1_2" => [
                "data" => [
                    "id" => 1,
                    "name_1" => "123ABC",
                    "name_0" => "abc",
                ]
            ],
            "Valid_data_0_1" => [
                "data" => [
                    "id" => 8,
                    "name_0" => "123ABC"
                ]
            ],
            "Valid_data_0_2" => [
                "data" => [
                    "id" => 8,
                    "name_1" => "abc",
                    "name_0" => "123ABC"
                ]
            ],
            "Invalid_data_1_1" => [
                "data" => [
                    "id" => 1,
                    "name_1" => ""
                ],
                "expected_msg" => ["name_1" => "name_1 can not be empty"]
            ],
            "Invalid_data_1_2" => [
                "data" => [
                    "id" => 1,
                    "name_1" => "abc"
                ],
                "expected_msg" => ["name_1" => "name_1 format is invalid, should be /^\d+.*/"]
            ],
            "Invalid_data_0_1" => [
                "data" => [
                    "id" => 8,
                    "name_0" => ""
                ],
                "expected_msg" => ["name_0" => "name_0 can not be empty"]
            ],
            "Invalid_data_0_2" => [
                "data" => [
                    "id" => 8,
                    "name_0" => "abc"
                ],
                "expected_msg" => ["name_0" => "name_0 format is invalid, should be /^\\d+.*/"]
            ],
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

    protected function test_required_when_rule()
    {
        $rule = [
            "id" => "required|<>[0,10]",
            "name_1" => "required:when(<(@id,5))|string|/^\d+.*/",
            "name_*_1" => "*:?(<(@id,5))|string|/^\d+.*/ >> {\"required:when\": \"@this can not be empty when id < 5\"}",
            "name_0" => "required:when_not(<(@id,5))|string|/^\d+.*/",
            "name_*_0" => "*:!?(<(@id,5))|string|/^\d+.*/ >> {\"required:when_not\": \"@this can not be empty when id is not less than 5\"}",
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "id" => 1,
                    "name_1" => "123ABC",
                    "name_*_1" => "123ABC",
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "id" => 1,
                    "name_1" => "123ABC",
                    "name_*_1" => "123ABC",
                    "name_0" => "",
                    "name_*_0" => "123ABC"
                ]
            ],
            "Valid_data_3" => [
                "data" => [
                    "id" => 8,
                    "name_0" => "123ABC",
                    "name_*_0" => "123ABC"
                ]
            ],
            "Valid_data_4" => [
                "data" => [
                    "id" => 8,
                    "name_1" => "123ABC",
                    "name_*_1" => "",
                    "name_0" => "123ABC",
                    "name_*_0" => "123ABC"
                ]
            ],
            "Invalid_data_1_1" => [
                "data" => [
                    "id" => 1,
                    "name_1" => ""
                ],
                "expected_msg" => ["name_1" => "Under certain circumstances, name_1 can not be empty"]
            ],
            "Invalid_data_1_2" => [
                "data" => [
                    "id" => 1,
                    "name_1" => "abc"
                ],
                "expected_msg" => ["name_1" => "name_1 format is invalid, should be /^\d+.*/"]
            ],
            "Invalid_data_1_3" => [
                "data" => [
                    "id" => 1,
                    "name_1" => "123ABC",
                    "name_*_1" => "",
                ],
                "expected_msg" => ["name_*_1" => "name_*_1 can not be empty when id < 5"]
            ],
            "Invalid_data_1_4" => [
                "data" => [
                    "id" => 1,
                    "name_1" => "123ABC",
                    "name_*_1" => "abc",
                ],
                "expected_msg" => ["name_*_1" => "name_*_1 format is invalid, should be /^\d+.*/"]
            ],
            "Invalid_data_1_5" => [
                "data" => [
                    "id" => 1,
                    "name_1" => "123ABC",
                    "name_*_1" => "123ABC",
                    "name_0" => "abc",
                ],
                "expected_msg" => ["name_0" => "name_0 format is invalid, should be /^\\d+.*/"]
            ],
            "Invalid_data_1_6" => [
                "data" => [
                    "id" => 1,
                    "name_1" => "123ABC",
                    "name_*_1" => "123ABC",
                    "name_0" => "",
                    "name_*_0" => "abc",
                ],
                "expected_msg" => ["name_*_0" => "name_*_0 format is invalid, should be /^\\d+.*/"]
            ],
            "Invalid_data_0_1" => [
                "data" => [
                    "id" => 8,
                    "name_0" => ""
                ],
                "expected_msg" => ["name_0" => "When certain circumstances are not met, name_0 can not be empty"]
            ],
            "Invalid_data_0_2" => [
                "data" => [
                    "id" => 8,
                    "name_0" => "abc"
                ],
                "expected_msg" => ["name_0" => "name_0 format is invalid, should be /^\\d+.*/"]
            ],
            "Invalid_data_0_3" => [
                "data" => [
                    "id" => 8,
                    "name_0" => "123ABC",
                    "name_*_0" => "",
                ],
                "expected_msg" => ["name_*_0" => "name_*_0 can not be empty when id is not less than 5"]
            ],
            "Invalid_data_0_4" => [
                "data" => [
                    "id" => 8,
                    "name_0" => "123ABC",
                    "name_*_0" => "abc",
                ],
                "expected_msg" => ["name_*_0" => "name_*_0 format is invalid, should be /^\\d+.*/"]
            ],
            "Invalid_data_0_5" => [
                "data" => [
                    "id" => 8,
                    "name_1" => "abc",
                    "name_0" => "123ABC",
                    "name_*_0" => "123ABC",
                ],
                "expected_msg" => ["name_1" => "name_1 format is invalid, should be /^\\d+.*/"]
            ],
            "Invalid_data_0_6" => [
                "data" => [
                    "id" => 8,
                    "name_1" => "",
                    "name_*_1" => "abc",
                    "name_0" => "123ABC",
                    "name_*_0" => "123ABC",
                ],
                "expected_msg" => ["name_*_1" => "name_*_1 format is invalid, should be /^\\d+.*/"]
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "field_path" => "name_1",
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_optional_when_rule()
    {
        $rule = [
            "id" => "required|<>[0,10]",
            "name_1" => "optional:when(>(@id,5))|string|/^\d+.*/",
            "name_*_1" => "O:?(>(@id,5))|string|/^\d+.*/ >> {\"optional:when\": \"@this can be empty when id > 5\"}",
            "name_0" => "optional:when_not(>(@id,5))|string|/^\d+.*/",
            "name_*_0" => "O:!?(>(@id,5))|string|/^\d+.*/ >> {\"optional:when_not\": \"@this can not be empty when id is not greater than 5\"}",
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "id" => 1,
                    "name_1" => "123ABC",
                    "name_*_1" => "123ABC",
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "id" => 1,
                    "name_1" => "123ABC",
                    "name_*_1" => "123ABC",
                    "name_0" => "",
                    "name_*_0" => "123ABC"
                ]
            ],
            "Valid_data_3" => [
                "data" => [
                    "id" => 8,
                    "name_0" => "123ABC",
                    "name_*_0" => "123ABC"
                ]
            ],
            "Valid_data_4" => [
                "data" => [
                    "id" => 8,
                    "name_1" => "123ABC",
                    "name_*_1" => "",
                    "name_0" => "123ABC",
                    "name_*_0" => "123ABC"
                ]
            ],
            "Invalid_data_1_1" => [
                "data" => [
                    "id" => 1,
                    "name_1" => ""
                ],
                "expected_msg" => ["name_1" => "name_1 can be empty only when certain circumstances are met"]
            ],
            "Invalid_data_1_2" => [
                "data" => [
                    "id" => 1,
                    "name_1" => "abc"
                ],
                "expected_msg" => ["name_1" => "name_1 format is invalid, should be /^\d+.*/"]
            ],
            "Invalid_data_1_3" => [
                "data" => [
                    "id" => 1,
                    "name_1" => "123ABC",
                    "name_*_1" => "",
                ],
                "expected_msg" => ["name_*_1" => "name_*_1 can be empty when id > 5"]
            ],
            "Invalid_data_1_4" => [
                "data" => [
                    "id" => 1,
                    "name_1" => "123ABC",
                    "name_*_1" => "abc",
                ],
                "expected_msg" => ["name_*_1" => "name_*_1 format is invalid, should be /^\d+.*/"]
            ],
            "Invalid_data_1_5" => [
                "data" => [
                    "id" => 1,
                    "name_1" => "123ABC",
                    "name_*_1" => "123ABC",
                    "name_0" => "abc",
                ],
                "expected_msg" => ["name_0" => "name_0 format is invalid, should be /^\\d+.*/"]
            ],
            "Invalid_data_1_6" => [
                "data" => [
                    "id" => 1,
                    "name_1" => "123ABC",
                    "name_*_1" => "123ABC",
                    "name_0" => "",
                    "name_*_0" => "abc",
                ],
                "expected_msg" => ["name_*_0" => "name_*_0 format is invalid, should be /^\\d+.*/"]
            ],
            "Invalid_data_0_1" => [
                "data" => [
                    "id" => 8,
                    "name_0" => ""
                ],
                "expected_msg" => ["name_0" => "name_0 can be empty only when certain circumstances are not met"]
            ],
            "Invalid_data_0_2" => [
                "data" => [
                    "id" => 8,
                    "name_0" => "abc"
                ],
                "expected_msg" => ["name_0" => "name_0 format is invalid, should be /^\\d+.*/"]
            ],
            "Invalid_data_0_3" => [
                "data" => [
                    "id" => 8,
                    "name_0" => "123ABC",
                    "name_*_0" => "",
                ],
                "expected_msg" => ["name_*_0" => "name_*_0 can not be empty when id is not greater than 5"]
            ],
            "Invalid_data_0_4" => [
                "data" => [
                    "id" => 8,
                    "name_0" => "123ABC",
                    "name_*_0" => "abc",
                ],
                "expected_msg" => ["name_*_0" => "name_*_0 format is invalid, should be /^\\d+.*/"]
            ],
            "Invalid_data_0_5" => [
                "data" => [
                    "id" => 8,
                    "name_1" => "abc",
                    "name_0" => "123ABC",
                    "name_*_0" => "123ABC",
                ],
                "expected_msg" => ["name_1" => "name_1 format is invalid, should be /^\\d+.*/"]
            ],
            "Invalid_data_0_6" => [
                "data" => [
                    "id" => 8,
                    "name_1" => "",
                    "name_*_1" => "abc",
                    "name_0" => "123ABC",
                    "name_*_0" => "123ABC",
                ],
                "expected_msg" => ["name_*_1" => "name_*_1 format is invalid, should be /^\\d+.*/"]
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "field_path" => "name_1",
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_optional_unset_when_rule()
    {
        $rule = [
            "id" => "required|<>[0,10]",
            "name_1" => "optional_unset:when(>(@id,5))|string|/^\d+.*/",
            "name_*_1" => "O!:?(>(@id,5))|string|/^\d+.*/ >> {\"optional_unset:when\": \"@this must be unset or must not be empty if it's set when id > 5. Otherwise it can not be empty\"}",
            "name_0" => "optional_unset:when_not(>(@id,5))|string|/^\d+.*/",
            "name_*_0" => "O!:!?(>(@id,5))|string|/^\d+.*/ >> {\"optional_unset:when_not\": \"@this must be unset or must not be empty if it's set when id is not greater than 5. Otherwise it can not be empty\"}",
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "id" => 1,
                    "name_1" => "123ABC",
                    "name_*_1" => "123ABC",
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "id" => 1,
                    "name_1" => "123ABC",
                    "name_*_1" => "123ABC",
                    "name_0" => null,
                    "name_*_0" => "123ABC"
                ]
            ],
            "Valid_data_3" => [
                "data" => [
                    "id" => 8,
                    "name_0" => "123ABC",
                    "name_*_0" => "123ABC"
                ]
            ],
            "Valid_data_4" => [
                "data" => [
                    "id" => 8,
                    "name_1" => "123ABC",
                    "name_*_1" => null,
                    "name_0" => "123ABC",
                    "name_*_0" => "123ABC"
                ]
            ],
            "Invalid_data_1_1" => [
                "data" => [
                    "id" => 1,
                ],
                "expected_msg" => ["name_1" => "Under certain circumstances, name_1 must be unset or must not be empty if it's set. Otherwise it can not be empty"]
            ],
            "Invalid_data_1_2" => [
                "data" => [
                    "id" => 1,
                    "name_1" => ""
                ],
                "expected_msg" => ["name_1" => "Under certain circumstances, name_1 must be unset or must not be empty if it's set. Otherwise it can not be empty"]
            ],
            "Invalid_data_1_3" => [
                "data" => [
                    "id" => 1,
                    "name_1" => "abc"
                ],
                "expected_msg" => ["name_1" => "name_1 format is invalid, should be /^\d+.*/"]
            ],
            "Invalid_data_1_4" => [
                "data" => [
                    "id" => 1,
                    "name_1" => "123ABC",
                ],
                "expected_msg" => ["name_*_1" => "name_*_1 must be unset or must not be empty if it's set when id > 5. Otherwise it can not be empty"]
            ],
            "Invalid_data_1_5" => [
                "data" => [
                    "id" => 1,
                    "name_1" => "123ABC",
                    "name_*_1" => "",
                ],
                "expected_msg" => ["name_*_1" => "name_*_1 must be unset or must not be empty if it's set when id > 5. Otherwise it can not be empty"]
            ],
            "Invalid_data_1_6" => [
                "data" => [
                    "id" => 1,
                    "name_1" => "123ABC",
                    "name_*_1" => "abc",
                ],
                "expected_msg" => ["name_*_1" => "name_*_1 format is invalid, should be /^\d+.*/"]
            ],
            "Invalid_data_1_7" => [
                "data" => [
                    "id" => 1,
                    "name_1" => "123ABC",
                    "name_*_1" => "123ABC",
                    "name_0" => "abc",
                ],
                "expected_msg" => ["name_0" => "name_0 format is invalid, should be /^\\d+.*/"]
            ],
            "Invalid_data_1_8" => [
                "data" => [
                    "id" => 1,
                    "name_1" => "123ABC",
                    "name_*_1" => "123ABC",
                    // "name_0" => "",
                    "name_*_0" => "abc",
                ],
                "expected_msg" => ["name_*_0" => "name_*_0 format is invalid, should be /^\\d+.*/"]
            ],
            "Invalid_data_0_1" => [
                "data" => [
                    "id" => 8,
                ],
                "expected_msg" => ["name_0" => "When certain circumstances are not met, name_0 must be unset or must not be empty if it's set. Otherwise it can not be empty"]
            ],
            "Invalid_data_0_2" => [
                "data" => [
                    "id" => 8,
                    "name_0" => ""
                ],
                "expected_msg" => ["name_0" => "When certain circumstances are not met, name_0 must be unset or must not be empty if it's set. Otherwise it can not be empty"]
            ],
            "Invalid_data_0_3" => [
                "data" => [
                    "id" => 8,
                    "name_0" => "abc"
                ],
                "expected_msg" => ["name_0" => "name_0 format is invalid, should be /^\\d+.*/"]
            ],
            "Invalid_data_0_4" => [
                "data" => [
                    "id" => 8,
                    "name_0" => "123ABC",
                ],
                "expected_msg" => ["name_*_0" => "name_*_0 must be unset or must not be empty if it's set when id is not greater than 5. Otherwise it can not be empty"]
            ],
            "Invalid_data_0_5" => [
                "data" => [
                    "id" => 8,
                    "name_0" => "123ABC",
                    "name_*_0" => "",
                ],
                "expected_msg" => ["name_*_0" => "name_*_0 must be unset or must not be empty if it's set when id is not greater than 5. Otherwise it can not be empty"]
            ],
            "Invalid_data_0_6" => [
                "data" => [
                    "id" => 8,
                    "name_0" => "123ABC",
                    "name_*_0" => "abc",
                ],
                "expected_msg" => ["name_*_0" => "name_*_0 format is invalid, should be /^\\d+.*/"]
            ],
            "Invalid_data_0_7" => [
                "data" => [
                    "id" => 8,
                    "name_1" => "abc",
                    "name_0" => "123ABC",
                    "name_*_0" => "123ABC",
                ],
                "expected_msg" => ["name_1" => "name_1 format is invalid, should be /^\\d+.*/"]
            ],
            "Invalid_data_0_8" => [
                "data" => [
                    "id" => 8,
                    "name_*_1" => "abc",
                    "name_0" => "123ABC",
                    "name_*_0" => "123ABC",
                ],
                "expected_msg" => ["name_*_1" => "name_*_1 format is invalid, should be /^\\d+.*/"]
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "field_path" => "name_1",
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_regular_expression_when_rule()
    {
        $rule = [
            "id" => "required|<>[0,10]",
            "name_1" => "/^\d+$/:when(<(@id,5))|len>[2]",
            "name_*_1" => "/^\d+$/:?(<(@id,5))|len>[2] >> {\"preg:when\": \"name_*_1 must be @preg when id < 5\"}",
            "name_0" => "/^\d+$/:when_not(<(@id,5))|len>[2]",
            "name_*_0" => "/^\d+$/:!?(<(@id,5))|len>[2] >> {\"preg:when_not\": \"@this must be @preg when id is not less than 5\"}",
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "id" => 1,
                    "name_1" => "123",
                    "name_*_1" => "123",
                    "name_0" => "123",
                    "name_*_0" => "123",
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "id" => 1,
                    "name_1" => "123",
                    "name_*_1" => "123",
                    "name_0" => "abc",
                    "name_*_0" => "abc",
                ]
            ],
            "Valid_data_3" => [
                "data" => [
                    "id" => 8,
                    "name_1" => "123",
                    "name_*_1" => "123",
                    "name_0" => "123",
                    "name_*_0" => "123",
                ]
            ],
            "Valid_data_4" => [
                "data" => [
                    "id" => 8,
                    "name_1" => "abc",
                    "name_*_1" => "abc",
                    "name_0" => "123",
                    "name_*_0" => "123",
                ]
            ],
            "Invalid_data_1_1" => [
                "data" => [
                    "id" => 1,
                    "name_1" => ""
                ],
                "expected_msg" => ["name_1" => "Under certain circumstances, name_1 format is invalid, should be /^\\d+$/"]
            ],
            "Invalid_data_1_2" => [
                "data" => [
                    "id" => 1,
                    "name_1" => "12"
                ],
                "expected_msg" => ["name_1" => "name_1 length must be greater than 2"]
            ],
            "Invalid_data_1_3" => [
                "data" => [
                    "id" => 1,
                    "name_1" => "123",
                    "name_*_1" => "abc",
                ],
                "expected_msg" => ["name_*_1" => "name_*_1 must be /^\\d+$/ when id < 5"]
            ],
            "Invalid_data_1_4" => [
                "data" => [
                    "id" => 1,
                    "name_1" => "123",
                    "name_*_1" => "12",
                ],
                "expected_msg" => ["name_*_1" => "name_*_1 length must be greater than 2"]
            ],
            "Invalid_data_0_1" => [
                "data" => [
                    "id" => 8,
                    "name_1" => "123",
                    "name_*_1" => "123",
                    "name_0" => ""
                ],
                "expected_msg" => ["name_0" => "When certain circumstances are not met, name_0 format is invalid, should be /^\\d+$/"]
            ],
            "Invalid_data_0_2" => [
                "data" => [
                    "id" => 8,
                    "name_1" => "123",
                    "name_*_1" => "123",
                    "name_0" => "abc"
                ],
                "expected_msg" => ["name_0" => "When certain circumstances are not met, name_0 format is invalid, should be /^\\d+$/"]
            ],
            "Invalid_data_0_3" => [
                "data" => [
                    "id" => 8,
                    "name_1" => "123",
                    "name_*_1" => "123",
                    "name_0" => "123",
                    "name_*_0" => "abc",
                ],
                "expected_msg" => ["name_*_0" => "name_*_0 must be /^\\d+$/ when id is not less than 5"]
            ],
            "Invalid_data_0_4" => [
                "data" => [
                    "id" => 8,
                    "name_1" => "123",
                    "name_*_1" => "123",
                    "name_0" => "123",
                    "name_*_0" => "12",
                ],
                "expected_msg" => ["name_*_0" => "name_*_0 length must be greater than 2"]
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "field_path" => "name_1",
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_when_rule()
    {
        $rule = [
            "id" => "required|<>[0,10]",
            "name_1" => "len<[5]:when(<(@id,5))|len>[2]",
            "name_*_1" => "len<[5]:?(<(@id,5))|len>[2] >> {\"len<:when\": \"name_*_1 length must be less than @p1 when id < 5\"}",
            "name_0" => "len<[5]:when_not(<(@id,5))|len>[2]",
            "name_*_0" => "len<[5]:!?(<(@id,5))|len>[2] >> {\"len<:when_not\": \"@this length must be less than @p1 when id is not less than 5\"}",
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "id" => 1,
                    "name_1" => "1234",
                    "name_*_1" => "1234",
                    "name_0" => "123",
                    "name_*_0" => "123",
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "id" => 1,
                    "name_1" => "1234",
                    "name_*_1" => "1234",
                    "name_0" => "123456",
                    "name_*_0" => "123456",
                ]
            ],
            "Valid_data_3" => [
                "data" => [
                    "id" => 8,
                    "name_1" => "123",
                    "name_*_1" => "123",
                    "name_0" => "1234",
                    "name_*_0" => "1234",
                ]
            ],
            "Valid_data_4" => [
                "data" => [
                    "id" => 8,
                    "name_1" => "123456",
                    "name_*_1" => "123456",
                    "name_0" => "1234",
                    "name_*_0" => "1234",
                ]
            ],
            "Invalid_data_1_1" => [
                "data" => [
                    "id" => 1,
                    "name_1" => ""
                ],
                "expected_msg" => ["name_1" => "name_1 length must be greater than 2"]
            ],
            "Invalid_data_1_2" => [
                "data" => [
                    "id" => 1,
                    "name_1" => "12345"
                ],
                "expected_msg" => ["name_1" => "Under certain circumstances, name_1 length must be less than 5"]
            ],
            "Invalid_data_1_3" => [
                "data" => [
                    "id" => 1,
                    "name_1" => "1234",
                    "name_*_1" => "",
                ],
                "expected_msg" => ["name_*_1" => "name_*_1 length must be greater than 2"]
            ],
            "Invalid_data_1_4" => [
                "data" => [
                    "id" => 1,
                    "name_1" => "123",
                    "name_*_1" => "12345",
                ],
                "expected_msg" => ["name_*_1" => "name_*_1 length must be less than 5 when id < 5"]
            ],
            "Invalid_data_0_1" => [
                "data" => [
                    "id" => 8,
                    "name_1" => "123",
                    "name_*_1" => "123",
                    "name_0" => ""
                ],
                "expected_msg" => ["name_0" => "name_0 length must be greater than 2"]
            ],
            "Invalid_data_0_2" => [
                "data" => [
                    "id" => 8,
                    "name_1" => "123",
                    "name_*_1" => "123",
                    "name_0" => "12345"
                ],
                "expected_msg" => ["name_0" => "When certain circumstances are not met, name_0 length must be less than 5"]
            ],
            "Invalid_data_0_3" => [
                "data" => [
                    "id" => 8,
                    "name_1" => "123",
                    "name_*_1" => "123",
                    "name_0" => "123",
                    "name_*_0" => "",
                ],
                "expected_msg" => ["name_*_0" => "name_*_0 length must be greater than 2"]
            ],
            "Invalid_data_0_4" => [
                "data" => [
                    "id" => 8,
                    "name_1" => "123",
                    "name_*_1" => "123",
                    "name_0" => "123",
                    "name_*_0" => "12345",
                ],
                "expected_msg" => ["name_*_0" => "name_*_0 length must be less than 5 when id is not less than 5"]
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "field_path" => "name_1",
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_or_rule()
    {
        $rule = [
            "name[or]" => [
                "required|bool",
                "required|bool_str",
            ],
            "height" => [
                "[or]" => [
                    "required|int|>[100]",
                    "required|string",
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
                "expected_msg" => ["name" => "name can not be empty"]
            ],
            "Invalid_0" => [
                "data" => [
                    "name" => 0
                ],
                "expected_msg" => ["name" => "name must be boolean or name must be boolean string"]
            ],
            "Invalid_1" => [
                "data" => [
                    "name" => "false",
                    "height" => 50,
                ],
                "expected_msg" => ["height" => "height must be greater than 100 or height must be string"]
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

    protected function test_or_rule_symbol()
    {
        $rule = [
            "name[||]" => [
                "required|bool",
                "required|bool_str",
            ],
            "height" => [
                "[||]" => [
                    "required|int|>[100]",
                    "required|string",
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
                "expected_msg" => ["name" => "name can not be empty"]
            ],
            "Invalid_0" => [
                "data" => [
                    "name" => 0
                ],
                "expected_msg" => ["name" => "name must be boolean or name must be boolean string"]
            ],
            "Invalid_1" => [
                "data" => [
                    "name" => "false",
                    "height" => 50,
                ],
                "expected_msg" => ["height" => "height must be greater than 100 or height must be string"]
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

    protected function test_assoc_array()
    {
        $rule = [
            "person" => [
                "name" => "required|string|/^\d+.*/"
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
                "expected_msg" => ["person.name" => "person.name can not be empty"]
            ],
            "Invalid_not_string" => [
                "data" => [
                    "person" => [
                        "name" => 123
                    ]
                ],
                "expected_msg" => ["person.name" => "person.name must be string"]
            ],
            "Invalid_not_start_num" => [
                "data" => [
                    "person" => [
                        "name" => "abcABC"
                    ]
                ],
                "expected_msg" => ["person.name" => "person.name format is invalid, should be /^\d+.*/"]
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

    protected function test_index_array()
    {
        $rule = [
            "person.*" => [
                "name" => "required|string|/^\d+.*/",
                "relation" => [
                    "father" => "required|string",
                    "mother" => "optional|string",
                ]
            ],
            "pet" => [
                "required|string",
                "required|string",
                [
                    "required|string",
                    "required|string",
                ]
            ],
            "flower.*" => "required|string",
            "clothes[optional]" => [
                [
                    "required|string",
                ]
            ],
            "shoes" => [
                "[optional].*" => "required|string"
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
                "expected_msg" => ["person.0.name" => "person.0.name can not be empty"]
            ],
            "Invalid_item_0-1(assoc_arr)" => [
                "data" => [
                    "person" => [
                        ["name" => "123", "relation" => ["father" => "f123", "mother" => "m123"]],
                        ["name" => "abcABC"],
                    ]
                ],
                "expected_msg" => ["person.1.name" => "person.1.name format is invalid, should be /^\d+.*/"]
            ],
            "Invalid_item_0-2(assoc_arr)" => [
                "data" => [
                    "person" => [
                        ["name" => "123", "relation" => ["father" => "f123", "mother" => "m123"]],
                        ["name" => "123ABC", "relation" => ["father" => "", "mother" => "m123ABC"]],
                    ]
                ],
                "expected_msg" => ["person.1.relation.father" => "person.1.relation.father can not be empty"]
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
                "expected_msg" => ["pet.0" => "pet.0 can not be empty"]
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
                "expected_msg" => ["pet.1" => "pet.1 must be string"]
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
                "expected_msg" => ["pet.2.1" => "pet.2.1 can not be empty"]
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
                "expected_msg" => ["flower.0" => "flower.0 can not be empty"]
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
                "expected_msg" => ["flower.2" => "flower.2 must be string"]
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
                "expected_msg" => ["clothes.0.0" => "clothes.0.0 can not be empty"]
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
                "expected_msg" => ["shoes.1" => "shoes.1 can not be empty"]
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
                "expected_msg" => ["shoes.0" => "shoes.0 must be string"]
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

    protected function test_index_assoc_array()
    {
        $rule = [
            // "person.*" => [
            //     "name" => "required|string",
            //     "relation" => [
            //         "father" => "required|string",
            //         "mother" => "optional|string",
            //         "brother" => [
            //             "[optional].*" => [
            //                 "*" => [
            //                     "name" => "required|string",
            //                     "level" => [
            //                         "[or]" => [
            //                             "required|int",
            //                             "required|string",
            //                         ]
            //                     ]
            //                 ]
            //             ]
            //         ]
            //     ],
            //     "fruit" => [
            //         "*" => [
            //             "*" => [
            //                 "name" => "required|string",
            //                 "color" => "optional|string",
            //             ]

            //         ]
            //     ],
            // ],
            "person" => [
                "*" => [
                    "name" => "required|string",
                    "relation" => [
                        "father" => "required|string",
                        "mother" => "optional|string",
                        "brother" => [
                            "[optional].*" => [
                                "*" => [
                                    "name" => "required|string",
                                    "level" => [
                                        "[or]" => [
                                            "required|int",
                                            "required|string",
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    "fruit" => [
                        "*" => [
                            "*" => [
                                "name" => "required|string",
                                "color" => "optional|string",
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
                "expected_msg" => ["person.0.fruit.0.0.name" => "person.0.fruit.0.0.name can not be empty"]
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
                "expected_msg" => ["person.0.fruit.1.1.name" => "person.0.fruit.1.1.name can not be empty"]
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
                "expected_msg" => ["person.1.fruit.0.1.name" => "person.1.fruit.0.1.name can not be empty"]
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
                "expected_msg" => ["person.0.relation.brother.0.0.level" => "person.0.relation.brother.0.0.level must be integer or person.0.relation.brother.0.0.level must be string"]
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
                "expected_msg" => ["person.0.relation.brother.1.1.level" => "person.0.relation.brother.1.1.level must be integer or person.0.relation.brother.1.1.level must be string"]
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

    protected function test_root_data_rule_1()
    {
        $rule = "required|string";

        $cases = [
            "Valid_data" => [
                "data" => "Hello World!",
            ],
            "Invalid_empty" => [
                "data" => "",
                "expected_msg" => ["data" => "data can not be empty"]
            ],
            "Invalid_0" => [
                "data" => 0,
                "expected_msg" => ["data" => "data must be string"]
            ],
            "Invalid_1" => [
                "data" => ["name" => "false"],
                "expected_msg" => ["data" => "data must be string"]
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

    protected function test_root_data_rule_2()
    {
        $rule = [
            "[or]" => [
                "required|string",
                "required|int"
            ]
        ];

        $cases = [
            "Valid_data" => [
                "data" => "Hello World!",
            ],
            "Invalid_empty" => [
                "data" => "",
                "expected_msg" => ["data" => "data can not be empty"]
            ],
            "Invalid_0" => [
                "data" => false,
                "expected_msg" => ["data" => "data must be string or data must be integer"]
            ],
            "Invalid_1" => [
                "data" => ["name" => "false"],
                "expected_msg" => ["data" => "data must be string or data must be integer"]
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

    protected function test_root_data_rule_3()
    {
        $rule = [
            "[optional]" => [
                "required|string",
                "required|int"
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
                "expected_msg" => ["data.0" => "data.0 can not be empty"]
            ],
            "Invalid_1" => [
                "data" => ["Hello World!", false],
                "expected_msg" => ["data.1" => "data.1 must be integer"]
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

    protected function test_root_data_rule_4()
    {
        $rule = [
            "*" => "required|string"
        ];

        $cases = [
            "Valid_data" => [
                "data" => ["Hello World!", "1"],
            ],
            "Invalid_data_empty" => [
                "data" => "",
                "expected_msg" => ["data" => "data must be a numeric array"]
            ],
            "Invalid_0" => [
                "data" => ["", 1],
                "expected_msg" => ["data.0" => "data.0 can not be empty"]
            ],
            "Invalid_1" => [
                "data" => ["Hello World!", false],
                "expected_msg" => ["data.1" => "data.1 must be string"]
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

    protected function test_dynamic_err_msg_general()
    {
        $rule = [
            "id" => "required|<=>=[1,100] >> Users define - @this should not be >= @p1 and <= @p2",
            "name" => "required|string >> Users define - @this should not be empty and must be string",
        ];

        $cases = [
            "Invalid_empty" => [
                "data" => [
                    "id" => 101
                ],
                "expected_msg" => ["id" => "Users define - id should not be >= 1 and <= 100"]
            ],
            "Invalid_unset" => [
                "data" => [
                    "id" => 1,
                ],
                "expected_msg" => ["name" => "Users define - name should not be empty and must be string"]
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

    protected function test_method_and_parameter()
    {
        $rule = [
            "id" => "required|string|check_id[1,100]",
            "name" => "required|string|check_name[@id]",
            "favourite_fruit" => [
                "fruit_id" => "optional|check_fruit_id(@root)",
                "fruit_name" => "optional|check_fruit_name(@parent)",
                "fruit_color" => "optional|check_fruit_color[@fruit_name,@this] >> fruit name(@p0) and color(@p1) is not matched",
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
                "expected_msg" => ["id" => "id validation failed"]
            ],
            "Invalid_add_1" => [
                "data" => [
                    "id" => "1",
                    "name" => "Tom"
                ],
                "expected_msg" => ["name" => "name validation failed"]
            ],
            "Invalid_add_2" => [
                "data" => [
                    "id" => "1",
                    "name" => "Admin",
                    "favourite_fruit" => [
                        "fruit_id" => "51",
                    ]
                ],
                "expected_msg" => ["favourite_fruit.fruit_id" => "favourite_fruit.fruit_id validation failed"]
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
                "expected_msg" => ["favourite_fruit.fruit_name" => "favourite_fruit.fruit_name validation failed"]
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
                "expected_msg" => ["favourite_fruit.fruit_color" => "fruit name(apple) and color(yellow) is not matched"]
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        $this->validation->add_method("check_name", function ($name, $id) {
            if ($id == 1) {
                if ($name != "Admin") {
                    return false;
                }
            }

            return true;
        });

        $this->validation->add_method("check_fruit_id", function ($data) {
            if ($data["id"] < 50) {
                if ($data["favourite_fruit"]["fruit_id"] >= 50) {
                    return false;
                }
            } else {
                if ($data["favourite_fruit"]["fruit_id"] < 50) {
                    return false;
                }
            }

            return true;
        });

        $this->validation->add_method("check_fruit_name", function ($favourite_fruit) {
            if ($favourite_fruit['fruit_id'] == 1) {
                if ($favourite_fruit['fruit_name'] != "Admin Fruit") {
                    return false;
                }
            }

            return true;
        });

        $this->validation->add_method("check_fruit_color", function ($fruit_name, $fruit_color) {
            $fruit_color_arr = [
                "Admin Fruit" => "",
                "apple" => "red",
                "banana" => "yellow",
                "watermelon" => "green",
            ];

            if (isset($fruit_color_arr[$fruit_name]) && $fruit_color_arr[$fruit_name] == $fruit_color) {
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

    protected function test_strict_parameter()
    {
        $rule = [
            "id" => "optional|(n)[1,\"10\",'100']",
            "color" => "optional|(s)[red,\"white\",'black']"
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "id" => 1,
                    "name" => "red"
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "id" => "1",
                    "name" => "red"
                ]
            ],
            "Valid_data_3" => [
                "data" => [
                    "id" => 10,
                    "name" => "white"
                ]
            ],
            "Valid_data_4" => [
                "data" => [
                    "id" => "10",
                    "name" => "white"
                ]
            ],
            "Valid_data_5" => [
                "data" => [
                    "id" => 100,
                    "name" => "black"
                ]
            ],
            "Valid_data_3" => [
                "data" => [
                    "id" => "100",
                    "name" => "black"
                ]
            ],
            "Invalid_1" => [
                "data" => [
                    "id" => "4",
                ],
                "expected_msg" => ["id" => "id must be numeric and in 1,10,100"]
            ],
            "Invalid_2" => [
                "data" => [
                    "color" => "green",
                ],
                "expected_msg" => ["color" => "color must be string and in red,white,black"]
            ]
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

    protected function test_exception()
    {
        $rule = [
            "id" => "optional|php_warning_id",
            "name" => "optional|php_exception_name",
            "height" => [
                "[or]" => [
                    "optional|int|>[100]",
                    "optional|string",
                ]
            ],
            "favourite_fruit[optional]" => [
                "fruit_id" => "optional|php_exception_fruit_id(@root)",
            ],
            "favourite_fruits" => [
                "[optional]*" => [
                    "fruit_id" => "optional|php_exception_fruit_id(@root)",
                ]
            ]
        ];

        $cases = [
            "Exception_add_1" => [
                "data" => [
                    "name" => "Devin",
                ],
                "expected_msg" => '@field:name, @method:php_exception_name - Undefined constant "githusband\\Test\\UNDEFINED_VAR"'
            ],
            "Exception_add_2" => [
                "data" => [
                    "favourite_fruit" => [
                        "fruit_id" => "f_1",
                    ]
                ],
                "expected_msg" => '@field:favourite_fruit.fruit_id, @method:php_exception_fruit_id - fruit id is not valid'
            ],
            "Exception_add_3" => [
                "data" => [
                    "favourite_fruits" => [
                        [
                            "fruit_id" => "",
                        ],
                        [
                            "fruit_id" => "f_2",
                        ],
                    ]
                ],
                "expected_msg" => '@field:favourite_fruits.1.fruit_id, @method:php_exception_fruit_id - fruit id is not valid'
            ],
            "Invalid_add_1" => [
                "data" => [
                    "id" => "1",
                ],
                "expected_msg" => ["id" => "id validation failed"]
            ],
        ];

        if (version_compare(PHP_VERSION, '8', '<')) {
            unset($cases['Exception_add_1']);
            $cases['Invalid_add_2'] = [
                "data" => [
                    "name" => "Devin",
                ],
                "expected_msg" => ["name" => "UNDEFINED_VAR"]
            ];
        }

        $extra = [
            "method_name" => __METHOD__,
        ];

        if (version_compare(PHP_VERSION, '8', '>=')) {
            $this->write_log(static::LOG_LEVEL_WARN, "Don't worry about it if you get Warning: Undefined variable \$fake_id\". It's a test case which can be ignored.\n");
        }
        $this->validation->add_method("php_warning_id", function ($id) {
            if (false) $fake_id = 0;
            return $fake_id > 1;
        });

        if (version_compare(PHP_VERSION, '8', '<') && version_compare(PHP_VERSION, '7', '>')) {
            $this->write_log(static::LOG_LEVEL_WARN, "Don't worry about it if you get Warning: Use of undefined constant UNDEFINED_VAR. It's a test case which can be ignored.\n");
        }
        $this->validation->add_method("php_exception_name", function ($name) {
            if (false) define('UNDEFINED_VAR', 1);
            return UNDEFINED_VAR;
        });

        $this->validation->add_method("php_exception_fruit_id", function ($fruit_id) {
            throw new \Exception("fruit id is not valid");
        });

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_validation_global_execute($error_data = false)
    {
        $rule = [
            "id" => "required|int",
            "name" => "required|string",
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

        if ($error_data !== false) return $this->get_method_info($rule, $cases, $extra, $error_data);

        $this->validation->set_validation_global(true);
        $result = $this->validate_cases($rule, $cases, $extra);
        $this->validation->set_validation_global(false);

        return $result;
    }

    protected function test_error_msg_format_execute($error_data = false)
    {
        $rule = [
            "id" => "required|int",
            "name" => "required|string",
            "favourite_fruit" => [
                "fruit_id" => "required|int",
                "fruit_name" => "required|string",
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
                "error_msg_format" => [
                    "format" => Validation::ERROR_FORMAT_DOTTED_GENERAL,
                    // "nested" => false,
                    // "general" => true,
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
                        "error_type" => "required",
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
                "error_msg_format" => [
                    "format" => Validation::ERROR_FORMAT_DOTTED_DETAILED,
                    // "nested" => false,
                    // "general" => false,
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
                "error_msg_format" => [
                    "format" => Validation::ERROR_FORMAT_NESTED_GENERAL,
                    // "nested" => true,
                    // "general" => true,
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
                        "error_type" => "required",
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
                "error_msg_format" => [
                    "format" => Validation::ERROR_FORMAT_NESTED_DETAILED,
                    // "nested" => true,
                    // "general" => false,
                ]
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        if ($error_data !== false) return $this->get_method_info($rule, $cases, $extra, $error_data);

        $this->validation->set_validation_global(true);
        $result = $this->validate_cases($rule, $cases, $extra);
        $this->validation->set_validation_global(false);

        return $result;
    }

    protected function test_dynamic_err_msg_complex()
    {
        $rule = [
            "id" => "required|check_err_field",
            "number" => "required|check_err_field >> @this error!",
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
                    "id" => [
                        "error_type" => "validation",
                        "message" => "id validation failed",
                    ]
                ],
                "error_msg_format" => [
                    "format" => Validation::ERROR_FORMAT_NESTED_DETAILED,
                    // "nested" => true,
                    // "general" => false,
                ]
            ],
            "Invalid_data_2" => [
                "data" => [
                    "id" => 11
                ],
                "expected_msg" => [
                    "id" => [
                        "error_type" => "validation",
                        "message" => "id: check_err_field error. [10, 20]",
                    ]
                ],
                "error_msg_format" => [
                    "format" => Validation::ERROR_FORMAT_NESTED_DETAILED,
                    // "nested" => true,
                    // "general" => false,
                ]
            ],
            "Invalid_data_3" => [
                "data" => [
                    "id" => 21
                ],
                "expected_msg" => [
                    "id" => [
                        "error_type" => "3",
                        "message" => "id: check_err_field error. [20, 30]",
                    ]
                ],
                "error_msg_format" => [
                    "format" => Validation::ERROR_FORMAT_NESTED_DETAILED,
                    // "nested" => true,
                    // "general" => false,
                ]
            ],
            "Invalid_data_4" => [
                "data" => [
                    "id" => 31
                ],
                "expected_msg" => [
                    "id" => [
                        "error_type" => "4",
                        "message" => "id: check_err_field error. [30, 40]",
                        "extra" => "It should be greater than 40"
                    ]
                ],
                "error_msg_format" => [
                    "format" => Validation::ERROR_FORMAT_NESTED_DETAILED,
                    // "nested" => true,
                    // "general" => false,
                ]
            ],
            "Invalid_data_5" => [
                "data" => [
                    "id" => 41,
                    "number" => 11
                ],
                "expected_msg" => [
                    "number" => [
                        "error_type" => "validation",
                        "message" => "number error!",
                    ]
                ],
                "error_msg_format" => [
                    "format" => Validation::ERROR_FORMAT_NESTED_DETAILED,
                    // "nested" => true,
                    // "general" => false,
                ]
            ],
            "Invalid_data_6" => [
                "data" => [
                    "id" => 41,
                    "number" => 31
                ],
                "expected_msg" => [
                    "number" => [
                        "error_type" => "4",
                        "message" => "number error!",
                        "extra" => "It should be greater than 40"
                    ]
                ],
                "error_msg_format" => [
                    "format" => Validation::ERROR_FORMAT_NESTED_DETAILED,
                    // "nested" => true,
                    // "general" => false,
                ]
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
            "field_path" => "name",
        ];

        $this->validation->add_method("check_err_field", function ($data) {
            if ($data < 10) {
                return false;
            } else if ($data < 20) {
                return "id: check_err_field error. [10, 20]";
            } else if ($data < 30) {
                return [
                    "error_type" => "3",
                    "message" => "@this: check_err_field error. [20, 30]",
                ];
            } else if ($data <= 40) {
                return [
                    "error_type" => "4",
                    "message" => "@this: check_err_field error. [30, 40]",
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

    protected function test_dynamic_err_msg_user_json()
    {
        $rule = [
            "id" => 'required|/^\d+$/|<=>=[1,100]| >> { "required": "Users define - @this is required", "preg": "Users define - @this should be \"MATCHED\" @preg"}',
            "name" => 'optional_unset|string >> { "optional_unset": "Users define - @this should be unset or not be empty", "string": "Users define - Note! @this should be string"}',
            "age" => 'optional|<=>=[1,60]|check_err_field >> { "<=>=": "Users define - @this is not allowed.", "check_err_field": "Users define - @this is not passed."}',
        ];

        $cases = [
            "Invalid_id_empty" => [
                "data" => [
                    "name" => "devin"
                ],
                "expected_msg" => ["id" => "Users define - id is required"]
            ],
            "Invalid_id_preg" => [
                "data" => [
                    "id" => "devin"
                ],
                "expected_msg" => ["id" => "Users define - id should be \"MATCHED\" /^\d+$/"]
            ],
            "Invalid_id_not_in" => [
                "data" => [
                    "id" => 101
                ],
                "expected_msg" => ["id" => "id must be greater than or equal to 1 and less than or equal to 100"]
            ],
            "Invalid_name_unset" => [
                "data" => [
                    "id" => 1,
                    "name" => ''
                ],
                "expected_msg" => ["name" => "Users define - name should be unset or not be empty"]
            ],
            "Invalid_name_unset_1" => [
                "data" => [
                    "id" => 1,
                    "name" => 123
                ],
                "expected_msg" => ["name" => "Users define - Note! name should be string"]
            ],
            "Invalid_age" => [
                "data" => [
                    "id" => 1,
                    "name" => "devin",
                    "age" => 61,
                ],
                "expected_msg" => ["age" => "Users define - age is not allowed."]
            ],
            "Invalid_age_1" => [
                "data" => [
                    "id" => 1,
                    "name" => "devin",
                    "age" => 11,
                ],
                "expected_msg" => ["age" => "Users define - age is not passed."]
            ]
        ];

        $extra = [
            "method_name" => __METHOD__,
            "field_path" => "name",
        ];

        $this->validation->add_method("check_err_field", function ($data) {
            if ($data < 10) {
                return false;
            } else if ($data < 20) {
                return "id: check_err_field error. [10, 20]";
            } else if ($data < 30) {
                return [
                    "error_type" => "3",
                    "message" => "@this: check_err_field error. [20, 30]",
                ];
            } else if ($data <= 40) {
                return [
                    "error_type" => "4",
                    "message" => "@this: check_err_field error. [30, 40]",
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

    protected function test_dynamic_err_msg_user_gh_string()
    {
        $rule = [
            "id" => "required|/^\d+$/|<=>=[1,100]| >> [required]=> Users define - @this is required [preg]=> Users define - @this should be \"MATCHED\" @preg",
            "name" => "optional_unset|string >> [optional_unset] => Users define - @this should be unset or not be empty [string]=> Users define - Note! @this should be string",
            "age" => "optional|<=>=[1,60]|check_err_field >> [<=>=]=> Users define - @this is not allowed. [check_err_field]=> Users define - @this is not passed.",
        ];

        $cases = [
            "Invalid_id_empty" => [
                "data" => [
                    "name" => "devin"
                ],
                "expected_msg" => ["id" => "Users define - id is required"]
            ],
            "Invalid_id_preg" => [
                "data" => [
                    "id" => "devin"
                ],
                "expected_msg" => ["id" => "Users define - id should be \"MATCHED\" /^\d+$/"]
            ],
            "Invalid_id_not_in" => [
                "data" => [
                    "id" => 101
                ],
                "expected_msg" => ["id" => "id must be greater than or equal to 1 and less than or equal to 100"]
            ],
            "Invalid_name_unset" => [
                "data" => [
                    "id" => 1,
                    "name" => ''
                ],
                "expected_msg" => ["name" => "Users define - name should be unset or not be empty"]
            ],
            "Invalid_name_unset_1" => [
                "data" => [
                    "id" => 1,
                    "name" => 123
                ],
                "expected_msg" => ["name" => "Users define - Note! name should be string"]
            ],
            "Invalid_age" => [
                "data" => [
                    "id" => 1,
                    "name" => "devin",
                    "age" => 61,
                ],
                "expected_msg" => ["age" => "Users define - age is not allowed."]
            ],
            "Invalid_age_1" => [
                "data" => [
                    "id" => 1,
                    "name" => "devin",
                    "age" => 11,
                ],
                "expected_msg" => ["age" => "Users define - age is not passed."]
            ]
        ];

        $extra = [
            "method_name" => __METHOD__,
            "field_path" => "name",
        ];

        $this->validation->add_method("check_err_field", function ($data) {
            if ($data < 10) {
                return false;
            } else if ($data < 20) {
                return "id: check_err_field error. [10, 20]";
            } else if ($data < 30) {
                return [
                    "error_type" => "3",
                    "message" => "@this: check_err_field error. [20, 30]",
                ];
            } else if ($data <= 40) {
                return [
                    "error_type" => "4",
                    "message" => "@this: check_err_field error. [30, 40]",
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

    protected function test_dynamic_err_msg_user_object()
    {
        $rule = [
            "id" => [
                "required|/^\d+$/|<=>=[1,100]",
                "error_message" => [
                    "required" => "Users define - @this is required",
                    "preg" => "Users define - @this should be \"MATCHED\" @preg"
                ]
            ],
            "name" => [
                "optional_unset|string",
                "error_message" => [
                    "optional_unset" => "Users define - @this should be unset or not be empty",
                    "string" => "Users define - Note! @this should be string"
                ]
            ],
            "age" => "optional|<=>=[1,60]|check_err_field >> [<=>=]=> Users define - @this is not allowed. [check_err_field]=> Users define - @this is not passed.",
            "age" => [
                "optional|<=>=[1,60]|check_err_field",
                "error_message" => [
                    "<=>=" => "Users define - @this is not allowed.",
                    "check_err_field" => "Users define - @this is not passed."
                ]
            ],
        ];

        $cases = [
            "Invalid_id_empty" => [
                "data" => [
                    "name" => "devin"
                ],
                "expected_msg" => ["id" => "Users define - id is required"]
            ],
            "Invalid_id_preg" => [
                "data" => [
                    "id" => "devin"
                ],
                "expected_msg" => ["id" => "Users define - id should be \"MATCHED\" /^\d+$/"]
            ],
            "Invalid_id_not_in" => [
                "data" => [
                    "id" => 101
                ],
                "expected_msg" => ["id" => "id must be greater than or equal to 1 and less than or equal to 100"]
            ],
            "Invalid_name_unset" => [
                "data" => [
                    "id" => 1,
                    "name" => ''
                ],
                "expected_msg" => ["name" => "Users define - name should be unset or not be empty"]
            ],
            "Invalid_name_unset_1" => [
                "data" => [
                    "id" => 1,
                    "name" => 123
                ],
                "expected_msg" => ["name" => "Users define - Note! name should be string"]
            ],
            "Invalid_age" => [
                "data" => [
                    "id" => 1,
                    "name" => "devin",
                    "age" => 61,
                ],
                "expected_msg" => ["age" => "Users define - age is not allowed."]
            ],
            "Invalid_age_1" => [
                "data" => [
                    "id" => 1,
                    "name" => "devin",
                    "age" => 11,
                ],
                "expected_msg" => ["age" => "Users define - age is not passed."]
            ]
        ];

        $extra = [
            "method_name" => __METHOD__,
            "field_path" => "name",
        ];

        $this->validation->add_method("check_err_field", function ($data) {
            if ($data < 10) {
                return false;
            } else if ($data < 20) {
                return "id: check_err_field error. [10, 20]";
            } else if ($data < 30) {
                return [
                    "error_type" => "3",
                    "message" => "@this: check_err_field error. [20, 30]",
                ];
            } else if ($data <= 40) {
                return [
                    "error_type" => "4",
                    "message" => "@this: check_err_field error. [30, 40]",
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

    protected function test_set_language_execute($error_data = false)
    {
        $rule = [
            "name" => "required|string"
        ];

        $cases = [
            "Invalid_empty" => [
                "data" => [
                    "name" => ""
                ],
                "expected_msg" => ["name" => "name 不能为空"]
            ],
            "Invalid_unset" => [
                "data" => [],
                "expected_msg" => ["name" => "name 不能为空"]
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

        if ($error_data !== false) return $this->get_method_info($rule, $cases, $extra, $error_data);

        $this->validation->set_language("zh-cn");
        $result = $this->validate_cases($rule, $cases, $extra);
        $this->validation->set_language("en-us");

        return $result;
    }

    protected function test_custom_language_file_execute($error_data = false)
    {
        $rule = [
            "id" => "check_custom[100]"
        ];

        $cases = [
            "Invalid_empty" => [
                "data" => [
                    "id" => ""
                ],
                "expected_msg" => ["id" => "id error!(CustomLang File)"]
            ],
            "Invalid_extra" => [
                "data" => [
                    "id" => 1,
                ],
                "expected_msg" => ["id" => "id error!(CustomLang File)"]
            ]
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        if ($error_data !== false) return $this->get_method_info($rule, $cases, $extra, $error_data);

        $this->validation->add_method("check_custom", function ($custom, $num) {
            return $custom > $num;
        });
        // You should add CustomLang.php in __DIR__.'/Language/'
        $this->validation->set_config(['lang_path' => __DIR__ . '/Language/'])->set_language('CustomLang');
        $result = $this->validate_cases($rule, $cases, $extra);

        return $result;
    }

    protected function test_custom_language_object_execute($error_data = false)
    {
        $rule = [
            "id" => "check_id[1,100]"
        ];

        $cases = [
            "Invalid_empty" => [
                "data" => [
                    "id" => ""
                ],
                "expected_msg" => ["id" => "id error!(customed)"]
            ],
            "Invalid_extra" => [
                "data" => [
                    "id" => 1000,
                ],
                "expected_msg" => ["id" => "id error!(customed)"]
            ]
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        if ($error_data !== false) return $this->get_method_info($rule, $cases, $extra, $error_data);

        $lang_config = (object)[];
        $lang_config->error_templates = [
            'check_id' => '@this error!(customed)'
        ];
        $this->validation->custom_language($lang_config);
        $result = $this->validate_cases($rule, $cases, $extra);

        return $result;
    }

    protected function test_default_config_execute($error_data = false)
    {
        $rule = [
            "id" => "required|int|/^\d+$/",
            "name" => "required|string|len<=>[8,32]",
            "gender" => "required|(s)[male,female]",
            "dob" => "required|dob",
            "age" => "required|check_age[@gender,30] >> @this is wrong",
            "height_unit" => "required|(s)[cm,m]",
            "height[or]" => [
                "required|=(@height_unit,cm)|<=>=[100,200] >> @this should be in [100,200] when height_unit is cm",
                "required|=(@height_unit,m)|<=>=[1,2] >> @this should be in [1,2] when height_unit is m",
            ],
            "education" => [
                "primary_school" => "required|=[Qiankeng Xiaoxue]",
                "junior_middle_school" => "required|!=[Foshan Zhongxue]",
                "high_school" => "if(=(@junior_middle_school,Mianhu Zhongxue))|required|len>[10]",
                "university" => "!if(=(@junior_middle_school,Qiankeng Zhongxue))|required|len>[10]",
            ],
            "company" => [
                "name" => "required|len<=>[8,64]",
                "country" => "optional|len>=[3]",
                "addr" => "required|len>[16]",
                "colleagues.*" => [
                    "name" => "required|string|len<=>[3,32]",
                    "position" => "required|(s)[Reception,Financial,PHP,JAVA]"
                ],
                "boss" => [
                    "required|=[Mike]",
                    "required|(s)[Johnny,David]",
                    "optional|(s)[Johnny,David]"
                ]
            ],
            "favourite_food[optional].*" => [
                "name" => "required|string",
                "place_name" => "optional|string"
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
                        // "high_school" => "education.high_school length must be greater than 10",
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
                "error_msg_format" => [
                    "format" => Validation::ERROR_FORMAT_NESTED_GENERAL,
                    "nested" => true,
                    "general" => true,
                ]

            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        if ($error_data !== false) return $this->get_method_info($rule, $cases, $extra, $error_data);

        $this->validation->set_validation_global(true);
        $result = $this->validate_cases($rule, $cases, $extra);
        $this->validation->set_validation_global(false);

        return $result;
    }

    // Don't test this function temporarily
    protected function test_user_config_execute($error_data = false)
    {
        $validation_conf = [
            'language' => 'en-us',                                      // Language, default is en-us
            // 'lang_path' => '',                                          // Customer Language file path
            'validation_global' => true,                                // If true, validate all rules; If false, stop validating when one rule was invalid
            // 'auto_field' => "data",                                     // If root data is string or numberic array, add the auto_field to the root data, can validate these kind of data type.
            'reg_msg' => '/ >>>(.*)$/',                                 // Set special error msg by user 
            'reg_preg' => '/^Reg:(\/.+\/.*)$/',                         // If match this, using regular expression instead of method
            // 'reg_preg_strict' => '/^(\/.+\/[imsxADSUXJun]*)$/',         // Verify if the regular expression is valid
            'reg_ifs' => '/^IF[yn]?\?(.*)$/',                           // A regular expression to match both reg_if and reg_if_not
            'reg_if' => '/^IFy?\?/',                                    // If match reg_if, validate this condition first, if true, then continue to validate the subsequnse rule
            'reg_if_not' => '/^IFn\?/',                                 // If match reg_if_not, validate this condition first, if false, then continue to validate the subsequnse rule
            'symbol_rule_separator' => '&&',                            // Rule reqarator for one field
            'symbol_method_omit_this' => '/^(.*)~(.*)$/',             // If set function by this symbol, will add a @this parameter at first 
            'symbol_method_standard' => '/^(.*)~~(.*)$/',                // If set function by this symbol, will not add a @this parameter at first 
            // 'symbol_param_separator' => ',',                            // Parameters separator, such as @this,@field1,@field2
            'symbol_field_name_separator' => '->',                      // Field name separator, suce as "fruit.apple"
            'symbol_required' => '!*',                                  // Symbol of required field, Same as "required"
            // 'symbol_required_if' => '/^\x\?\((.*)\)/',                  // Symbol of required field which is required only when the condition is true, Same as "required_if"
            // 'symbol_required_if_not' => '/^\x\?x\((.*)\)/',             // Symbol of required field which is required only when the condition is not true, Same as "required_if_not"
            // 'symbol_required_ifs' => '/^\x\?x?\((.*)\)/',               // A regular expression to match both symbol_required_if and symbol_required_if_not
            'symbol_optional' => 'o',                                   // Symbol of optional field, can be not set or empty, Same as "optional"
            // 'symbol_optional_unset' => 'O!',                            // Symbol of optional field, can be not set only, Same as "optional_unset"
            'symbol_parallel_rule' => '[or]',                                      // Symbol of or rule, Same as "[or]"
            'symbol_array_optional' => '[o]',                           // Symbol of array optional rule, Same as "[optional]"
            'symbol_index_array' => '[N]',                              // Symbol of index array rule
        ];

        $rule = [
            "id" => "!*&&int&&Reg:/^\d+$/i",
            "name" => "!*&&string&&len<=>~8,32",
            "gender" => "!*&&(s)~male,female",
            "dob" => "!*&&dob",
            "age" => "!*&&check_age~@gender,30 >>>@this is wrong",
            "height_unit" => "!*&&(s)~cm,m",
            "height[or]" => [
                "!*&&=~~@height_unit,cm&&<=>=~100,200 >>>@this should be in [100,200] when height_unit is cm",
                "!*&&=~~@height_unit,m&&<=>=~1,2 >>>@this should be in [1,2] when height_unit is m",
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
                "colleagues[N]" => [
                    "name" => "!*&&string&&len<=>~3,32",
                    "position" => "!*&&(s)~Reception,Financial,PHP,JAVA"
                ],
                "boss" => [
                    "!*&&=~Mike",
                    "!*&&(s)~Johnny,David",
                    "o&&(s)~Johnny,David"
                ]
            ],
            "favourite_food[o][N]" => [
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
                        // "high_school" => "education->high_school length must be greater than 10",
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
                "error_msg_format" => [
                    "format" => Validation::ERROR_FORMAT_NESTED_GENERAL,
                    // "nested" => true,
                    // "general" => true,
                ]
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        if ($error_data !== false) return $this->get_method_info($rule, $cases, $extra, $error_data);

        $this->validation->set_config($validation_conf);
        $result = $this->validate_cases($rule, $cases, $extra);
        $this->validation->reset_config();
        $this->validation->set_validation_global(false);

        return $result;
    }

    protected function test_regular_expression()
    {
        $rule = [
            "id" => "required|/^\d+$/",
            "name" => "required|/Tom/i|string"
        ];

        $rule1 = [
            "id" => "required|Reg>/^\d+$/",
            "name" => "required|Reg>/Tom/i|string"
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
                "expected_msg" => ["id" => "id format is invalid, should be /^\d+$/"]
            ],
            "Invalid_2" => [
                "data" => [
                    "id" => "1",
                    "name" => "John",
                ],
                "expected_msg" => ["name" => "name format is invalid, should be /Tom/i"]
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

    protected function test_extend_rule()
    {
        $rule = [
            // id 必要的，且必须大于等于 1
            "id" => "required|>=1",
            // parent_id 可选的，且必须等于 1
            "parent_id" => "optional|euqal_to_1",
            // child_id 可选的，且必须通过 validate_data_limit 方法的验证
            "child_id" => "optional|validate_data_limit",
        ];

        $cases = [
            "Valid_data_1" => [
                "data" => [
                    "id" => 1,
                ]
            ],
            "Valid_data_2" => [
                "data" => [
                    "id" => 100,
                    "parent_id" => 1,
                    "child_id" => 10,
                ],
            ],
            "Invalid_1" => [
                "data" => [
                    "id" => 0,
                    "parent_id" => 1,
                ],
                "expected_msg" => ["id" => "id validation failed"]
            ],
            "Invalid_2" => [
                "data" => [
                    "id" => 2,
                    "parent_id" => 0,
                ],
                "expected_msg" => ["parent_id" => "parent_id validation failed"]
            ],
            "Invalid_3" => [
                "data" => [
                    "id" => 10,
                    "parent_id" => 1,
                    "child_id" => 1000,
                ],
                "expected_msg" => ["child_id" => "child_id can not be greater than or equal to 1000"]
            ],
            "Invalid_4" => [
                "data" => [
                    "id" => 10,
                    "parent_id" => 1,
                    "child_id" => 10000,
                ],
                "expected_msg" => ["child_id" => "child_id is out of limited"]
            ],
            "Invalid_5" => [
                "data" => [
                    "id" => 10,
                    "parent_id" => 1,
                    "child_id" => "10k",
                ],
                "expected_msg" => ["child_id" => "child_id must be integer"]
            ],
        ];

        $extra = [
            "validation_class" => new MyValidation([
                "validation_global" => false,
            ]),
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rule" => $rule,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    use TestRuleDefaultDeprecated;
}
