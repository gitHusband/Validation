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
      * [4.4 方法拓展](#44-方法拓展)
      * [4.5 串联并联规则](#45-串联并联规则)
      * [4.6 条件规则](#46-条件规则)
         * [When 条件规则](#when-条件规则)
         * [标准条件规则](#标准条件规则)
      * [4.7 无限嵌套的数据结构](#47-无限嵌套的数据结构)
      * [4.8 可选字段](#48-可选字段)
      * [4.9 特殊的验证规则](#49-特殊的验证规则)
      * [4.10 客制化配置](#410-客制化配置)
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

**有任何意见或想法，我们可以一起交流探讨！**

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
- 一个字段对应一个验证规则，一个规则由多个验证方法（函数）组成。
- 验证方法支持用标志代替，易于理解，简化规则。采用`*`, `>`, `<`, `len>` 等方法标志，比如 `*` 表示必要的
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
    "name" => "required|len<=>[3,32]",  // 必要的，且字符串长度必须大于3，小于等于32
    "favorite_animation" => [
        "name" => "required|len<=>[1,64]",          // 必要的，且字符串长度必须大于1，小于等于64
        "release_date" => "optional|len<=>[4,64]",  // 可选的，如果不为空，那么字符串长度必须大于4，小于等于64
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

克隆本项目后，请先生成项目的自动加载文件：
```BASH
$ composer dump-autoload
```

- **单元测试类**

这里包含了所有功能的测试，只包含部分内置函数。
原则上修改代码后跑一遍单元测试，确保功能正常。

```BASH
// 测试所有例子
$ composer run-script test
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
一般，一个字段对应一个验证规则，一个规则由多个验证方法（函数）组成。
为了便于理解以及简化规则，允许采用一些方法 **标志** 代表实际的方法（函数）。

```PHP
// 名字是必要的，必须是字符串，长度必须大于3且小于等于32
"name" => "required|string|length_greater_lessequal[3,32]"

// 采用函数标志，同上
// 若是觉得函数标志难以理解，请直接使用函数全称即可
"name" => "*|string|len<=>[3,32]"
```

例如:

标志 | 方法 | 含义
---|---|---
\* | required | 必要的，不允许为空
O | optional | 可选的，允许不设置或为空
O! | optional_unset | 可选的，允许不设置，一旦设置则不能为空
\>[20] | greater_than | 数字必须大于20
len<=>[2,16] | length_greater_lessequal | 字符长度必须大于2且小于等于16
ip | ip | 必须是ip地址

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

**参数类型表**

参数 | 含义
---|---
静态值 | 代表参数是静态字符串，允许为空。例如 20
@this | 代表参数是当前字段的值
@parent | 代表参数是当前字段的父元素的值
@root | 代表参数是整个原始验证数据
@field_name | 代表参数是整个验证数据中的字段名是 field_name 的字段

### 4.4 方法拓展
Validation 类中内置了一些验证方法，例如 `*`，`>`, `len>=`, `ip` 等等。详见 [附录 1 - 方法标志及其含义](#附录-1---方法标志及其含义)

如果验证规则比较复杂，内置方法无法满足你的需求，可以拓展你自己的方法。
如果方法中根据不同的判断可能返回不同的错误信息，见 [4.13 错误信息模板 - 3. 在方法中直接返回模板](#413-错误信息模板) 一节。

拓展方法有三种方式：
1. **注册新的方法**：`add_method`

<details>
  <summary><span>&#128071;</span> <strong>点击查看代码</strong></summary>

```PHP
// 注册一个新的方法，check_id
$validation->add_method('check_id', function ($id) {
    if ($id == 0) {
        return false;
    }

    return true;
});

// 规则这么写
$rule = [
    // 必要的，且只能是数字，且必须大于 0
    "id" => "required|/^\d+$/|check_id",
];
```

</details>

2. **拓展 `Validation` 类**

拓展 `Validation` 类并重写内置方法或者增加新的内置方法。推荐用 [trait](https://www.php.net/manual/zh/language.oop5.traits.php)

<details>
  <summary><span>&#128071;</span> <strong>点击查看代码</strong></summary>
 
```PHP
use githusband\Validation;

/**
 * 1. 推荐用 trait 拓展验证方法
 * 如果需要定义方法标志，将他们放在属性中，属性命名规则：“method_symbol_of_” + 类名（大驼峰转下划线）
 */
trait RuleCustome
{
    protected $method_symbol_of_rule_custome = [
        '=1' => 'euqal_to_1',
    ];

    protected function euqal_to_1($data)
    {
        return $data == 1;
    }
}

/**
 * 2. 拓展类，直接增加验证方法
 * 如果需要定义方法标志，将他们放在属性 method_symbol 中
 */
class MyValidation extends Validation
{
    use RuleCustome;

    protected $method_symbol = [
        ">=1" => "grater_than_or_equal_to_1",
    ];

    protected function grater_than_or_equal_to_1($data)
    {
        return $data >= 1;
    }
}

/**
 * 规则就这么写
 */
$rule = [
    // id 必要的，且必须大于等于 1
    "id" => "required|>=1",
    // parent_id 可选的，且必须等于 1
    "parent_id" => "optional|euqal_to_1",
];
```

</details>

- 3. **全局函数**
包括系统自带的函数和用户自定义的全局函数。

**三种函数的优先级**
`add_method` > `内置方法` > `全局函数`

若方法都不存在，则报错：未定义

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
"height_unit" => "required|(s)[cm,m]",
// A. 并联，规则可以这么写，[or] 可以替换成标志 [||]
"height[or]" => [
    // 若身高单位是厘米 cm, 则身高必须大于等于100，小于等于200 
    "required|=(@height_unit,cm)|<=>=[100,200]",
    // 若身高单位是米 m, 则身高必须大于等于1，小于等于2
    "required|=(@height_unit,m)|<=>=[1,2]",
]
// B. 并联，规则也可以这么写，标志 [||] 可以替换成 [or]
"height" => [
    "[||]" => [
        // 若身高单位是厘米 cm, 则身高必须大于等于100，小于等于200 
        "required|=(@height_unit,cm)|<=>=[100,200]",
        // 若身高单位是米 m, 则身高必须大于等于1，小于等于2
        "required|=(@height_unit,m)|<=>=[1,2]",
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
    "id" => "required|<>[0,10]",
    // 当 id 小于 5 时，name 只能是数字且长度必须大于 2
    // 当 id 大于等于 5 时，name 可以是任何字符串且长度必须大于 2
    "name" => "/^\d+$/:when(<(@id,5))|len>[2]",
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
    "attribute" => "required|(s)[height,weight]",
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
    "attribute" => "required|(s)[height,weight]",
    // 若属性不是 weight, 则 centimeter 是必要的，若是 weight，则是可选的。
    // 无论如何，若该值非空，则必须大于 180
    "centimeter" => "required:when_not(=(@attribute,weight))|>[180]",
];
```

#### 标准条件规则

标准条件规则的写法跟 `PHP` 语法差不多，`if()` 和 `!if()`

- **标准正条件**：`if()`

1. 如果条件成立，则继续验证后续方法
2. 如果条件不成立，则不继续验证后续方法

```PHP
$rule = [
    // 特征是必要的，且只能是 height(身高) 或 weight(体重)
    "attribute" => "required|(s)[height,weight]",
    // 若属性是 height, 则 centimeter 是必要的，且必须大于 180
    // 若不是 height，则不继续验证后续规则，即 centimeter 为任何值都可以。
    "centimeter" => "if(=(@attribute,height))|required|>[180]",
];
```
- **标准否条件**：`!if()`

1. 如果条件不成立，则继续验证后续方法
2. 如果条件成立，则不继续验证后续方法

```PHP
$rule = [
    // 特征是必要的，且只能是 height(身高) 或 weight(体重)
    "attribute" => "required|(s)[height,weight]",
    // 若属性不是 weight, 则 centimeter 是必要的，且必须大于 180
    // 若是 weight，则不继续验证后续规则，即 centimeter 为任何值都可以。
    "centimeter" => "!if(=(@attribute,weight))|required|>[180]",
];
```

抱歉，*标准条件规则暂不支持 `else` 和 `else if`，将在后续版本中支持。*

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
    "name" => "required|len>[3]",
    "favourite_fruit" => [
        "name" => "required|len>[3]",
        "color" => "required|len>[3]",
        "shape" => "required|len>[3]"
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
    "name" => "required|len>[3]",
    "favourite_color.*" => "required|len>[3]",
    "favourite_fruits.*" => [
        "name" => "required|len>[3]",
        "color" => "required|len>[3]",
        "shape" => "required|len>[3]"
    ]
];

// 也可以这么写
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
    "name" => "!*&&len<=>~3+32",        // 必要的，且字符串长度必须大于3，小于等于32
    "favorite_animation" => [
        "name" => "!*&&len<=>~1+64",                // 必要的，且字符串长度必须大于1，小于等于64
        "release_date" => "o?&&len<=>#@this+4+64",  // 可选的，如果不为空，那么字符串长度必须大于4，小于等于64
    ]
];
```
是不是变得更加漂亮了呢？<span>&#128525;</span> <strong style="font-size: 20px">快来试试吧！</strong>


### 4.11 国际化

为不同的方法定制默认的错误消息模板。见 [4.13 错误信息模板](#413-错误信息模板) - 第 2 点

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
```PHP
<?php

class MyLang
{
    public $error_template = [
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
$MyLang->error_template = [
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

**当一个字段验证失败，你可能希望**
- 为一整个规则设置一个错误信息模板
- 为每一个方法设置一个错误信息模板

**那么，你有三个途径可以设置错误信息模板：**
1. 在规则数组中设置模板
2. 通过[国际化](#411-国际化)设置模板
3. 在方法中直接返回模板

模板**优先级** 从高到低：`1` > `2` > `3`

**1. 在规则数组中设置模板**

1.1 在一个规则最后，加入标志 "` >> `", 注意前后各有一个空格。自定义标志见 [4.10 客制化配置](#410-客制化配置)
1.1.1. **普通字符串**：表示无论规则中的任何方法验证失败，都返回此错误信息
```PHP
// required 或者 正则 或者 <=>= 方法，无论哪一个验证失败都报错 "id is incorrect."
"id" => 'required|/^\d+$/|<=>=[1,100] >> @this is incorrect.'
```

1.1.2. **JSON 字符串**：为每一个方法设置一个错误信息模板

```PHP
"id" => 'required|/^\d+$/|<=>=[1,100] >> { "required": "Users define - @this is required", "preg": "Users define - @this should be \"MATCHED\" @preg"}'
```
当其中任一方法验证错误，对应的报错信息为
- `required`: Users define - id is required
- `/^\d+$/`: Users define - id should be "MATCHED" /^\d+$/
- `<=>=`: id must be greater than or equal to 1 and less than or equal to 100

1.1.3. ~~专属字符串（不推荐）~~：为每一个方法设置一个错误信息模板，同 JSON

```PHP
"id" => "required|/^\d+$/|<=>=[1,100] >> [required]=> Users define - @this is required [preg]=> Users define - @this should be \"MATCHED\" @preg"
```

1.2. **错误信息模板数组**：为每一个方法设置一个错误信息模板，同 JSON
- 键 `0`: 验证规则
- 键 `error_message`：错误信息模板数组

不允许包含任何其他键。

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

2. **通过国际化设置模板**
见 [4.11 国际化](#411-国际化)

3. **在方法中直接返回模板**

当且仅当函数 `返回值 === true` 时，表示验证成功，否则表示验证失败。

所以函数允许三种错误返回：
- 返回 `false`
- 返回错误信息字符串
- 返回错误信息数组，默认有两个字段，`error_type` 和 `message`，可自行增加其他字段

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

<details>
  <summary><span>&#128071;</span> <strong>点击查看 附录 1 - 方法标志及其含义</strong></summary>

标志 | 函数 | 含义
---|---|---
/ | `default` | @this 验证错误
`.*` | `index_array` | @this 必须是索引数组
`:?` | `when` | 在特定情况下，
`:!?` | `when_not` | 在非特定情况下，
`*` | `required` | @this 不能为空
`*:?` | `required:when` | 在特定情况下，@this 不能为空
`*:!?` | `required:when_not` | 在非特定情况下，@this 不能为空
`O` | `optional` | @this 永远不会出错
`O:?` | `optional:when` | 在特定情况下，@this 才能为空
`O:!?` | `optional:when_not` | 在非特定情况下，@this 才能为空
`O!` | `optional_unset` | @this 允许不设置，且一旦设置则不能为空
`O!:?` | `optional_unset:when` | 在特定情况下，@this 允许不设置，且一旦设置则不能为空。否则不能为空
`O!:!?` | `optional_unset:when_not` | 在非特定情况下，@this 允许不设置，且一旦设置则不能为空。否则不能为空
/ | `preg` | @this 格式错误，必须是 @preg
/ | `preg_format` | @this 方法 @preg 不是合法的正则表达式
/ | `call_method` | @method 未定义
`=` | `equal` | @this 必须等于 @p1
`!=` | `not_equal` | @this 必须不等于 @p1
`==` | `strictly_equal` | @this 必须全等于 @p1
`!==` | `not_strictly_equal` | @this 必须不全等于 @p1
`>` | `greater_than` | @this 必须大于 @p1
`<` | `less_than` | @this 必须小于 @p1
`>=` | `greater_than_equal` | @this 必须大于等于 @p1
`<=` | `less_than_equal` | @this 必须小于等于 @p1
`<>` | `between` | @this 必须大于 @p1 且小于 @p2
`<=>` | `greater_lessequal` | @this 必须大于 @p1 且小于等于 @p2
`<>=` | `greaterequal_less` | @this 必须大于等于 @p1 且小于 @p2
`<=>=` | `greaterequal_lessequal` | @this 必须大于等于 @p1 且小于等于 @p2
`(n)` | `in_number` | @this 必须是数字且在此之内 @p1
`!(n)` | `not_in_number` | @this 必须是数字且不在此之内 @p1
`(s)` | `in_string` | @this 必须是字符串且在此之内 @p1
`!(s)` | `not_in_string` | @this 必须是字符串且不在此之内 @p1
`len=` | `length_equal` | @this 长度必须等于 @p1
`len!=` | `length_not_equal` | @this 长度必须不等于 @p1
`len>` | `length_greater_than` | @this 长度必须大于 @p1
`len<` | `length_less_than` | @this 长度必须小于 @p1
`len>=` | `length_greater_than_equal` | @this 长度必须大于等于 @p1
`len<=` | `length_less_than_equal` | @this 长度必须小于等于 @p1
`len<>` | `length_between` | @this 长度必须大于 @p1 且小于 @p2
`len<=>` | `length_greater_lessequal` | @this 长度必须大于 @p1 且小于等于 @p2
`len<>=` | `length_greaterequal_less` | @this 长度必须大于等于 @p1 且小于 @p2
`len<=>=` | `length_greaterequal_lessequal` | @this 长度必须大于等于 @p1 且小于等于 @p2
`int` | `integer` | @this 必须是整型
/ | `float` | @this 必须是小数
/ | `string` | @this 必须是字符串
`arr` | `is_array` | @this 必须是数组
/ | `bool` | @this 必须是布尔型
`bool=` | `bool_equal` | @this 必须是布尔型且等于 @p1
/ | `bool_str` | @this 必须是布尔型字符串
`bool_str=` | `bool_str_equal` | @this 必须是布尔型字符串且等于 @p1
/ | `email` | @this 必须是邮箱
/ | `url` | @this 必须是网址
/ | `ip` | @this 必须是IP地址
/ | `mac` | @this 必须是MAC地址
/ | `dob` | @this 必须是正确的日期
/ | `file_base64` | @this 必须是正确的文件的base64码
/ | `uuid` | @this 必须是 UUID
/ | `oauth2_grant_type` | @this 必须是合法的 OAuth2 授权类型

</details>

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
        "name" => "required|len<=>[3,32]",  // name 是必要的，且字符串长度必须大于3，小于等于32
        "favorite_animation" => [
            // favorite_animation.name 是必要的，且字符串长度必须大于1，小于等于64
            "name" => "required|len<=>[1,16]",
            // favorite_animation.release_date 是可选的，如果不为空，那么字符串长度必须大于4，小于等于64
            "release_date" => "optional|len<=>[4,64]",
            // "*" 表示 favorite_animation.series_directed_by 是一个索引数组
            "series_directed_by" => [
                // favorite_animation.series_directed_by.* 每一个子元素必须满足其规则：不能为空且长度必须大于 3
                "*" => "required|len>[3]"
            ],
            // [optional] 表示 favorite_animation.series_cast 是可选的
            // ".*"(同上面的“*”) 表示 favorite_animation.series_cast 是一个索引数组，每一个子元素又都是关联数组。
            "series_cast" => [
                "[optional].*" => [
                    // favorite_animation.series_cast.*.actor 不能为空且长度必须大于 3且必须满足正则
                    "actor" => "required|len>[3]|/^[A-Za-z ]+$/",
                    // favorite_animation.series_cast.*.character 不能为空且长度必须大于 3
                    "character" => "required|len>[3]",
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
