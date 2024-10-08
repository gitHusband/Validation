<p align="center"> <a href="README.md">中文</a> | English</p>

<p align=center>
    <strong style="font-size: 35px">Validation</strong>
</p>
<p align="center"><b>Simple, intuitive and customizable</b></p>
<p align="center">
  <a href="https://hits.seeyoufarm.com">
     <img src="https://img.shields.io/github/license/gitHusband/Validation"/>
  </a>
  <a href="https://hits.seeyoufarm.com">
     <img src="https://img.shields.io/github/languages/top/gitHusband/Validation"/>
  </a>
  <a href="https://hits.seeyoufarm.com">
     <img src="https://img.shields.io/github/actions/workflow/status/gitHusband/Validation/unit-tests.yml?label=unit-tests"/>
  </a>
  <a href="https://hits.seeyoufarm.com">
     <img src="https://img.shields.io/github/v/release/gitHusband/Validation"/>
  </a>
</p>

Table of contents
=================

* [Table of contents](#table-of-contents)
* [Validation —— A PHP Intuitive Data Validator](#validation--a-php-intuitive-data-validator)
   * [1. Overview](#1-overview)
      * [1.1 Feature](#11-feature)
      * [1.2 An Example](#12-an-example)
   * [2. Install](#2-install)
   * [3. Develop](#3-develop)
   * [4. Features](#4-features)
      * [4.1 Methods And Their Symbols](#41-methods-and-their-symbols)
      * [4.2 Regular Expression](#42-regular-expression)
      * [4.3 Method Parameters](#43-method-parameters)
        * [The ways to pass parameters](#the-ways-to-pass-parameters)
        * [Parameter type](#parameter-type)
      * [4.4 Method Extension](#44-method-extension)
        * [The ways to extend methods](#the-ways-to-extend-methods)
        * [Set method symbols](#set-method-symbols)
      * [4.5 Series And Parallel Rules](#45-series-and-parallel-rules)
      * [4.6 Conditional Rules](#46-conditional-rules)
         * [The When Conditional Rules](#the-when-conditional-rules)
         * [IF Rules](#if-rules)
      * [4.7 Infinitely Nested Data Structures](#47-infinitely-nested-data-structures)
         * [The Rules Of The Array Itself](#the-rules-of-the-array-itself)
      * [4.8 Optional Field](#48-optional-field)
      * [4.9 Special Validation Rules](#49-special-validation-rules)
      * [4.10 Customized Configuration](#410-customized-configuration)
         * [Enable Entity (emphasis)](#enable-entity-emphasis)
      * [4.11 Internationalization](#411-internationalization)
      * [4.12 Validate Whole Data](#412-validate-whole-data)
      * [4.13 Error Message Template](#413-error-message-template)
      * [4.14 Error Message Format](#414-error-message-format)
   * [Appendix 1 - Methods And Symbols](#appendix-1---methods-and-symbols)
   * [Appendix 2 - Validation Complete Example](#appendix-2---validation-complete-example)
   * [Appendix 3 - Error Message Format](#appendix-3---error-message-format)
      * [One-dimensional String Structure](#one-dimensional-string-structure)
      * [One-dimensional Associative Structure](#one-dimensional-associative-structure)
      * [Infinitely Nested String Structure](#infinitely-nested-string-structure)
      * [Infinitely Nested Associative Structure](#infinitely-nested-associative-structure)


# Validation —— A PHP Intuitive Data Validator

Validation is used to check the legality of data.
Goal is only 5 words - **Rule structure is data structure**。

**Looking forward to your contribution to develop or optimize Validation. Thank you!**

> [Github Repository](https://github.com/gitHusband/Validation)

<details>
    <summary><span>&#128587;</span> Why write this tool?</summary>

1. For API parameters, every parameter should theoretically be checked for legitimacy, especially those that need to be forwarded to other API interfaces or stored in a database.
*For example, the database is basically limited to the data length type, but the verification of the length is simple and cumbersome, and the use of this tool can greatly simplify the code.*
2. If there are too many API parameters, the amount of verification code is bound to be large, and the parameter format cannot be intuitively understood through the code.
3. Customize an array of validation rules, and the request parameters format will look the same as rules format.
4. Easily vary the error messages returned by validation methods
5. ~~I'll make it up when I get another Lofty rhetoric~~ <span>&#128054;</span>

</details>

## 1. Overview
### 1.1 Feature
- One field corresponds to one validation ruleset, and a ruleset consists of multiple validation rules or methods (functions).
- The validation method supports the substitution of symbol, which is easy to understand and simplifies the rules. e.g. `*`, `>`, `<`, `length>`
- Supports regular expressions

<details>
    <summary><span>&#128071;</span> Read more...</summary>

- Supports method parameter passing. For example, `@this` represents the value of current field
- Supports to extend method
- Supports series validation: multiple methods for one parameter must all be valid
- Supports parallel validation: one of the multiple rules for a parameter is valid
- Supports conditional validation: If the condition are met, the subsequent methods continue to be validated. If the conditions are not met, the field is optional
- Supports infinite nested data structures, including associative arrays, indexed arrays
- Supports for special validation rules
- Supports customized configuration. For example, the separator for multiple methods defaults to `|` and can be changed to other characters, such as `;`
- Support internationalization. The default is English. User-defined methods are supported to return error messages
- You can validate the whole data at once (by default). You can also set the parameter validation to end immediately after the parameter validation fails
- Supports custom error messages, support multiple formats of error messages, infinite nested or dotted array of error message formats
- ~~I'll make it up when I get another Lofty Rhetoric~~ <span>&#128054;</span>

</details>

### 1.2 An Example
```PHP
use githusband\Validation;

// A simple example of a parameter to be validated. In fact, no matter how complex the parameters are, an array of validation rules is supported to complete validation
$data = [
    "id" => 1,
    "name" => "Devin",
    "age" => 18,
    "favorite_animation" => [
        "name" => "A Record of A Mortal's Journey to Immortality",
        "release_date" => "July 25, 2020 (China)"
    ]
];

// The array of validation rules. The format of the rule array is the same as the format of the parameters to be validated.
$rule = [
    "id" => "required|/^\d+$/",         // Must not be empty, and must be numbers
    "name" => "required|length><=[3,32]",  // Must not be empty and the string length must be greater than 3 and less than or equal to 32
    "favorite_animation" => [
        "name" => "required|length><=[1,64]",          // Must not be empty, the string length must be greater than 1 and less than 64
        "release_date" => "optional|length><=[4,64]",  // Optional, If it is not empty, then the string length must be greater than 4 and less than or equal to 64
    ]
];

$config = [];
// Accepts a custom configuration array, but not necessary
$validation = new Validation($config);

// Set validation rules and validate data, return true if successful, false if failed
if ($validation->set_rules($rule)->validate($data)) {
    // Get the validation result here. There are parameters validated by the rule {$rule}. If successful, modify the field value to true. If failed, modify the field value to error information.
    // Parameters that have not been validated remain unchanged. For example, age remains unchanged at 18.
    return $validation->get_result();
} else {
    // There are four error message formats to choose from. Default Validation::ERROR_FORMAT_DOTTED_GENERAL
    return $validation->get_error();
}
```

In theory, the tool is meant for validating complex data structures, but if you want to validate a single string, that's also possible, e.g.

```PHP
$validation->set_rules("required|string")->validate("Hello World!");
```

- The above only shows a simple example. In fact, no matter how complex the request parameters are, an array of validation rules is supported to complete the validation. Refer to [Appendix 2 - Validation Complete Example](#appendix-2---validation-complete-example)
- Are the rules too ugly? Refer to [4.10 Customized Configuration](#410-customized-configuration)

## 2. Install
```BASH
$ composer require githusband/validation
```

## 3. Develop
If you have ideas to optimize the development of this tool, the following will help you:

The unit test class `Unit.php` and the document test class `Readme.php` have been built in the `src/Test` directory.

Execute them via [Composer Script](https://getcomposer.org/doc/articles/scripts.md).

~~After cloning this project, please generate the project's automatic loading file by:~~
```BASH
$ composer dump-autoload --dev
```

Since some external libraries are used in the unit testing, such as `uuid`, we cannot simply load the project's automatic loading file, but need to download the external library.
```BASH
$ composer install
```

- **Unit testing class**

This contains tests for all functions, only some built-in methods
In principle, after modifying the code, run the unit test to ensure that the functions are normal.

```BASH
// Test all examples, and print debug information.
$ VALIDATION_LOG_LEVEL=1 composer run-script test
// Test a single example, for example, test a regular expression
$ composer run-script test test_regular_expression
```

If you have installed [Docker](https://www.docker.com/products/docker-desktop/)，then you can test multiple `PHP` versions at once： [PHP v5.6](https://hub.docker.com/layers/library/php/5.6-cli-alpine/images/sha256-5dd6b6ea600342303f987d33524c0fae0347ae13be6ae55691d4acb873c203ea?context=explore), [PHP v7.4.33](https://hub.docker.com/layers/library/php/7.4.33-cli-alpine/images/sha256-1e1b3bb4ee1bcb039f559adb9a3fae391c87205ba239b619cdc239b78b7f2557?context=explore) 和 [PHP 最新版本](https://hub.docker.com/layers/library/php/latest/images/sha256-43b84b891f59311867c9b8e18f1ec646b32cb6376475bcd2d489bab912f4f21f?context=explore)
```BASH
$ composer run-script multi-test
```

- **Document testing class**

Document code: [1.2 An Example](#12-an-example)
```BASH
$ composer run-script readme test_simple_example
```
Document code: [Appendix 2 - Validation Complete Example](#appendix-2---validation-complete-example)
```BASH
$ composer run-script readme test_complete_example
```


## 4. Features

### 4.1 Methods And Their Symbols
One field corresponds to one validation ruleset, and a ruleset consists of multiple validation **rules**, **methods (functions)** and *error message templates(optional)*.
In order to facilitate understanding and simplify the rules, some method **symbols** are allowed to represent actual methods (functions).

```PHP
// The name must not be empty, must be a string, and the length must be greater than 3 and less than or equal to 32
"name" => "required|string|length_greater_lessequal[3,32]"

// Use method symbols, same as above
// If you think the method symbols difficult to understand, please just use the full name of the methods.
"name" => "*|string|length><=[3,32]"
```

For example:

Symbol | Method | Desc
---|---|---
`*` | `required` | Required, not allowed to be empty
`O` | `optional` | Optional, allowed not to be set or empty
`>[20]` | `greater_than` | Number must be greater than 20
`length><=[2,16]` | `length_greater_lessequal` | Character length must be greater than 2 and less than or equal to 16
`/` | `ip` | Must be an ip address

**The complete method and its symbol can be found in** [Appendix 1 - Methods And Symbols](#appendix-1---methods-and-symbols)

### 4.2 Regular Expression
Generally starts with `/` and ends with `/`, indicating a regular expression
The `/` at the end of the regular expression may be followed by a pattern modifier, such as `/i`
```PHP
// id is required, and must be a number
"id" => "required|/^\d+$/",
```
Supports multiple regular expressions in a series rule

### 4.3 Method Parameters
How to pass parameters to the methods in rules written as strings?

#### The ways to pass parameters

1. **Standard parameters**
Just like the parameters used by PHP functions, the parameters are written in parentheses `()`. Multiple parameters are separated by commas `,`. No extra spaces are allowed before and after `,`
For example,
```
"age" => "equal(@this,20)"
```
*Indicates that age must be equal to 20. `@this` represents the value of the current age field.*

2. **Omit the `@this` parameter**
When the parameters are written inside square brackets `[]`, the first `@this` parameter can be omitted.
For example, the above example can be shortened to:
```
"age" => "equal[20]"
```

3. **Omit parameters**
When there is only one method parameter and it is the current field value, you can omit `()` and `[]` and only write the method.
For example, 
```
"id" => "uuid"
```

4. **Default parameters**
Default parameters are supported but require additional configuration. See [Set method symbols](#set-method-symbols)
For example, the following indicates that the data is an indexed array, and `unique` verifies that its subdata must be unique. However, it is a bit redundant to write `@parent` for parameters every time. It can be omitted by configuring default parameters.

```PHP
$rule = [
    // Standard parameters
    "*" => "unique(@this,@parent)",
    // Omit the `@this` parameter
    "*" => "unique[@parent]",
    // Default parameters
    "*" => "unique"
];
```

**Parameters List**

Parameter | Desc
---|---
Static Value | Indicates that the parameter is a static string and is allowed to be empty. For example `20`
@this | Indicates that the parameter is the value of the current field
@parent | Indicates the parameter is the value of the parent of the current field
@root | Indicates that this parameter is the whole validation data
@field_path | Indicates that the parameter is the value of a field whose name is `field_path`. e.g. `@age`, `@person.name`

**Parameter separator:**
- `symbol_parameter_separator`: `,`
  Parameters separator to split the parameter string of a method into multiple parameters; e.g. `equal(@this,1)`
  In the following special cases, `,` will not be treated as a parameter separator:
  - `\,`
  - Wrapped by `[]`. For example: `my_method[[1,2,3],100]` means there are two parameters, array `[1,2,3]` and integer `100`
  - Wrapped by `{}`.
- For custom parameter separator, see [4.10 Customized Configuration](#410-customized-configuration)

#### Parameter Type

Automatically detect the type of the parameter and forcibly convert it to the corresponding type.
1. Text quoted with double quotes (`"`) or single quotes (`''`) is treated as a string. Otherwise the type is detected and forcibly converted.
  - For example, `"abc"`, `'abc'` or `abc` are treated as string `abc`
2. Types that support conversion are:
  - `int`：For example, `123` is an integer and `"123"` is a string
  - `float`: For example, `123.0`
  - `bool`: For example, `false` or `TRUE`
  - `array`: For example, `[1,2,3]` or `["a", "b"]`
  - `object`: For example, `{"a": "A", "b": "B"}`
  - For example, `my_method[[1,"2",'3'],100,false,"true"]`:
    - `[1,"2",'3']` will be converted to `array([1,"2","3"])`
    - `100` will be converted to `int(100)`
    - `false` will be converted to `bool(false)`
    - `"true"` will be converted to `string(true)`

For custom parameter separator and parameter type, see [4.10 Customized Configuration](#410-customized-configuration)

### 4.4 Method Extension
There are some validation methods built in the Validation tool, such as `*`, `>`, `length>=`, `ip` and so on. 
For details, refer to [Appendix 1 - Methods And Symbols](#appendix-1---methods-and-symbols)

If the validation rules are complex and the built-in methods cannot meet your needs, you can extend your own methods.

If the method may return different error messages based on different judgments, see the section [4.13 Error message template - 3. Return the template directly in the method](#413-error-message-template).

#### The ways to extend methods

There are several ways to extend your own methods:
##### 1. Add method：`add_method($method, $callable, $method_symbol = '')`
Add a new method
- `$method`: Method name
- `$callable`: Method definition
- `$method_symbol`: Method symbol. Optional.

<details>
  <summary><span>&#128071;</span> <strong>Click to view code</strong></summary>

Add a new method, `check_id`, and set its symbol to `c_id`
```PHP
$validation->add_method('check_id', function ($id) {
    if ($id == 0) {
        return false;
    }

    return true;
}, 'c_id');
```

The rule is
```PHP
$rule = [
    // Required, must be a number and must be not equal to 0
    "id" => "required|/^\d+$/|check_id",
    // Or use its symbol instead of the method name
    "id" => "required|/^\d+$/|c_i",
];
```

</details>
</br>

##### 2. Add rule class：by `add_rule_class`
A rule class contains multiple methods and their symbols. Supports static or non-static methods.
Due to priority reasons, if you need to override the built-in methods in the validation class Validation, please use the new rule class. The extended class may not be able to override it.

<details>
  <summary><span>&#128071;</span> <strong>Click to view code</strong></summary>

Create a new file，`RuleClassTest.php`
```PHP
/**
 * Use rule class to add validation methods
 * If you need to define method symbols, put them in the method_symbols attribute
 */
class RuleClassTest
{
    // method symbol
    public static $method_symbols = [
        'is_custom_string' => 'cus_str',
    ];

    // method
    public static function is_custom_string($data)
    {
        return preg_match('/^[\w\d -]{8,32}$/', $data) ? true : false;
    }
}
```

Call `add_rule_class` to add a new rule class，RuleClassTest
```PHP
use RuleClassTest;

// Add a new rule class，RuleClassTest
$validation->add_rule_class(RuleClassTest::class);
```
In fact, `add_rule_class` adds the rule class into the `rule_classes` attribute, so that we can add a new rule class through another more direct method:

```PHP
use githusband\Validation;
use RuleClassTest;

class MyValidation extends Validation
{
    protected $rule_classes = [
        RuleClassTest::class
    ];
}
```

The rule is
```PHP
$rule = [
    // Required, format must be /^[\w\d -]{8,32}$/
    "id" => "required|is_custom_string",
    // Or use its symbol instead of the method name
    "id" => "required|cus_str",
];
```

</details>
</br>

##### 3. Extend `Validation` class

Extend the `Validation` class and override the built-in methods or add new built-in methods. Recommended [trait](https://www.php.net/manual/zh/language.oop5.traits.php)

<details>
  <summary><span>&#128071;</span> <strong>Click to view code</strong></summary>

Create a new file，`RuleExtendTrait.php`
```PHP
/**
 * 1. It is recommended to use traits to expand validation methods
 * If you need to define method symbols, put them in an attribute. The attribute naming rule is: "method_symbols_of_" + class name (high camel case converted to underline)
 */
trait RuleExtendTrait
{
    // method symbol
    protected $method_symbols_of_rule_extend_trait = [
        'euqal_to_1' => '=1',
    ];

    // method
    protected function euqal_to_1($data)
    {
        return $data == 1;
    }
}
```

Extend the class, add new methods and their symbols
```PHP
use githusband\Validation;

class MyValidation extends Validation
{
    // 1. Use Trait
    use RuleExtendTrait;

    /**
     * 2. Directly add methods and their symbols
     * If you need to define method symbols, place them in an attribute named method_symbols
     */
    protected $method_symbols = [
        'grater_than_or_equal_to_1' => '>=1',
    ];

    protected function grater_than_or_equal_to_1($data)
    {
        return $data >= 1;
    }
}
```

The rule is
```PHP
$rule = [
    // id must not be empty, and must be greater than or equal to 1
    // ">=1" is a method symbols, corresponding to the method named "grater_than_or_equal_to_1"
    "id" => "required|>=1",
    // parent_id is optional, if not empty, it must be equal to 1
    "parent_id" => "optional|euqal_to_1",
];
```

</details>
</br>

##### 4. Global function
Including the system functions and user-defined global functions.

**The priority of the these ways**
`Add method` > `Add rule class` > `Extend Validation class` > `Built-in method` > `Global function`

If the method can not be found from the three way, an error will be reported: Undefined

#### Set method symbols

Allows setting method symbols, making it more intuitive. For example, the symbol for `greater_than` is `>`. Refer to [Appendix 1 - Methods And Symbols](#appendix-1---methods-and-symbols)
From the previous section, you should have noticed the use of method symbols. That's also the most common way to set the symbols. For example,
```PHP
public static $method_symbols = [
    'is_custom_string' => 'cus_str',
];
```

Method symbols `$method_symbols` may also support other attributes:

- If the value is a string, such as 'cus_str', it represents the symbol.
- If the value is an array, the following fields are supported:
  - symbols: The symbols of a method. e.g. 'cus_str'
  - is_variable_length_argument: Default to false. Whether the second parameter of the current rule is variable length argument or not
  - default_arguments: Default to nothing. Set the default arguments for the method. {@see githusband\Rule\RuleClassArray::$method_symbols['is_unique']}
    - The key of default_arguments array must be int. It indicates what argument it is. e.g. `2` means the 2th argument.
    - The value of default_arguments array can be anything. Specially, about the value what likes "@parent"(means the parent data of the current field), see [4.3 Method Parameters](#43-method-parameters)

For [2. Add rule class](#2-add-rule-classby-add_rule_class), if all attributes of the method symbols are supported, the example is as follows,

<details>
  <summary><span>&#128071;</span> <strong>Click to view code</strong></summary>

```PHP
class RuleClassTest
{
    /**
     * The method symbols of rule default.
     * 
     * - If the value is a string, such as 'cus_str', it represents the symbol.
     * - If the value is an array, the following fields are supported:
     *   - symbols: The symbols of a method.
     *   - is_variable_length_argument: Default to false. Whether the second parameter of the current rule is variable length argument or not
     *   - default_arguments: Default to nothing. Set the default arguments for the method. {@see githusband\Rule\RuleClassArray::$method_symbols['is_unique']}
     *     - The key of default_arguments array must be int. It indicates what argument it is. e.g. `2` means the 2th argument.
     *     - The value of default_arguments array can be anything. Specially, about the value what likes "@parent"(means the parent data of the current field), please @see https://github.com/gitHusband/Validation/blob/main/README-EN.md#43-method-parameters
     *
     * @var array<string, string|array{symbols: string|string[], is_variable_length_argument: bool, default_arguments: <int, mixed>}>
     */
    public static $method_symbols = [
        'is_custom_string' => 'cus_str',
        'is_in_custom_list' => [
            'symbols' => '<custom>',
            'is_variable_length_argument' => true,  // All parameters after the first parameter are considered subelements of the second parameter array.
        ],
        'is_equal_to_password' => [
            'symbols' => '=pwd',
            'default_arguments' => [
                2 => '@password'    // The second parameter defaults to the value of the `password` field
            ]
        ]
    ];

    /**
     * Test method 1 - Test whether the format of the current field meets the requirements
     * 
     * Usage:
     * - 'id' => 'is_custom_string'
     * - 'id' => 'cus_str'
     *
     * @param string $data
     * @return bool
     */
    public static function is_custom_string($data)
    {
        return preg_match('/^[\w\d -]{8,32}$/', $data) ? true : false;
    }

    /**
     * Test method 2 - Test whether the current field exists in the list
     * 
     * Usage:
     * - 'sequence' => 'is_in_custom_list[1st, First, 2nd, Second]'
     * - 'sequence' => '<custom>[1st, First, 2nd, Second]'
     * 
     * This is an example of that the second parameter is a variable length parameter. If you do not set the is_variable_length_argument, then it is written as follows. Note that the second parameter must be a legal JSON Encoded string. For example:
     * - 'sequence' => 'is_in_custom_list[["1st", "First", "2nd", "Second"]]'
     * - 'sequence' => '<custom>[["1st", "First", "2nd", "Second"]]'
     *
     * @param mixed $data
     * @param array $list
     * @return bool
     */
    public static function is_in_custom_list($data, $list)
    {
        return in_array($data, $list);
    }

    /**
     * Test Method 3 - Verify that the current field is equal to the `password` field
     * 
     * Usage:
     * - 'confirm_password' => 'is_equal_to_password'
     * - 'confirm_password' => '=pwd'
     * 
     * This is an example of default parameters. The same effect is achieved by using the `euqal` method, which is equivalent to adding the default parameter '@password' to the equal method. For example:
     * - 'confirm_password' => `equal[@password]`，
     * - 'confirm_password' => `=[@password]`，
     *
     * @param string $data
     * @param string $password
     * @return bool
     */
    public static function is_equal_to_password($data, $password)
    {
        return $data == $password;
    }
}
```

</details>


### 4.5 Series And Parallel Rules
- Series: Multiple methods in a rule of one field must all be valid, the flag is `|`
```PHP
"age" => "required|equal[20]"
```
- Parallel: Multiple rules for one field only need to valid one of them. 
  Two options:
  - A. `{字段名}` + `[or]`
  - B. Add a unique subfield under the current field: `[or]`

The symbol of `[or]` is `[||]`, the symbol can be customized, and the usage is the same as `[or]`

```PHP
// Series: The height_unit is required and must be cm or m
"height_unit" => "required|<string>[cm,m]",
// A. Parallel: The rule can be like this, [or] can be replaced by its symbol [||]
"height[or]" => [
    // If the height_unit is cm(centimeter), the height must be greater than or equal to 100 and less than or equal to 200
    "required|=(@height_unit,cm)|>=<=[100,200]",
    // If the height_unit is m(meter), the height must be greater than or equal to 1 and less than or equal to 2
    "required|=(@height_unit,m)|>=<=[1,2]",
]
// B. Parallel: The rules can also be like this, and the symblo [||] can be replaced by [or]
"height" => [
    "[||]" => [
        "required|=(@height_unit,cm)|>=<=[100,200]",
        "required|=(@height_unit,m)|>=<=[1,2]",
    ]
]
```

### 4.6 Conditional Rules

#### The When Conditional Rules

The **When** conditional rule: Add conditions to any rule or method. Generally, the method will be validated only when the condition is met, otherwise the method will be skipped.
- Usage：`{any rule or method}` + `:` + `when()`
- The rule or methods include： `required`, `optional`, `optional_unset`, `regular expression` 和 `any method`（Such as method `>=`）
- Two conditional rules：`when()` and `when_not()`，condition is written between parentheses. *Currently only one condition is supported*。
- The When Conditional Rules support customization, see [4.10 Customized Configuration](#410-customized-configuration)

1. Generally, the method will be validated only if the condition is met, otherwise the method will be skipped. For example:
```PHP
$rule = [
    "id" => "required|><[0,10]",
    // When the id is less than 5, the name can only be a number and its length must be greater than 2
    // When the id is greater than or equal to 5, the name can be any string and its length must be greater than 2
    "name" => "/^\d+$/:when(<(@id,5))|length>[2]",
    // When the id is not less than 5, the age must be less than or equal to 18
    // When id is less than 5, age can be any number
    "age" => "int|<=[18]:when_not(<(@id,5))",
];
```

2. Specifically，`required`, `optional`, `optional_unset`, these three rules may need to validate whether the field is empty or not.

I take the `required` as an example to illustrate the usage of `when()` and `when_not()`.

- **2.1 Positive conditionally required rule**: `required:when()`

1. If the condition is met, the validate the `required` method
2. If the condition is not met, the field is optional:
  2.1. If this field is empty, validation success will be returned immediately;
  2.2. If this field is not empty, continue to validate subsequent methods

```PHP
$rule = [
    // The attribute must not be empty, and it must be "height" or "weight"
    "attribute" => "required|<string>[height,weight]",
    // If the attribute is height, then the centimeter must not be empty
    // If the attribute is not height, then the centimeter is optional
    // However, if the value of the centimeter is not empty, it must be greater than 180
    "centimeter" => "required:when(=(@attribute,height))|required|>[180]",
];
```
- **2.2 Negative conditionally required rule**: `required:when_not()`

1. If the condition is not met, the validate the `required` method
2. If the condition is met, the field is optional:
  2.1. If this field is empty, validation success will be returned immediately;
  2.2. If this field is not empty, continue to validate subsequent methods

```PHP
$rule = [
    // The attribute must not be empty, and it must be "height" or "weight"
    "attribute" => "required|<string>[height,weight]",
    // If the attribute is not weight, then the centimeter must not be empty
    // If the attribute is weight, then the centimeter is optional
    // However, if the value of the centimeter is not empty, it must be greater than 180
    "centimeter" => "required:when_not(=(@attribute,weight))|required|>[180]",
];
```

#### IF Rules

The usage of if rules is similar to PHP `if structure` syntax, for example:
- `if ( expr ) { statement }`
- `if ( !expr ) { statement }`
- `if ( expr1 || !expr2 ) { statement1 } else { statement2 }`

**Supported logical operators:**
- `!`: Logical Operator Not.
  - The condition does not contain `!`: It means that when the condition result is strictly the same as the Boolean value `true` (`===`), the condition is true.
  - The condition contains `!`: It means that when the condition result is not strictly the same as the Boolean value `true` (`!==`), the condition is true.
- `||`：Logical Operator Or. It just means that if one of the conditions is true, the conditions is true.

**The validation logic is:**
1. If the conditions is true, continue to validate subsequent methods
2. If the conditions is not true, don't continue to validate subsequent methods

**Example 1:**
```PHP
$rule = [
    // The attribute must not be empty, and it must be "height" or "weight"
    "attribute" => "required|<string>[height,weight]",
    // If the attribute is height, then the centimeter must not be empty and must be greater than 180
    // If the attribute is not height，then the subsequent rules will not be validated, that is, the centimeter can be any value.
    "centimeter" => "if(=(@attribute,height)){required|>[180]}",
];
```

**Example 2:**
```PHP
$rule = [
    // The attribute must not be empty, and it must be "height" or "weight"
    "attribute" => "required|<string>[height,weight]",
    // If the attribute is not weight, then the centimeter must not be empty and must be greater than 180
    // If the attribute is weight，then the subsequent rules will not be validated, that is, the centimeter can be any value.
    "centimeter" => "if ( !=(@attribute,weight) ) { required|>[180] }",
];
```

**Example 3：**
```PHP
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
```
For example 3, it should be noted that we only support one logical not `!`. The `!!=(@id,50)`, which is actually two parts, the logical not `!` and the method `not_equal` symbol `!=`. For the symbol, see [Appendix 1 - Methods And Symbols](#appendix-1---methods-and-symbols)

### 4.7 Infinitely Nested Data Structures

Supports infinitely nested data structures, including associative arrays and index arrays

**1. Infinitely nested associative array**
The rule structures will look the same as data structures.

For example:
```PHP
$data = [
    "id" => 1,
    "name" => "Johnny",
    "favourite_fruit" => [
        "name" => "apple",
        "color" => "red",
        "shape" => "circular"
    ]
];

// To validate the above $data, the rule can be like this
$rule = [
    "id" => "required|/^\d+$/",
    "name" => "required|length>[3]",
    "favourite_fruit" => [
        "name" => "required|length>[3]",
        "color" => "required|length>[3]",
        "shape" => "required|length>[3]"
    ]
];
```

**2. Infinitely nested index array**
Add the flag `.*` after the name of the index array field, 
or add the unique subelement `*` to the index array field

<details>
  <summary><span>&#128071;</span> <strong>Click to view code</strong></summary>

```PHP
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

// To validate the above $data, the rule can be like this
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

// You can also write it like this
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
```

</details>

#### The Rules Of The Array Itself

In the above examples, a rule array can complete the validation of complex structured data. But only the leaf fields can be validated. If you want to validate the parent array itself, it is not yet possible.

Then, we design the rule to represent the parent array itself through a `__self__` leaf field.

- The `__self__` field allows customization, see [4.10 Customized Configuration](#410-customized-configuration)
- Specially, if the array itself has only the `optional` rule, there is a simple way to write it, see [4.8 Optional Field](#48-optional-field)

**1. Rules for associative array itself**
```PHP
$rule = [
    // Indicates that the root data can be empty. If it is not empty, its fields are required to contain and only contain "id, name, favorite_fruit".
    "__self__" => "optional|require_array_keys[id, name, favourite_fruit]",
    "id" => "required|/^\d+$/",
    "name" => "required|length>[3]",
    "favourite_fruit" => [
        // Indicates that the `favourite_fruit` can be empty. If it is not empty, its fields are required to contain and only contain "name, color, shape".
        "__self__" => "optional|require_array_keys[name, color, shape]",
        "name" => "required|length>[3]",
        "color" => "required|length>[3]",
        "shape" => "required|length>[3]"
    ]
];
```

**2. Rules for index array itself**
```PHP
$rule = [
    "id" => "required|/^\d+$/",
    "name" => "required|length>[3]",
    "favourite_color" => [
        // Indicates that the `favourite_color` can be empty. If it is not empty, its fields are required to contain and only contain "0, 1". Only two child elements are allowed.
        "__self__" => "optional|require_array_keys[0,1]",
        "*" => "required|length>[3]"
    ],
    "favourite_fruits" => [
        // Indicates that the `favourite_fruits` can be empty. If it is not empty, its fields are required to contain and only contain "0, 1". Only two child elements are allowed.
        "__self__" => "optional|require_array_keys[0,1]",
        "*" => [
            "name" => "required|length>[3]",
            "color" => "required|length>[3]",
            "shape" => "required|length>[3]"
        ]
    ]
];
```

### 4.8 Optional Field

1. Generally, for a leaf field (without any subfields), you can directly use the `optional` method to indicate that the field is optional.
2. Sometimes, arrays are also optional, but once set, the subelements must be validated according to the rules. In this case, just add `[optional]` after the array field name to indicate that the array is optional.
3. It has the same effect as adding `[optional]` after the field name. Adding a unique sub-element `[optional]` to the field also indicates that the field is optional.
4. The symbol of `[optional]` is `[O]`, and the two are interchangeable.

For example:
```PHP
$rule = [
    // 1. For leaf fields, use the optional method directly to indicate that the field is optional.
    "name" => "optional|string",
    // 2. For any field, add [optional] after the field name to indicate that the field is optional.
    "favourite_fruit[optional]" => [
        "name" => "required|string",
        "color" => "required|string"
    ],
    // 3. For any field, add the only sub-element [optional] to indicate that the field is optional
    "gender" => [ "[optional]" => "string" ],
    "favourite_food" => [
        "[optional]" => [
            "name" => "required|string",
            "taste" => "required|string"
        ]
    ],
];
```
### 4.9 Special Validation Rules

List of special rules:

Full Name | Symbol | Desc
---|---|---
[optional] | [O] | Indicates the field is optional. Array supported. See [4.8 Optional Field](#48-optional-field)
[or] | [\|\|] | Indicates it's a parallel rule, one of the rules validated means the field is valid. See [4.5 Series And Parallel Rules](#45-series-and-parallel-rules)
 N/A | .* | Indicates the field is an indexed array. See [4.7 Infinitely Nested Data Structures](#47-infinitely-nested-data-structures)

**NOTE**: The usage of the symbol is the same as the full method name, and the symbol allows to be [customized](#410-customized-configuration).

### 4.10 Customized Configuration

The configurations that support customization include:

<details>
  <summary><span>&#128071;</span> <strong>Click to view configurations</strong></summary>

```PHP
$config = [
    'language' => 'en-us',                                      // Language, Default en-us
    'lang_path' => '',                                          // Customer Language file path
    'enable_entity' => false,                                   // Pre-parse ruleset into ruleset entity to reuse the ruleset without re-parse
    'validation_global' => true,                                // 1. true - validate all rules even though previous rule had been failed; 2. false - stop validating when any rule is failed
    'auto_field' => "data",                                     // If root data is string or numberic array, add the auto_field as the root data field name
    'reg_msg' => '/ >> (.*)$/sm',                               // Set the error message format for all the methods after a rule string
    'reg_preg' => '/^(\/.+\/.*)$/',                             // If a rule match reg_preg, indicates it's a regular expression instead of method
    'reg_preg_strict' => '/^(\/.+\/[imsxADSUXJun]*)$/',         // Verify if a regular expression is valid
    'symbol_if' => 'if',                                        // The start of IF construct. e.g. `if ( expr ) { statement }`
    'symbol_else' => 'else',                                    // The else part of IF construct. e.g. `else { statement }`. Then the elseif part is `else if ( expr ) { statement }`
    'symbol_logical_operator_not' => '!',                       // The logical operator not. e.g. `if ( !expr ) { statement }`
    'symbol_logical_operator_or' => '||',                       // The logical operator or. e.g. `if ( expr || expr ) { statement }`
    'symbol_rule_separator' => '|',                             // Serial rules seqarator to split a rule into multiple methods
    'symbol_parallel_rule' => '[||]',                           // Symbol of the parallel rule, Same as "[or]"
    'symbol_method_standard' => '/^([^\\(]*)\\((.*)\\)$/',      // Standard method format, e.g. equal(@this,1)
    'symbol_method_omit_this' => '/^([^\\[]*)\\[(.*)\\]$/',     // @this omitted method format, will add a @this parameter at first. e.g. equal[1]
    'symbol_parameter_separator' => ',',                        // Parameters separator to split the parameter string of a method into multiple parameters, e.g. equal(@this,1)
    'symbol_field_name_separator' => '.',                       // Field name separator of error message, e.g. "fruit.apple"
    'symbol_required' => '*',                                   // Symbol of required field, Same as the rule "required"
    'symbol_optional' => 'O',                                   // Symbol of optional field, can be not set or empty, Same as the rule "optional"
    'symbol_optional_unset' => 'O!',                            // Symbol of optional field, can be not set only, Same as the rule "optional_unset"
    'symbol_array_optional' => '[O]',                           // Symbol of array optional rule, Same as "[optional]"
    'symbol_index_array' => '.*',                               // Symbol of index array rule
    'reg_whens' => '/^(.+):(!)?\?\((.*)\)/',                    // A regular expression to match both reg_when and reg_when_not. Most of the methods are allowed to append a if rule, e.g. required:when, optional:when_not
    'reg_when' => '/^(.+):\?\((.*)\)/',                         // A regular expression to match a field which must be validated by method($1) only when the condition($3) is true
    'symbol_when' => ':?',                                      // We don't use the symbol to match a When Rule, it's used to generate the symbols in README
    'reg_when_not' => '/^(.+):!\?\((.*)\)/',                    // A regular expression to match a field which must be validated by method($1) only when the condition($3) is not true
    'symbol_when_not' => ':!?',                                 // We don't use the symbol to match a When Rule, it's used to generate the symbols in README
    'self_ruleset_key' => '__self__',                           // If an array has such a subfield with the same name as {self_ruleset_key}, then the ruleset of this subfield is the ruleset of the array.
];
```

</details>

For example, you think the rules I designed are too ugly and not easy to understand at all. <span>&#128545;</span> 
So you made the following customizations:

```PHP
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

$validation = new Validation($custom_config);
```

Then, the rule in [1.2 An Example](#12-an-example) can be written as follows:

```PHP
$rule = [
    "id" => "!*&&Reg:/^\d+$/",          // Must not be empty, and must be numbers
    "name" => "!*&&length><=~3+32",        // Must not be empty and the string length must be greater than 3 and less than or equal to 32
    "favorite_animation" => [
        "name" => "!*&&length><=~1+64",                // Must not be empty, the string length must be greater than 1 and less than 64
        "release_date" => "o?&&length><=#@this+4+64",  // Optional, If it is not empty, then the string length must be greater than 4 and less than or equal to 64
    ]
];
```
Has it become more beautiful? <span>&#128525;</span> <strong style="font-size: 20px">Come and try it!</strong>

#### Enable Entity (emphasis)

As you can see, the rulesets are written in strings. Every time we validate the data, we need to parse the ruleset into individual rules, then parse the rules into methods and their parameters, and finally validate the corresponding data.
For php-fpm requests, each request will start a process. Generally, only one data needs to be validated. The process will be destroyed after the request is completed, so enable the entity will waste performance.
But for services that reside in the background, such as [swoole](https://wiki.swoole.com/zh-cn/#/), you can enable entity configuration, parse the ruleset into entity classes to avoid the issue of repeated parsing.

-`enable_entity`: Default `false`.

Enabling entity does not affect the process of validating data in any way. If you are interested in the entity structure, please see [here](ENTITY.md).

### 4.11 Internationalization

Customize default error message templates for different methods. See [4.13 Error Message Template](#413-error-message-template) - point 3

**Internationalization Lists:**

Language | File Name | Class Name | Alias
---|---|---|---
English(Default) | EnUs.php | `EnUs` | `en-us`
Chinese | ZhCn.php | `ZhCn` | `zh-cn`

- Internationalization file names and class names are named using camel case.
- Modify the default error message template through [4.10 Customized Configuration](#410-customized-configuration).
- Modify the default error message template through the `set_language` interface. Supports using class names or alias as parameters.

```PHP
// Add configuration when instantiating the class
$validation_conf = [
    'language' => 'zh-cn',
];
$validation = new Validation($validation_conf);

// Or call the interface
$validation->set_language('zh-cn'); // The ZhCn.php Internationalization file will be loaded
```

**Add your internationalization files**

1. Create a file `/MyPath/MyLang.php`

If the method has a corresponding [Symbol](#appendix-1---methods-and-symbols), for example, the symbol of `equal` is `=`, then it is recommended to use the method symbol `=` for the key here.
If the method does not have a corresponding symbol, then only the method name can be used, such as `check_custom`

```PHP
<?php

class MyLang
{
    public $error_templates = [
        // Override error message template for default method =
        '=' => '@this must be equal to @p1(From MyLang)',
        // Added error message template for new method check_custom
        'check_custom' => '@this check_custom error!'
    ];
}
```

2. Configure the path to the internationalization file
```PHP
$validation->set_config(['lang_path' => '/MyPath/'])->set_language('MyLang');
```

**Use internationalization objects directly**
In fact, the method of internationalizing the file above ultimately calls the `custom_language` interface.
```PHP
// Must be an object
$MyLang = (object)[];
$MyLang->error_templates = [
    // Override error message template for default method =
    '=' => '@this must be equal to @p1(From MyLang)',
    // Added error message template for new method check_custom
    'check_custom' => '@this check_custom error!'
];

$validation->custom_language($MyLang, 'MyLang');
```

### 4.12 Validate Whole Data

By default, even if a field fails validation, all subsequent data will continue to be validated.
*You can set it to end the validation of subsequent fields immediately when any field validation fails.*
```PHP
// Add configuration when instantiating the class
$validation_conf = [
    'validation_global' => false,
];
$validation = new Validation($validation_conf);

// Or call the set_validation_global interface
$validation->set_validation_global(false);
```

### 4.13 Error Message Template

<u>**How to determine if method validation failed?**</u>
If and only if the method result `result === true`, it means the validation is successful, otherwise it means the validation fails.

<u>**When a field fails validation, you may want to**</u>
- Set an error message template for an entire ruleset
- Set an error message template for each rule in a ruleset

<u>**Then, you have several ways to set the error message template:**</u>
1. Set a unified template (general string) for the ruleset
2. Return the template (general string) directly from method
3. Set templates for each method
  3.1. Set temporary templates (JSON strings, etc.) in the ruleset
  3.2. Set default templates via [Internationalization](#411-internationalization)

Template **priority** from high to low: `1` > `2` > `3`

<u>**To support the method of setting templates individually, the requirements are:**</u>
- Method returns `false`: matches the error message template based on the method or its symbol
- Method returns tag: Match the error message template based on the returned tag
  *For example, `return 'TAG:='` matches the error message template based on `=`*

**Template variables**
Variable | Describe | Example
---|---|---
`@this` | Current field | `id` or `favorite_animation.name`
`@method` | Current method | `>` or `greater_than`
`@p{x}` | The xth parameter of the current field | `@p1` represents the value of the first parameter. e.g. `100`
`@t{x}` | The type of the xth parameter of the current field | `@t1` represents the type of the first parameter. e.g. `int`

**1. Set temporary template in ruleset**

Temporary templates are only enabled for the current ruleset and have no effect on other rulesets.

1.1 At the end of a ruleset, add the symbol "` >> `", note that there is a space at the start and end. For custom symbol, see [4.10 Customized Configuration](#410-customized-configuration)

1.1.1. **General string**：Indicates that any rule in the ruleset fails to validate, this error message will be returned.

*It takes effect for all rules for the current field. This error message template is used whether the method returns `false` or any other error templates*
```PHP
// required OR regular expression OR >=<= method，no matter which validation fails, the error is "id is incorrect."
"id" => 'required|/^\d+$/|>=<=[1,100] >> @this is incorrect.'
```

1.1.2. **JSON string**：Set an error message template for each method

*It only takes effect for the corresponding rules. If the method returns `false`, use the corresponding error message template*
```PHP
"id" => 'required|/^\d+$/|>=<=[1,100] >> { "required": "Users define - @this is required", "preg": "Users define - @this should be \"MATCHED\" @preg"}'
```
When any of the methods fails to validateand returns `false`, the corresponding error message tempalte is:
- `required`: Users define - id is required
- `/^\d+$/`: Users define - id should be "MATCHED" /^\d+$/
- `>=<=`: id must be greater than or equal to 1 and less than or equal to 100

1.1.3. ~~Exclusive string (not recommended)~~：Set an error message template for each method，same as JSON

```PHP
"id" => "required|/^\d+$/|>=<=[1,100] >> [required]=> Users define - @this is required [preg]=> Users define - @this should be \"MATCHED\" @preg"
```

1.2. **Error message template array**：Set an error message template for each method，same as JSON
- Key `0`: The rule
- Key `error_message`：Error message template array

Any extra key is not allowed.

```PHP
$rule = [
    "id" => [
        'required|/^\d+$/|>=<=[1,100]',
        'error_message' => [                        
            'required' => 'Users define - @this is required',
            'preg' => 'Users define - @this should be \"MATCHED\" @preg',
        ]
    ]
];
```

2. **Return the template directly in the method**

If and only if the method result `result === true`, it means the validation is successful, otherwise it means the validation fails.

So the method allows four types of error returns:
- Return `false` (Supports [Internationalization](#411-internationalization))
Match error message template based on method or its symbol, see [Appendix 1 - Methods And Symbols](#appendix-1---methods-and-symbols)
```PHP
return false;
```
- Return tag (Supports [Internationalization](#411-internationalization))
Match the error message template based on the returned tag. For example the tag `is_exclude_animal`
```PHP
return "TAG:is_exclude_animal";
```
- Return error message template string
```PHP
return "I don't like mouse";
```
- Returns an array of error messages template. There are two fields by default, `error_type` and `message`. You can add other extra fields if you need.
```PHP
return [
    "error_type" => "server_error",
    "message" => "I don't like snake",
    "extra" => "You scared me"
];
```

<details>
  <summary><span>&#128071;</span> <strong>Click to view the complete method return example</strong></summary>

```PHP
function check_animal($animal) {
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
            "message" => "TAG:is_fake_animals"
        ];
    }

    return true;
}
```

</details>
<br>

3. **Set default template via Internationalization**

The default template is effective for all rulesets, but may be overridden by temporary templates.
Refer to [4.11 Internationalization](#411-internationalization)

### 4.14 Error Message Format

There are four different error message formats:

- `ERROR_FORMAT_NESTED_GENERAL`: 'NESTED_GENERAL'
```JSON
{
     "A": {
         "1": "error_msg_A1",
         "2": {
             "a": "error_msg_A2a"
         }
     }
}
```
- `ERROR_FORMAT_NESTED_DETAILED`: 'NESTED_DETAILED'
This format is similar to the above format, except that the error information becomes an array and contains more error information.
- `ERROR_FORMAT_DOTTED_GENERAL`: 'DOTTED_GENERAL'
```JSON
{
     "A.1": "error_msg_A1",
     "A.2.a": "error_msg_A2a",
}
```
- `ERROR_FORMAT_DOTTED_DETAILED`: 'DOTTED_DETAILED'
This format is similar to the above format, except that the error information becomes an array and contains more error information.

For details, see [Appendix 3 -Error Message Format](#Appendix-3---Error Message Format)


---

## Appendix 1 - Methods And Symbols

Symbol | Method | Variable-Length Arguments | Error Message Template
---|---|:---:|---
`*` | `required` | No | @this can not be empty
`O:?` | `optional:when` | No | @this can be empty only when certain circumstances are met
`uuid` | `is_uuid` | No | @this must be a UUID
`>` | `greater_than` | No | @this must be greater than @p1
`length>=<=` | `length_between` | No | @this length must be greater than or equal to @p1 and less than or equal to @p2
`<number>` | `in_number_array` | **Yes** | @this must be numeric and in @p1
`date>` | `date_greater_than` | No | @this must be a valid date and greater than @p1

- `is_variable_length_argument`: The second parameter of the method is a [variable-length parameter](https://www.php.net/manual/en/functions.arguments.php#functions.variable-arg-list), that means all parameters after the first parameter in the ruleset will be treated as the child elements of the second parameter.

<details>
  <summary><span>&#128071;</span> <strong>Click to view Appendix 1 - Methods And Symbols</strong></summary>

Symbol | Method | Variable-Length Arguments | Error Message Template
---|---|:---:|---
/ | `default` | No | @this validation failed
`.*` | `index_array` | No | @this must be a numeric array
`:?` | `when` | No | Under certain circumstances, 
`:!?` | `when_not` | No | When certain circumstances are not met, 
`*` | `required` | No | @this can not be empty
`*:?` | `required:when` | No | Under certain circumstances, @this can not be empty
`*:!?` | `required:when_not` | No | When certain circumstances are not met, @this can not be empty
`O` | `optional` | No | @this never go wrong
`O:?` | `optional:when` | No | @this can be empty only when certain circumstances are met
`O:!?` | `optional:when_not` | No | @this can be empty only when certain circumstances are not met
`O!` | `optional_unset` | No | @this must be unset or must not be empty if it's set
`O!:?` | `optional_unset:when` | No | Under certain circumstances, @this must be unset or must not be empty if it's set. Otherwise it can not be empty
`O!:!?` | `optional_unset:when_not` | No | When certain circumstances are not met, @this must be unset or must not be empty if it's set. Otherwise it can not be empty
/ | `preg` | No | @this format is invalid, should be @preg
/ | `preg_format` | No | @this method @preg is not a valid regular expression
/ | `call_method` | No | @method is undefined
`=` | `equal` | No | @this must be equal to @p1
`!=` | `not_equal` | No | @this must be not equal to @p1
`==` | `strictly_equal` | No | @this must be strictly equal to @t1(@p1)
`!==` | `not_strictly_equal` | No | @this must not be strictly equal to @t1(@p1)
`>` | `greater_than` | No | @this must be greater than @p1
`<` | `less_than` | No | @this must be less than @p1
`>=` | `greater_equal` | No | @this must be greater than or equal to @p1
`<=` | `less_equal` | No | @this must be less than or equal to @p1
`><` | `greater_less` | No | @this must be greater than @p1 and less than @p2
`><=` | `greater_lessequal` | No | @this must be greater than @p1 and less than or equal to @p2
`>=<` | `greaterequal_less` | No | @this must be greater than or equal to @p1 and less than @p2
`>=<=` | `between` | No | @this must be greater than or equal to @p1 and less than or equal to @p2
`<number>` | `in_number_array` | **Yes** | @this must be numeric and in @p1
`!<number>` | `not_in_number_array` | **Yes** | @this must be numeric and can not be in @p1
`<string>` | `in_string_array` | **Yes** | @this must be string and in @p1
`!<string>` | `not_in_string_array` | **Yes** | @this must be string and can not be in @p1
`length=` | `length_equal` | No | @this length must be equal to @p1
`length!=` | `length_not_equal` | No | @this length must be not equal to @p1
`length>` | `length_greater_than` | No | @this length must be greater than @p1
`length<` | `length_less_than` | No | @this length must be less than @p1
`length>=` | `length_greater_equal` | No | @this length must be greater than or equal to @p1
`length<=` | `length_less_equal` | No | @this length must be less than or equal to @p1
`length><` | `length_greater_less` | No | @this length must be greater than @p1 and less than @p2
`length><=` | `length_greater_lessequal` | No | @this length must be greater than @p1 and less than or equal to @p2
`length>=<` | `length_greaterequal_less` | No | @this length must be greater than or equal to @p1 and less than @p2
`length>=<=` | `length_between` | No | @this length must be greater than or equal to @p1 and less than or equal to @p2
`int` | `integer` | No | @this must be integer
/ | `float` | No | @this must be float
/ | `string` | No | @this must be string
`array` | `is_array` | No | @this must be array
/ | `bool` | No | @this must be boolean
`bool=` | `bool_equal` | No | @this must be boolean @p1
/ | `bool_str` | No | @this must be boolean string
/ | `bool_string` | No | @this must be boolean string
`bool_string=` | `bool_string_equal` | No | @this must be boolean string @p1
`<keys>` | `require_array_keys` | **Yes** | @this must be array and its keys must contain and only contain @p1
/ | `file_base64` | No | @this must be a valid file base64
/ | `file_base64:mime` | No | @this file mine must be euqal to @p1
/ | `file_base64:size` | No | @this file size must be less than @p2kb
/ | `oauth2_grant_type` | No | @this is not a valid OAuth2 grant type
`email` | `is_email` | No | @this must be email
`url` | `is_url` | No | @this must be url
`ip` | `is_ip` | No | @this must be IP address
`ipv4` | `is_ipv4` | No | @this must be IPv4 address
`ipv6` | `is_ipv6` | No | @this must be IPv6 address
`mac` | `is_mac` | No | @this must be MAC address
/ | `dob` | No | @this must be a valid date
`uuid` | `is_uuid` | No | @this must be a UUID
`ulid` | `is_ulid` | No | @this must be a ULID
`alpha` | `is_alpha` | No | @this must only contain letters
`alpha_ext` | `is_alpha_ext` | No | @this must only contain letters and _-
/ | `alpha_ext:@p2` | No | @this must only contain letters and @p2
`alphanumeric` | `is_alphanumeric` | No | @this must only contain letters and numbers
`alphanumeric_ext` | `is_alphanumeric_ext` | No | @this must only contain letters and numbers and _-
/ | `alphanumeric_ext:@p2` | No | @this must only contain letters and numbers and @p2
`datetime` | `is_datetime` | No | @this must be a valid datetime
/ | `datetime:format:@p1` | No | @this must be a valid datetime in format @p1
/ | `datetime:format:@p2` | No | @this must be a valid datetime in format @p2
/ | `datetime:format:@p3` | No | @this must be a valid datetime in format @p3
/ | `datetime:invalid_format:@p1` | No | @this format @p1 is not a valid datetime format
/ | `datetime:invalid_format:@p2` | No | @this format @p2 is not a valid datetime format
/ | `datetime:invalid_format:@p3` | No | @this format @p3 is not a valid datetime format
`datetime=` | `datetime_equal` | No | @this must be a valid datetime and equal to @p1
`datetime!=` | `datetime_not_equal` | No | @this must be a valid datetime and not equal to @p1
`datetime>` | `datetime_greater_than` | No | @this must be a valid datetime and greater than @p1
`datetime>=` | `datetime_greater_equal` | No | @this must be a valid datetime and greater than or equal to @p1
`datetime<` | `datetime_less_than` | No | @this must be a valid datetime and less than @p1
`datetime<=` | `datetime_less_equal` | No | @this must be a valid datetime and less than or equal to @p1
`datetime><` | `datetime_greater_less` | No | @this must be a valid datetime and greater than @p1 and less than @p2
`datetime>=<` | `datetime_greaterequal_less` | No | @this must be a valid datetime and greater than or equal to @p1 and less than @p2
`datetime><=` | `datetime_greater_lessequal` | No | @this must be a valid datetime and greater than @p1 and less than or equal to @p2
`datetime>=<=` | `datetime_between` | No | @this datetime must be between @p1 and @p2
`date` | `is_date` | No | @this must be a valid date in format Y-m-d
/ | `date:format:@p1` | No | @this must be a valid date in format @p1
/ | `date:format:@p2` | No | @this must be a valid date in format @p2
/ | `date:format:@p3` | No | @this must be a valid date in format @p3
/ | `date:invalid_format:@p1` | No | @this format @p1 is not a valid date format
/ | `date:invalid_format:@p2` | No | @this format @p2 is not a valid date format
/ | `date:invalid_format:@p3` | No | @this format @p3 is not a valid date format
`date=` | `date_equal` | No | @this must be a valid date and equal to @p1
`date!=` | `date_not_equal` | No | @this must be a valid date and not equal to @p1
`date>` | `date_greater_than` | No | @this must be a valid date and greater than @p1
`date>=` | `date_greater_equal` | No | @this must be a valid date and greater than or equal to @p1
`date<` | `date_less_than` | No | @this must be a valid date and less than @p1
`date<=` | `date_less_equal` | No | @this must be a valid date and less than or equal to @p1
`date><` | `date_greater_less` | No | @this must be a valid date and greater than @p1 and less than @p2
`date>=<` | `date_greaterequal_less` | No | @this must be a valid date and greater than or equal to @p1 and less than @p2
`date><=` | `date_greater_lessequal` | No | @this must be a valid date and greater than @p1 and less than or equal to @p2
`date>=<=` | `date_between` | No | @this date must be between @p1 and @p2
`time` | `is_time` | No | @this must be a valid time in format H:i:s
/ | `time:format:@p1` | No | @this must be a valid time in format @p1
/ | `time:format:@p2` | No | @this must be a valid time in format @p2
/ | `time:format:@p3` | No | @this must be a valid time in format @p3
/ | `time:invalid_format:@p1` | No | @this format @p1 is not a valid time format
/ | `time:invalid_format:@p2` | No | @this format @p2 is not a valid time format
/ | `time:invalid_format:@p3` | No | @this format @p3 is not a valid time format
`time=` | `time_equal` | No | @this must be a valid time and equal to @p1
`time!=` | `time_not_equal` | No | @this must be a valid time and not equal to @p1
`time>` | `time_greater_than` | No | @this must be a valid time and greater than @p1
`time>=` | `time_greater_equal` | No | @this must be a valid time and greater than or equal to @p1
`time<` | `time_less_than` | No | @this must be a valid time and less than @p1
`time<=` | `time_less_equal` | No | @this must be a valid time and less than or equal to @p1
`time><` | `time_greater_less` | No | @this must be a valid time and greater than @p1 and less than @p2
`time>=<` | `time_greaterequal_less` | No | @this must be a valid time and greater than or equal to @p1 and less than @p2
`time><=` | `time_greater_lessequal` | No | @this must be a valid time and greater than @p1 and less than or equal to @p2
`time>=<=` | `time_between` | No | @this time must be between @p1 and @p2

</details>
</br>

If you don't know how to use any of the methods in the appendix, search for `test_method_` + the method name in the `tests` folder. For example:
- `test_method_is_uuid`
- `test_method_datetime_between`

There are detailed examples inside.

*No usage documentation for the methods currently.*

---

## Appendix 2 - Validation Complete Example

Imagine that if the user data is as follows, it contains associative arrays and index arrays. How do we set the rules to validate it, and how do we make it simple and intuitive?

```PHP
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
```
<details>
  <summary><span>&#128071;</span> <strong>Click to view code</strong></summary>

```PHP
// $data - The above data to be validated 
function validate($data) {
    // Set validation rules
    $rule = [
        "id" => "required|/^\d+$/",         // id must not be empty, and must be numbers
        "name" => "required|length><=[3,32]",  // name must not be empty, and the string length must be greater than 3 and less than or equal to 32
        "favorite_animation" => [
            // favorite_animation.name must not be empty, and the string length must be greater than 1 and less than or equal to 64
            "name" => "required|length><=[1,16]",
            // favorite_animation.release_date is optional. If not empty, the string length must be greater than 4 and less than or equal to 64
            "release_date" => "optional|length><=[4,64]",
            // "*" indicates favorite_animation.series_directed_by is an index array
            "series_directed_by" => [
                // favorite_animation.series_directed_by.* each child field must meet its rules:  cannot be empty and its length must be greater than 3
                "*" => "required|length>[3]"
            ],
            // [optional] indicates favorite_animation.series_cast is optional
            // ".*"(Same as above “*”) indicates favorite_animation.series_cast is an index array, and each sub-field is an associative array.
            "series_cast" => [
                "[optional].*" => [
                    // favorite_animation.series_cast.*.actor cannot be empty and the length must be greater than 3 and must match the regular expression
                    "actor" => "required|length>[3]|/^[A-Za-z ]+$/",
                    // favorite_animation.series_cast.*.character can not be empty and length must be greater than 3
                    "character" => "required|length>[3]",
                ]
            ]
        ]
    ];
    
    $config = [];
    // Accepts a custom configuration array, but not necessary
    $validation = new Validation($config);

    // Set validation rules and validate data, return true if successful, false if failed
    if ($validation->set_rules($rule)->validate($data)) {
        // Get the validation result here. There are parameters validated by the rule {$rule}. If successful, modify the field value to true. If failed, modify the field value to error information.
        // Parameters that have not been validated remain unchanged. For example, age remains unchanged at 18.
        return $validation->get_result();
    } else {
        // There are four error message formats to choose from. Default Validation::ERROR_FORMAT_DOTTED_GENERAL
        return $validation->get_error();
    }
}

// You can find an error message format that you like by changing the parameters of get_error.
// The $data in the example basically does not satisfy the $rule. You can change the value of $data to check whether the validation rules are correct.
echo json_encode(validate($data), JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE) . "\n";
```

The result is

```JSON
{
    "name": "name length must be greater than 3 and less than or equal to 32",
    "favorite_animation.name": "favorite_animation.name length must be greater than 1 and less than or equal to 16",
    "favorite_animation.series_directed_by.0": "favorite_animation.series_directed_by.0 can not be empty",
    "favorite_animation.series_cast.1.actor": "favorite_animation.series_cast.1.actor format is invalid, should be /^[A-Za-z ]+$/"
}
```
For more error message formats, see [Appendix 3 - Error Message Format](#appendix-3---error-message-format)

</details>

---
## Appendix 3 - Error Message Format

### One-dimensional String Structure
```PHP
// Default Validation::ERROR_FORMAT_DOTTED_GENERAL
$validation->get_error();
```

```JSON
{
    "name": "name length must be greater than 3 and less than or equal to 32",
    "favorite_animation.name": "favorite_animation.name length must be greater than 1 and less than or equal to 16",
    "favorite_animation.series_directed_by.0": "favorite_animation.series_directed_by.0 can not be empty",
    "favorite_animation.series_cast.1.actor": "favorite_animation.series_cast.1.actor format is invalid, should be /^[A-Za-z ]+$/"
}
```

### One-dimensional Associative Structure
```PHP
$validation->get_error(Validation::ERROR_FORMAT_DOTTED_DETAILED);
```

```JSON
{
    "name": {
        "error_type": "validation",
        "message": "name length must be greater than 3 and less than or equal to 32"
    },
    "favorite_animation.name": {
        "error_type": "validation",
        "message": "favorite_animation.name length must be greater than 1 and less than or equal to 16"
    },
    "favorite_animation.series_directed_by.0": {
        "error_type": "required_field",
        "message": "favorite_animation.series_directed_by.0 can not be empty"
    },
    "favorite_animation.series_cast.1.actor": {
        "error_type": "validation",
        "message": "favorite_animation.series_cast.1.actor format is invalid, should be /^[A-Za-z ]+$/"
    }
}
```

### Infinitely Nested String Structure
```PHP
$validation->get_error(Validation::ERROR_FORMAT_NESTED_GENERAL);
```

```JSON
{
    "name": "name length must be greater than 3 and less than or equal to 32",
    "favorite_animation": {
        "name": "favorite_animation.name length must be greater than 1 and less than or equal to 16",
        "series_directed_by": [
            "favorite_animation.series_directed_by.0 can not be empty"
        ],
        "series_cast": {
            "1": {
                "actor": "favorite_animation.series_cast.1.actor format is invalid, should be /^[A-Za-z ]+$/"
            }
        }
    }
}
```

### Infinitely Nested Associative Structure
```PHP
$validation->get_error(Validation::ERROR_FORMAT_NESTED_DETAILED);
```

```JSON
{
    "name": {
        "error_type": "validation",
        "message": "name length must be greater than 3 and less than or equal to 32"
    },
    "favorite_animation": {
        "name": {
            "error_type": "validation",
            "message": "favorite_animation.name length must be greater than 1 and less than or equal to 16"
        },
        "series_directed_by": [
            {
                "error_type": "required_field",
                "message": "favorite_animation.series_directed_by.0 can not be empty"
            }
        ],
        "series_cast": {
            "1": {
                "actor": {
                    "error_type": "validation",
                    "message": "favorite_animation.series_cast.1.actor format is invalid, should be /^[A-Za-z ]+$/"
                }
            }
        }
    }
}
```

---
