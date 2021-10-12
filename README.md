# Validation —— 一款功能丰富的 PHP 参数验证器

Validation 用于对后台参数的合法性检查。使用方便，直观。

> https://github.com/gitHusband/Validation

**有任何意见或想法，咱们可以一起交流探讨！**

至于为啥写这个工具的**原因**：
- 1. 对于后台参数，理论上对每个参数都应该进行合法性检查，尤其是那些需要转发给其他API接口或者需要存储到数据库的参数。比如，数据库基本上对数据长度类型等有限制，对于长度的验证可谓是简单繁琐，使用该工具可以大大简化代码。

- 2. 如果参数过多，验证的代码量势必太大，凌乱不直观。使用该工具，只需要定制一套验证规则数组即可，美观直观。
- 3. 对于某些需要版本控制的API接口，每个版本的参数可能不尽相同，使用该工具，可以更加直观的记录参数的整体格式，每个版本的参数也可简化成一套验证规则。
- 4. 可以方便地定制每个验证方法返回不同的错误信息
- 5. ~~暂时想不到，想到了再给你们编。~~

**下面简单介绍下该工具的用法：**
```php
// 实例化类，接受一个配置数组，但不必要
$validation = new Validation($config);

// 设置验证规则并验证数据
if($validation->set_rules($rule)->validate($data)) {
    // 这里获取检测结果，有验证到的参数，成功则修改其值为true，失败则修改其值为错误信息，
    // 如无验证到的参数，保持原值不变。
    return $validation->get_result();
}else {
    // 这里有两个参数，分别对应不同的错误信息格式，一共有四种错误信息可供选择。
    return $validation->get_error(true, false);
}
```

> 目录

* [Validation —— 一款功能丰富的 PHP 参数验证器](#validation--%E4%B8%80%E6%AC%BE%E5%8A%9F%E8%83%BD%E4%B8%B0%E5%AF%8C%E7%9A%84-php-%E5%8F%82%E6%95%B0%E9%AA%8C%E8%AF%81%E5%99%A8)
  * [1\. 简介](#1-%E7%AE%80%E4%BB%8B)
    * [1\.1 特点](#11-%E7%89%B9%E7%82%B9)
  * [2\. 安装](#2-%E5%AE%89%E8%A3%85)
  * [3\. 完整示例](#3-%E5%AE%8C%E6%95%B4%E7%A4%BA%E4%BE%8B)
  * [4\. 功能介绍](#4-%E5%8A%9F%E8%83%BD%E4%BB%8B%E7%BB%8D)
    * [4\.1 语意明确](#41-%E8%AF%AD%E6%84%8F%E6%98%8E%E7%A1%AE)
    * [4\.2 支持正则表达式验证](#42-%E6%94%AF%E6%8C%81%E6%AD%A3%E5%88%99%E8%A1%A8%E8%BE%BE%E5%BC%8F%E9%AA%8C%E8%AF%81)
    * [4\.3 条件验证](#43-%E6%9D%A1%E4%BB%B6%E9%AA%8C%E8%AF%81)
    * [4\.4 支持串联，并联验证](#44-%E6%94%AF%E6%8C%81%E4%B8%B2%E8%81%94%E5%B9%B6%E8%81%94%E9%AA%8C%E8%AF%81)
    * [4\.5 自定义函数验证](#45-%E8%87%AA%E5%AE%9A%E4%B9%89%E5%87%BD%E6%95%B0%E9%AA%8C%E8%AF%81)
    * [4\.6 无限嵌套的数据结构的验证](#46-%E6%97%A0%E9%99%90%E5%B5%8C%E5%A5%97%E7%9A%84%E6%95%B0%E6%8D%AE%E7%BB%93%E6%9E%84%E7%9A%84%E9%AA%8C%E8%AF%81)
    * [4\.7 支持特殊的验证规则](#47-%E6%94%AF%E6%8C%81%E7%89%B9%E6%AE%8A%E7%9A%84%E9%AA%8C%E8%AF%81%E8%A7%84%E5%88%99)
    * [4\.8 支持自定义配置](#48-%E6%94%AF%E6%8C%81%E8%87%AA%E5%AE%9A%E4%B9%89%E9%85%8D%E7%BD%AE)
    * [4\.9 支持国际化配置](#49-%E6%94%AF%E6%8C%81%E5%9B%BD%E9%99%85%E5%8C%96%E9%85%8D%E7%BD%AE)
    * [4\.10 支持一次性验证所有参数](#410-%E6%94%AF%E6%8C%81%E4%B8%80%E6%AC%A1%E6%80%A7%E9%AA%8C%E8%AF%81%E6%89%80%E6%9C%89%E5%8F%82%E6%95%B0)
    * [4\.11 支持自定义错误信息](#411-%E6%94%AF%E6%8C%81%E8%87%AA%E5%AE%9A%E4%B9%89%E9%94%99%E8%AF%AF%E4%BF%A1%E6%81%AF)
    * [4\.12 支持多种错误信息格式](#412-%E6%94%AF%E6%8C%81%E5%A4%9A%E7%A7%8D%E9%94%99%E8%AF%AF%E4%BF%A1%E6%81%AF%E6%A0%BC%E5%BC%8F)
  * [附录 1](#%E9%99%84%E5%BD%95-1)
  * [附录 2](#%E9%99%84%E5%BD%95-2)
    * [第一种](#%E7%AC%AC%E4%B8%80%E7%A7%8D)
    * [第二种](#%E7%AC%AC%E4%BA%8C%E7%A7%8D)
    * [第三种](#%E7%AC%AC%E4%B8%89%E7%A7%8D)
    * [第四种](#%E7%AC%AC%E5%9B%9B%E7%A7%8D)

## 1. 简介
### 1.1 特点
- 语意明确，易于理解。采用*, >, <, >=, len>, int, (n), (s) 等函数标志，比如(n)表示in_array, 且必须是数字
- 支持正则表达式验证
- 支持条件验证，条件满足则继续验证后续规则，不满足则表明该字段是可选择
- 支持串联(一个参数多个规则同时满足, &&)，并联(一个参数多个规则满足其一, ||)验证
- 支持自定义函数验证
- 支持函数验证时自由传参，@root(原数据), @parent(验证字段的父数据), @me(当前字段)，@anything(任意字段)
- 支持无限嵌套的数据结构的验证，包括关联数组，索引数组
- 支持特殊的验证规则
- 支持自定义配置，比如规则分隔符号"|"，参数分隔符号","等等
- 支持国际化配置，默认英语，支持自定义方法返回错误信息
- 支持一次性验证所有参数(默认)，也可设置参数验证失败后立即结束验证
- 支持自定义错误信息，支持多种格式的错误信息，无限嵌套或者一维数组的错误信息格式
- ~~暂时想不到，想到了再给你们编。~~

## 2. 安装
> composer require githusband/validation

## 3. 完整示例
设想一下，如果用户数据如下，它包含关联数组，索引数组，我们要如何定制规则去验证它，如何做到简单直观呢？

```
$data = [
    "id" => "",
    "name" => "12",
    "email" => "10000@qq.com.123@qq",
    "phone" => "15620004000-",
    "education" => [
        "primary_school" => "???Qiankeng Xiaoxue",
        "junior_middle_school" => "Foshan Zhongxue",
        "high_school" => "Mianhu Gaozhong",
        "university" => "Foshan",
    ],
    "company" => [
        "name" => "Qianken",
        "website" => "https://www.qiankeng.com1",
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
];
```

```php
// $data - 上述待验证的数据 
function validate($data) {
    // 设置验证规则
    $rule = [
        // id 是必要的且必须匹配正则 /^\d+$/， >> 后面的required 和 正则对应的报错信息
        "id" => 'required|/^\d+$/ >> { "required": "用户自定义 - @me 是必要的", "preg": "用户自定义 - @me 必须匹配 @preg" }',
        // name 是必要的且必须是字符串且长度在区间 【8，32)
        "name" => "required|string|len<=>:8,32",
        "email" => "required|email",
        "phone" => "required|/(^1[3|4|5|6|7|8|9]\d{9}$)|(^09\d{8}$)/ >> 用户自定义 - phone number 错误",
        // ip 是可选的
        "ip" => "optional|ip",
        "education" => [
            // education.primary_school 必须等于 “Qiankeng Xiaoxue”
            "primary_school" => "required|=:Qiankeng Xiaoxue",
            "junior_middle_school" => "required|!=:Foshan Zhongxue",
            "high_school" => "optional|string",
            "university" => "optional|string",
        ],
        "company" => [
            "name" => "required|len<=>:8,64",
            "website" => "required|url",
            "colleagues.*" => [
                "name" => "required|string|len<=>:3,32",
                // company.colleagues.*.position 必须等于 Reception,Financial,PHP,JAVA 其中之一
                "position" => "required|(s):Reception,Financial,PHP,JAVA"
            ],
            // 以下三个规则只对 boss.0, boss.1, boss.2 有效，boss.3 及其他都无效 
            "boss" => [
                "required|=:Mike",
                "required|(s):Johnny,David",
                "optional|(s):Johnny,David"
            ]
        ],
        // favourite_food 是可选的索引数组，允许为空
        "favourite_food[optional].*" => [
            // favourite_food.*.name 必须是字符串
            "name" => "required|string",
            "place_name" => "optional|string" 
        ]
    ];
    
    // 简单的自定义配置，它还不是完整的，也不是必要的
    $validation_conf = [
        'language' => 'zh-cn',
        'validation_global' => true,
    ];
    
    // 实例化类，不要忘了事先引用类文件
    // 接受一个配置数组，但不必要
    $validation = new Validation($validation_conf);
    
    // 设置验证规则并验证数据
    if($validation->set_rules($rule)->validate($data)) {
        // 这里获取检测结果，有验证到的参数，成功则修改其值为true，失败则修改其值为错误信息，
        // 如无验证到的参数，保持原值不变。
        return $validation->get_result();
    }else {
        // 这里有两个参数，分别对应不同的错误信息格式，一共有四种错误信息可供选择。
        return $validation->get_error(true, false);
    }
}

// 可以通过改变get_error 的两个参数，找到适合自己的报错格式
// 例子中的 $data 基本都不满足 $rule ，可以改变 $data 的值，验证规则是否正确
print_r(validate($data));
```
打印结果为

```
Array
(
    [id] => 用户自定义 - id 是必要的
    [name] => name 长度必须大于 8 且小于等于 32
    [email] => email 必须是邮箱
    [phone] => 用户自定义 - phone number 错误
    [education.primary_school] => education.primary_school 必须等于 Qiankeng Xiaoxue
    [education.junior_middle_school] => education.junior_middle_school 必须不等于 Foshan Zhongxue
    [company.name] => company.name 长度必须大于 8 且小于等于 64
    [company.website] => company.website 必须是网址
    [company.colleagues.0.name] => company.colleagues.0.name 必须是字符串
    [company.colleagues.1.name] => company.colleagues.1.name 必须是字符串
    [company.colleagues.1.position] => company.colleagues.1.position 必须是字符串且在此之内 Reception,Financial,PHP,JAVA
    [company.colleagues.2.name] => company.colleagues.2.name 必须是字符串
    [company.colleagues.3.position] => company.colleagues.3.position 必须是字符串且在此之内 Reception,Financial,PHP,JAVA
    [company.boss.0] => company.boss.0 必须等于 Mike
    [company.boss.2] => company.boss.2 必须是字符串且在此之内 Johnny,David
)
```



理论上，该工具是用于验证复杂的数据结构的，但如果你想验证单一字符串，也可以，例如

```
$validation->set_rules("required|string")->validate("Hello World!");
```


**注意**：在 src/Test 目录下已经内置了完整测试类Tests.php 和单元测试类 Unit.php。

**完整测试类**：
```
// 上述测试代码
php Tests.php readme_case
// 验证成功测试, 返回测试结果：
php Tests.php success
// 验证成功测试，返回错误信息：
php Tests.php error
```

**单元测试类**：

包含了所有功能的测试，只包含部分内置函数

原则上修改代码后跑一遍单元测试，确保功能正常。如果测试报错，定位问题再解决。

```
// 测试所有例子：
php Unit.php run
// 测试单一例子，如测试正则表达式：
php Unit.php run test_regular_expression
```



## 4. 功能介绍

### 4.1 语意明确
为了便于理解以及使规则更加简短，允许采用一些函数***标志***代表实际的功能。
```php
// 名字是必要的，必须是字符串，长度必须大于3且小于等于32
"name" => "required|string|length_greater_lessequal:3,32"

// 采用函数标志，同上
// 若是觉得函数标志难以理解，请直接使用函数全称即可
"name" => "required|string|len<=>:3,32"
```

例如:

标志 | 函数 | 含义
---|---|---
\* | required | 必要的，不允许为空
O | optional | 可选的，允许不设置或为空
O! | unset_required | 可选的，允许不设置，一旦设置则不能为空
\>:20 | greater_than | 数字必须大于20
len<=>:2,16 | length_greater_lessequal | 字符长度必须大于2且小于等于16
ip | ip | 必须是ip地址

**完整功能请查看附录1**

### 4.2 支持正则表达式验证
以 "**/**" 开头，以 "**/**" 结尾，表示是正则表达式
```php
// id 必须是数字
"id" => "required|/^\d+$/",
```

### 4.3 条件验证
条件验证标志是 "**if[01]?\\?**" 

正条件：**if?** 和 **if1?**

- 如果条件成立，则继续验证后续规则
- 如果条件不成立，说明该字段是可选的：1. 若该字段为空，立刻返回验证成功；2. 若该字段不为空，则继续验证后续规则

```php
$rule = [
    "gender" => "required|(s):male,female",
    // 若性别是女性，则要求年龄大于22岁，若为男性，则对年龄无要求
    "age" => "if?=::@gender,female|required|>:22",
],
```
否条件：**if0?**

- 如果条件不成立，则继续验证后续规则
- 如果条件成立，说明该字段是可选的：1. 若该字段为空，立刻返回验证成功；2. 若该字段不为空，则继续验证后续规则

```php
$rule = [
    "gender" => "required|(s):male,female",
    // 若性别不是女性，则要求年龄大于22岁，若为女性，则对年龄无要求
    "age" => "if0?=::@gender,female|required|>:22",
],
```

### 4.4 支持串联，并联验证
- 串联：一个参数多个规则同时满足，标志是 **|**
- 并联：一个参数多个规则满足其一，标志是 {字段名} + **[or]**
```php
// 并联
"height[or]" => [
    // 若身高单位是cm, 则身高必须大于等于100，小于等于200 
    "required|=::@height_unit,cm|<=>=:100,200 >> @me should be in [100,200] when height_unit is cm",
    // 若身高单位是m, 则身高必须大于等于1，小于等于2
    "required|=::@height_unit,m|<=>=:1,2 >> @me should be in [1,2] when height_unit is m",
],
// 串联，身高单位是必须的，且必须是 cm 或者 m
"height_unit" => "required|(s):cm,m",
```

### 4.5 自定义函数验证
本工具已经内置了不少验证函数，例如 \*，>, len>=, ip 等等

如果内置函数无法满足需求，或者验证规则过于复杂，可以使用自定义函数验证

自定义函数验证支持两种方式：
- 自定义接口：add_method

```
$validation->add_method('check_postcode', function($company) {
    if(isset($company['country']) && $company['country'] == "US"){
        if(!isset($company['postcode']) || $company['postcode'] != "123"){
            return false;
        }
    }

    return true;
});
```

- 全局函数

三种函数的优先级是
> add_method > 内置函数 > 全局函数

如若函数不存在，则报错。

使用函数允许自由传参，每一个参数以 "**,**" 分隔

参数 | 含义
---|---
@root | 代表参数是整个验证数据$data
@parent | 代表参数是当前字段的父元素
@me | 代表参数是当前字段
@field_name | 代表参数是整个验证数据中的字段名是 field_name的字段
value | 代表参数是value 字符串，允许为空

参数的标志是 ":" 和 "::"
> ":" 这个标志，如果不存在@me, 会自动添加 @me 参数到第一个参数

> "::" 这个标志，如果不存在@me, 也不会自动添加

### 4.6 无限嵌套的数据结构的验证
支持无限嵌套的数据结构的验证，包括关联数组，索引数组, 例如：
```php
$data = [
    "name" => "Johnny",
    "favourite_color" => {
        "white",
        "red"
    },
    "favourite_fruits" => {
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
    }
]

// 若要验证上述 $data，规则可以这么写
$rule = [
    "name" => "required|len>:4",
    "favourite_color" => [
        "required|len>:4",
        "required|len>:4",
    ],
    "favourite_fruits.*" => [
        "name" => "required|len>:4",
        "color" => "required|len>:4",
        "shape" => "required|len>:4"
    ]
]
```
**发现了吗？**

关联数组和普通索引数组正常写就可以了，而索引数组里的元素是关联数组，则需要在字段后面加上 "**.\***" 这个标志即可。

**可选数组规则**

有时候，数组也是可选的，但是一旦设置，其中的子元素必须按规则验证，这时候只需要在数组字段名后面加上"**[optional]**" 标志，表示该数组可选，如：

```
"favourite_fruits[optional].*" => [
    "name" => "required|len>:4",
    "color" => "required|len>:4",
    "shape" => "required|len>:4"
]
```
### 4.7 支持特殊的验证规则
支持的特殊规则有：

"**[optional]**" - 表明单个字段或数组是可选的。
注意，表明单个字段可选，可在字段规则上加上 **optional** 即可。

```
$rule = [
    "name" => "optional|string",
    "gender" => [ "[optional]" => "string" ],
    // favourite_fruit 是可选的，如果存在，则必须是数组
    "favourite_fruit[optional]" => [
        "name" => "required|string",
        "color" => "required|string"
    ],
    // 等同于上的写法
    "favourite_meat" => [
        "[optional]" => [
            "name" => "required|string",
            "from" => "required|string"
        ]
    ],
];
```
"**[or]**" - 表明单个字段是或规则，多个规则满足其一即可。

```
$rule = [
    // name 可以是布尔值或者布尔字符串
    "name[or]" => [
        "required|bool",
        "required|bool_str",
    ],
    // 等同于上的写法
    "height" => [
        "[or]" => [
            "required|int|>:100",
            "required|string",
        ]
    ]
];
```
"**.\***" - 表明该字段是索引数组。

当索引数组的标志以 . 开头时，在标志不是跟随在字段名后面的情况下，可省略 .

```
$rule = [
    "person" => [
        // 表明 person 是索引数组, person.* 是关联数组
        // 在这种情况下，可省略 . ,只写 *
        "*" => [
            "name" => "required|string",
            // 表明 person.*.relation 是关联数组
            "relation" => [
                "father" => "required|string",
                "mother" => "optional|string",
                "brother" => [
                    // 表明 person.*.relation.*.brother 是可选的索引数组
                    "[optional].*" => [
                        // 表明 person.*.relation.*.brother.* 是索引数组
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

// 验证数据格式如下
$data = [
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
```


### 4.8 支持自定义配置
支持自定义的配置有：

```
$config = array(
    'language' => 'en-us',                  // Language, default is en-us
    'lang_path' => '',                      // Customer Language file path
    'validation_global' => true,            // If true, validate all rules; If false, stop validating when one rule was invalid
    'auto_field' => "data",                 // If root data is string or numberic array, add the auto_field to the root data, can validate these kind of data type.
    'reg_msg' => '/ >> (.*)$/',             // Set special error msg by user 
    'reg_preg' => '/^(\/.+\/.*)$/',         // If match this, using regular expression instead of method
    'reg_if' => '/^if[01]?\?/',             // If match this, validate this condition first
    'reg_if_true' => '/^if1?\?/',           // If match this, validate this condition first, if true, then validate the field
    'reg_if_false' => '/^if0\?/',           // If match this, validate this condition first, if false, then validate the field
    'symbol_rule_separator' => '|',         // Rule reqarator for one field
    'symbol_param_classic' => ':',          // If set function by this symbol, will add a @me parameter at first 
    'symbol_param_force' => '::',           // If set function by this symbol, will not add a @me parameter at first 
    'symbol_param_separator' => ',',        // Parameters separator, such as @me,@field1,@field2
    'symbol_field_name_separator' => '.',   // Field name separator, suce as "fruit.apple"
    'symbol_required' => '*',               // Symbol of required field, Same as "required"
    'symbol_optional' => 'O',               // Symbol of optional field, can be unset or empty, Same as "optional"
    'symbol_unset_required' => 'O!',        // Symbol of optional field, can only be unset or not empty, Same as "unset_required"
    'symbol_or' => '[||]',                  // Symbol of or rule, Same as "[or]"
    'symbol_array_optional' => '[O]',       // Symbol of array optional rule, Same as "[optional]"
    'symbol_index_array' => '.*',           // Symbol of index array rule
);
```
例如:

```
$validation_conf = array(
    'language' => 'en-us',                  // Language, default is en-us
    'lang_path' => '/my_path/',             // Customer Language file path
    'validation_global' => true,            // If true, validate all rules; If false, stop validating when one rule was invalid.
    'auto_field' => "param",                // If root data is string or numberic array, add the auto_field to the root data, can validate these kind of data type.
    'reg_msg' => '/ >>>(.*)$/',             // Set special error msg by user 
    'reg_preg' => '/^Reg:(\/.+\/.*)$/',     // If match this, using regular expression instead of method
    'reg_if' => '/^IF[yn]?\?/',             // If match this, validate this condition first
    'reg_if_true' => '/^IFy?\?/',           // If match this, validate this condition first, if true, then validate the field
    'reg_if_false' => '/^IFn\?/',           // If match this, validate this condition first, if false, then validate the field
    'symbol_rule_separator' => '&&',        // Rule reqarator for one field
    'symbol_param_classic' => '~',          // If set function by this symbol, will add a @me parameter at first 
    'symbol_param_force' => '~~',           // If set function by this symbol, will not add a @me parameter at first 
    'symbol_param_separator' => ',',        // Parameters separator, such as @me,@field1,@field2
    'symbol_field_name_separator' => '->',  // Field name separator, suce as "fruit.apple"
    'symbol_required' => '!*',              // Symbol of required field, Same as "required"
    'symbol_optional' => 'o',               // Symbol of optional field, can be unset or empty, Same as "optional"
    'symbol_unset' => 'O!',                 // Symbol of optional field, can only be unset or not empty, Same as "unset_required"
    'symbol_or' => '[or]',                  // Symbol of or rule, Same as "[or]"
    'symbol_array_optional' => '[o]',       // Symbol of array optional rule, Same as "[optional]"
    'symbol_index_array' => '[N]',          // Symbol of index array rule
);
```
相关规则可以这么写：

```
$rule = [
    "id" => "!*&&int&&Reg:/^\d+$/i",
    "name" => "!*&&string&&len<=>~8,32",
    "gender" => "!*&&(s)~male,female",
    "dob" => "!*&&dob",
    "age" => "!*&&check_age~@gender,30 >>>@me is wrong",
    "height_unit" => "!*&&(s)~cm,m",
    "height[or]" => [
        "!*&&=~~@height_unit,cm&&<=>=~100,200 >>>@me should be in [100,200] when height_unit is cm",
        "!*&&=~~@height_unit,m&&<=>=~1,2 >>>@me should be in [1,2] when height_unit is m",
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
```



### 4.9 支持国际化配置

配置文件名和类名都采用大驼峰命名方式。

调用时，支持使用标准的语言代码，如"zh-cn", "en-us"等。

目前支持的语言有，"zh-cn", "en-us"，默认语言是"en-us"英语。

```
// 调用接口
$validation->set_language('zh-cn'); //将加载 ZhCn.php 配置文件

// 或者在实例化类的时候加入配置
$validation_conf = [
    'language' => 'zh-cn',
];

$validation = new Validation($validation_conf);
```

**自定义国际化文件**

如 /MyPath/MyLang.php。

内容如下：
```
<?php

class MyLang
{
    public $error_template = array(
        'check_custom' => '@me error!(CustomLang File)'
    );
}
```
修改语言文件路径

```
// You should add CustomLang.php in '/MyPath/'
$validation->set_config(array('lang_path' => /MyPath/'))->set_language('MyLang');
```

实际上，国际化配置，配置的是每个验证函数返回的错误信息，也可以自由地覆盖增加你的验证函数返回的错误信息。

```
// 必须是对象
$lang_config = (object)array();
$lang_config->error_template = array(
    'check_id' => '@me error!(customed)'
);

$validation->custom_language($lang_config);
```
以上为错误模版增加了一个check_id, 如果check_id 函数验证错误，则返回信息

```
'@me error!(customed)'
```

### 4.10 支持一次性验证所有参数
支持一次性验证所有参数(默认)，也可设置参数验证失败后立即结束验证
```
// 调用接口
$validation->set_validation_global(false);

// 或者在实例化类的时候加入配置
$validation_conf = [
    'validation_global' => false,
];

$validation = new Validation($validation_conf);
```

### 4.11 支持自定义错误信息
自定义错误信息的标志是 " >> ", 注意前后空格。

例如：

1. \*或者**正则**或者<=>=方法 错误都报错 "id is incorrect."
```
"id" => 'required|/^\d+$/|<=>=:1,100| >> @me is incorrect.'
```
2. 支持JSON 格式错误信息，为每一个方法设置不同的错误信息

```
"id" => 'required|/^\d+$/|<=>=:1,100| >> { "required": "Users define - @me is required", "preg": "Users define - @me should be \"MATCHED\" @preg"}'

# 对应的报错信息为
# id - Users define - id is required
# /^\d+$/ - Users define - id should be \"MATCHED\" /^\d+$/
# <=>= - id must be greater than or equal to 1 and less than or equal to 100
```
3. 支持特殊格式错误信息，为每一个方法设置不同的错误信息，同JSON

```
"id" => "required|/^\d+$/|<=>=:1,100| >> [required]=> Users define - @me is required [preg]=> Users define - @me should be \"MATCHED\" @preg"

# 对应的报错信息为
# id - Users define - id is required
# /^\d+$/ - Users define - id should be \"MATCHED\" /^\d+$/
# <=>= - id must be greater than or equal to 1 and less than or equal to 100
```

**自定义函数也可自定义错误信息, 优先级低于 " >> "**

当函数返回值 === true 时，表示验证成功，否则表示验证失败

所以函数允许三种错误返回：
1. 直接返回 false
2. 返回错误信息字符串
3. 返回错误信息数组，默认有两个字段，error_type 和 message，支持自定义字段

```
function check_age($data, $gender, $param) {
    if($gender == "male") {
        // if($data > $param) return false;
        if($data > $param) return "@me should be greater than @p1 when gender is male";
    }else {
        if($data < $param) return array(
            'error_type' => 'server_error',
            'message' => '@me should be less than @p1 when gender is female',
            "extra" => "extra message"
        );
    }

    return true;
}
```
### 4.12 支持多种错误信息格式
如果是验证一旦错误则立即返回的情况下，有两种错误信息格式可以返回：

返回错误信息字符串
> $validation->get_error(false, true);

```
"id 必须是整型"
```

返回错误信息数组，默认有两个字段，error_type 和 message，支持自定义字段

> $validation->get_error(false, false);

```
{
    "error_type": "validation",
    "message": "id 必须是整型"
}
```

如果是验证所有字段的情况下，有两种错误信息格式可以返回：

详见附录 2


---

## 附录 1

标志 | 函数 | 含义
---|---|---
\* | required | 必要的，@me 不能为空
O | optional | 可选的，允许不设置或为空
O! | symbol_unset_required | 可选的，允许不设置，一旦设置则不能为空
= | equal | @me 必须等于 @p1
!= | not_equal | @me 必须不等于 @p1
== | identically_equal | @me 必须全等于 @p1
!== | not_identically_equal | @me 必须不全等于 @p1
\> | greater_than | @me 必须大于 @p1
< | less_than | @me 必须小于 @p1
\>= | greater_than_equal | @me 必须大于等于 @p1
<= | less_than_equal | @me 必须小于等于 @p1
<> | interval | @me 必须大于 @p1 且小于 @p2
<=> | greater_lessequal | @me 必须大于 @p1 且小于等于 @p2
<>= | greaterequal_less | @me 必须大于等于 @p1 且小于 @p2
<=>= | greaterequal_lessequal | @me 必须大于等于 @p1 且小于等于 @p2
(n) | in_number | @me 必须是数字且在此之内 @p1
!(n) | not_in_number | @me 必须是数字且不在此之内 @p1
(s) | in_string | @me 必须是字符串且在此之内 @p1
!(s) | not_in_string | @me 必须是字符串且不在此之内 @p1
len= | length_equal | @me 长度必须等于 @p1
len!= | length_not_equal | @me 长度必须不等于 @p1
len> | length_greater_than | @me 长度必须大于 @p1
len< | length_less_than | @me 长度必须小于 @p1
len>= | length_greater_than_equal | @me 长度必须大于等于 @p1
len<= | length_less_than_equal | @me 长度必须小于等于 @p1
len<> | length_interval | @me 长度必须大于 @p1 且小于 @p2
len<=> | length_greater_lessequal | @me 长度必须大于 @p1 且小于等于 @p2
len<>= | length_greaterequal_less | @me 长度必须大于等于 @p1 且小于 @p2
len<=>= | length_greaterequal_lessequal | @me 长度必须大于等于 @p1 且小于等于 @p2
int | integer | @me 必须是整型
float | float | @me 必须是小数
string | string | @me 必须是字符串
arr | arr | @me 必须是数组,
bool | bool | @me 必须是布尔型
bool= | bool | @me 必须是布尔型且等于 @p1
bool_str | bool_str | @me 必须是布尔型字符串
bool_str= | bool_str | @me 必须是布尔型字符串且等于 @p1
email | email | @me 必须是邮箱
url | url | @me 必须是网址
ip | ip | @me 必须是IP地址
mac | mac | @me 必须是MAC地址
dob | dob | @me 必须是正确的日期
file_base64 | file_base64 | @me 必须是正确的文件的base64码
uuid | uuid | @me 必须是 UUID

---


---
## 附录 2
### 第一种
> $validation->get_error(false, true);

```
{
    "id": "用户自定义 - id 是必要的",
    "name": "name 长度必须大于 8 且小于等于 32",
    "email": "email 必须是邮箱",
    "phone": "用户自定义 - phone number 错误",
    "education.primary_school": "education.primary_school 必须等于 Qiankeng Xiaoxue",
    "education.junior_middle_school": "education.junior_middle_school 必须不等于 Foshan Zhongxue",
    "company.name": "company.name 长度必须大于 8 且小于等于 64",
    "company.website": "company.website 必须是网址",
    "company.colleagues.0.name": "company.colleagues.0.name 必须是字符串",
    "company.colleagues.1.name": "company.colleagues.1.name 必须是字符串",
    "company.colleagues.1.position": "company.colleagues.1.position 必须是字符串且在此之内 Reception,Financial,PHP,JAVA",
    "company.colleagues.2.name": "company.colleagues.2.name 必须是字符串",
    "company.colleagues.3.position": "company.colleagues.3.position 必须是字符串且在此之内 Reception,Financial,PHP,JAVA",
    "company.boss.0": "company.boss.0 必须等于 Mike",
    "company.boss.2": "company.boss.2 必须是字符串且在此之内 Johnny,David"
}
```
### 第二种
> $validation->get_error(false, false);

```
{
    "id": {
        "error_type": "required_field",
        "message": "用户自定义 - id 是必要的"
    },
    "name": {
        "error_type": "validation",
        "message": "name 长度必须大于 8 且小于等于 32"
    },
    "email": {
        "error_type": "validation",
        "message": "email 必须是邮箱"
    },
    "phone": {
        "error_type": "validation",
        "message": "用户自定义 - phone number 错误"
    },
    "education.primary_school": {
        "error_type": "validation",
        "message": "education.primary_school 必须等于 Qiankeng Xiaoxue"
    },
    "education.junior_middle_school": {
        "error_type": "validation",
        "message": "education.junior_middle_school 必须不等于 Foshan Zhongxue"
    },
    "company.name": {
        "error_type": "validation",
        "message": "company.name 长度必须大于 8 且小于等于 64"
    },
    "company.website": {
        "error_type": "validation",
        "message": "company.website 必须是网址"
    },
    "company.colleagues.0.name": {
        "error_type": "validation",
        "message": "company.colleagues.0.name 必须是字符串"
    },
    "company.colleagues.1.name": {
        "error_type": "validation",
        "message": "company.colleagues.1.name 必须是字符串"
    },
    "company.colleagues.1.position": {
        "error_type": "validation",
        "message": "company.colleagues.1.position 必须是字符串且在此之内 Reception,Financial,PHP,JAVA"
    },
    "company.colleagues.2.name": {
        "error_type": "validation",
        "message": "company.colleagues.2.name 必须是字符串"
    },
    "company.colleagues.3.position": {
        "error_type": "validation",
        "message": "company.colleagues.3.position 必须是字符串且在此之内 Reception,Financial,PHP,JAVA"
    },
    "company.boss.0": {
        "error_type": "validation",
        "message": "company.boss.0 必须等于 Mike"
    },
    "company.boss.2": {
        "error_type": "validation",
        "message": "company.boss.2 必须是字符串且在此之内 Johnny,David"
    }
}
```

### 第三种
> $validation->get_error(true, true);

```
{
    "id": "用户自定义 - id 是必要的",
    "name": "name 长度必须大于 8 且小于等于 32",
    "email": "email 必须是邮箱",
    "phone": "用户自定义 - phone number 错误",
    "education": {
        "primary_school": "education.primary_school 必须等于 Qiankeng Xiaoxue",
        "junior_middle_school": "education.junior_middle_school 必须不等于 Foshan Zhongxue"
    },
    "company": {
        "name": "company.name 长度必须大于 8 且小于等于 64",
        "website": "company.website 必须是网址",
        "colleagues": [
            {
                "name": "company.colleagues.0.name 必须是字符串"
            },
            {
                "name": "company.colleagues.1.name 必须是字符串",
                "position": "company.colleagues.1.position 必须是字符串且在此之内 Reception,Financial,PHP,JAVA"
            },
            {
                "name": "company.colleagues.2.name 必须是字符串"
            },
            {
                "position": "company.colleagues.3.position 必须是字符串且在此之内 Reception,Financial,PHP,JAVA"
            }
        ],
        "boss": {
            "0": "company.boss.0 必须等于 Mike",
            "2": "company.boss.2 必须是字符串且在此之内 Johnny,David"
        }
    }
}
```
### 第四种
> $validation->get_error(true, false);


```
{
    "id": {
        "error_type": "required_field",
        "message": "用户自定义 - id 是必要的"
    },
    "name": {
        "error_type": "validation",
        "message": "name 长度必须大于 8 且小于等于 32"
    },
    "email": {
        "error_type": "validation",
        "message": "email 必须是邮箱"
    },
    "phone": {
        "error_type": "validation",
        "message": "用户自定义 - phone number 错误"
    },
    "education": {
        "primary_school": {
            "error_type": "validation",
            "message": "education.primary_school 必须等于 Qiankeng Xiaoxue"
        },
        "junior_middle_school": {
            "error_type": "validation",
            "message": "education.junior_middle_school 必须不等于 Foshan Zhongxue"
        }
    },
    "company": {
        "name": {
            "error_type": "validation",
            "message": "company.name 长度必须大于 8 且小于等于 64"
        },
        "website": {
            "error_type": "validation",
            "message": "company.website 必须是网址"
        },
        "colleagues": [
            {
                "name": {
                    "error_type": "validation",
                    "message": "company.colleagues.0.name 必须是字符串"
                }
            },
            {
                "name": {
                    "error_type": "validation",
                    "message": "company.colleagues.1.name 必须是字符串"
                },
                "position": {
                    "error_type": "validation",
                    "message": "company.colleagues.1.position 必须是字符串且在此之内 Reception,Financial,PHP,JAVA"
                }
            },
            {
                "name": {
                    "error_type": "validation",
                    "message": "company.colleagues.2.name 必须是字符串"
                }
            },
            {
                "position": {
                    "error_type": "validation",
                    "message": "company.colleagues.3.position 必须是字符串且在此之内 Reception,Financial,PHP,JAVA"
                }
            }
        ],
        "boss": {
            "0": {
                "error_type": "validation",
                "message": "company.boss.0 必须等于 Mike"
            },
            "2": {
                "error_type": "validation",
                "message": "company.boss.2 必须是字符串且在此之内 Johnny,David"
            }
        }
    }
}
```

---
