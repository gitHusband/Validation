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
        'file_base64' => '@this must be a valid file base64',
        'file_base64:mime' => '@this file mine must be euqal to @p1',
        'file_base64:size' => '@this file size must be less than @p2kb',
        'oauth2_grant_type' => '@this is not a valid OAuth2 grant type',
        'email' => '@this must be email',
        'url' => '@this must be url',
        'ip' => '@this must be IP address',
        'ipv4' => '@this must be IPv4 address',
        'ipv6' => '@this must be IPv6 address',
        'mac' => '@this must be MAC address',
        'dob' => '@this must be a valid date',
        'uuid' => '@this must be a UUID',
        'ulid' => '@this must be a ULID',
        'alpha' => '@this must only contain letters',
        'alpha_ext' => '@this must only contain letters and _-',
        'alpha_ext:@p2' => '@this must only contain letters and @p2',
        'alphanumeric' => '@this must only contain letters and numbers',
        'alphanumeric_ext' => '@this must only contain letters and numbers and _-',
        'alphanumeric_ext:@p2' => '@this must only contain letters and numbers and @p2',
        // Datetime
        'datetime' => '@this must be a valid datetime',
        'datetime:format:@p1' => '@this must be a valid datetime in format @p1',
        'datetime:format:@p2' => '@this must be a valid datetime in format @p2',
        'datetime:format:@p3' => '@this must be a valid datetime in format @p3',
        'datetime:invalid_format:@p1' => '@this format @p1 is not a valid datetime format',
        'datetime:invalid_format:@p2' => '@this format @p2 is not a valid datetime format',
        'datetime:invalid_format:@p3' => '@this format @p3 is not a valid datetime format',
        'datetime=' => '@this must be a valid datetime and equal to @p1',
        'datetime!=' => '@this must be a valid datetime and not equal to @p1',
        'datetime>' => '@this must be a valid datetime and greater than @p1',
        'datetime>=' => '@this must be a valid datetime and greater than or equal to @p1',
        'datetime<' => '@this must be a valid datetime and less than @p1',
        'datetime<=' => '@this must be a valid datetime and less than or equal to @p1',
        'datetime><' => '@this must be a valid datetime and greater than @p1 and less than @p2',
        'datetime>=<' => '@this must be a valid datetime and greater than or equal to @p1 and less than @p2',
        'datetime><=' => '@this must be a valid datetime and greater than @p1 and less than or equal to @p2',
        'datetime>=<=' => '@this datetime must be between @p1 and @p2',
        // Date
        'date' => '@this must be a valid date in format Y-m-d',
        'date:format:@p1' => '@this must be a valid date in format @p1',
        'date:format:@p2' => '@this must be a valid date in format @p2',
        'date:format:@p3' => '@this must be a valid date in format @p3',
        'date:invalid_format:@p1' => '@this format @p1 is not a valid date format',
        'date:invalid_format:@p2' => '@this format @p2 is not a valid date format',
        'date:invalid_format:@p3' => '@this format @p3 is not a valid date format',
        'date=' => '@this must be a valid date and equal to @p1',
        'date!=' => '@this must be a valid date and not equal to @p1',
        'date>' => '@this must be a valid date and greater than @p1',
        'date>=' => '@this must be a valid date and greater than or equal to @p1',
        'date<' => '@this must be a valid date and less than @p1',
        'date<=' => '@this must be a valid date and less than or equal to @p1',
        'date><' => '@this must be a valid date and greater than @p1 and less than @p2',
        'date>=<' => '@this must be a valid date and greater than or equal to @p1 and less than @p2',
        'date><=' => '@this must be a valid date and greater than @p1 and less than or equal to @p2',
        'date>=<=' => '@this date must be between @p1 and @p2',
        // Time
        'time' => '@this must be a valid time in format H:i:s',
        'time:format:@p1' => '@this must be a valid time in format @p1',
        'time:format:@p2' => '@this must be a valid time in format @p2',
        'time:format:@p3' => '@this must be a valid time in format @p3',
        'time:invalid_format:@p1' => '@this format @p1 is not a valid time format',
        'time:invalid_format:@p2' => '@this format @p2 is not a valid time format',
        'time:invalid_format:@p3' => '@this format @p3 is not a valid time format',
        'time=' => '@this must be a valid time and equal to @p1',
        'time!=' => '@this must be a valid time and not equal to @p1',
        'time>' => '@this must be a valid time and greater than @p1',
        'time>=' => '@this must be a valid time and greater than or equal to @p1',
        'time<' => '@this must be a valid time and less than @p1',
        'time<=' => '@this must be a valid time and less than or equal to @p1',
        'time><' => '@this must be a valid time and greater than @p1 and less than @p2',
        'time>=<' => '@this must be a valid time and greater than or equal to @p1 and less than @p2',
        'time><=' => '@this must be a valid time and greater than @p1 and less than or equal to @p2',
        'time>=<=' => '@this time must be between @p1 and @p2',
    ];
}
