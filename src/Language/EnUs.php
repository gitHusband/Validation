<?php

/**
 * zn-us
 */
class EnUs
{
    public $error_template = array(
        'default' => '@this validation failed',
        'index_array' => '@this must be a numeric array',
        'required' => '@this can not be empty',
        'optional_unset' => '@this must be unset or not empty',
        'preg' => '@this format is invalid, should be @preg',
        'preg_format' => '@this method @preg is not a valid regular expression',
        'call_method' => '@method is undefined',
        '=' => '@this must be equal to @p1',
        '!=' => '@this must be not equal to @p1',
        '==' => '@this must be identically equal to @p1',
        '!==' => '@this must be not identically equal to @p1',
        '>' => '@this must be greater than @p1',
        '<' => '@this must be less than @p1',
        '>=' => '@this must be greater than or equal to @p1',
        '<=' => '@this must be less than or equal to @p1',
        '<>' => '@this must be greater than @p1 and less than @p2',
        '<=>' => '@this must be greater than @p1 and less than or equal to @p2',
        '<>=' => '@this must be greater than or equal to @p1 and less than @p2',
        '<=>=' => '@this must be greater than or equal to @p1 and less than or equal to @p2',
        '(n)' => '@this must be numeric and in @p1',
        '!(n)' => '@this must be numeric and can not be in @p1',
        '(s)' => '@this must be string and in @p1',
        '!(s)' => '@this must be string and can not be in @p1',
        'len=' => '@this length must be equal to @p1',
        'len!=' => '@this length must be not equal to @p1',
        'len>' => '@this length must be greater than @p1',
        'len<' => '@this length must be less than @p1',
        'len>=' => '@this length must be greater than or equal to @p1',
        'len<=' => '@this length must be less than or equal to @p1',
        'len<>' => '@this length must be greater than @p1 and less than @p2',
        'len<=>' => '@this length must be greater than @p1 and less than or equal to @p2',
        'len<>=' => '@this length must be greater than or equal to @p1 and less than @p2',
        'len<=>=' => '@this length must be greater than or equal to @p1 and less than or equal to @p2',
        'int' => '@this must be integer',
        'float' => '@this must be float',
        'string' => '@this must be string',
        'arr' => '@this must be array',
        'bool' => '@this must be boolean',
        'bool=' => '@this must be boolean @p1',
        'bool_str' => '@this must be boolean string',
        'bool_str=' => '@this must be boolean string @p1',
        'email' => '@this must be email',
        'url' => '@this must be url',
        'ip' => '@this must be IP address',
        'mac' => '@this must be MAC address',
        'dob' => '@this must be a valid date',
        'file_base64' => '@this must be a valid file base64',
        'uuid' => '@this must be a UUID',
        'oauth2_grant_type' => '@this is not a valid OAuth2 grant type'
    );
}
