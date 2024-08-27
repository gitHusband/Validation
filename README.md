<p align="center"><a href="README-EN.md">English</a> | 中文</p>

<p align=center>
    <strong style="font-size: 35px">Validation</strong>
</p>
<p align="center"><b>简单，直观，客制化</b></p>
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

目录
=================

* [Validation —— 一款 PHP 直观的数据验证器](#validation--一款-php-直观的数据验证器)
   * [1. 简介](#1-简介)
      * [1.1 特点](#11-特点)
      * [1.2 一个例子](#12-一个例子)
   * [2. 安装](#2-安装)
   * [3. 开发](#3-开发)
   * [4. 功能介绍](#4-功能介绍)
      * [4.1 方法及其标志](#41-方法及其标志)
      * [4.2 正则表达式](#42-正则表达式)
      * [4.3 方法传参](#43-方法传参)
        * [传参的几种方式](#传参的几种方式)
        * [参数类型](#参数类型)
      * [4.4 自定义方法](#44-自定义方法)
        * [自定义方法的几种方式](#自定义方法的几种方式)
        * [设置方法标志](#设置方法标志)
      * [4.5 串联并联规则](#45-串联并联规则)
      * [4.6 条件规则](#46-条件规则)
         * [When 条件规则](#when-条件规则)
         * [IF 条件规则](#if-条件规则)
      * [4.7 无限嵌套的数据结构](#47-无限嵌套的数据结构)
         * [数组自身的规则](#数组自身的规则)
      * [4.8 可选字段](#48-可选字段)
      * [4.9 特殊的验证规则](#49-特殊的验证规则)
      * [4.10 客制化配置](#410-客制化配置)
         * [启用实体（重点）](#启用实体重点)
      * [4.11 国际化](#411-国际化)
      * [4.12 验证全部数据](#412-验证全部数据)
      * [4.13 错误信息模板](#413-错误信息模板)
      * [4.14 错误信息格式](#414-错误信息格式)
   * [附录 1 - 方法标志及其含义](#附录-1---方法标志及其含义)
   * [附录 2 - 验证完整示例](#附录-2---验证完整示例)
   * [附录 3 - 错误信息格式](#附录-3---错误信息格式)
      * [一维字符型结构](#一维字符型结构)
      * [一维关联型结构](#一维关联型结构)
      * [无限嵌套字符型结构](#无限嵌套字符型结构)
      * [无限嵌套关联型结构](#无限嵌套关联型结构)


# Validation —— 一款 PHP 直观的数据验证器

Validation 用于对数据合法性的检查。
目标只有 10 个字 - **规则结构即是数据结构**。

> [github 仓库](https://github.com/gitHusband/Validation)

**期待任何形式的贡献，让我们一起开发或优化 Validation！谢谢！**

<details>
    <summary><span>&#128587;</span> <strong>为什么写这个工具？</strong></summary>

1. 对于API的参数，理论上对每个参数都应该进行合法性检查，尤其是那些需要转发给其他API接口或者需要存储到数据库的参数。
*比如，数据库基本上对数据长度类型等有限制，对于长度的验证可谓是简单繁琐，使用该工具可以大大简化代码。*
2. 如果API参数过多，验证的代码量势必很大，无法直观通过代码明白参数格式。
3. 只需定制一个验证规则数组，规则数组长啥样，请求参数就长啥样。
4. 方便地多样化地设置验证方法返回的错误信息
5. ~~暂时想不到，想到了再给你们编。~~ <span>&#128054;</span>

</details>

## 1. 简介
### 1.1 特点
- 一个字段对应一个验证规则集，一个规则集由多个验证规则和方法（函数）组成。
- 验证方法支持用标志代替，易于理解，简化规则。采用`*`, `>`, `<`, `length>` 等方法标志，比如 `*` 表示必要的
- 支持正则表达式
<details>
    <summary><span>&#128071;</span> 点击查看更多特性...</summary>

- 支持方法传参。如 `@this` 代表当前字段值
- 支持拓展方法
- 支持串联验证：一个参数多个方法必须全部满足
- 支持并联验证：一个参数多个规则满足其一即可
- 支持条件验证：条件满足则继续验证后续方法，不满足则表明该字段是可选的
- 支持无限嵌套的数据结构，包括关联数组，索引数组
- 支持特殊的验证规则
- 支持客制化配置。比如多个方法的分隔符号默认 `|`，可以改为其他字符，如 `;`
- 支持国际化。默认英语，支持自定义方法返回错误信息
- 支持一次性验证全部数据(默认)，也可设置参数验证失败后立即结束验证
- 支持自定义错误信息，支持多种格式的错误信息，无限嵌套或者一维数组的错误信息格式
- ~~暂时想不到，想到了再给你们编。~~ <span>&#128054;</span>

</details>

### 1.2 一个例子
```PHP
use githusband\Validation;

// 待验证参数的简单示例。实际上无论参数多么复杂，都支持一个验证规则数组完成验证
$data = [
    "id" => 1,
    "name" => "Devin",
    "age" => 18,
    "favorite_animation" => [
        "name" => "A Record of A Mortal's Journey to Immortality",
        "release_date" => "July 25, 2020 (China)"
    ]
];

// 验证规则数组。规则数组的格式与待验证参数的格式相同。
$rule = [
    "id" => "required|/^\d+$/",         // 必要的，且只能是数字
    "name" => "required|length><=[3,32]",  // 必要的，且字符串长度必须大于3，小于等于32
    "favorite_animation" => [
        "name" => "required|length><=[1,64]",          // 必要的，且字符串长度必须大于1，小于等于64
        "release_date" => "optional|length><=[4,64]",  // 可选的，如果不为空，那么字符串长度必须大于4，小于等于64
    ]
];

$config = [];
// 实例化类，接受一个自定义配置数组，但不必要
$validation = new Validation($config);

// 设置验证规则并验证数据，成功返回 true，失败返回 false
if ($validation->set_rules($rule)->validate($data)) {
    // 这里获取验证结果，有被规则{$rule}验证到的参数，成功则修改其值为true，失败则修改其值为错误信息，
    // 没有被验证到的参数，保持原值不变。比如 age 保持 18 不变。
    return $validation->get_result();
} else {
    // 一共有四种错误信息格式可供选择。默认 Validation::ERROR_FORMAT_DOTTED_GENERAL
    return $validation->get_error();
}
```

理论上，该工具是用于验证复杂的数据结构的，但如果你想验证单一字符串，也可以，例如

```PHP
$validation->set_rules("required|string")->validate("Hello World!");
```

- 以上仅展示简单示例，实际上无论请求参数多么复杂，都支持一个验证规则数组完成验证。参考 [附录 2 - 验证完整示例](#附录-2---验证完整示例)
- 规则写的太丑了？参考 [4.10 客制化配置](#410-客制化配置)

## 2. 安装
```BASH
$ composer require githusband/validation
```

## 3. 开发
如果你有想法优化开发本工具，以下将为你提供帮助：

在 `src/Test` 目录下已经内置了单元测试类 `Unit.php` 和 文档测试类 `Readme.php`
通过 [Composer Script](https://getcomposer.org/doc/articles/scripts.md) 调用它们。

~~克隆本项目后，请先加载项目的自动加载文件：~~
```BASH
$ composer dump-autoload --dev
```

由于测试文件中使用了部分外部库，如 `uuid`, 我们不能简单地加载项目的自动加载文件，而需要下载外部库,
```BASH
$ composer install
```

- **单元测试类**

这里包含了所有功能的测试，只包含部分内置函数。
原则上修改代码后跑一遍单元测试，确保功能正常。

```BASH
// 测试所有例子，并打印 debug 信息。
$ VALIDATION_LOG_LEVEL=1 composer run-script test
// 测试单一例子，例如，测试正则表达式
$ composer run-script test test_regular_expression
```

如果你已经安装 [Docker](https://www.docker.com/products/docker-desktop/)，那么，你可以一次性测试多个 `PHP` 版本： [PHP v5.6](https://hub.docker.com/layers/library/php/5.6-cli-alpine/images/sha256-5dd6b6ea600342303f987d33524c0fae0347ae13be6ae55691d4acb873c203ea?context=explore), [PHP v7.4.33](https://hub.docker.com/layers/library/php/7.4.33-cli-alpine/images/sha256-1e1b3bb4ee1bcb039f559adb9a3fae391c87205ba239b619cdc239b78b7f2557?context=explore) 和 [PHP 最新版本](https://hub.docker.com/layers/library/php/latest/images/sha256-43b84b891f59311867c9b8e18f1ec646b32cb6376475bcd2d489bab912f4f21f?context=explore)
```BASH
$ composer run-script multi-test
```

- **文档测试类**

文档代码：[1.2 一个例子](#12-一个例子)
```BASH
// 验证成功测试, 返回测试结果
$ composer run-script readme test_simple_example
```
文档代码：[附录 2 - 验证完整示例](#附录-2---验证完整示例)
```BASH
// 验证成功测试，返回错误信息
$ composer run-script readme test_complete_example
```


## 4. 功能介绍

### 4.1 方法及其标志
一个字段对应一个验证**规则集**，一个规则集由多个验证 **规则**，**方法（函数）** 和 *错误消息模板(可选的)* 组成。
为了便于理解以及简化规则，允许采用一些方法 **标志** 代表实际的方法（函数）。

```PHP
// 名字是必要的，必须是字符串，长度必须大于3且小于等于32
"name" => "required|string|length_greater_lessequal[3,32]"

// 采用函数标志，同上
// 若是觉得函数标志难以理解，请直接使用函数全称即可
"name" => "*|string|length><=[3,32]"
```

例如:

标志 | 方法 | 含义
---|---|---
`*` | `required` | 必要的，不允许为空
`O` | `optional` | 可选的，允许不设置或为空
`>[20]` | `greater_than` | 数字必须大于20
`length><=[2,16]` | `length_greater_lessequal` | 字符长度必须大于2且小于等于16
`ip` | `ip` | 必须是ip地址

**完整的方法及其标志见** [附录 1 - 方法标志及其含义](#附录-1---方法标志及其含义)

### 4.2 正则表达式
一般以 `/` 开头，以 `/` 结尾，表示是正则表达式
正则表达式最后面的 `/` 之后可能跟随着 模式修饰符，如 `/i`
```PHP
// id 是必要的，且必须是数字
"id" => "required|/^\d+$/",
```
支持在一个串联规则中，同时使用多个正则表达式

### 4.3 方法传参
在以字符串书写的规则中，如何给方法传参呢？

#### 传参的几种方式

1. **标准参数**
跟 PHP 函数使用参数一样，参数写在小括号`()`里面，多个参数以逗号`,`分隔，`,`前后不允许多余的空格
例如，
```
"age" => "equal(@this,20)"
```
*表示 age 必须等于20。这里的 `@this` 代表当前 age 字段的值。*

2. **省略 `@this` 参数**
当参数写在中括号`[]`里面时，首个 `@this` 参数可省略不写。
例如，上述例子可简写为：
```
"age" => "equal[20]"
```

3. **省略参数**
当函数参数只有一个且为当前字段值时，可省略 `()` 和 `[]` ，只写方法。
例如，
```
"id" => "uuid"
```

4. **默认参数**
支持默认参数，但需要额外配置。见 [设置方法标志](#设置方法标志)
例如，以下表示数据为索引数组，`unique` 验证其子数据必须是唯一的。但参数每次都得写 `@parent` 稍显多余，通过配置默认参数，可以省略之。
```PHP
$rule = [
    // 标准写法
    "*" => "unique(@this,@parent)",
    // 省略 `@this`
    "*" => "unique[@parent]",
    // 默认参数写法
    "*" => "unique"
];
```

**参数变量表**

参数 | 描述
---|---
静态值 | 代表参数是静态字符串，允许为空。例如 20
`@this` | 代表参数是当前字段的值
`@parent` | 代表参数是当前字段的父元素的值
`@root` | 代表参数是整个原始验证数据
`@{field_path}` | 代表参数是整个验证数据中的字段名是 `field_path` 的字段的值。例如 `@id`, `@person.name`

**参数分隔符：**
- `symbol_parameter_separator`: `,`
  将参数字符串分割为多个参数的符号。 例如：`equal(@this,1)`
  以下特殊的情况，`,` 不会被视为参数分隔符：
  - `\,`
  - 被 `[]` 包裹起来的。例如：`my_method[[1,2,3],100]` 表示这里有两个参数，数组 `[1,2,3]` 和 整形 `100`
  - 被 `{}` 包裹起来的。
- 自定义参数分隔符，见 [4.10 客制化配置](#410-客制化配置)


#### 参数类型

自动探测参数的类型并强制转换为对应类型。
1. 被双引号（`"`）或单引号（`'`）包含的内容即被视为字符串。否则将探测并强制转换类型。
  - 例如 `"abc"`, `'abc'` 或者 `abc` 都被视为字符串 `abc`
2. 支持转换的类型有：
  - `int`：例如 `123` 为整形，`"123"` 为字符串 
  - `float`: 例如 `123.0`
  - `bool`: 例如 `false` 或者 `TRUE`
  - `array`: 例如 `[1,2,3]` 或者 `["a", "b"]`
  - `object`: 例如 `{"a": "A", "b": "B"}`
  - 例如： `my_method[[1,"2",'3'],100,false,"true"]`
    - `[1,"2",'3']` 将被转换为 `array([1,"2","3"])`
    - `100` 将被转换为 `int(100)`
    - `false` 将被转换为 `bool(false)`
    - `"true"` 将被转换为 `string(true)`

### 4.4 自定义方法
Validation 类中内置了一些验证方法，例如 `*`，`>`, `length>=`, `ip` 等等。详见 [附录 1 - 方法标志及其含义](#附录-1---方法标志及其含义)

如果验证规则比较复杂，内置方法无法满足你的需求，可以拓展你自己的方法。
如果方法中根据不同的判断可能返回不同的错误信息，见 [4.13 错误信息模板 - 3. 在方法中直接返回模板](#413-错误信息模板) 一节。

#### 自定义方法的几种方式

拓展方法有以下几种方式：

##### 1. 新增方法：`add_method($method, $callable, $method_symbol = '')`
新增一个新的方法
- `$method`：方法名
- `$callable`：方法定义
- `$method_symbol`：方法标志。可选的。

<details>
  <summary><span>&#128071;</span> <strong>点击查看代码</strong></summary>

注册一个新的方法，`check_id`，并设置其标志为 `c_id`
```PHP
$validation->add_method('check_id', function ($id) {
    if ($id == 0) {
        return false;
    }

    return true;
}, 'c_id');
```

规则这么写
```PHP
$rule = [
    // 必要的，且只能是数字，且必须大于 0
    "id" => "required|/^\d+$/|check_id",
    // 或者使用标志代替方法名
    "id" => "required|/^\d+$/|c_i",
];
```

</details>
</br>

##### 2. 新增方法类：`add_rule_class`
一个方法类包含多个方法及其标志。支持静态或非静态的方法。
由于优先级原因，如需覆盖验证类 Validation 中的原生方法，请使用新增方法类，拓展类可能无法覆盖。

<details>
  <summary><span>&#128071;</span> <strong>点击查看代码</strong></summary>

创建一个新的文件，`RuleClassTest.php`
```PHP
/**
 * 推荐用 rule class 增加验证方法
 * 如果需要定义方法标志，将他们放在 method_symbols 属性中
 */
class RuleClassTest
{
    // 方法标志
    public static $method_symbols = [
        'is_custom_string' => 'cus_str',
    ];

    // 方法
    public static function is_custom_string($data)
    {
        return preg_match('/^[\w\d -]{8,32}$/', $data) ? true : false;
    }
}
```

调用 `add_rule_class` 注册一个新的方法类，RuleClassTest
```PHP
use RuleClassTest;

// 注册一个新的方法类，RuleClassTest
$validation->add_rule_class(RuleClassTest::class);
```
实际上，`add_rule_class` 也是将规则类插入到 `rule_classes` 属性中，那么我们也可以通过另一种更加直接的方法注册新的方法类：

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

规则这么写
```PHP
$rule = [
    // 必要的，且格式必须是 /^[\w\d -]{8,32}$/
    "id" => "required|is_custom_string",
    // 或者使用标志代替方法名
    "id" => "required|cus_str",
];
```

</details>
</br>

##### 3. 拓展 `Validation` 类

拓展 `Validation` 类并重写内置方法或者增加新的内置方法。推荐用 [trait](https://www.php.net/manual/zh/language.oop5.traits.php)

<details>
  <summary><span>&#128071;</span> <strong>点击查看代码</strong></summary>

创建一个新的文件，`RuleExtendTrait.php`
```PHP
/**
 * 1. 推荐用 trait 拓展验证方法
 * 如果需要定义方法标志，将他们放在属性中，属性命名规则：“method_symbols_of_” + 类名（大驼峰转下划线）
 */
trait RuleExtendTrait
{
    // 方法标志
    protected $method_symbols_of_rule_extend_trait = [
        'euqal_to_1' => '=1',
    ];

    // 方法
    protected function euqal_to_1($data)
    {
        return $data == 1;
    }
}
```

拓展类, 新增方法及其标志
```PHP
use githusband\Validation;

class MyValidation extends Validation
{
    // 1. 使用 Trait
    use RuleExtendTrait;

    /**
     * 2. 直接增加验证方法及其标志
     * 如果需要定义方法标志，将他们放在属性 method_symbols 中
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

规则就这么写
```PHP
$rule = [
    // id 必要的，且必须大于等于 1
    "id" => "required|>=1",
    // parent_id 可选的，且必须等于 1
    "parent_id" => "optional|euqal_to_1",
];
```

</details>
</br>

##### 4. 全局函数
包括系统自带的函数和用户自定义的全局函数。

**几种方法的优先级**
`新增方法` > `新增方法类` > `拓展 Validation 类` > `内置方法` > `全局函数`

若方法都不存在，则报错：未定义

#### 设置方法标志

允许设置方法的标志，更加直观。例如，`greater_than` 的标志是 `>`。参考 [附录 1 - 方法标志及其含义](#附录-1---方法标志及其含义)
从上一节中，你应该已经注意到方法标志的使用了。那也是最通用的设置标志的方式。例如，
```PHP
public static $method_symbols = [
    'is_custom_string' => 'cus_str',
];
```

方法标志 `$method_symbols` 可能还支持其他属性：

- 如果值为字符串，例如 'cus_str'，则表示标志。
- 如果值为数组，则支持以下字段:
  - `symbols`: 表示标志，例如 'cus_str'。
  - `is_variable_length_argument`: 默认为 false。表示方法第二个参数为可变长度参数，规则集 中的第一个参数之后的所有参数都会被第二个参数的子元素。参考 `githusband\Rule\RuleClassDefault::$method_symbols['in_number_array']`。
  - `default_arguments`: 默认为 无。设置方法的默认参数。参考 `githusband\Rule\RuleClassArray::$method_symbols['is_unique']`。
    - `default_arguments` 数组的键必须是整形数字，表示第几个默认参数. 例如，`2` 表示第二个参数。
    - `default_arguments` 数组的值可以是任意值。对于类似 "@parent" (表示当前字段的父数据)，参考 [4.3 方法传参](#43-方法传参)

对于 [2. 新增方法类](#2-新增方法类add_rule_class)，如果支持方法标志的全部属性，例子如下，

<details>
  <summary><span>&#128071;</span> <strong>点击查看代码</strong></summary>

```PHP
class RuleClassTest
{
    /**
     * 方法标志：
     * - 如果值为字符串，则表示标志。
     * - 如果值为数组，则支持以下字段:
     *   - `symbols`: 表示标志
     *   - `is_variable_length_argument`: 默认为 false。表示方法第二个参数为可变长度参数，规则集 中的第一个参数之后的所有参数都会被第二个参数的子元素。参考 `githusband\Rule\RuleClassDefault::$method_symbols['in_number_array']`。
     *   - `default_arguments`: 默认为 无。设置方法的默认参数。参考 `githusband\Rule\RuleClassArray::$method_symbols['is_unique']`。
     *     - `default_arguments` 数组的键必须是整形数字，表示第几个默认参数. 例如，`2` 表示第二个参数。
     *     - `default_arguments` 数组的值可以是任意值。对于类似 "@parent" (表示当前字段的父数据)，参考 https://github.com/gitHusband/Validation?tab=readme-ov-file#43-%E6%96%B9%E6%B3%95%E4%BC%A0%E5%8F%82
     * 
     * @example `in_number_array[1,2,3]` 第二个参数是一个数组 `[1,2,3]`
     * @see githusband\Rule\RuleClassDefault::$method_symbols
     * @see githusband\Rule\RuleClassArray::$method_symbols
     * @var array<string, string|array>
     */
    public static $method_symbols = [
        'is_custom_string' => 'cus_str',
        'is_in_custom_list' => [
            'symbols' => '<custom>',
            'is_variable_length_argument' => true,  // 第一个参数之后的所有参数，都被认作第二个参数数组的子元素
        ],
        'is_equal_to_password' => [
            'symbols' => '=pwd',
            'default_arguments' => [
                2 => '@password'    // 第二个参数默认为 password 字段的值
            ]
        ]
    ];

    /**
     * 测试方法 1 - 测试当前字段的格式是否满足要求
     * 
     * 用法：
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
     * 测试方法 2 - 测试当前字段是否存在于列表内
     * 
     * 用法：
     * - 'sequence' => 'is_in_custom_list[1st, First, 2nd, Second]'
     * - 'sequence' => '<custom>[1st, First, 2nd, Second]'
     * 
     * 这是一个第二个参数为可变长度参数的例子。如果不设置可变长度参数，那么它的写法如下，注意第二个参数必须是合法的 JSON Encoded 字符串。例如：
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
     * 测试方法 3 - 验证当前字段是否与 password 字段相等
     * 
     * 用法：
     * - 'confirm_password' => 'is_equal_to_password'
     * - 'confirm_password' => '=pwd'
     * 
     * 这是一个默认参数的例子。用 `euqal` 方法来写效果也一样, 它相当于给 equal 方法加了默认参数 '@password'。例如：
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

### 4.5 串联并联规则
- 串联：一个参数的多个方法必须全部满足，标志是 `|`
```PHP
"age" => "required|equal[20]"
```
- 并联：一个参数的多个规则满足其一即可。
  两种方法：
  - A. `{字段名}` + `[or]`
  - B. 在当前字段下增加唯一子字段 `[or]`

`[or]` 的标志是 `[||]` , 标志支持自定义，使用方法同 `[or]`
```PHP
// 串联，身高单位是必须的，且必须是 cm 或者 m
"height_unit" => "required|<string>[cm,m]",
// A. 并联，规则可以这么写，[or] 可以替换成标志 [||]
"height[or]" => [
    // 若身高单位是厘米 cm, 则身高必须大于等于100，小于等于200 
    "required|=(@height_unit,cm)|>=<=[100,200]",
    // 若身高单位是米 m, 则身高必须大于等于1，小于等于2
    "required|=(@height_unit,m)|>=<=[1,2]",
]
// B. 并联，规则也可以这么写，标志 [||] 可以替换成 [or]
"height" => [
    "[||]" => [
        // 若身高单位是厘米 cm, 则身高必须大于等于100，小于等于200 
        "required|=(@height_unit,cm)|>=<=[100,200]",
        // 若身高单位是米 m, 则身高必须大于等于1，小于等于2
        "required|=(@height_unit,m)|>=<=[1,2]",
    ]
]
```

### 4.6 条件规则

#### When 条件规则

**When** 条件规则：给任意规则或方法增设条件，一般只有当条件满足时，才验证该方法，否则跳过该方法。
- 用法：`{任意规则或方法}` + `:` + `when()`
- 规则或方法包括： `required`, `optional`, `optional_unset`, `正则表达式` 和 `任意方法`（比如方法 `>=`）
- 两种条件规则：`when()` 和 `when_not()`，条件写在小括号中间，*目前仅支持一个条件*。
- 条件规则支持自定义，见 [4.10 客制化配置](#410-客制化配置)

1. 一般地，只有当条件满足时，才验证该方法，否则跳过该方法。例如：
```PHP
$rule = [
    "id" => "required|><[0,10]",
    // 当 id 小于 5 时，name 只能是数字且长度必须大于 2
    // 当 id 大于等于 5 时，name 可以是任何字符串且长度必须大于 2
    "name" => "/^\d+$/:when(<(@id,5))|length>[2]",
    // 当 id 不小于 5 时，age 必须小于等于 18
    // 当 id 小于 5 时，age 可以是任何数字
    "age" => "int|<=[18]:when_not(<(@id,5))",
];
```

2. 特殊地，`required`, `optional`, `optional_unset`，这三个规则，可能需要验证字段是否非空。

下面，我以 `required` 为例，说明 `when()` 和 `when_not()` 的用法。

- **2.1 正条件的必要规则**：`required:when()`

1. 如果条件成立，则验证 `required` 方法
2. 如果条件不成立，说明该字段是可选的：
  2.1. 若该字段为空，立刻返回验证成功；
  2.2. 若该字段不为空，则继续验证后续方法

```PHP
$rule = [
    // 特征是必要的，且只能是 height(身高) 或 weight(体重)
    "attribute" => "required|<string>[height,weight]",
    // 若属性是 height, 则 centimeter 是必要的，若不是 height，则是可选的。
    // 无论如何，若该值非空，则必须大于 180
    "centimeter" => "required:when(=(@attribute,height))|>[180]",
];
```

- **2.2 否条件的必要规则**：`required:when_not()`

1. 如果条件不成立，则验证 `required` 方法
2. 如果条件成立，说明该字段是可选的：
  2.1. 若该字段为空，立刻返回验证成功；
  2.2. 若该字段不为空，则继续验证后续方法

```PHP
$rule = [
    // 特征是必要的，且只能是 height(身高) 或 weight(体重)
    "attribute" => "required|<string>[height,weight]",
    // 若属性不是 weight, 则 centimeter 是必要的，若是 weight，则是可选的。
    // 无论如何，若该值非空，则必须大于 180
    "centimeter" => "required:when_not(=(@attribute,weight))|>[180]",
];
```

#### IF 条件规则

IF 条件规则的写法跟 `PHP` 的 `if 结构` 语法差不多, 例如：
- `if ( expr ) { statement }`
- `if ( !expr ) { statement }`
- `if ( expr1 || !expr2 ) { statement1 } else { statement2 }`

**支持的逻辑操作符：**
- `!`：逻辑操作符否。
  - 条件中不包含 `!`: 表示当条件结果与布尔值 `true` 严格相同(`===`)，则条件成立。
  - 条件中包含 `!`: 表示当条件结果与布尔值 `true` 不严格相同(`!==`)，则条件成立。
- `||`：逻辑操作符或。表示其中一个条件成立即可。

**执行逻辑是：**
1. 如果条件成立，则继续验证后续方法。
2. 如果条件不成立，则不继续验证后续方法，立即返回成功。

**例子 1：**
```PHP
$rule = [
    // 特征是必要的，且只能是 height(身高) 或 weight(体重)
    "attribute" => "required|<string>[height,weight]",
    // 若属性是 height, 则 centimeter 是必要的，且必须大于 180
    // 若不是 height，则不继续验证后续规则，即 centimeter 为任何值都可以。
    "centimeter" => "if(=(@attribute,height)){required|>[180]}",
];
```
**例子 2：**
```PHP
$rule = [
    // 特征是必要的，且只能是 height(身高) 或 weight(体重)
    "attribute" => "required|<string>[height,weight]",
    // 若属性不是 weight, 则 centimeter 是必要的，且必须大于 180
    // 若是 weight，则不继续验证后续规则，即 centimeter 为任何值都可以。
    "centimeter" => "if ( !=(@attribute,weight) ) { required|>[180] }",
];
```
**例子 3：**
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
对于例子 3 ，需要说明的是，我们只支持一个逻辑否 `!`, `!!=(@id,50)` 其实是两个部分，逻辑否 `!` 和 方法 `not_equal` 的标志 `!=`。标志见 [附录 1 - 方法标志及其含义](#附录-1---方法标志及其含义)

### 4.7 无限嵌套的数据结构

支持无限嵌套的数据结构，包括关联数组，索引数组

**1. 无限嵌套的关联数组**
验证数据怎么写，规则数组就怎么写。例如：
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

// 若要验证上述 $data，规则可以这么写
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

**2. 无限嵌套的索引数组**
索引数组字段的名称后面加上标志 `.*`，或者给索引数组字段加上唯一子元素 `*`

<details>
  <summary><span>&#128071;</span> <strong>点击查看代码</strong></summary>

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

// 若要验证上述 $data，规则可以这么写
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

// 也可以这么写
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

#### 数组自身的规则

上述的例子，一个规则数组便可完成对复杂结构的数据的验证。但只能对叶子字段进行验证，如果要对父数组本身进行验证，还无法实现。
那么，我们设计通过一个 `__self__` 叶子字段，表示父数组本身的规则。

- `__self__` 字段允许自定义，见 [4.10 客制化配置](#410-客制化配置)
- 特殊的，如果数组本身规则只有可选的 `optional`，有简便的写法，见 [4.8 可选字段](#48-可选字段)

**1. 关联数组的自身规则**
```PHP
$rule = [
    // 表示根数据是可以为空的，如果不为空，要求其字段有且只有包含 id, name, favourite_fruit
    "__self__" => "optional|require_array_keys[id, name, favourite_fruit]",
    "id" => "required|/^\d+$/",
    "name" => "required|length>[3]",
    "favourite_fruit" => [
        // 表示 favourite_fruit 是可以为空的，如果不为空，要求其字段有且只有包含 name, color, shape
        "__self__" => "optional|require_array_keys[name, color, shape]",
        "name" => "required|length>[3]",
        "color" => "required|length>[3]",
        "shape" => "required|length>[3]"
    ]
];
```

**2. 索引数组的自身规则**
```PHP
$rule = [
    "id" => "required|/^\d+$/",
    "name" => "required|length>[3]",
    "favourite_color" => [
        // 表示 favourite_color 是可以为空的，如果不为空，要求其字段有且只有包含 0, 1，也就是只有两个子数据。
        "__self__" => "optional|require_array_keys[0,1]",
        "*" => "required|length>[3]"
    ],
    "favourite_fruits" => [
        // 表示 favourite_fruits 是可以为空的，如果不为空，要求其字段有且只有包含 0, 1，也就是只有两个子数据。
        "__self__" => "optional|require_array_keys[0,1]",
        "*" => [
            "name" => "required|length>[3]",
            "color" => "required|length>[3]",
            "shape" => "required|length>[3]"
        ]
    ]
];
```

### 4.8 可选字段

1. 一般的，对于一个叶子字段（无任何子字段），可以直接使用 `optional` 方法，表示该字段是可选的。
2. 有时候，数组也是可选的，但是一旦设置，其中的子元素必须按规则验证。对于这种情况，只需要在数组字段名后面加上 `[optional]`，表示该数组是可选的。
3. 与在字段名后面加上 `[optional]` 一样的效果，给字段增加唯一子元素 `[optional]`，也表示该字段是可选的。
4. `[optional]` 的标志是 `[O]`，两者可以互相替换。

例如：
```PHP
$rule = [
    // 1. 叶子字段，直接使用 optional 方法，表示该字段是可选的
    "name" => "optional|string",
    // 2. 任意字段，在字段名后面添加 [optional]，表示该字段是可选的
    "favourite_fruit[optional]" => [
        "name" => "required|string",
        "color" => "required|string"
    ],
    // 3. 任意字段，增加唯一子元素 [optional]，表示该字段是可选的
    "gender" => [ "[optional]" => "string" ],
    "favourite_food" => [
        "[optional]" => [
            "name" => "required|string",
            "taste" => "required|string"
        ]
    ],
];
```
### 4.9 特殊的验证规则

特殊规则列表：

全称 | 标志 | 含义
---|---|---
[optional] | [O] | 表明字段是可选的，支持数组。见 [4.8 可选字段](#48-可选字段)
[or] | [\|\|] | 表明单个字段是或规则，多个规则满足其一即可。见 [4.5 串联并联规则](#45-串联并联规则)
 (无全称) | .* | 表明字段是索引数组。见 [4.7 无限嵌套的数据结构](#47-无限嵌套的数据结构)

注意：标志使用方法和全称一样，且标志支持 [客制化](#410-客制化配置)。

### 4.10 客制化配置

支持客制化的配置有：

<details>
  <summary><span>&#128071;</span> <strong>点击查看配置</strong></summary>

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

例如，你觉得我设计的规则太丑了，一点都不好理解。<span>&#128545;</span> 
于是你做了如下的定制:

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

那么，[1.2 一个例子](#12-一个例子) 中的规则可以这么写：

```PHP
$rule = [
    "id" => "!*&&Reg:/^\d+$/",          // 必要的，且只能是数字
    "name" => "!*&&length><=~3+32",        // 必要的，且字符串长度必须大于3，小于等于32
    "favorite_animation" => [
        "name" => "!*&&length><=~1+64",                // 必要的，且字符串长度必须大于1，小于等于64
        "release_date" => "o?&&length><=#@this+4+64",  // 可选的，如果不为空，那么字符串长度必须大于4，小于等于64
    ]
];
```
是不是变得更加漂亮了呢？<span>&#128525;</span> <strong style="font-size: 20px">快来试试吧！</strong>

#### 启用实体（重点）

如你所见，规则集都是用字符串书写的，每次验证数据，我们需要将规则集解析成一个个规则，再将规则解析成方法及其参数，最后验证对应数据。
对于 php-fpm 的请求，每一个请求都将启动一个进程，一般只需验证一个数据，结束请求后进程也销毁，所以开启实体反而浪费性能。
但对于常驻后台的服务, 例如 [swoole](https://wiki.swoole.com/zh-cn/#/)，可以开启实体配置，将规则集解析成实体类，避免重复解析的问题。

- `enable_entity`: 默认 `false`。

启用实体不会影响验证数据过程产生任何变化。如果你对实体结构感兴趣，请看[这里](ENTITY.md)。

### 4.11 国际化

为不同的方法定制默认的错误消息模板。见 [4.13 错误信息模板](#413-错误信息模板) - 第 3 点

**国际化列表**

语言 | 文件名 | 类名 | 别名
---|---|---|---
English(Default) | EnUs.php | `EnUs` | `en-us`
Chinese | ZhCn.php | `ZhCn` | `zh-cn`

- 国际化文件名和类名都采用大驼峰命名方式。
- 通过 [4.10 客制化配置](#410-客制化配置) 修改默认错误信息模板。
- 通过 `set_language` 方法修改默认错误信息模板。支持用类名或者别名作为参数。

```PHP
// 在实例化类的时候加入配置
$validation_conf = [
    'language' => 'zh-cn',
];
$validation = new Validation($validation_conf);

// 或者调用接口
$validation->set_language('zh-cn'); // 将加载 ZhCn.php 国际化文件
```

**创建你的国际化文件**

1. 创建文件 `/MyPath/MyLang.php`

如果方法有对应的[标志](#附录-1---方法标志及其含义)，比如 `equal` 的标志是 `=`, 那么这里的键推荐用方法标志 `=`。
如果方法没有对应标志，那么就只能用方法名，比如 `check_custom`

```PHP
<?php

class MyLang
{
    public $error_templates = [
        // 覆盖默认方法 = 的错误信息模板
        '=' => '@this must be equal to @p1(From MyLang)',
        // 新增方法 check_custom 的错误信息模板
        'check_custom' => '@this check_custom error!'
    ];
}
```

2. 配置国际化文件的路径
```PHP
$validation->set_config(['lang_path' => '/MyPath/'])->set_language('MyLang');
```

**直接使用国际化对象**
实际上，上面国际化文件的方法最终调用的是 `custom_language` 接口。
```PHP
// 必须是对象
$MyLang = (object)[];
$MyLang->error_templates = [
    // 覆盖默认方法 = 的错误信息模板
    '=' => '@this must be equal to @p1(From MyLang)',
    // 新增方法 check_custom 的错误信息模板
    'check_custom' => '@this check_custom error!'
];

$validation->custom_language($MyLang, 'MyLang');
```

### 4.12 验证全部数据

默认地，即使某个字段验证失败，也会继续验证后续的全部数据。
*可设置当任意字段验证失败后，立即结束验证后续字段。*
```PHP
// 在实例化类的时候加入配置
$validation_conf = [
    'validation_global' => false,
];
$validation = new Validation($validation_conf);

// 或者调用 set_validation_global 接口
$validation->set_validation_global(false);
```

### 4.13 错误信息模板

<u>**如何判定方法验证失败？**</u>
当且仅当方法 `返回值 === true` 时，表示验证成功，否则表示验证失败。

<u>**当一个字段验证失败，你可能希望**</u>
- 为整个规则集设置一个错误信息模板
- 为规则集中的每一个规则单独设置一个错误信息模板

<u>**那么，你有多个途径可以设置错误信息模板：**</u>
1. 在规则集中设置一个统一的模板(普通字符串)
2. 在方法中直接返回模板(普通字符串)
3. 为每一个方法定制模板
  3.1. 在 规则集 中设置临时模板(JSON字符串等)
  3.2. 通过 [国际化](#411-国际化) 设置默认的模板

模板**优先级** 从高到低：`1` > `2` > `3`

<u>**支持被单独设置模板的方法，要求：**</u>

- 方法返回 `false`: 根据方法或标志匹配错误信息模板
- 方法返回标志：根据返回的标志匹配错误信息模板
  *比如 `TAG:=`，根据 `=` 匹配错误信息模板*

<u>**模板变量**</u>

变量 | 描述 | 例子
---|---|---
`@this` | 当前字段 | `id` 或者 `favorite_animation.name`
`@method` | 当前方法 | `>` 或者 `greater_than`
`@p{x}` | 当前字段的第 x 个参数 | `@p1` 代表第 1 个参数的值。比如 `100`
`@t{x}` | 当前字段的第 x 个参数的类型 | `@t1` 代表第 1 个参数的类型。比如 `int`


**1. 在 规则集 中设置临时模板**

临时模板只对当前规则集有效，对其他规则集无效。

1.1 在一个规则集的最后，加入标志 "` >> `", 注意前后各有一个空格。自定义标志见 [4.10 客制化配置](#410-客制化配置)

1.1.1. **普通字符串**：表示无论规则中的任何方法验证失败，都使用此错误信息
*它对当前字段的所有规则都生效。无论方法返回 `false` 或者其他错误模板，都使用此错误信息*
```PHP
// required 或者 正则 或者 >=<= 方法，无论哪一个验证失败都报错 "id is incorrect."
"id" => 'required|/^\d+$/|>=<=[1,100] >> @this is incorrect.'
```

1.1.2. **JSON 字符串**：为每一个方法设置一个错误信息模板
*它只对对应的规则都生效。如果方法返回 `false`，则使用对应的错误信息*
```PHP
"id" => 'required|/^\d+$/|>=<=[1,100] >> { "required": "Users define - @this is required", "preg": "Users define - @this should be \"MATCHED\" @preg"}'
```
当其中任一方法验证错误并返回 `false`，对应的报错信息为
- `required`: Users define - id is required
- `/^\d+$/`: Users define - id should be "MATCHED" /^\d+$/
- `>=<=`: id must be greater than or equal to 1 and less than or equal to 100

1.1.3. ~~专属字符串（不推荐）~~：为每一个方法设置一个错误信息模板，同 JSON

```PHP
"id" => "required|/^\d+$/|>=<=[1,100] >> [required]=> Users define - @this is required [preg]=> Users define - @this should be \"MATCHED\" @preg"
```

1.2. **错误信息模板数组**：为每一个方法设置一个错误信息模板，同 JSON
- 键 `0`: 验证规则
- 键 `error_message`：错误信息模板数组

不允许包含任何其他键。

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

**2. 在方法中直接返回模板**

当且仅当方法 `返回值 === true` 时，表示验证成功，否则表示验证失败。

所以方法允许四种错误返回：
- 返回 `false` (支持[国际化](#411-国际化))
根据方法的标志匹配错误信息模板，见 [附录 1 - 方法标志及其含义](#附录-1---方法标志及其含义)
```PHP
return false;
```
- 返回标志 (支持[国际化](#411-国际化))
根据返回的标志匹配错误信息模板。比如标志 `is_exclude_animal`
```PHP
return "TAG:is_exclude_animal";
```
- 返回错误信息模板
```PHP
return "I don't like mouse";
```
- 返回错误信息模板数组，默认有两个字段，`error_type` 和 `message`，可自行增加其他字段
```PHP
return [
    "error_type" => "server_error",
    "message" => "I don't like snake",
    "extra" => "You scared me"
];
```

<details>
  <summary><span>&#128071;</span> <strong>点击查看完整方法返回示例</strong></summary>

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

**3. 通过国际化设置默认模板**

默认模板对所有规则集生效，但可能被临时模板替代。

见 [4.11 国际化](#411-国际化)

### 4.14 错误信息格式

一共有四种不同的错误信息格式：

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
这种格式和上面的格式类似，只是错误信息变成了数组，包含更多的错误信息。
- `ERROR_FORMAT_DOTTED_GENERAL`: 'DOTTED_GENERAL'
```JSON
{
     "A.1": "error_msg_A1",
     "A.2.a": "error_msg_A2a",
}
```
- `ERROR_FORMAT_DOTTED_DETAILED`: 'DOTTED_DETAILED'
这种格式和上面的格式类似，只是错误信息变成了数组，包含更多的错误信息。

详见 [附录 3 - 错误信息格式](#附录-3---错误信息格式)

---

## 附录 1 - 方法标志及其含义

标志 | 方法 | 可变长度参数 | 错误消息模板
---|---|:---:|---
`*` | `required` | 否 | @this 不能为空
`O:?` | `optional:when` | 否 | 在特定情况下，@this 才能为空
`uuid` | `is_uuid` | 否 | @this 必须是 UUID
`>` | `greater_than` | 否 | @this 必须大于 @p1
`length>=<=` | `length_between` | 否 | @this 长度必须大于等于 @p1 且小于等于 @p2
`<number>` | `in_number_array` | **是** | @this 必须是数字且在此之内 @p1
`date>` | `date_greater_than` | 否 | @this 日期必须大于 @p1

- `is_variable_length_argument`: 表示方法第二个参数是否为[可变长度参数](https://www.php.net/manual/en/functions.arguments.php#functions.variable-arg-list)。即方法第二个参数为数组，规则集中第一个参数之后的所有参数，都被视为方法的第二个参数的子元素。

<details>
  <summary><span>&#128071;</span> <strong>点击查看 附录 1 - 方法标志及其含义</strong></summary>

标志 | 方法 | 可变长度参数 | 错误消息模板
---|---|:---:|---
/ | `default` | 否 | @this 验证错误
`.*` | `index_array` | 否 | @this 必须是索引数组
`:?` | `when` | 否 | 在特定情况下，
`:!?` | `when_not` | 否 | 在非特定情况下，
`*` | `required` | 否 | @this 不能为空
`*:?` | `required:when` | 否 | 在特定情况下，@this 不能为空
`*:!?` | `required:when_not` | 否 | 在非特定情况下，@this 不能为空
`O` | `optional` | 否 | @this 永远不会出错
`O:?` | `optional:when` | 否 | 在特定情况下，@this 才能为空
`O:!?` | `optional:when_not` | 否 | 在非特定情况下，@this 才能为空
`O!` | `optional_unset` | 否 | @this 允许不设置，且一旦设置则不能为空
`O!:?` | `optional_unset:when` | 否 | 在特定情况下，@this 允许不设置，且一旦设置则不能为空。否则不能为空
`O!:!?` | `optional_unset:when_not` | 否 | 在非特定情况下，@this 允许不设置，且一旦设置则不能为空。否则不能为空
/ | `preg` | 否 | @this 格式错误，必须是 @preg
/ | `preg_format` | 否 | @this 方法 @preg 不是合法的正则表达式
/ | `call_method` | 否 | @method 未定义
`=` | `equal` | 否 | @this 必须等于 @p1
`!=` | `not_equal` | 否 | @this 必须不等于 @p1
`==` | `strictly_equal` | 否 | @this 必须严格等于 @t1(@p1)
`!==` | `not_strictly_equal` | 否 | @this 必须严格不等于 @t1(@p1)
`>` | `greater_than` | 否 | @this 必须大于 @p1
`<` | `less_than` | 否 | @this 必须小于 @p1
`>=` | `greater_equal` | 否 | @this 必须大于等于 @p1
`<=` | `less_equal` | 否 | @this 必须小于等于 @p1
`><` | `greater_less` | 否 | @this 必须大于 @p1 且小于 @p2
`><=` | `greater_lessequal` | 否 | @this 必须大于 @p1 且小于等于 @p2
`>=<` | `greaterequal_less` | 否 | @this 必须大于等于 @p1 且小于 @p2
`>=<=` | `between` | 否 | @this 必须大于等于 @p1 且小于等于 @p2
`<number>` | `in_number_array` | **是** | @this 必须是数字且在此之内 @p1
`!<number>` | `not_in_number_array` | **是** | @this 必须是数字且不在此之内 @p1
`<string>` | `in_string_array` | **是** | @this 必须是字符串且在此之内 @p1
`!<string>` | `not_in_string_array` | **是** | @this 必须是字符串且不在此之内 @p1
`length=` | `length_equal` | 否 | @this 长度必须等于 @p1
`length!=` | `length_not_equal` | 否 | @this 长度必须不等于 @p1
`length>` | `length_greater_than` | 否 | @this 长度必须大于 @p1
`length<` | `length_less_than` | 否 | @this 长度必须小于 @p1
`length>=` | `length_greater_equal` | 否 | @this 长度必须大于等于 @p1
`length<=` | `length_less_equal` | 否 | @this 长度必须小于等于 @p1
`length><` | `length_greater_less` | 否 | @this 长度必须大于 @p1 且小于 @p2
`length><=` | `length_greater_lessequal` | 否 | @this 长度必须大于 @p1 且小于等于 @p2
`length>=<` | `length_greaterequal_less` | 否 | @this 长度必须大于等于 @p1 且小于 @p2
`length>=<=` | `length_between` | 否 | @this 长度必须大于等于 @p1 且小于等于 @p2
`int` | `integer` | 否 | @this 必须是整型
/ | `float` | 否 | @this 必须是小数
/ | `string` | 否 | @this 必须是字符串
`array` | `is_array` | 否 | @this 必须是数组
/ | `bool` | 否 | @this 必须是布尔型
`bool=` | `bool_equal` | 否 | @this 必须是布尔型且等于 @p1
/ | `bool_str` | 否 | @this 必须是布尔型字符串
/ | `bool_string` | 否 | @this 必须是布尔型字符串
`bool_string=` | `bool_string_equal` | 否 | @this 必须是布尔型字符串且等于 @p1
`<keys>` | `require_array_keys` | **是** | @this 必须是数组且其字段有且只有包含 @p1
/ | `dob` | 否 | @this 必须是正确的日期
/ | `file_base64` | 否 | @this 必须是正确的文件的base64码
/ | `file_base64:mime` | 否 | @this 文件类型必须是 @p1
/ | `file_base64:size` | 否 | @this 文件尺寸必须小于 @p2kb
/ | `oauth2_grant_type` | 否 | @this 必须是合法的 OAuth2 授权类型
`email` | `is_email` | 否 | @this 必须是邮箱
`url` | `is_url` | 否 | @this 必须是网址
`ip` | `is_ip` | 否 | @this 必须是IP地址
`ipv4` | `is_ipv4` | 否 | @this 必须是IPv6地址
`ipv6` | `is_ipv6` | 否 | @this 必须是IPv6地址
`mac` | `is_mac` | 否 | @this 必须是MAC地址
`uuid` | `is_uuid` | 否 | @this 必须是 UUID
`ulid` | `is_ulid` | 否 | @this 必须是 ULID
`alpha` | `is_alpha` | 否 | @this 只能包含字母
`alpha_ext` | `is_alpha_ext` | 否 | @this 只能包含字母和_-
/ | `alpha_ext:@p2` | 否 | @this 只能包含字母和@p2
`alphanumeric` | `is_alphanumeric` | 否 | @this 只能包含字母和数字
`alphanumeric_ext` | `is_alphanumeric_ext` | 否 | @this 只能包含字母，数字和_-
/ | `alphanumeric_ext:@p2` | 否 | @this 只能包含字母，数字和@p2
`datetime` | `is_datetime` | 否 | @this 必须是格式正确的日期时间
/ | `datetime:format:@p1` | 否 | @this 必须是日期时间且格式为 @p1
/ | `datetime:format:@p2` | 否 | @this 必须是日期时间且格式为 @p2
/ | `datetime:format:@p3` | 否 | @this 必须是日期时间且格式为 @p3
/ | `datetime:invalid_format:@p1` | 否 | @this 格式 @p1 不是合法的日期时间格式
/ | `datetime:invalid_format:@p2` | 否 | @this 格式 @p2 不是合法的日期时间格式
/ | `datetime:invalid_format:@p3` | 否 | @this 格式 @p3 不是合法的日期时间格式
`datetime=` | `datetime_equal` | 否 | @this 日期时间必须等于 @p1
`datetime!=` | `datetime_not_equal` | 否 | @this 日期时间必须不等于 @p1
`datetime>` | `datetime_greater_than` | 否 | @this 日期时间必须大于 @p1
`datetime>=` | `datetime_greater_equal` | 否 | @this 日期时间必须大于等于 @p1
`datetime<` | `datetime_less_than` | 否 | @this 日期时间必须小于 @p1
`datetime<=` | `datetime_less_equal` | 否 | @this 日期时间必须小于等于 @p1
`datetime><` | `datetime_greater_less` | 否 | @this 日期时间必须大于 @p1 且小于 @p2
`datetime>=<` | `datetime_greaterequal_less` | 否 | @this 日期时间必须大于等于 @p1 且小于 @p2
`datetime><=` | `datetime_greater_lessequal` | 否 | @this 日期时间必须大于 @p1 且小于等于 @p2
`datetime>=<=` | `datetime_between` | 否 | @this 日期时间必须在 @p1 和 @p2 之间
`date` | `is_date` | 否 | @this 必须是日期且格式为 Y-m-d
/ | `date:format:@p1` | 否 | @this 必须是日期且格式为 @p1
/ | `date:format:@p2` | 否 | @this 必须是日期且格式为 @p2
/ | `date:format:@p3` | 否 | @this 必须是日期且格式为 @p3
/ | `date:invalid_format:@p1` | 否 | @this 格式 @p1 不是合法的日期格式
/ | `date:invalid_format:@p2` | 否 | @this 格式 @p2 不是合法的日期格式
/ | `date:invalid_format:@p3` | 否 | @this 格式 @p3 不是合法的日期格式
`date=` | `date_equal` | 否 | @this 日期必须等于 @p1
`date!=` | `date_not_equal` | 否 | @this 日期必须不等于 @p1
`date>` | `date_greater_than` | 否 | @this 日期必须大于 @p1
`date>=` | `date_greater_equal` | 否 | @this 日期必须大于等于 @p1
`date<` | `date_less_than` | 否 | @this 日期必须小于 @p1
`date<=` | `date_less_equal` | 否 | @this 日期必须小于等于 @p1
`date><` | `date_greater_less` | 否 | @this 日期必须大于 @p1 且小于 @p2
`date>=<` | `date_greaterequal_less` | 否 | @this 日期必须大于等于 @p1 且小于 @p2
`date><=` | `date_greater_lessequal` | 否 | @this 日期必须大于 @p1 且小于等于 @p2
`date>=<=` | `date_between` | 否 | @this 日期必须在 @p1 和 @p2 之间
`time` | `is_time` | 否 | @this 必须是时间且格式为 H:i:s
/ | `time:format:@p1` | 否 | @this 必须是时间且格式为 @p1
/ | `time:format:@p2` | 否 | @this 必须是时间且格式为 @p2
/ | `time:format:@p3` | 否 | @this 必须是时间且格式为 @p3
/ | `time:invalid_format:@p1` | 否 | @this 格式 @p1 不是合法的时间格式
/ | `time:invalid_format:@p2` | 否 | @this 格式 @p2 不是合法的时间格式
/ | `time:invalid_format:@p3` | 否 | @this 格式 @p3 不是合法的时间格式
`time=` | `time_equal` | 否 | @this 时间必须等于 @p1
`time!=` | `time_not_equal` | 否 | @this 时间必须不等于 @p1
`time>` | `time_greater_than` | 否 | @this 时间必须大于 @p1
`time>=` | `time_greater_equal` | 否 | @this 时间必须大于等于 @p1
`time<` | `time_less_than` | 否 | @this 时间必须小于 @p1
`time<=` | `time_less_equal` | 否 | @this 时间必须小于等于 @p1
`time><` | `time_greater_less` | 否 | @this 时间必须大于 @p1 且小于 @p2
`time>=<` | `time_greaterequal_less` | 否 | @this 时间必须大于等于 @p1 且小于 @p2
`time><=` | `time_greater_lessequal` | 否 | @this 时间必须大于 @p1 且小于等于 @p2
`time>=<=` | `time_between` | 否 | @this 时间必须在 @p1 和 @p2 之间

</details>
</br>

如果您不知道如何使用附录中的任意方法，请于 `tests` 文件夹中搜索 `test_method_` + 方法名。例如：
- `test_method_is_uuid`
- `test_method_datetime_between`

里面有详细的示例。

*目前暂无方法的使用文档。*

---

## 附录 2 - 验证完整示例

设想一下，如果用户数据如下，它包含关联数组，索引数组，我们要如何定制规则去验证它，如何做到简单直观呢？

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
  <summary><span>&#128071;</span> <strong>点击查看代码</strong></summary>

```PHP
// $data - 上述待验证的数据 
function validate($data) {
    // 设置验证规则
    $rule = [
        "id" => "required|/^\d+$/",         // id 是必要的，且只能是数字
        "name" => "required|length><=[3,32]",  // name 是必要的，且字符串长度必须大于3，小于等于32
        "favorite_animation" => [
            // favorite_animation.name 是必要的，且字符串长度必须大于1，小于等于64
            "name" => "required|length><=[1,16]",
            // favorite_animation.release_date 是可选的，如果不为空，那么字符串长度必须大于4，小于等于64
            "release_date" => "optional|length><=[4,64]",
            // "*" 表示 favorite_animation.series_directed_by 是一个索引数组
            "series_directed_by" => [
                // favorite_animation.series_directed_by.* 每一个子元素必须满足其规则：不能为空且长度必须大于 3
                "*" => "required|length>[3]"
            ],
            // [optional] 表示 favorite_animation.series_cast 是可选的
            // ".*"(同上面的“*”) 表示 favorite_animation.series_cast 是一个索引数组，每一个子元素又都是关联数组。
            "series_cast" => [
                "[optional].*" => [
                    // favorite_animation.series_cast.*.actor 不能为空且长度必须大于 3且必须满足正则
                    "actor" => "required|length>[3]|/^[A-Za-z ]+$/",
                    // favorite_animation.series_cast.*.character 不能为空且长度必须大于 3
                    "character" => "required|length>[3]",
                ]
            ]
        ]
    ];
    
    $config = [
        'language' => 'zh-cn'
    ];
    // 实例化类，接受一个自定义配置数组，但不必要
    $validation = new Validation($config);

    // 设置验证规则并验证数据，成功返回 true，失败返回 false
    if ($validation->set_rules($rule)->validate($data)) {
        // 这里获取验证结果，有被规则{$rule}验证到的参数，成功则修改其值为true，失败则修改其值为错误信息，
        // 没有被验证到的参数，保持原值不变。比如 age 保持 18 不变。
        return $validation->get_result();
    } else {
        // 一共有四种错误信息格式可供选择。默认 Validation::ERROR_FORMAT_DOTTED_GENERAL
        return $validation->get_error();
    }
}

// 可以通过改变 get_error 的参数，找到适合自己的报错格式
// 例子中的 $data 基本都不满足 $rule ，可以改变 $data 的值，检测验证规则是否正确
echo json_encode(validate($data), JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE) . "\n";
```
打印结果为

```JSON
{
    "name": "name 长度必须大于 3 且小于等于 32",
    "favorite_animation.name": "favorite_animation.name 长度必须大于 1 且小于等于 16",
    "favorite_animation.series_directed_by.0": "favorite_animation.series_directed_by.0 不能为空",
    "favorite_animation.series_cast.1.actor": "favorite_animation.series_cast.1.actor 格式错误，必须是 /^[A-Za-z ]+$/"
}
```
更多的错误信息格式，见 [附录 3 - 错误信息格式](#附录-3---错误信息格式)

</details>

---
## 附录 3 - 错误信息格式

### 一维字符型结构
```PHP
// 默认 Validation::ERROR_FORMAT_DOTTED_GENERAL
$validation->get_error();
```

```JSON
{
    "name": "name 长度必须大于 3 且小于等于 32",
    "favorite_animation.name": "favorite_animation.name 长度必须大于 1 且小于等于 16",
    "favorite_animation.series_directed_by.0": "favorite_animation.series_directed_by.0 不能为空",
    "favorite_animation.series_cast.1.actor": "favorite_animation.series_cast.1.actor 格式错误，必须是 /^[A-Za-z ]+$/"
}
```

### 一维关联型结构
```PHP
$validation->get_error(Validation::ERROR_FORMAT_DOTTED_DETAILED);
```

```JSON
{
    "name": {
        "error_type": "validation",
        "message": "name 长度必须大于 3 且小于等于 32"
    },
    "favorite_animation.name": {
        "error_type": "validation",
        "message": "favorite_animation.name 长度必须大于 1 且小于等于 16"
    },
    "favorite_animation.series_directed_by.0": {
        "error_type": "required_field",
        "message": "favorite_animation.series_directed_by.0 不能为空"
    },
    "favorite_animation.series_cast.1.actor": {
        "error_type": "validation",
        "message": "favorite_animation.series_cast.1.actor 格式错误，必须是 /^[A-Za-z ]+$/"
    }
}
```

### 无限嵌套字符型结构
```PHP
$validation->get_error(Validation::ERROR_FORMAT_NESTED_GENERAL);
```

```JSON
{
    "name": "name 长度必须大于 3 且小于等于 32",
    "favorite_animation": {
        "name": "favorite_animation.name 长度必须大于 1 且小于等于 16",
        "series_directed_by": [
            "favorite_animation.series_directed_by.0 不能为空"
        ],
        "series_cast": {
            "1": {
                "actor": "favorite_animation.series_cast.1.actor 格式错误，必须是 /^[A-Za-z ]+$/"
            }
        }
    }
}
```

### 无限嵌套关联型结构
```PHP
$validation->get_error(Validation::ERROR_FORMAT_NESTED_DETAILED);
```

```JSON
{
    "name": {
        "error_type": "validation",
        "message": "name 长度必须大于 3 且小于等于 32"
    },
    "favorite_animation": {
        "name": {
            "error_type": "validation",
            "message": "favorite_animation.name 长度必须大于 1 且小于等于 16"
        },
        "series_directed_by": [
            {
                "error_type": "required_field",
                "message": "favorite_animation.series_directed_by.0 不能为空"
            }
        ],
        "series_cast": {
            "1": {
                "actor": {
                    "error_type": "validation",
                    "message": "favorite_animation.series_cast.1.actor 格式错误，必须是 /^[A-Za-z ]+$/"
                }
            }
        }
    }
}
```

---
