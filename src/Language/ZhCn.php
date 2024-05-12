<?php

/**
 * zh-cn
 */
class ZhCn
{
    /**
     * The error message templates for the system rules and method symbols.
     * - Symbols rules: Such as "index_array" or "when"
     *   Some of the system rules can be customized. Such as ":when" -> ":?"
     *   Please use their default value here, use "required:when" instead of "required:?"
     * - Method symbols: The method name or its symbol, such as "=" or "length>"
     *   Suggest using symbols instead of method names here.
     *
     * @var array
     */
    public $error_templates = [
        'default' => '@this 验证错误',
        'index_array' => '@this 必须是索引数组',
        'when' => '在特定情况下，',
        'when_not' => '在非特定情况下，',
        'required' => '@this 不能为空',
        'required:when' => '在特定情况下，@this 不能为空',
        'required:when_not' => '在非特定情况下，@this 不能为空',
        'optional' => '@this 永远不会出错',
        'optional:when' => '在特定情况下，@this 才能为空',
        'optional:when_not' => '在非特定情况下，@this 才能为空',
        'optional_unset' => '@this 允许不设置，且一旦设置则不能为空',
        'optional_unset:when' => '在特定情况下，@this 允许不设置，且一旦设置则不能为空。否则不能为空',
        'optional_unset:when_not' => '在非特定情况下，@this 允许不设置，且一旦设置则不能为空。否则不能为空',
        'preg' => '@this 格式错误，必须是 @preg',
        'preg_format' => '@this 方法 @preg 不是合法的正则表达式',
        'call_method' => '@method 未定义',
        '=' => '@this 必须等于 @p1',
        '!=' => '@this 必须不等于 @p1',
        '==' => '@this 必须严格等于 @t1(@p1)',
        '!==' => '@this 必须严格不等于 @t1(@p1)',
        '>' => '@this 必须大于 @p1',
        '<' => '@this 必须小于 @p1',
        '>=' => '@this 必须大于等于 @p1',
        '<=' => '@this 必须小于等于 @p1',
        '<>' => '@this 必须大于 @p1 且小于 @p2', // @deprecated 2.3.0
        '><' => '@this 必须大于 @p1 且小于 @p2',
        '<=>' => '@this 必须大于 @p1 且小于等于 @p2', // @deprecated 2.3.0
        '><=' => '@this 必须大于 @p1 且小于等于 @p2',
        '<>=' => '@this 必须大于等于 @p1 且小于 @p2', // @deprecated 2.3.0
        '>=<' => '@this 必须大于等于 @p1 且小于 @p2',
        '<=>=' => '@this 必须大于等于 @p1 且小于等于 @p2', // @deprecated 2.3.0
        '>=<=' => '@this 必须大于等于 @p1 且小于等于 @p2',
        '(n)' => '@this 必须是数字且在此之内 @p1', // @deprecated 2.3.0
        '<number>' => '@this 必须是数字且在此之内 @p1',
        '!(n)' => '@this 必须是数字且不在此之内 @p1', // @deprecated 2.3.0
        '!<number>' => '@this 必须是数字且不在此之内 @p1',
        '(s)' => '@this 必须是字符串且在此之内 @p1', // @deprecated 2.3.0
        '<string>' => '@this 必须是字符串且在此之内 @p1',
        '!(s)' => '@this 必须是字符串且不在此之内 @p1', // @deprecated 2.3.0
        '!<string>' => '@this 必须是字符串且不在此之内 @p1',
        'len=' => '@this 长度必须等于 @p1', // @deprecated 2.3.0
        'length=' => '@this 长度必须等于 @p1',
        'len!=' => '@this 长度必须不等于 @p1', // @deprecated 2.3.0
        'length!=' => '@this 长度必须不等于 @p1',
        'len>' => '@this 长度必须大于 @p1', // @deprecated 2.3.0
        'length>' => '@this 长度必须大于 @p1',
        'len<' => '@this 长度必须小于 @p1', // @deprecated 2.3.0
        'length<' => '@this 长度必须小于 @p1',
        'len>=' => '@this 长度必须大于等于 @p1', // @deprecated 2.3.0
        'length>=' => '@this 长度必须大于等于 @p1',
        'len<=' => '@this 长度必须小于等于 @p1', // @deprecated 2.3.0
        'length<=' => '@this 长度必须小于等于 @p1',
        'len<>' => '@this 长度必须大于 @p1 且小于 @p2', // @deprecated 2.3.0
        'length><' => '@this 长度必须大于 @p1 且小于 @p2',
        'len<=>' => '@this 长度必须大于 @p1 且小于等于 @p2', // @deprecated 2.3.0
        'length><=' => '@this 长度必须大于 @p1 且小于等于 @p2',
        'len<>=' => '@this 长度必须大于等于 @p1 且小于 @p2', // @deprecated 2.3.0
        'length>=<' => '@this 长度必须大于等于 @p1 且小于 @p2',
        'len<=>=' => '@this 长度必须大于等于 @p1 且小于等于 @p2', // @deprecated 2.3.0
        'length>=<=' => '@this 长度必须大于等于 @p1 且小于等于 @p2',
        'int' => '@this 必须是整型',
        'float' => '@this 必须是小数',
        'string' => '@this 必须是字符串',
        'arr' => '@this 必须是数组', // @deprecated 2.3.0
        'array' => '@this 必须是数组',
        'bool' => '@this 必须是布尔型',
        'bool=' => '@this 必须是布尔型且等于 @p1',
        'bool_str' => '@this 必须是布尔型字符串', // @deprecated 2.3.0
        'bool_string' => '@this 必须是布尔型字符串',
        'bool_str=' => '@this 必须是布尔型字符串且等于 @p1', // @deprecated 2.3.0
        'bool_string=' => '@this 必须是布尔型字符串且等于 @p1',
        'email' => '@this 必须是邮箱',
        'url' => '@this 必须是网址',
        'ip' => '@this 必须是IP地址',
        'mac' => '@this 必须是MAC地址',
        'dob' => '@this 必须是正确的日期',
        'file_base64' => '@this 必须是正确的文件的base64码',
        'file_base64:mime' => '@this 文件类型必须是 @p1',
        'file_base64:size' => '@this 文件尺寸必须小于 @p2kb',
        'uuid' => '@this 必须是 UUID',
        'oauth2_grant_type' => '@this 必须是合法的 OAuth2 授权类型',
        // Datetime
        'datetime' => '@this 必须是格式正确的日期时间',
        'datetime:format:@p1' => '@this 必须是日期时间且格式为 @p1',
        'datetime:format:@p2' => '@this 必须是日期时间且格式为 @p2',
        'datetime:format:@p3' => '@this 必须是日期时间且格式为 @p3',
        'datetime:invalid_format:@p1' => '@this 格式 @p1 不是合法的日期时间格式',
        'datetime:invalid_format:@p2' => '@this 格式 @p2 不是合法的日期时间格式',
        'datetime:invalid_format:@p3' => '@this 格式 @p3 不是合法的日期时间格式',
        'datetime=' => '@this 日期时间必须等于 @p1',
        'datetime!=' => '@this 日期时间必须不等于 @p1',
        'datetime>' => '@this 日期时间必须大于 @p1',
        'datetime>=' => '@this 日期时间必须大于等于 @p1',
        'datetime<' => '@this 日期时间必须小于 @p1',
        'datetime<=' => '@this 日期时间必须小于等于 @p1',
        'datetime><' => '@this 日期时间必须大于 @p1 且小于 @p2',
        'datetime>=<' => '@this 日期时间必须大于等于 @p1 且小于 @p2',
        'datetime><=' => '@this 日期时间必须大于 @p1 且小于等于 @p2',
        'datetime>=<=' => '@this 日期时间必须在 @p1 和 @p2 之间',
        // Date
        'date' => '@this 必须是日期且格式为 Y-m-d',
        'date:format:@p1' => '@this 必须是日期且格式为 @p1',
        'date:format:@p2' => '@this 必须是日期且格式为 @p2',
        'date:format:@p3' => '@this 必须是日期且格式为 @p3',
        'date:invalid_format:@p1' => '@this 格式 @p1 不是合法的日期格式',
        'date:invalid_format:@p2' => '@this 格式 @p2 不是合法的日期格式',
        'date:invalid_format:@p3' => '@this 格式 @p3 不是合法的日期格式',
        'date=' => '@this 日期必须等于 @p1',
        'date!=' => '@this 日期必须不等于 @p1',
        'date>' => '@this 日期必须大于 @p1',
        'date>=' => '@this 日期必须大于等于 @p1',
        'date<' => '@this 日期必须小于 @p1',
        'date<=' => '@this 日期必须小于等于 @p1',
        'date><' => '@this 日期必须大于 @p1 且小于 @p2',
        'date>=<' => '@this 日期必须大于等于 @p1 且小于 @p2',
        'date><=' => '@this 日期必须大于 @p1 且小于等于 @p2',
        'date>=<=' => '@this 日期必须在 @p1 和 @p2 之间',
        // Time
        'time' => '@this 必须是时间且格式为 H:i:s',
        'time:format:@p1' => '@this 必须是时间且格式为 @p1',
        'time:format:@p2' => '@this 必须是时间且格式为 @p2',
        'time:format:@p3' => '@this 必须是时间且格式为 @p3',
        'time:invalid_format:@p1' => '@this 格式 @p1 不是合法的时间格式',
        'time:invalid_format:@p2' => '@this 格式 @p2 不是合法的时间格式',
        'time:invalid_format:@p3' => '@this 格式 @p3 不是合法的时间格式',
        'time=' => '@this 时间必须等于 @p1',
        'time!=' => '@this 时间必须不等于 @p1',
        'time>' => '@this 时间必须大于 @p1',
        'time>=' => '@this 时间必须大于等于 @p1',
        'time<' => '@this 时间必须小于 @p1',
        'time<=' => '@this 时间必须小于等于 @p1',
        'time><' => '@this 时间必须大于 @p1 且小于 @p2',
        'time>=<' => '@this 时间必须大于等于 @p1 且小于 @p2',
        'time><=' => '@this 时间必须大于 @p1 且小于等于 @p2',
        'time>=<=' => '@this 时间必须在 @p1 和 @p2 之间',
    ];
}
