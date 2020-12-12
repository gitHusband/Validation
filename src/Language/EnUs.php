<?php 

/**
 * zn-us
 */
class EnUs
{
    public $error_template = array(
        'default' => '@me validation failed',
        'numeric_array' => '@me must be a numeric array',
        'required' => '@me can not be empty',
        'preg' => '@me format is invalid, should be @preg',
        'call_method' => '@method is undefined',
        '=' => '@me must be equal to @p1',
        '!=' => '@me must be not equal to @p1',
        '==' => '@me must be identically equal to @p1',
        '!==' => '@me must be not identically equal to @p1',
        '>' => '@me must be greater than @p1',
        '<' => '@me must be less than @p1',
        '>=' => '@me must be greater than or equal to @p1',
        '<=' => '@me must be less than or equal to @p1',
        '<>' => '@me must be greater than @p1 and less than @p2',
        '<=>' => '@me must be greater than @p1 and less than or equal to @p2',
        '<>=' => '@me must be greater than or equal to @p1 and less than @p2',
        '<=>=' => '@me must be greater than or equal to @p1 and less than or equal to @p2',
        '(n)' => '@me must be numeric and in @p1',
        '!(n)' => '@me must be numeric and can not be in @p1',
        '(s)' => '@me must be string and in @p1',
        '!(s)' => '@me must be string and can not be in @p1',
        'len=' => '@me length must be equal to @p1',
        'len!=' => '@me length must be not equal to @p1',
        'len>' => '@me length must be greater than @p1',
        'len<' => '@me length must be less than @p1',
        'len>=' => '@me length must be greater than or equal to @p1',
        'len<=' => '@me length must be less than or equal to @p1',
        'len<>' => '@me length must be greater than @p1 and less than @p2',
        'len<=>' => '@me length must be greater than @p1 and less than or equal to @p2',
        'len<>=' => '@me length must be greater than or equal to @p1 and less than @p2',
        'len<=>=' => '@me length must be greater than or equal to @p1 and less than or equal to @p2',
        'int' => '@me must be integer',
        'float' => '@me must be float',
        'string' => '@me must be string',
        'arr' => '@me must be array',
        'bool' => '@me must be boolean',
        'bool=' => '@me must be boolean @p1',
        'bool_str' => '@me must be boolean string',
        'bool_str=' => '@me must be boolean string @p1',
        'email' => '@me must be email',
        'url' => '@me must be url',
        'ip' => '@me must be IP address',
        'mac' => '@me must be MAC address',
        'dob' => '@me must be a valid date',
        'file_base64' => '@me must be a valid file base64',
        'uuid' => '@me must be a UUID'
    );
}
