# CHANGELOG

## v1 版本升级 v2 版本

### 规则格式的主要变化：

1. 把 `:` 替换成`[]`, 那 `::` 替换成`()`.
- [] 中括号支持自动传@this，() 括号不支持，刚好（）跟 正常调用一样
- 例如：`equal:20` 将变成 `equal[20]`; `equal::@age,20` 将变成 `equal(@age,20)`

搜索替换规则：`::([^\||"]*)` => `($1)`
搜索替换规则：`:([^\||"]*)` => `[$1]`

2. 把 `if?`，`if1?` 替换成 `if()`, 把 `if0?` 替换成 `!if()`
- 例如： `if?equal::@age,20|required` 替换成 `if(equal(@age,20))|required`

搜索替换规则：`if0\?([^\|]*)` => `!if($1)`
搜索替换规则：`if\?([^\|]*)` => `if($1)`

3. 把 @me 替换成 @this

### 修复项目规则的方法

1. 搜索替换规则：`if1?\?([^\|]*)` => `if($1)`
2. 搜索替换规则：`::([^\||"|)]*)(?=\)\|)` => `($1)`
3. 搜索替换规则：`::([^\||"]*)` => `($1)`
4. 搜索替换规则：`(?<!:| |\d|:\w|\]):([\w@\d][^\||"|:| >>]*)(?=\||"|'| >>)` => `[$1]`
5. 把 @me 替换成 @this

## v2.0.1 
版本更新后 validation_global = false 的错误
### 修复 Unit.php
1. 搜索替换规则：`"expected_msg" => ("(Users define - )?(Note! )?([^ ]*) .*")` => `"expected_msg" => [ "$4" => $1 ]`

## v2.1.0

1. 支持一个串联规则中， 包含多个正则表达式。如 `required|/^.*$/|/^[\w-]+$/i`
2. 支持判断正则表达式是否合法
3. 当验证过程中发生意外错误， 在错误信息前加上当前验证字段的信息，方便定位错误
4. 将规则方法与主逻辑区分开
5. 更新文档并增加英文文档
6. **修改 `get_error` 的参数(*)**，`true`,`false` 已经不推荐使用。
  - ERROR_FORMAT_NESTED_GENERAL
  - ERROR_FORMAT_NESTED_DETAILED
  - ERROR_FORMAT_DOTTED_GENERAL
  - ERROR_FORMAT_DOTTED_DETAILED
7. **更名的配置(*)：**
- symbol_param_classic -> `symbol_param_this_omitted`
- symbol_param_force -> `symbol_param_standard`
- unset_required -> `optional_unset`

## v2.2.0

1. 优化测试代码
2. 修复语言模板文件名的大小写问题
3. 支持 PHP 5+/7+/8+
4. 删除 PHP 5 不支持的 `empty` 方法
5. 修复 单元测试 支持多个 PHP 版本
6. 支持 Docker 一次性测试多个 PHP 版本
7. 推送代码后自动测试 composer 和 单元测试

## v2.2.1

### 条件规则优化(*)
1. 修改 `if` 和 `!if` 规则，如果不匹配条件则不验证后续规则
2. 以 `required:when` 和 `required:when_not` 替代旧的 `if` 和 `!if`
3. 任意规则和方法都支持 `when` 和 `when_not` 规则

## v2.3.0

### 配置优化

**更名的配置(*)：**
- symbol_param_standard -> `symbol_method_standard`
- symbol_param_this_omitted -> `symbol_method_omit_this`
- symbol_param_separator -> `symbol_parameter_separator`
- symbol_or -> `symbol_parallel_rule`

**新增的配置：**
- `is_strict_parameter_separator`: 是否严格解析分割一个方法中的多个参数。
  1. 默认 false：根据分隔符分割多个参数. 参数值中不允许包含分隔符(比如 ",")
  2. true：支持参数的值中包含 symbol_parameter_separator(比如 ",")
- `is_strict_parameter_type`: 支持探测参数类型。
  1. 默认 false：所有参数类型都是 `string`
  2. true：规则中的 数字，布尔值，数组，JSON型字符串，如果没有被单引号（`'`）或者双引号（`"`）包裹，将被强制转换为对应数据类型。
比如 `required|(n)[1,"2"]` 规则，1 是 `int`, "2" 则是 `string`.

**更名的属性(*)：**
- *default_config_backup* -> `config_backup`
- *symbol_full_name* -> `config_default`
- *language* -> `languages`
- **method_symbol** -> `method_symbols`
- **error_template** -> `error_templates`


### 方法优化

所有内置方法改为公共静态方法<strong>(*)</strong>： `public static function`

**更名的方法(*):**
- identically_equal -> `strictly_equal`
- not_identically_equal -> `not_strictly_equal`
- interval -> `between`
- length_interval -> `length_between`

**新增的单元测试：**
- 所有的内置方法。

### Bug

1. 修复方法名无法匹配其标志的 bug
2. 去除参数中首次成对的单/双引号：为了保证，无论参数 strict 与否，都能正常验证规则

## v2.4.0

### 优化方法及其标志

**更名的标志(*):**
- `len=` -> `length=`
- `len!=` -> `length!=`
- `len<=>=` -> `length>=<=`
- `len<>=` -> `length>=<`
- `len<=>` -> `length><=`
- `len>=` -> `length>=`
- `len<=` -> `length<=`
- `len<>` -> `length><`
- `len>` -> `length>`
- `len<` -> `length<`
- `<=>=` -> `>=<=`
- `<=>` -> `><=`
- `<>=` -> `>=<`
- `<>` -> `><`
- `(n)` -> `<number>`
- `!(n)` -> `!<number>`
- `(s)` -> `<string>`
- `!(s)` -> `!<string>`
- `arr` -> `array`
- `bool_str` -> `bool_string`
- `bool_str_equal` -> `bool_string_equal`
- `bool_str=` -> `bool_string=`

搜索替换规则：按上述顺序依次替换。
特殊的:
- `([|"'])arr` => `$1array`

**更名的方法(*):**
- `in_number` -> `in_number_array`
- `not_in_number` -> `not_in_number_array`
- `in_string` -> `in_string_array`
- `not_in_string` -> `not_in_string_array`
- `bool_str` -> `bool_string`
- `bool_str_equal` -> `bool_string_equal`

搜索替换规则：按上述顺序依次替换。
特殊的:
- `in_number(?!_array)` => `in_number_array`
- `in_string(?!_array)` => `in_string_array`

### 优化配置
默认为严格参数格式
- `is_strict_parameter_separator` 改为默认 true，并在后续版本中弃用该配置，请勿将其改为 false
- `is_strict_parameter_type` 改为默认 true，并在后续版本中弃用该配置，请勿将其改为 false

## v2.4.1

1. 支持嵌套数组作为参数
2. 优化注释，变量，方法名等，这里不修改逻辑，只改了名字和注释，为了便于后期维护

## v2.4.2

1. 参数支持 null 数据类型 和 空字符串 “” 或者 ‘’
2. 忽略规则或方法前后的空格
3. 忽略参数前后的空格
4. 不允许连续的规则分隔符和以分隔符结尾
5. 不允许连续的参数分隔符和以分隔符结尾
6. 检查参数中 JSON 的合法性
7. 规则不合法，则抛出 Exception
8. 优化注释，变量，方法名等，这里不修改逻辑，只改了名字和注释，为了便于后期维护

## v2.4.3

1. 删除废弃的配置（@deprecated 2.4.0）
- is_strict_parameter_separator
- is_strict_parameter_type

2. 优化错误消息模板
- 优化模板优先级，
- 方法返回错误消息模板，不会被国际化配置覆盖
- 支持从方法返回模板标签，从而允许国际化

## v2.4.4

1. 修复参数中，反斜杠的 bug

2. 更新 ‘between’ 方法，改为包含边界值。
- between -> greater_less
- length_between -> length_greater_less
- greaterequal_lessequal -> between
- length_greaterequal_lessequal -> length_between

3. 更新部分方法名
- greater_than_equal -> greater_equal
- less_than_equal -> less_equal
- length_greater_than_equal -> length_greater_equal
- length_less_than_equal -> length_less_equal

## v2.5.1

### 优化正则表达式的解析方法
- 支持在任意参数中直接使用 `/`

### 新增的方法
- `ulid`: `is_ulid`,
- `ipv4`: `is_ipv4`,
- `ipv6`: `is_ipv6`,
- `alpha`: `is_alpha`,
- `alpha_ext`: `is_alpha_ext`,
- `alphanumeric_ext`: `is_alphanumeric_ext`,
- `alphanumeric_ext`: `is_alphanumeric_ext`,
- `datetime`: `is_datetime`,
- `datetime=`: `datetime_equal`,
- `datetime!=`: `datetime_not_equal`,
- `datetime>`: `datetime_greater_than`,
- `datetime>=`: `datetime_greater_equal`,
- `datetime<`: `datetime_less_than`,
- `datetime<=`: `datetime_less_equal`,
- `datetime><`: `datetime_greater_less`,
- `datetime><=`: `datetime_greater_lessequal`,
- `datetime>=<`: `datetime_greaterequal_less`,
- `datetime>=<=`: `datetime_between`,
- `date`: `is_date`,
- `date=`: `date_equal`,
- `date!=`: `date_not_equal`,
- `date>`: `date_greater_than`,
- `date>=`: `date_greater_equal`,
- `date<`: `date_less_than`,
- `date<=`: `date_less_equal`,
- `date><`: `date_greater_less`,
- `date><=`: `date_greater_lessequal`,
- `date>=<`: `date_greaterequal_less`,
- `date>=<=`: `date_between`,
- `time`: `is_time`,
- `time=`: `time_equal`,
- `time!=`: `time_not_equal`,
- `time>`: `time_greater_than`,
- `time>=`: `time_greater_equal`,
- `time<`: `time_less_than`,
- `time<=`: `time_less_equal`,
- `time><`: `time_greater_less`,
- `time><=`: `time_greater_lessequal`,
- `time>=<`: `time_greaterequal_less`,
- `time>=<=`: `time_between`,

### 优化的方法名

Symbol | Old Method Name | New Method Name | Is Optimized
---|---|---|---
`email` | ~~email~~ | `is_email` ｜ Y
`url` | ~~url~~ | `is_url` | Y
`ip` | ~~ip~~ | `is_ip` | Y
`ipv4` | / | `is_ipv4` | /
`ipv6` | / | `is_ipv6` | /
`mac` | ~~mac~~ | `is_mac` | /
`uuid` | ~~uuid~~ | `is_uuid` | Y
`ulid` | / | `is_ulid` | /

## v2.5.2

- 新增一个方法去拓展验证方法：`add_rule_class`
  或者在拓展类的时候，直接插入到属性 `$this->rule_classes`
- 优化代码注释