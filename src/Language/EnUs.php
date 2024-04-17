<?php

/**
 * zn-us
 */
class EnUs
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
        'default' => '@this validation failed',
        'index_array' => '@this must be a numeric array',
        'when' => 'Under certain circumstances, ',
        'when_not' => 'When certain circumstances are not met, ',
        'required' => '@this can not be empty',
        'required:when' => 'Under certain circumstances, @this can not be empty',
        'required:when_not' => 'When certain circumstances are not met, @this can not be empty',
        'optional' => '@this never go wrong',
        'optional:when' => '@this can be empty only when certain circumstances are met',
        'optional:when_not' => '@this can be empty only when certain circumstances are not met',
        'optional_unset' => '@this must be unset or must not be empty if it\'s set',
        'optional_unset:when' => 'Under certain circumstances, @this must be unset or must not be empty if it\'s set. Otherwise it can not be empty',
        'optional_unset:when_not' => 'When certain circumstances are not met, @this must be unset or must not be empty if it\'s set. Otherwise it can not be empty',
        'preg' => '@this format is invalid, should be @preg',
        'preg_format' => '@this method @preg is not a valid regular expression',
        'call_method' => '@method is undefined',
        '=' => '@this must be equal to @p1',
        '!=' => '@this must be not equal to @p1',
        '==' => '@this must be strictly equal to @t1(@p1)',
        '!==' => '@this must not be strictly equal to @t1(@p1)',
        '>' => '@this must be greater than @p1',
        '<' => '@this must be less than @p1',
        '>=' => '@this must be greater than or equal to @p1',
        '<=' => '@this must be less than or equal to @p1',
        '<>' => '@this must be greater than @p1 and less than @p2', // @deprecated 2.3.0
        '><' => '@this must be greater than @p1 and less than @p2',
        '<=>' => '@this must be greater than @p1 and less than or equal to @p2', // @deprecated 2.3.0
        '><=' => '@this must be greater than @p1 and less than or equal to @p2',
        '<>=' => '@this must be greater than or equal to @p1 and less than @p2', // @deprecated 2.3.0
        '>=<' => '@this must be greater than or equal to @p1 and less than @p2',
        '<=>=' => '@this must be greater than or equal to @p1 and less than or equal to @p2', // @deprecated 2.3.0
        '>=<=' => '@this must be greater than or equal to @p1 and less than or equal to @p2',
        '(n)' => '@this must be numeric and in @p1', // @deprecated 2.3.0
        '<number>' => '@this must be numeric and in @p1',
        '!(n)' => '@this must be numeric and can not be in @p1', // @deprecated 2.3.0
        '!<number>' => '@this must be numeric and can not be in @p1',
        '(s)' => '@this must be string and in @p1', // @deprecated 2.3.0
        '<string>' => '@this must be string and in @p1',
        '!(s)' => '@this must be string and can not be in @p1', // @deprecated 2.3.0
        '!<string>' => '@this must be string and can not be in @p1',
        'len=' => '@this length must be equal to @p1', // @deprecated 2.3.0
        'length=' => '@this length must be equal to @p1',
        'len!=' => '@this length must be not equal to @p1', // @deprecated 2.3.0
        'length!=' => '@this length must be not equal to @p1',
        'len>' => '@this length must be greater than @p1', // @deprecated 2.3.0
        'length>' => '@this length must be greater than @p1',
        'len<' => '@this length must be less than @p1', // @deprecated 2.3.0
        'length<' => '@this length must be less than @p1',
        'len>=' => '@this length must be greater than or equal to @p1', // @deprecated 2.3.0
        'length>=' => '@this length must be greater than or equal to @p1',
        'len<=' => '@this length must be less than or equal to @p1', // @deprecated 2.3.0
        'length<=' => '@this length must be less than or equal to @p1',
        'len<>' => '@this length must be greater than @p1 and less than @p2', // @deprecated 2.3.0
        'length><' => '@this length must be greater than @p1 and less than @p2',
        'len<=>' => '@this length must be greater than @p1 and less than or equal to @p2', // @deprecated 2.3.0
        'length><=' => '@this length must be greater than @p1 and less than or equal to @p2',
        'len<>=' => '@this length must be greater than or equal to @p1 and less than @p2', // @deprecated 2.3.0
        'length>=<' => '@this length must be greater than or equal to @p1 and less than @p2',
        'len<=>=' => '@this length must be greater than or equal to @p1 and less than or equal to @p2', // @deprecated 2.3.0
        'length>=<=' => '@this length must be greater than or equal to @p1 and less than or equal to @p2',
        'int' => '@this must be integer',
        'float' => '@this must be float',
        'string' => '@this must be string',
        'arr' => '@this must be array', // @deprecated 2.3.0
        'array' => '@this must be array',
        'bool' => '@this must be boolean',
        'bool=' => '@this must be boolean @p1',
        'bool_str' => '@this must be boolean string', // @deprecated 2.3.0
        'bool_string' => '@this must be boolean string',
        'bool_str=' => '@this must be boolean string @p1', // @deprecated 2.3.0
        'bool_string=' => '@this must be boolean string @p1',
        'email' => '@this must be email',
        'url' => '@this must be url',
        'ip' => '@this must be IP address',
        'mac' => '@this must be MAC address',
        'dob' => '@this must be a valid date',
        'file_base64' => '@this must be a valid file base64',
        'uuid' => '@this must be a UUID',
        'oauth2_grant_type' => '@this is not a valid OAuth2 grant type'
    ];
}
