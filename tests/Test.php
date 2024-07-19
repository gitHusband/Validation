<?php

/**
 * Unit Tests Bootstrap
 * 
 * Run script:
 *  - php Test.php Unit run [method_name]
 *  - php Test.php Unit run [method_name]
 *  - php Test.php Readme test_simple_example
 *  - php Test.php Demo success
 * 
 * If you need more debug info, run started with `VALIDATION_LOG_LEVEL=1`
 *  - VALIDATION_LOG_LEVEL=1 php Test.php Unit run
 * 
 * @package UnitTests
 */

require_once __DIR__ . '/../vendor/autoload.php';

use githusband\Tests\Unit;
use githusband\Tests\UnitDeprecated;
use githusband\Tests\Readme;
use githusband\Tests\Demo;

/**
 * In order to test if global function works
 *
 * @param mixed $data
 * @param int $min
 * @param int $max
 * @return bool
 */
function check_id($data, $min, $max)
{
    return $data >= $min && $data <= $max;
}

/**
 * In order to test if global function works
 *
 * @param int $data
 * @param string $gender
 * @param int $param
 * @return bool
 */
function check_age($data, $gender, $param)
{
    if ($gender == "male") {
        if ($data > $param) return false;
    } else {
        if ($data < $param) return false;
    }

    return true;
}

$class_lists = [
    'Unit' => Unit::class,
    'UnitDeprecated' => UnitDeprecated::class,
    'Readme' => Readme::class,
    'Demo' => Demo::class,
];

$class_method_default_list = [
    'Unit' => "run",
    'Readme' => "test_simple_example",
    'Demo' => "success",
];

$arguments = $argv;
$class_name = isset($arguments[1]) ? $arguments[1] : "";
if (empty($class_name)) {
    echo "Class not set\n";
    exit(1);
} else if (!isset($class_lists[$class_name])) {
    echo "Class not existed: {$class_name}\n";
    exit(1);
}

$method = isset($arguments[2]) ? $arguments[2] : $class_method_default_list[$class_name];
unset($arguments[0], $arguments[1], $arguments[2]);

/**
 * Skip the Warning: xxx: It is not safe to rely on the system's timezone settings for testing PHP 5
 */
date_default_timezone_set('Asia/Shanghai');

$types = [
    "default",
    "enable_entity",
];

$result = true;
foreach ($types as $type) {
    if ($type == 'enable_entity' && $class_name != 'Unit') continue;
    $enable_entity = false;
    if ($type == 'enable_entity') {
        echo "# Test Ruleset Entity without re-parsing the ruleset!\n";
        $enable_entity = true;
    }

    /** @var Unit|UnitDeprecated|Readme|Demo */
    $class = new $class_lists[$class_name]($enable_entity);

    if (method_exists($class, $method)) {
        $result = call_user_func_array([$class, $method], $arguments);
    } else {
        echo "Method not existed: {$class_lists[$class_name]}::{$method}\n";
        $class->help();
        exit(1);
    }

    if ($result !== true) break;
}

if ($result === true) exit(0);

if (is_array($result)) {
    echo json_encode($result, JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE) . "\n";
} else {
    print_r($result);
    echo "\n";
}

if ($class_name == 'Unit') exit(1);
else exit(0);