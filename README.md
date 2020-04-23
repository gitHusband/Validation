# Validation —— 一款功能丰富的 PHP 参数验证器

Validation 用于对后台参数的合法性检查。使用方便，直观。

至于为啥写这个工具的**原因**：
- 1. 对于后台参数，理论上对每个参数都应该进行合法性检查，尤其是那些需要转发给其他API接口或者需要存储到数据库的参数。比如，数据库基本上对数据长度类型等有限制，对于长度的验证可谓是简单繁琐，使用该工具可以大大简化代码。

- 2. 如果参数过多，验证的代码量势必太大，凌乱不直观。使用该工具，只需要定制一套验证规则数组即可，美观直观。
- 3. 对于某些需要版本控制的API接口，每个版本的参数可能不尽相同，使用该工具，可以更加直观的记录参数的整体格式，每个版本的参数也可简化成一套验证规则。
- 4. ~~暂时想不到，想到了再给你们编。~~

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
## 1. 简介
### 1.1 特点
- 语意明确，易于理解。采用*, >, <, >=, len>, int, (n), (s) 等函数标志，比如(n)表示in_array, 且必须是数字
- 支持正则表达式验证
- 支持条件验证，条件满足则继续验证后续规则，不满足则表明该字段是可选择
- 支持串联(一个参数多个规则同时满足, &&)，并联(一个参数多个规则满足其一, ||)验证
- 支持自定义函数验证
- 支持函数验证时自由传参，@root(原数据), @parent(验证字段的父数据), @me(当前字段)，@anything(任意字段)
- 支持无限嵌套的数据结构的验证，包括关联数组，索引数组
- 支持自定义配置，比如规则分隔符号"|"，参数分隔符号","等等
- 支持国际化配置，默认英语
- 支持一次性验证所有参数(默认)，也可设置参数验证失败后立即结束验证
- 支持自定义错误信息，支持多种格式的错误信息，无限嵌套或者一维数组的错误信息格式
- ~~暂时想不到，想到了再给你们编。~~

## 2. 安装
暂不支持compser安装，可手动下载放至项目里面引用即可。
## 3. 完整示例
```php
// 这是一个全局函数，规则里面的 age 字段会用到该函数，需要提前定义
function check_age($data, $gender, $param) {
    if($gender == "male") {
        if($data > $param) return false;
    }else {
        if($data < $param) return false;
    }

    return true;
}

function validate() {
    // 验证规则
    $rule = [
        "id" => "*|int|/^\d+$/",
        "name" => "*|string|len<=>:8,32",
        "gender" => "*|(s):male,female",
        "dob" => "*|dob",
        "age" => "*|check_age:@gender,30 >> @me is wrong",
        "height[||]" => [
            "*|=::@height_unit,cm|<=>=:100,200 >> @me should be in [100,200] when height_unit is cm",
            "*|=::@height_unit,m|<=>=:1,2 >> @me should be in [1,2] when height_unit is m",
        ],
        "height_unit" => "*|(s):cm,m",
        "weight[||]" => [
            "*|=::@weight_unit,kg|<=>=:40,100 >> @me should be in [40,100] when height_unit is kg",
            "*|=::@weight_unit,lb|<=>:88,220 >> @me should be in [88,220] when height_unit is lb",
        ],
        "weight_unit" => "*|(s):kg,lb",
        "email" => "*|email",
        "phone" => "*|/(^1[3|4|5|6|7|8|9]\d{9}$)|(^09\d{8}$)/ >> phone number error",
        "ip" => "O|ip",
        "mac" => "O|mac",
        "education" => [
            "primary_school" => "*|=:Qiankeng Xiaoxue",
            "junior_middle_school" => "*|!=:Foshan Zhongxue",
            "high_school" => "if?=::@junior_middle_school,Mianhu Zhongxue|*|len>:18",
            "university" => "O|bool_str",
        ],
        "company" => [
            "name" => "*|len<=>:8,64",
            "website" => "*|url",
            "country" => "O|len>=:6",
            "addr" => "*|len>:16",
            "postcode" => "O|len<:16|check_postcode::@parent",
            "colleagues[n]" => [
                "name" => "*|string|len<=>:3,32",
                "position" => "*|(s):Reception,Financial,PHP,JAVA"
            ],
            "boss" => [
                "*|=:Mike",
                "*|(s):Johnny,David",
                "O|(s):Johnny,David"
            ]
        ]
    ];
    
    // 待验证的数据
    $data = [
        "id" => "ABBC",
        "name" => "12",
        "gender" => "female2",
        "dob" => "2000-01-01",
        "age" => 11,
        "height" => 1.65,
        "height_unit" => "cm",
        "weight" => 80,
        "weight_unit" => "lb1",
        "email" => "10000@qq.com.123@qq",
        "phone" => "15620004000-",
        "ip" => "192.168.1.1111",
        "mac" => "06:19:C2:FA:36:2B111",
        "education" => [
            "primary_school" => "???Qiankeng Xiaoxue",
            "junior_middle_school" => "Foshan Zhongxue",
            "high_school" => "Mianhu Gaozhong",
            "university" => "false？？？",
        ],
        "company" => [
            "name" => "Qianken",
            "website" => "https://www.qiankeng.com1",
            "country" => "US",
            "addr" => "Foshan Nanhai",
            "postcode" => "532000",
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
        ]
    ];
    
    // 简单的自定义配置，不是完整的
    $validation_conf = [
        'language' => 'zh-cn',
        'validation_global' => true,
    ];
    
    // 实例化类，不要忘了事先引用类文件
    // 接受一个配置数组，但不必要
    $validation = new Validation($validation_conf);
    
    // 支持自定义验证函数，但不必要
    $validation->add_method('check_postcode', function($company) {
        if(isset($company['country']) && $company['country'] == "US"){
            if(!isset($company['postcode']) || $company['postcode'] != "123"){
                // 支持三种返回错误的方式
                // return false;
                // return "#### check_postcode method error message(@me)";
                return array(
                    'error_type' => 'server_error',
                    'message' => '*** check_postcode method error message(@me)',
                    "extra" => "extra message"
                );
            }
        }
    
        // 只有返回true，才表示验证成功，否则表示失败
        return true;
    });
    
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
print_r(validate());
```
## 4. 功能介绍

### 4.1 语意明确
为了便于理解，采用一些***标志***代表实际的功能。
```php
// 名字是必要的，必须是字符串，长度必须大于3且小于等于32
"name" => "*|string|len<=>:3,32",
```

例如:

标志 | 含义
---|---
\* | 必要的，不允许为空
O | 可选的，允许不设置或为空
\>:20 | 数字必须大于20
len<=>:2,16 | 字符长度必须大于2且小于等于16
ip | 必须是ip地址

**完整功能请查看附录1**

### 4.2 支持正则表达式验证
以 "**/**" 开头，以 "**/**" 结尾，表示是正则表达式
```php
// id 必须是数字
"id" => "*|/^\d+$/",
```

### 4.3 条件验证
条件验证标志是 "**if?**" 
- 如果条件成立，则继续验证后续规则
- 如果条件不成立，说明该字段是可选的：1. 若该字段为空，立刻返回验证成功；2. 若该字段不为空，则继续验证后续规则

```php
$rule = [
    "gender" => "*|(s):male,female",
    // 若性别是女性，则要求年龄大于22岁，若为男性，则对年龄无要求
    "age" => "if?=::female@gender,*|>:22",
],
```

### 4.4 支持串联，并联验证
- 串联：一个参数多个规则同时满足，标志是 **|**
- 并联：一个参数多个规则满足其一，标志是 {字段名} + **[||]**
```php
// 并联
"height[||]" => [
    // 若身高单位是cm, 则身高必须大于等于100，小于等于200 
    "*|=::@height_unit,cm|<=>=:100,200 >> @me should be in [100,200] when height_unit is cm",
    // 若身高单位是m, 则身高必须大于等于1，小于等于2
    "*|=::@height_unit,m|<=>=:1,2 >> @me should be in [1,2] when height_unit is m",
],
// 串联，身高单位是必须的，且必须是 cm 或者 m
"height_unit" => "*|(s):cm,m",
```

### 4.5 自定义函数验证
本工具已经内置了不少验证函数，例如 *，>, len>=, ip 等等

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
> 内置函数 > add_method > 全局函数

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
    "name" => "*|len>:4",
    "favourite_color" => [
        "*|len>:4",
        "*|len>:4",
    ],
    "favourite_fruits[n]" => [
        "name" => "*|len>:4",
        "color" => "*|len>:4",
        "shape" => "*|len>:4"
    ]
]
```
**发现了吗？**

关联数组和普通索引数组正常写就可以了，而索引数组里的元素是关联数组，则需要在字段后面加上 "**[n]**" 这个标志即可。

### 4.7 支持自定义配置
支持自定义的配置有：

```
$_config = array(
    'language' => 'en-us',                  // Language, default is en-us
    'validation_global' => true,            // If true, validate all rules; If false, stop validating when one rule was invalid.
    'reg_msg' => '/ >> (.*)$/',             // Set special error msg by user 
    'reg_preg' => '/^\/.+\/$/',             // If match this, using regular expression instead of method
    'reg_if' => '/^if\?/',                  // If match this, validate this condition first, if true, then validate the field.
    'symbol_or' => '[||]',                  // Symbol of or rule
    'symbol_rule_separator' => '|',         // Rule reqarator for one field
    'symbol_param_classic' => ':',          // If set function by this symbol, will add a @me parameter at first 
    'symbol_param_force' => '::',           // If set function by this symbol, will not add a @me parameter at first 
    'symbol_param_separator' => ',',        // Parameters separator, such as @me,@field1,@field2
    'symbol_field_name_separator' => '.',   // Field name separator, suce as "fruit.apple"
    'symbol_required' => '*',               // Symbol of required field
    'symbol_optional' => 'O',               // Symbol of optional field
    'symbol_numeric_array' => '[n]',        // Symbol of association array
);
```

### 4.8 支持国际化配置
默认语言是英语。
建议使用标准的语言代码，如"zh-cn", "en-us"等。

在 language 文件夹下新建 国际化文件，如 zh-cn.php。

内容如下：
```
<?php

return array(
    'default' => '@me 验证错误',
    'numeric_array' => '@me 必须是索引数组',
    'required' => '@me 不能为空',
    'preg' => '@me 格式错误，必须是 @preg',
    'call_method' => '@method 未定义',
    '=' => '@me 必须等于 @p1',
    '!=' => '@me 必须不等于 @p1',
    '==' => '@me 必须全等于 @p1',
    '!==' => '@me 必须不全等于 @p1',
    '>' => '@me 必须大于 @p1',
    '<' => '@me 必须小于 @p1',
    '>=' => '@me 必须大于等于 @p1',
    '<=' => '@me 必须小于等于 @p1',
    '<>' => '@me 必须大于 @p1 且小于 @p2',
    '<=>' => '@me 必须大于 @p1 且小于等于 @p2',
    '<>=' => '@me 必须大于等于 @p1 且小于 @p2',
    '<=>=' => '@me 必须大于等于 @p1 且小于等于 @p2',
    '(n)' => '@me 必须是数字且在此之内 @p1',
    '!(n)' => '@me 必须是数字且不在此之内 @p1',
    '(s)' => '@me 必须是字符串且在此之内 @p1',
    '!(s)' => '@me 必须是字符串且不在此之内 @p1',
    'len=' => '@me 长度必须等于 @p1',
    'len!=' => '@me 长度必须不等于 @p1',
    'len>' => '@me 长度必须大于 @p1',
    'len<' => '@me 长度必须小于 @p1',
    'len>=' => '@me 长度必须大于等于 @p1',
    'len<=' => '@me 长度必须小于等于 @p1',
    'len<>' => '@me 长度必须大于 @p1 且小于 @p2',
    'len<=>' => '@me 长度必须大于 @p1 且小于等于 @p2',
    'len<>=' => '@me 长度必须大于等于 @p1 且小于 @p2',
    'len<=>=' => '@me 长度必须大于等于 @p1 且小于等于 @p2',
    'int' => '@me 必须是整型',
    'float' => '@me 必须是小数',
    'string' => '@me 必须是字符串',
    'bool' => '@me 必须是布尔型',
    'bool=' => '@me 必须是布尔型且等于 @p1',
    'bool_str' => '@me 必须是布尔型字符串',
    'bool_str=' => '@me 必须是布尔型字符串且等于 @p1',
    'email' => '@me 必须是邮箱',
    'url' => '@me 必须是网址',
    'ip' => '@me 必须是IP地址',
    'mac' => '@me 必须是MAC地址',
    'dob' => '@me 必须是正确的日期',
    'file_base64' => '@me 必须是正确的文件的base64码',
);
```
最后需要修改语言配置

```
// 调用接口
$validation->set_language('zh-cn');

// 或者在实例化类的时候加入配置
$validation_conf = [
    'language' => 'zh-cn',
];

$validation = new Validation($validation_conf);
```
### 4.9 支持一次性验证所有参数
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

### 4.10 支持自定义错误信息
自定义错误信息的标志是 " >> ", 注意前后空格。

例如：

```
"phone" => "*|/(^1[3|4|5|6|7|8|9]\d{9}$)|(^09\d{8}$)/ >> phone number error",
```


---

## 附录 1

标志 | 含义
---|---
\* | 必要的，@me 不能为空
O | 可选的，允许不设置或为空
= | @me 必须等于 @p1
!= | @me 必须不等于 @p1
== | @me 必须全等于 @p1
!== | @me 必须不全等于 @p1
\> | @me 必须大于 @p1
< | @me 必须小于 @p1
\>= | @me 必须大于等于 @p1
<= | @me 必须小于等于 @p1
<> | @me 必须大于 @p1 且小于 @p2
<=> | @me 必须大于 @p1 且小于等于 @p2
<>= | @me 必须大于等于 @p1 且小于 @p2
<=>= | @me 必须大于等于 @p1 且小于等于 @p2
(n) | @me 必须是数字且在此之内 @p1
!(n) | @me 必须是数字且不在此之内 @p1
(s) | @me 必须是字符串且在此之内 @p1
!(s) | @me 必须是字符串且不在此之内 @p1
len= | @me 长度必须等于 @p1
len!= | @me 长度必须不等于 @p1
len> | @me 长度必须大于 @p1
len< | @me 长度必须小于 @p1
len>= | @me 长度必须大于等于 @p1
len<= | @me 长度必须小于等于 @p1
len<> | @me 长度必须大于 @p1 且小于 @p2
len<=> | @me 长度必须大于 @p1 且小于等于 @p2
len<>= | @me 长度必须大于等于 @p1 且小于 @p2
len<=>= | @me 长度必须大于等于 @p1 且小于等于 @p2
int | @me 必须是整型
float | @me 必须是小数
string | @me 必须是字符串
bool | @me 必须是布尔型
bool= | @me 必须是布尔型且等于 @p1
bool_str | @me 必须是布尔型字符串
bool_str= | @me 必须是布尔型字符串且等于 @p1
email | @me 必须是邮箱
url | @me 必须是网址
ip | @me 必须是IP地址
mac | @me 必须是MAC地址
dob | @me 必须是正确的日期
file_base64 | @me 必须是正确的文件的base64码

---






