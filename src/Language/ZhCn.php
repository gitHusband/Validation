<?php

/**
 * zh-cn
 */
class ZhCn
{
    public $error_template = array(
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
        'arr' => '@me 必须是数组',
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
        'uuid' => '@me 必须是 UUID',
        'oauth2_grant_type' => '@me 必须是合法的 OAuth2 授权类型'
    );
}
