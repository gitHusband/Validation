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
      * [4.4 Method Extension](#44-method-extension)
      * [4.5 Series And Parallel Rules](#45-series-and-parallel-rules)
      * [4.6 Conditional Rules](#46-conditional-rules)
         * [The When Conditional Rules](#the-when-conditional-rules)
         * [Standard Conditional Rules](#standard-conditional-rules)
      * [4.7 Infinitely Nested Data Structures](#47-infinitely-nested-data-structures)
      * [4.8 Optional Field](#48-optional-field)
      * [4.9 Special Validation Rules](#49-special-validation-rules)
      * [4.10 Customized Configuration](#410-customized-configuration)
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
- One field corresponds to one validation rule, and a rule consists of multiple validation methods (functions).
- The validation method supports the substitution of symbol, which is easy to understand and simplifies the rules. e.g. `*`, `>`, `<`, `len>`
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
    "name" => "required|len<=>[3,32]",  // Must not be empty and the string length must be greater than 3 and less than or equal to 32
    "favorite_animation" => [
        "name" => "required|len<=>[1,64]",          // Must not be empty, the string length must be greater than 1 and less than 64
        "release_date" => "optional|len<=>[4,64]",  // Optional, If it is not empty, then the string length must be greater than 4 and less than or equal to 64
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

After cloning this project, please generate the project's automatic loading file by:
```BASH
$ composer dump-autoload
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
Generally, a field corresponds to a validation rule, and a rule consists of multiple validation methods (functions).
In order to facilitate understanding and simplify the rules, some method **symbols** are allowed to represent actual methods (functions).

```PHP
// The name must not be empty, must be a string, and the length must be greater than 3 and less than or equal to 32
"name" => "required|string|length_greater_lessequal[3,32]"

// Use method symbols, same as above
// If you think the method symbols difficult to understand, please just use the full name of the methods.
"name" => "*|string|len<=>[3,32]"
```

For example:

Symbol | Method | Desc
---|---|---
`*` | `required` | Required, not allowed to be empty
`O` | `optional` | Optional, allowed not to be set or empty
`>[20]` | `greater_than` | Number must be greater than 20
`len<=>[2,16]` | `length_greater_lessequal` | Character length must be greater than 2 and less than or equal to 16
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

**Parameters List**

Parameter | Desc
---|---
Static Value | Indicates that the parameter is a static string and is allowed to be empty. For example `20`
@this | Indicates that the parameter is the value of the current field
@parent | Indicates the parameter is the value of the parent of the current field
@root | Indicates that this parameter is the whole validation data
@field_name | Indicates that the parameter is the value of a field whose name is `field_name`. e.g. `@age`

**Parameter separator:**
- `symbol_parameter_separator`: `,`
  Parameters separator to split the parameter string of a method into multiple parameters; e.g. `equal(@this,1)`
- `is_strict_parameter_separator`: false
  1. false - Fast way to parse parameters but not support `,` as part of a parameter;
  2. true - Slow but support `,` and `array`.
    e.g. `my_method[[1,2,3],100]`: Two parameters and both of them are string: `[1,2,3]` and `100`

**Parameter Type:**
- `is_strict_parameter_type`: false
  1. false - All the parameters type is string;
  2. true - Detect the parameters type and forcibly convert to the corresponding type. Whether a string can be converted depends on whether it is enclosed in double quotes (`"`) or single quotes (`''`).
    e.g. `my_method[[1,"2",'3'],100,false,"true"]`:
        - `[1,"2",'3']` will be converted to `array([1,"2","3"])`
        - `100` will be converted to `int(100)`
        - `false` will be converted to `bool(false)`
        - `"true"` will be converted to `string(true)`

For custom parameter separator and parameter type, see [4.10 Customized Configuration](#410-customized-configuration)

### 4.4 Method Extension
There are some validation methods built in the Validation tool, such as `*`, `>`, `len>=`, `ip` and so on. 
For details, refer to [Appendix 1 - Methods And Symbols](#appendix-1---methods-and-symbols)

If the validation rules are complex and the built-in methods cannot meet your needs, you can extend your own methods.

If the method may return different error messages based on different judgments, see the section [4.13 Error message template - 3. Return the template directly in the method](#413-error-message-template).

There are three ways to extend your own methods:
1. **Register new method by**：`add_method`

<details>
  <summary><span>&#128071;</span> <strong>Click to view code</strong></summary>

```PHP
// Register a new method, check_id
$validation->add_method('check_id', function ($id) {
    if ($id == 0) {
        return false;
    }

    return true;
});

// The rule is
$rule = [
    // Must not be empty, must be a number and must be not equal to 0
    "id" => "required|/^\d+$/|check_id",
];
```

</details>

2. **Extend `Validation` class**

Extend the `Validation` class and override the built-in methods or add new built-in methods. Recommended [trait](https://www.php.net/manual/zh/language.oop5.traits.php)

<details>
  <summary><span>&#128071;</span> <strong>Click to view code</strong></summary>
 
```PHP
use githusband\Validation;

/**
 * 1. It is recommended to use traits to extend validation methods
 * If you need to define method symbols, put them in an attribute. The attribute naming rule is: "method_symbols_of_" + class name (high camel case converted to underline)
 */
trait RuleCustome
{
    protected $method_symbols_of_rule_custome = [
        '=1' => 'euqal_to_1',
    ];

    protected function euqal_to_1($data)
    {
        return $data == 1;
    }
}

/**
 * 2. Extend the class and directly add validation methods
 * If you need to define method symbols, place them in an attribute named method_symbols
 */
class MyValidation extends Validation
{
    use RuleCustome;

    protected $method_symbols = [
        ">=1" => "grater_than_or_equal_to_1",
    ];

    protected function grater_than_or_equal_to_1($data)
    {
        return $data >= 1;
    }
}

/**
 * The rule is
 */
$rule = [
    // id must not be empty, and must be greater than or equal to 1
    // ">=1" is a method symbols, corresponding to the method named "grater_than_or_equal_to_1"
    "id" => "required|>=1",
    // parent_id is optional, if not empty, it must be equal to 1
    "parent_id" => "optional|euqal_to_1",
];
```

</details>

- 3. **Global function**
Including the system functions and user-defined global functions.

**The priority of the three way**
`add_method` > `built-in methods` > `global function`

If the method can not be found from the three way, an error will be reported: Undefined

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
"height_unit" => "required|(s)[cm,m]",
// A. Parallel: The rule can be like this, [or] can be replaced by its symbol [||]
"height[or]" => [
    // If the height_unit is cm(centimeter), the height must be greater than or equal to 100 and less than or equal to 200
    "required|=(@height_unit,cm)|<=>=[100,200]",
    // If the height_unit is m(meter), the height must be greater than or equal to 1 and less than or equal to 2
    "required|=(@height_unit,m)|<=>=[1,2]",
]
// B. Parallel: The rules can also be like this, and the symblo [||] can be replaced by [or]
"height" => [
    "[||]" => [
        "required|=(@height_unit,cm)|<=>=[100,200]",
        "required|=(@height_unit,m)|<=>=[1,2]",
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
    "id" => "required|<>[0,10]",
    // When the id is less than 5, the name can only be a number and its length must be greater than 2
    // When the id is greater than or equal to 5, the name can be any string and its length must be greater than 2
    "name" => "/^\d+$/:when(<(@id,5))|len>[2]",
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
    "attribute" => "required|(s)[height,weight]",
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
    "attribute" => "required|(s)[height,weight]",
    // If the attribute is not weight, then the centimeter must not be empty
    // If the attribute is weight, then the centimeter is optional
    // However, if the value of the centimeter is not empty, it must be greater than 180
    "centimeter" => "required:when_not(=(@attribute,weight))|required|>[180]",
];
```

#### Standard Conditional Rules

The usage of standard conditional rules is similar to PHP syntax, `if()` and `!if()`

- **Positive standard conditional rule**: `if()`

1. If the condition is met, continue to validate subsequent methods
2. If the condition is not met, don't continue to validate subsequent methods

```PHP
$rule = [
    // The attribute must not be empty, and it must be "height" or "weight"
    "attribute" => "required|(s)[height,weight]",
    // If the attribute is height, then the centimeter must not be empty and must be greater than 180
    // If the attribute is not height，then the subsequent rules will not be validated, that is, the centimeter can be any value.
    "centimeter" => "if(=(@attribute,height))|required|>[180]",
];
```
- **Negative standard conditional rule**: `!if()`

1. If the condition is not met, continue to validate subsequent methods
2. If the condition is met, don't continue to validate subsequent methods

```PHP
$rule = [
    // The attribute must not be empty, and it must be "height" or "weight"
    "attribute" => "required|(s)[height,weight]",
    // If the attribute is not weight, then the centimeter must not be empty and must be greater than 180
    // If the attribute is weight，then the subsequent rules will not be validated, that is, the centimeter can be any value.
    "centimeter" => "!if(=(@attribute,weight))|required|>[180]",
];
```

Apologize, *Standard Conditional Rules does not currently support `else` and `else if`, it will be supported in subsequent versions.*

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
    "name" => "required|len>[3]",
    "favourite_fruit" => [
        "name" => "required|len>[3]",
        "color" => "required|len>[3]",
        "shape" => "required|len>[3]"
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
    "name" => "required|len>[3]",
    "favourite_color.*" => "required|len>[3]",
    "favourite_fruits.*" => [
        "name" => "required|len>[3]",
        "color" => "required|len>[3]",
        "shape" => "required|len>[3]"
    ]
];

// You can also write it like this
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
```

</details>

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
    'validation_global' => true,                                // 1. true - validate all rules even though previous rule had been failed; 2. false - stop validating when any rule is failed
    'auto_field' => "data",                                     // If root data is string or numberic array, add the auto_field as the root data field name
    'reg_msg' => '/ >> (.*)$/',                                 // Set the error message format for all the methods after a rule string
    'reg_preg' => '/^(\/.+\/.*)$/',                             // If a rule match reg_preg, indicates it's a regular expression instead of method
    'reg_preg_strict' => '/^(\/.+\/[imsxADSUXJun]*)$/',         // Verify if a regular expression is valid
    'reg_ifs' => '/^!?if\((.*)\)/',                             // A regular expression to match both reg_if and reg_if_not
    'reg_if' => '/^if\((.*)\)/',                                // If match reg_if, validate this condition first, if true, then continue to validate the subsequnse rule
    'reg_if_not' => '/^!if\((.*)\)/',                           // If match reg_if_not, validate this condition first, if false, then continue to validate the subsequnse rule
    'symbol_rule_separator' => '|',                             // Serial rules seqarator to split a rule into multiple methods
    'symbol_parallel_rule' => '[||]',                           // Symbol of the parallel rule, Same as "[or]"
    'symbol_method_standard' => '/^([^\\(]*)\\((.*)\\)$/',      // Standard method format, e.g. equal(@this,1)
    'symbol_method_omit_this' => '/^([^\\[]*)\\[(.*)\\]$/',     // @this omitted method format, will add a @this parameter at first. e.g. equal[1]
    'symbol_parameter_separator' => ',',                        // Parameters separator to split the parameter string of a method into multiple parameters, e.g. equal(@this,1)
    'is_strict_parameter_separator' => false,                   // 1. false - Fast way to parse parameters but not support "," as part of a parameter; 2. true - Slow but support "," and array
    'is_strict_parameter_type' => false,                        // 1. false - all the parameters type is string; 2. true - Detect the parameters type, e.g. 123 is int, "123" is string
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
    "name" => "!*&&len<=>~3+32",        // Must not be empty and the string length must be greater than 3 and less than or equal to 32
    "favorite_animation" => [
        "name" => "!*&&len<=>~1+64",                // Must not be empty, the string length must be greater than 1 and less than 64
        "release_date" => "o?&&len<=>#@this+4+64",  // Optional, If it is not empty, then the string length must be greater than 4 and less than or equal to 64
    ]
];
```
Has it become more beautiful? <span>&#128525;</span> <strong style="font-size: 20px">Come and try it!</strong>

### 4.11 Internationalization

Customize default error message templates for different methods. See [4.13 Error Message Template](#413-error-message-template) - point 2

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

**When a field fails validation, you may want to**
- Set an error message template for an entire rule
- Set an error message template for each method

**Then, you have three ways to set the error message template:**
1. Set template in rules array
2. Set template via [Internationalization](#411-internationalization)
3. Return the template directly in the method

Template **priority** from high to low: `1` > `2` > `3`

**Template variables**
Variable | Describe | Example
---|---|---
`@this` | current field | `id` or `favorite_animation.name`
`@p{x}` | The xth parameter of the current field | `@p1` represents the value of the first parameter. e.g. `100`
`@t{x}` | The type of the xth parameter of the current field | `@t1` represents the type of the first parameter. e.g. `int`

**1. Set template in rules array**

1.1 At the end of a rule, add the symbol "` >> `", note that there is a space at the start and end. For custom symbol, see [4.10 Customized Configuration](#410-customized-configuration)

1.1.1. **Common string**：Indicates that no matter whether any method in the rule fails to validate, this error message will be returned.
```PHP
// required OR regular expression OR <=>= method，no matter which validation fails, the error is "id is incorrect."
"id" => 'required|/^\d+$/|<=>=[1,100] >> @this is incorrect.'
```

1.1.2. **JSON string**：Set an error message template for each method

```PHP
"id" => 'required|/^\d+$/|<=>=[1,100] >> { "required": "Users define - @this is required", "preg": "Users define - @this should be \"MATCHED\" @preg"}'
```
When any of the methods fails to validate, the corresponding error message is:
- `required`: Users define - id is required
- `/^\d+$/`: Users define - id should be "MATCHED" /^\d+$/
- `<=>=`: id must be greater than or equal to 1 and less than or equal to 100

1.1.3. ~~Exclusive string (not recommended)~~：Set an error message template for each method，same as JSON

```PHP
"id" => "required|/^\d+$/|<=>=[1,100] >> [required]=> Users define - @this is required [preg]=> Users define - @this should be \"MATCHED\" @preg"
```

1.2. **Error message template array**：Set an error message template for each method，same as JSON
- Key `0`: The rule
- Key `error_message`：Error message template array

Any extra key is not allowed.

```PHP
$rule = [
    "id" => [
        'required|/^\d+$/|<=>=[1,100]',
        'error_message' => [                        
            'required' => 'Users define - @this is required',
            'preg' => 'Users define - @this should be \"MATCHED\" @preg',
        ]
    ]
];
```

2. **Set template via Internationalization**
Refer to [4.11 Internationalization](#411-internationalization)

3. **Return the template directly in the method**

If and only if the method result `result === true`, it means the validation is successful, otherwise it means the validation fails.

So the method allows three types of error returns:
- Return `false`
- Return error message template string
- Returns an array of error messages template. There are two fields by default, `error_type` and `message`. You can add other extra fields if you need.

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
    }

    return true;
}
```

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

<details>
  <summary><span>&#128071;</span> <strong>Click to view Appendix 1 - Methods And Symbols</strong></summary>

Symbol | Method | Error Message Template
---|---|---
/ | `default` | @this validation failed
`.*` | `index_array` | @this must be a numeric array
`:?` | `when` | Under certain circumstances, 
`:!?` | `when_not` | When certain circumstances are not met, 
`*` | `required` | @this can not be empty
`*:?` | `required:when` | Under certain circumstances, @this can not be empty
`*:!?` | `required:when_not` | When certain circumstances are not met, @this can not be empty
`O` | `optional` | @this never go wrong
`O:?` | `optional:when` | @this can be empty only when certain circumstances are met
`O:!?` | `optional:when_not` | @this can be empty only when certain circumstances are not met
`O!` | `optional_unset` | @this must be unset or must not be empty if it's set
`O!:?` | `optional_unset:when` | Under certain circumstances, @this must be unset or must not be empty if it's set. Otherwise it can not be empty
`O!:!?` | `optional_unset:when_not` | When certain circumstances are not met, @this must be unset or must not be empty if it's set. Otherwise it can not be empty
/ | `preg` | @this format is invalid, should be @preg
/ | `preg_format` | @this method @preg is not a valid regular expression
/ | `call_method` | @method is undefined
`=` | `equal` | @this must be equal to @p1
`!=` | `not_equal` | @this must be not equal to @p1
`==` | `strictly_equal` | @this must be strictly equal to @t1(@p1)
`!==` | `not_strictly_equal` | @this must not be strictly equal to @t1(@p1)
`>` | `greater_than` | @this must be greater than @p1
`<` | `less_than` | @this must be less than @p1
`>=` | `greater_than_equal` | @this must be greater than or equal to @p1
`<=` | `less_than_equal` | @this must be less than or equal to @p1
`<>` | `between` | @this must be greater than @p1 and less than @p2
`<=>` | `greater_lessequal` | @this must be greater than @p1 and less than or equal to @p2
`<>=` | `greaterequal_less` | @this must be greater than or equal to @p1 and less than @p2
`<=>=` | `greaterequal_lessequal` | @this must be greater than or equal to @p1 and less than or equal to @p2
`(n)` | `in_number` | @this must be numeric and in @p1
`!(n)` | `not_in_number` | @this must be numeric and can not be in @p1
`(s)` | `in_string` | @this must be string and in @p1
`!(s)` | `not_in_string` | @this must be string and can not be in @p1
`len=` | `length_equal` | @this length must be equal to @p1
`len!=` | `length_not_equal` | @this length must be not equal to @p1
`len>` | `length_greater_than` | @this length must be greater than @p1
`len<` | `length_less_than` | @this length must be less than @p1
`len>=` | `length_greater_than_equal` | @this length must be greater than or equal to @p1
`len<=` | `length_less_than_equal` | @this length must be less than or equal to @p1
`len<>` | `length_between` | @this length must be greater than @p1 and less than @p2
`len<=>` | `length_greater_lessequal` | @this length must be greater than @p1 and less than or equal to @p2
`len<>=` | `length_greaterequal_less` | @this length must be greater than or equal to @p1 and less than @p2
`len<=>=` | `length_greaterequal_lessequal` | @this length must be greater than or equal to @p1 and less than or equal to @p2
`int` | `integer` | @this must be integer
/ | `float` | @this must be float
/ | `string` | @this must be string
`arr` | `is_array` | @this must be array
/ | `bool` | @this must be boolean
`bool=` | `bool_equal` | @this must be boolean @p1
/ | `bool_str` | @this must be boolean string
`bool_str=` | `bool_str_equal` | @this must be boolean string @p1
/ | `email` | @this must be email
/ | `url` | @this must be url
/ | `ip` | @this must be IP address
/ | `mac` | @this must be MAC address
/ | `dob` | @this must be a valid date
/ | `file_base64` | @this must be a valid file base64
/ | `uuid` | @this must be a UUID
/ | `oauth2_grant_type` | @this is not a valid OAuth2 grant type

</details>

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
        "name" => "required|len<=>[3,32]",  // name must not be empty, and the string length must be greater than 3 and less than or equal to 32
        "favorite_animation" => [
            // favorite_animation.name must not be empty, and the string length must be greater than 1 and less than or equal to 64
            "name" => "required|len<=>[1,16]",
            // favorite_animation.release_date is optional. If not empty, the string length must be greater than 4 and less than or equal to 64
            "release_date" => "optional|len<=>[4,64]",
            // "*" indicates favorite_animation.series_directed_by is an index array
            "series_directed_by" => [
                // favorite_animation.series_directed_by.* each child field must meet its rules:  cannot be empty and its length must be greater than 3
                "*" => "required|len>[3]"
            ],
            // [optional] indicates favorite_animation.series_cast is optional
            // ".*"(Same as above “*”) indicates favorite_animation.series_cast is an index array, and each sub-field is an associative array.
            "series_cast" => [
                "[optional].*" => [
                    // favorite_animation.series_cast.*.actor cannot be empty and the length must be greater than 3 and must match the regular expression
                    "actor" => "required|len>[3]|/^[A-Za-z ]+$/",
                    // favorite_animation.series_cast.*.character can not be empty and length must be greater than 3
                    "character" => "required|len>[3]",
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
