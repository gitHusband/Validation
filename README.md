> 目录

- [Validation —— 一款功能丰富的 PHP 参数验证器](#validation--一款功能丰富的-php-参数验证器)
  - [1. 简介](#1-简介)
    - [1.1 特点](#11-特点)
  - [2. 安装](#2-安装)
  - [3. 规则测试示例](#3-规则测试示例)
  - [4. 功能介绍](#4-功能介绍)
    - [4.1 标志性语意](#41-标志性语意)
    - [4.2 支持正则表达式验证](#42-支持正则表达式验证)
    - [4.3 自定义验证函数的参数](#43-自定义验证函数的参数)
    - [4.4 自定义验证函数](#44-自定义验证函数)
    - [4.5 串联，并联验证](#45-串联并联验证)
    - [4.6 条件验证](#46-条件验证)
    - [4.7 无限嵌套的数据结构的验证](#47-无限嵌套的数据结构的验证)
    - [4.8 特殊的验证规则](#48-特殊的验证规则)
    - [4.9 自定义配置](#49-自定义配置)
    - [4.10 支持国际化配置](#410-支持国际化配置)
    - [4.11 支持一次性验证所有参数](#411-支持一次性验证所有参数)
    - [4.12 支持自定义错误信息](#412-支持自定义错误信息)
    - [4.13 支持多种错误信息格式](#413-支持多种错误信息格式)
  - [附录 1 - 函数标志及其含义](#附录-1---函数标志及其含义)
  - [附录 2. 验证完整示例](#附录-2-验证完整示例)
  - [附录 3. 错误信息格式](#附录-3-错误信息格式)
    - [第一种 - 一维字符型数组](#第一种---一维字符型数组)
    - [第二种 - 一维关联型数组](#第二种---一维关联型数组)
    - [第三种 - 无限嵌套字符型数组](#第三种---无限嵌套字符型数组)
    - [第四种 - 无限嵌套关联型数组](#第四种---无限嵌套关联型数组)



# Validation —— 一款功能丰富的 PHP 参数验证器

Validation 用于对后台参数的合法性检查。

> https://github.com/gitHusband/Validation

**有任何意见或想法，咱们可以一起交流探讨！**
**联系我：707077549@qq.com**

**# 为什么写这个工具？**
- 1. 对于后台参数，理论上对每个参数都应该进行合法性检查，尤其是那些需要转发给其他API接口或者需要存储到数据库的参数。
*比如，数据库基本上对数据长度类型等有限制，对于长度的验证可谓是简单繁琐，使用该工具可以大大简化代码。*
- 2. 如果参数过多，验证的代码量势必很大，无法直观通过代码明白参数格式。
- 3. 定制一个验证规则数组，规则数组长啥样，请求参数就长啥样。
- 4. 方便地多样化地设置验证方法返回的错误信息
- 5. ~~暂时想不到，想到了再给你们编。【狗头保命】~~

**# 下面简单介绍下该工具的用法：**
```php
// 请求参数
// 简单示例，实际上无论请求参数多么复杂，都支持一个验证规则数组完成验证
$data = [
    "id" => 1,
    "name" => "Peter",
    "age" => 18
];

// 验证规则数组
$rule = [
    "id" => "required|/^\d+$/",         // 必要的，且只能是数字
    "name" => "required|len<=>[3,32]"   // 必要的，且字符串长度必须大于3，小于等于32
];

// 实例化类，接受一个配置数组，但不必要
$config = [];
$validation = new Validation($config);

// 设置验证规则并验证数据，成功返回 true，失败返回 false
if($validation->set_rules($rule)->validate($data)) {
    // 这里获取验证结果，有被规则{$rule}验证到的参数，成功则修改其值为true，失败则修改其值为错误信息，
    // 没有被验证到的参数，保持原值不变。比如 age 保持 18 不变。
    return $validation->get_result();
}else {
    // 这里有两个参数，分别对应不同的错误信息格式，一共有四种错误信息可供选择。
    return $validation->get_error(true, false);
}
```

理论上，该工具是用于验证复杂的数据结构的，但如果你想验证单一字符串，也可以，例如

```
$validation->set_rules("required|string")->validate("Hello World!");
```

以上仅展示简单示例，实际上无论请求参数多么复杂，都支持一个验证规则数组完成验证。具体参考 [附录 2. 验证完整示例](#附录-2-验证完整示例)

## 1. 简介
### 1.1 特点
- 标志性语意，易于理解。采用*, >, <, >=, len>, int, (n), (s) 等函数标志，比如(n)表示in_array, 且必须是数字
- 支持正则表达式
- 支持自定义验证函数的参数
*@root(原数据), @parent(验证字段的父数据), @this(当前字段)，@anything(任意字段，如@id)*
- 支持自定义函数
- 支持串联验证(一个参数多个规则必须全部满足)，并联验证(一个参数多个规则满足其一即可)
- 支持条件验证，条件满足则继续验证后续规则，不满足则表明该字段是可选的
- 支持无限嵌套的数据结构的验证，包括关联数组，索引数组
- 支持特殊的验证规则
- 支持自定义配置，比如规则分隔符号"|"，参数分隔符号","等等
- 支持国际化配置，默认英语，支持自定义方法返回错误信息
- 支持一次性验证所有参数(默认)，也可设置参数验证失败后立即结束验证
- 支持自定义错误信息，支持多种格式的错误信息，无限嵌套或者一维数组的错误信息格式
- ~~暂时想不到，想到了再给你们编。【狗头保命】~~

## 2. 安装
> composer require githusband/validation

## 3. 规则测试示例
**注意**：在 src/Test 目录下已经内置了完整测试类Tests.php 和单元测试类 Unit.php。

**完整测试类**：
```
// 验证成功测试, 返回测试结果：
php Tests.php success
// 验证成功测试，返回错误信息：
php Tests.php error
// 附录2 测试代码
php Tests.php readme_case
```
详见[附录 2. 验证完整示例](#附录-2-验证完整示例)

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

### 4.1 标志性语意
为了便于理解以及简化规则，允许采用一些函数**标志**代表实际的函数。
```php
// 名字是必要的，必须是字符串，长度必须大于3且小于等于32
"name" => "required|string|length_greater_lessequal[3,32]"

// 采用函数标志，同上
// 若是觉得函数标志难以理解，请直接使用函数全称即可
"name" => "required|string|len<=>[3,32]"
```

例如:

标志 | 函数 | 含义
---|---|---
\* | required | 必要的，不允许为空
O | optional | 可选的，允许不设置或为空
O! | unset_required | 可选的，允许不设置，一旦设置则不能为空
\>[20] | greater_than | 数字必须大于20
len<=>[2,16] | length_greater_lessequal | 字符长度必须大于2且小于等于16
ip | ip | 必须是ip地址

**完整函数标志请查看 [附录 1 - 函数标志及其含义](#附录-1---函数标志及其含义)**

### 4.2 支持正则表达式验证
以 "**/**" 开头，以 "**/**" 结尾，表示是正则表达式
```php
// id 是必要的，且必须是数字
"id" => "required|/^\d+$/",
```

### 4.3 自定义验证函数的参数
1. **验证函数使用参数**
跟 PHP 函数使用参数一样，参数写在小括号`()`里面，多个参数以逗号`,`分隔
例如，
```
"age" => "equal(@this,20)"
```
*表示 age 必须等于20。这里的 `@this` 代表当前 age 字段的值。*

2. **函数默认参数**
当参数写在中括号[]里面时，首个 @this 参数可省略不写。
例如，上述例子可简写为
```
"age" => "equal[20]"
```

3. 当函数参数只有一个且为当前字段值时，可省略 () 和 [] ，只写函数名。

参数 | 含义
---|---
value | 代表参数是 value 字符串，允许为空。例如 20
@this | 代表参数是当前字段的值
@parent | 代表参数是当前字段的父元素的值
@root | 代表参数是整个原始验证数据
@field_name | 代表参数是整个验证数据中的字段名是 field_name 的字段

### 4.4 自定义验证函数
本工具已经内置了不少验证函数，例如 \*，>, len>=, ip 等等。详见[附录 3. 错误信息格式](#附录-3-错误信息格式)

如果验证规则比较复杂，内置函数无法满足需求，可以使用自定义函数验证

自定义函数验证支持两种方式：
1. 注册自定义函数的接口：add_method

```
// 注册一个自定义函数，check_postcode
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

若函数都不存在，则报错。

### 4.5 串联，并联验证
- 串联：一个参数多个规则必须全部满足，标志是 **|**
```
"age" => "required|equal[20]"
```
- 并联：一个参数多个规则满足其一即可，使用方法： {字段名} + **[or]**（标志是 **[||]** , 标志支持自定义，使用方法同 **[or]** ）
```php
// 串联，身高单位是必须的，且必须是 cm 或者 m
"height_unit" => "required|(s)[cm,m]",
// 并联
"height[or]" => [
    // 若身高单位是厘米 cm, 则身高必须大于等于100，小于等于200 
    "required|=(@height_unit,cm)|<=>=[100,200]",
    // 若身高单位是米 m, 则身高必须大于等于1，小于等于2
    "required|=(@height_unit,m)|<=>=[1,2]",
]
```

### 4.6 条件验证
条件验证写法也跟 PHP 语法差不多，"**if()**" 

正条件：**if()**

- 如果条件成立，则继续验证后续规则
- 如果条件不成立，说明该字段是可选的：1. 若该字段为空，立刻返回验证成功；2. 若该字段不为空，则继续验证后续规则

```php
$rule = [
    // 性别的必要的，且只能是 male(男性) 或 (female)女性
    "gender" => "required|(s)[male,female]",
    // 若性别是女性 female，则要求年龄大于22岁，若为男性，则对年龄无要求
    "age" => "if(=(@gender,female))|required|>[22]",
],
```
否条件：**!if()**

- 如果条件不成立，则继续验证后续规则
- 如果条件成立，说明该字段是可选的：1. 若该字段为空，立刻返回验证成功；2. 若该字段不为空，则继续验证后续规则

```php
$rule = [
    // 性别的必要的，且只能是 male(男性) 或 (female)女性
    "gender" => "required|(s)[male,female]",
    // 若性别不是女性 female，则要求年龄大于22岁，若为女性，则对年龄无要求
    "age" => "!if(=(@gender,female))|required|>[22]",
],
```

### 4.7 无限嵌套的数据结构的验证
支持无限嵌套的数据结构的验证，包括关联数组，索引数组

1. 无限嵌套的关联数组
验证数据怎么写，规则数组就怎么写。
```php
"data" => [
    "id" => 1,
    "name" => "Johnny",
    "favourite_fruit" => [
        "name" => "apple",
        "color" => "red",
        "shape" => "circular"
    ]
]

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

2. 无限嵌套的索引数组
索引数组字段名称后面加上标志 **.\***，或者索引数组加上唯一子元素 **\***
```php
$data = [
    "id" => 1,
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
    "favourite_fruits.*" => [
        "*" => [
            "name" => "required|len>[3]",
            "color" => "required|len>[3]",
            "shape" => "required|len>[3]"
        ]
    ]
];
```

**可选数组规则**

有时候，数组也是可选的，但是一旦设置，其中的子元素必须按规则验证，这时候只需要在数组字段名后面加上"**[optional]**" 标志，表示该数组可选，如：

```
"favourite_fruits[optional].*" => [
    "name" => "required|len>[4]",
    "color" => "required|len>[4]",
    "shape" => "required|len>[4]"
]
```
### 4.8 特殊的验证规则
支持的特殊规则有：

全称 | 标志 | 含义
---|---|---
[optional] | [O] | 表明字段是可选的，支持数组
[or] | [\|\|] | 表明单个字段是或规则，多个规则满足其一即可
 (无全称) | .* | 表明字段是可选的，支持数组

注意：标志使用方法和全称一样，且标志支持自定义为你喜欢的标志。

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
            "required|int|>[100]",
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


### 4.9 自定义配置
支持自定义的配置有：

```
$config = array(
    'language' => 'en-us',                                  // Language, default is en-us
    'lang_path' => '',                                      // Customer Language file path
    'validation_global' => true,                            // If true, validate all rules; If false, stop validating when one rule was invalid
    'auto_field' => "data",                                 // If root data is string or numberic array, add the auto_field to the root data, can validate these kind of data type.
    'reg_msg' => '/ >> (.*)$/',                             // Set special error msg by user 
    'reg_preg' => '/^(\/.+\/.*)$/',                         // If match this, using regular expression instead of method
    'reg_if' => '/^!?if\((.*)\)/',                          // If match this, validate this condition first
    'reg_if_true' => '/^if\((.*)\)/',                       // If match this, validate this condition first, if true, then validate the field
    'reg_if_false' => '/^!if\((.*)\)/',                     // If match this, validate this condition first, if false, then validate the field
    'symbol_rule_separator' => '|',                         // Rule reqarator for one field
    'symbol_param_classic' => '/^(.*)\\[(.*)\\]$/',         // If set function by this symbol, will add a @this parameter at first 
    'symbol_param_force' => '/^(.*)\\((.*)\\)$/',           // If set function by this symbol, will not add a @this parameter at first 
    'symbol_param_separator' => ',',                        // Parameters separator, such as @this,@field1,@field2
    'symbol_field_name_separator' => '.',                   // Field name separator, suce as "fruit.apple"
    'symbol_required' => '*',                               // Symbol of required field, Same as "required"
    'symbol_optional' => 'O',                               // Symbol of optional field, can be unset or empty, Same as "optional"
    'symbol_unset_required' => 'O!',                        // Symbol of optional field, can only be unset or not empty, Same as "unset_required"
    'symbol_or' => '[||]',                                  // Symbol of or rule, Same as "[or]"
    'symbol_array_optional' => '[O]',                       // Symbol of array optional rule, Same as "[optional]"
    'symbol_index_array' => '.*',                           // Symbol of index array rule
);
```
例如:

```
$validation_conf = array(
    'language' => 'en-us',                          // Language, default is en-us
    'validation_global' => true,                    // If true, validate all rules; If false, stop validating when one rule was invalid.
    'reg_msg' => '/ >>>(.*)$/',                     // Set special error msg by user 
    'reg_preg' => '/^Reg:(\/.+\/.*)$/',             // If match this, using regular expression instead of method
    'reg_if' => '/^IF[yn]?\?(.*)$/',                     // If match this, validate this condition first
    'reg_if_true' => '/^IFy?\?/',                   // If match this, validate this condition first, if true, then validate the field
    'reg_if_true' => '/^IFn\?/',                    // If match this, validate this condition first, if false, then validate the field
    'symbol_or' => '[or]',                          // Symbol of or rule
    'symbol_rule_separator' => '&&',                // Rule reqarator for one field
    'symbol_param_classic' => '/^(.*)~(.*)$/',      // If set function by this symbol, will add a @this parameter at first 
    'symbol_param_force' => '/^(.*)~~(.*)$/',       // If set function by this symbol, will not add a @this parameter at first 
    'symbol_param_separator' => ',',                // Parameters separator, such as @this,@field1,@field2
    'symbol_field_name_separator' => '->',          // Field name separator, suce as "fruit.apple"
    'symbol_required' => '!*',                      // Symbol of required field
    'symbol_optional' => 'o',                       // Symbol of optional field
    'symbol_array_optional' => '[o]',               // Symbol of array optional
    'symbol_index_array' => '[N]',                  // Symbol of index array
);
```
相关规则可以这么写：

```
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
```



### 4.10 支持国际化配置

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
        'check_custom' => '@this error!(CustomLang File)'
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
    'check_id' => '@this error!(customed)'
);

$validation->custom_language($lang_config);
```
以上为错误模版增加了一个check_id, 如果check_id 函数验证错误，则返回信息

```
'@this error!(customed)'
```

### 4.11 支持一次性验证所有参数
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

### 4.12 支持自定义错误信息
自定义错误信息的标志是 " >> ", 注意前后空格。

例如：

1. \*或者**正则**或者<=>=方法 错误都报错 "id is incorrect."
```
"id" => 'required|/^\d+$/|<=>=[1,100] >> @this is incorrect.'
```
2. 支持JSON 格式错误信息，为每一个方法设置不同的错误信息

```
"id" => 'required|/^\d+$/|<=>=[1,100] >> { "required": "Users define - @this is required", "preg": "Users define - @this should be \"MATCHED\" @preg"}'

# 对应的报错信息为
# id - Users define - id is required
# /^\d+$/ - Users define - id should be \"MATCHED\" /^\d+$/
# <=>= - id must be greater than or equal to 1 and less than or equal to 100
```
3. 支持特殊格式错误信息，为每一个方法设置不同的错误信息，同JSON

```
"id" => "required|/^\d+$/|<=>=[1,100] >> [required]=> Users define - @this is required [preg]=> Users define - @this should be \"MATCHED\" @preg"

# 对应的报错信息为
# id - Users define - id is required
# /^\d+$/ - Users define - id should be \"MATCHED\" /^\d+$/
# <=>= - id must be greater than or equal to 1 and less than or equal to 100
```

4. 支持错误信息数组，格式如下。
- 键 0: 验证规则
- 键 error_message：错误信息数组
```
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

**自定义函数也可自定义错误信息, 优先级低于 " >> " 和错误信息数组**

当函数返回值 === true 时，表示验证成功，否则表示验证失败

所以函数允许三种错误返回：
1. 直接返回 false
2. 返回错误信息字符串
3. 返回错误信息数组，默认有两个字段，error_type 和 message，支持自定义字段

```
function check_age($data, $gender, $param) {
    if($gender == "male") {
        // if($data > $param) return false;
        if($data > $param) return "@this should be greater than @p1 when gender is male";
    }else {
        if($data < $param) return array(
            'error_type' => 'server_error',
            'message' => '@this should be less than @p1 when gender is female',
            "extra" => "extra message"
        );
    }

    return true;
}
```
### 4.13 支持多种错误信息格式
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

详见[附录 3. 错误信息格式](#附录-3-错误信息格式)


---

## 附录 1 - 函数标志及其含义

标志 | 函数 | 含义
---|---|---
\* | required | 必要的，@this 不能为空
O | optional | 可选的，允许不设置或为空
O! | symbol_unset_required | 可选的，允许不设置，一旦设置则不能为空
= | equal | @this 必须等于 @p1
!= | not_equal | @this 必须不等于 @p1
== | identically_equal | @this 必须全等于 @p1
!== | not_identically_equal | @this 必须不全等于 @p1
\> | greater_than | @this 必须大于 @p1
< | less_than | @this 必须小于 @p1
\>= | greater_than_equal | @this 必须大于等于 @p1
<= | less_than_equal | @this 必须小于等于 @p1
<> | interval | @this 必须大于 @p1 且小于 @p2
<=> | greater_lessequal | @this 必须大于 @p1 且小于等于 @p2
<>= | greaterequal_less | @this 必须大于等于 @p1 且小于 @p2
<=>= | greaterequal_lessequal | @this 必须大于等于 @p1 且小于等于 @p2
(n) | in_number | @this 必须是数字且在此之内 @p1
!(n) | not_in_number | @this 必须是数字且不在此之内 @p1
(s) | in_string | @this 必须是字符串且在此之内 @p1
!(s) | not_in_string | @this 必须是字符串且不在此之内 @p1
len= | length_equal | @this 长度必须等于 @p1
len!= | length_not_equal | @this 长度必须不等于 @p1
len> | length_greater_than | @this 长度必须大于 @p1
len< | length_less_than | @this 长度必须小于 @p1
len>= | length_greater_than_equal | @this 长度必须大于等于 @p1
len<= | length_less_than_equal | @this 长度必须小于等于 @p1
len<> | length_interval | @this 长度必须大于 @p1 且小于 @p2
len<=> | length_greater_lessequal | @this 长度必须大于 @p1 且小于等于 @p2
len<>= | length_greaterequal_less | @this 长度必须大于等于 @p1 且小于 @p2
len<=>= | length_greaterequal_lessequal | @this 长度必须大于等于 @p1 且小于等于 @p2
int | integer | @this 必须是整型
float | float | @this 必须是小数
string | string | @this 必须是字符串
arr | arr | @this 必须是数组,
bool | bool | @this 必须是布尔型
bool= | bool | @this 必须是布尔型且等于 @p1
bool_str | bool_str | @this 必须是布尔型字符串
bool_str= | bool_str | @this 必须是布尔型字符串且等于 @p1
email | email | @this 必须是邮箱
url | url | @this 必须是网址
ip | ip | @this 必须是IP地址
mac | mac | @this 必须是MAC地址
dob | dob | @this 必须是正确的日期
file_base64 | file_base64 | @this 必须是正确的文件的base64码
uuid | uuid | @this 必须是 UUID

---

## 附录 2. 验证完整示例
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
        "id" => 'required|/^\d+$/ >> { "required": "用户自定义 - @this 是必要的", "preg": "用户自定义 - @this 必须匹配 @preg" }',
        // name 是必要的且必须是字符串且长度在区间 【8，32)
        "name" => "required|string|len<=>[8,32]",
        "email" => "required|email",
        "phone" => "required|/(^1[3|4|5|6|7|8|9]\d{9}$)|(^09\d{8}$)/ >> 用户自定义 - phone number 错误",
        // ip 是可选的
        "ip" => "optional|ip",
        "education" => [
            // education.primary_school 必须等于 “Qiankeng Xiaoxue”
            "primary_school" => "required|=[Qiankeng Xiaoxue]",
            "junior_middle_school" => "required|!=[Foshan Zhongxue]",
            "high_school" => "optional|string",
            "university" => "optional|string",
        ],
        "company" => [
            "name" => "required|len<=>[8,64]",
            "website" => "required|url",
            "colleagues.*" => [
                "name" => "required|string|len<=>[3,32]",
                // company.colleagues.*.position 必须等于 Reception,Financial,PHP,JAVA 其中之一
                "position" => "required|(s)[Reception,Financial,PHP,JAVA]"
            ],
            // 以下三个规则只对 boss.0, boss.1, boss.2 有效，boss.3 及其他都无效 
            "boss" => [
                "required|=[Mike]",
                "required|(s)[Johnny,David]",
                "optional|(s)[Johnny,David]"
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
    
    // 实例化类，不要忘了事先引入类文件
    // 接受一个配置数组，但不必要
    $validation = new Validation($validation_conf);
    
    // 设置验证规则并验证数据
    if($validation->set_rules($rule)->validate($data)) {
        // 这里获取验证结果，有被规则{$rule}验证到的参数，成功则修改其值为true，失败则修改其值为错误信息，
        // 没有被验证到的参数，保持原值不变。比如 age 保持 18 不变。
        return $validation->get_result();
    }else {
        // 这里有两个参数，分别对应不同的错误信息格式，一共有四种错误信息可供选择。
        return $validation->get_error(false, true);
    }
}

// 可以通过改变get_error 的两个参数，找到适合自己的报错格式
// 例子中的 $data 基本都不满足 $rule ，可以改变 $data 的值，检测验证规则是否正确
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


---
## 附录 3. 错误信息格式
### 第一种 - 一维字符型数组
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
### 第二种 - 一维关联型数组
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

### 第三种 - 无限嵌套字符型数组
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
### 第四种 - 无限嵌套关联型数组
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
