<?php

// namespace Devin\Validation;

class Validation {
    /**
     * Validation rules. Default empty. Should be set before validation.
     * @var array
     */
    private $_rules = array();

    /**
     * Add by users using $this->add_method
     * @var array
     */
    private $_methods = array();

    /**
     * Back up the original data
     * @var array
     */
    private $_data = array();

    /**
     * Validation result of rules.
     * format: rule field => true | error message
     * @var array
     */
    private $_result = array();

    /**
     * Define $_result format
     * If set to true, replace field value to "true" if it's valid, replace field value to error_message if it's invalid.
     * If set to false, don't replace field value if it's valid, replace field value to error_message if it's invalid.
     * @var array
     */
    private $_result_classic = true;

    /**
     * Contains all error messages.
     * One-dimensional array
     * Format: parent_field.error_filed => error_message
     * @var array
     */
    private $_classic_errors = array(
        'simple' => array(),    // only message string
        'complex' => array()    // message array, contains error type and error message
    );

    /**
     * Contains all error messages.
     * Multidimensional array
     * Format: error_filed => error_message
     * @var array
     */
    private $_standard_errors = array(
        'simple' => array(),    // only message string
        'complex' => array()    // message array, contains error type and error message
    );

    private $_symbol_me = '@me';
    private $_symbol_root = '@root';
    private $_symbol_parent = '@parent';
    private $_symbol_preg = '@preg';

    private $_config = array(
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

    /**
     * While validating, if one field was invalid, set this to false;
     * @var boolean
     */
    private $_validation_status = true;

    /**
     * While validating, if one field was invalid, set this to false;
     * @var boolean
     */
    // private $_is_or_rule = true;
    // private $_or_status = true;

    /**
     * If true, validate all rules;
     * If false, stop validating when one rule was invalid.
     * @var boolean
     */
    private $_validation_global = true;

    /**
     * method template
     * mapped to real method
     * @var array
     */
    private $_method_template = array(
        '=' => 'equal',
        '!=' => 'not_equal',
        '==' => 'identically_equal',
        '!==' => 'not_identically_equal',
        '>' => 'greater_than',
        '<' => 'less_than',
        '>=' => 'greater_than_equal',
        '<=' => 'less_than_equal',
        '<>' => 'interval',
        '<=>' => 'greater_lessequal',
        '<>=' => 'greaterequal_less',
        '<=>=' => 'greaterequal_lessequal',
        '(n)' => 'in_number',
        '!(n)' => 'not_in_number',
        '(s)' => 'in_string',
        '!(s)' => 'not_in_string',
        'len=' => 'length_equal',
        'len!=' => 'length_not_equal',
        'len>' => 'length_greater_than',
        'len<' => 'length_less_than',
        'len>=' => 'length_greater_than_equal',
        'len<=' => 'length_less_than_equal',
        'len<>' => 'length_interval',
        'len<=>' => 'length_greater_lessequal',
        'len<>=' => 'length_greaterequal_less',
        'len<=>=' => 'length_greaterequal_lessequal',
        'int' => 'integer',
        'float' => 'float',
        'string' => 'string',
        'bool=' => 'bool',
        'bool_str=' => 'bool_str',
    );

    /**
     * If user don't set a error messgae, use this.
     * @var array
     */
    private $_error_template = array(
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
    );

    public function __construct($config=array())
    {
        $this->_initialzation($config);
    }

    private function _initialzation($config=array())
    {
        $this->_config = array_merge($this->_config, $config);

        $this->set_language($this->_config['language']);

        $this->set_validation_global($this->_config['validation_global']);
    }

    /**
     * If user don't set a error messgae, use this.
     * @var array
     */
    public function set_language($lang) {
        $lang_conf_file = __DIR__.'/language/'.$lang.'.php';

        if(file_exists($lang_conf_file)) {
            $lang_conf = include($lang_conf_file);
            $this->_error_template = $lang_conf;
            return true;
        }else {
            return false;
        }
    }

    /**
     * Set validation rules
     * @Author   Devin
     * @param    array                    $rules [description]
     */
    public function set_rules($rules = array())
    {
        if(empty($rules) || !is_array($rules)) {
            return $this;
        }

        $this->_rules = $rules;
        return $this;
    }

    /**
     * If false, stop validating data immediately when a field was invalid.
     * @Author   Devin
     * @param    boolean                   $bool
     */
    public function set_validation_global($bool) {
        $this->_validation_global = $bool;
        return $this;
    }

    /**
     * Allow user to add special methods 
     * @Author   Devin
     * @param    string                   $tag    tag name
     * @param    string                   $method function definition
     */
    public function add_method($tag, $method)
    {
        $this->_methods[$tag] = $method;
        return $this;
    }

    /**
     * Start validating
     * @Author   Devin
     * @param    array                    $data the data you want to validate
     * @return   boolean                  validation result
     */
    public function validate($data = array())
    {   
        $this->_data = $data;
        $this->_result = $data;
        $this->_errors = array();

        if(count($this->_rules) == 0) {
            return true;
        }

        $this->_validation_status = true;

        $this->_execute($data, $this->_rules);

        return $this->_validation_status;
    }

    /**
     * Execute validation with all data and all rules
     * @Author   Devin
     * @param    array                      $data       the data you want to validate
     * @param    array                      $rules      the rules you set
     * @param    boolean                    $field_path the current field path, suce as fruit.apple
     */
    private function _execute($data, $rules, $field_path=false)
    {
        foreach($rules as $field => $rule) {
            $field_path_tmp = '';
            if($field_path === false) $field_path_tmp = $field;
            else $field_path_tmp = $field_path. $this->_config['symbol_field_name_separator'] .$field;

            if(is_array($rule)) {
                // Validate "or" rules. 
                // If one of "or" rules is valid, then the field is valid.
                if(strpos($field, $this->_config['symbol_or']) !== false) {
                    $field = str_replace($this->_config['symbol_or'], '', $field);;
                    $field_path_tmp = str_replace($this->_config['symbol_or'], '', $field_path_tmp);

                    $or_result = false;
                    $or_len = count($rule);
                    foreach($rule as $key => $rule_or) {
                        $rule_or = $this->_parse_one_rule($rule_or);
                        $result = $this->_execute_one_rule($data, $field, $rule_or['rules'], $rule_or['msg'], $field_path_tmp, true);
                        $this->_set_result($field_path_tmp, $result);
                        if($result) {
                            // $or_result = true;
                            $this->_unset($this->_standard_errors['simple'], $field_path_tmp);
                            $this->_unset($this->_standard_errors['complex'], $field_path_tmp);
                            unset($this->_classic_errors['simple'][$field_path_tmp]);
                            unset($this->_classic_errors['complex'][$field_path_tmp]);
                            break;
                        }
                        if($key == $or_len-1) {
                            if(!$or_result) {
                                // If one of "or" rule is invalid, don't set _validation_status to false
                                // If all of "or" rule is invalid, then set _validation_status to false
                                $this->_validation_status = false;

                                if(!$this->_validation_global) return false;
                            }
                        }
                        
                    }
                // Validate numeric array
                }else if(strpos($field, $this->_config['symbol_numeric_array']) !== false) {
                    $field = str_replace($this->_config['symbol_numeric_array'], '', $field);;
                    $field_path_tmp = str_replace($this->_config['symbol_numeric_array'], '', $field_path_tmp);

                    if(!isset($data[$field]) || isset($data[$field]) && !$this->_is_numeric_array($data[$field])) {
                        $msg = $this->_get_error_template('numeric_array');
                        $msg = str_replace($this->_symbol_me, $field_path_tmp, $msg);
                        $message = array(
                            "error_type" => 'validation',
                            "message" => $msg,
                        );
                        $this->_set_error($field_path_tmp, $message);
                        $result = false;
                        if(!$result && !$this->_validation_global) {
                            return false;
                        }
                    }else {
                        foreach($data[$field] as $key => $value) {
                            $result = $this->_execute($data[$field][$key], $rule, $field_path_tmp.  $this->_config['symbol_field_name_separator'] .$key);
                            if(!$result && !$this->_validation_global) {
                                return false;
                            }
                        }
                    }
                // Validate association array
                }else {
                    $result = $this->_execute(isset($data[$field])? $data[$field]:null, $rule, $field_path_tmp);
                    if(!$result && !$this->_validation_global) {
                        return false;
                    }
                }
                
            }else {
                $rule = $this->_parse_one_rule($rule);
                $result = $this->_execute_one_rule($data, $field, $rule['rules'], $rule['msg'], $field_path_tmp);
                $this->_set_result($field_path_tmp, $result);
                // If _validation_global is set to false, stop validating when one rule was invalid.
                if(!$result && !$this->_validation_global) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Parse rule. Rule contains: 
     * 1. method and parameters
     * 2. regular expression
     * 3. error message
     * @Author   Devin
     * @param    string                   $rule 
     * @return   array                          
     */
    private function _parse_one_rule($rule)
    {
        $msg = '';

        if(preg_match($this->_config['reg_msg'], $rule, $matches)) {
            $msg = $matches[1];
            $rule = preg_replace($this->_config['reg_msg'], '', $rule);
        }

        // In consideration of the case that the regular expression cantains the character(|) which is the same as rule separator(|)
        // Using the way to handle regular expression
        // Only one regular expression is allowed in one rule string 
        $reg_flag = false;
        $reg_mark = "@reg_mark";
        // $reg_preg = '/\/.+?(?<!\\\)\//';
        $reg_preg = '/\/.+\|+.+?(?<!\\\)\//';
        if(preg_match_all($reg_preg, $rule, $matches)) {
            $rule = preg_replace($reg_preg, $reg_mark, $rule);
            $reg_flag = true;
        }

        $rules = empty($rule) ? array() : explode($this->_config['symbol_rule_separator'], trim($rule));

        if($reg_flag == true) {
            $i = 0;
            foreach($rules as &$value) {
                if(strpos($value, $reg_mark) !== false) {
                    $value = str_replace($reg_mark, $matches[0][$i], $value);
                    $i ++;
                }
            }
        }
        
        $parse_rule = array(
            'rules' => $rules,
            'msg' => $msg
        );

        return $parse_rule;
    }

    /**
     * Execute validation with field and its rule. Contains cases:
     * 1. If rule
     * 2. required(*) rule
     * 3. regular expression
     * 4. method
     * @Author   Devin
     * @param    array                      $data       The parent data of the field which is related to the rule
     * @param    string                     $field      The field which is related to the rule
     * @param    array                      $rules      The rule
     * @param    string                     $msg        The error message
     * @param    boolean                    $field_path Field path, suce as fruit.apple
     * @param    boolean                    $is_or_rule Flag of or rule
     * @return   boolean                                the result of validation
     */
    private function _execute_one_rule($data, $field, $rules = array(), $msg = '', $field_path=false, $is_or_rule=false)
    {
        if(empty($rules)) {
            return true;
        }

        foreach($rules as $rule) {
            if(empty($rule)) {
                continue;
            }

            $result = true;
            $error_type = 'validation';
            $if_flag = false;
            $params = array();

            // If rule
            if(preg_match($this->_config['reg_if'], $rule, $matches)) {
                $if_flag = true;

                $rule = preg_replace($this->_config['reg_if'], '', $rule);
                
                $method_rule = $this->_parse_method($rule, $data, $field);
                $params = $method_rule['params'];
                $result = $this->_execute_method($method_rule, $field_path);

                if($result === "Undefined") return false;
                if(!$result) {
                    // if it's a 'if' rule -> means this field is optional;
                    // If result is not true and this field is not set and not empty, no need to validate the other rule
                    // If result is not true and this field is set and not empty, need to validate the other rule
                    if(!isset($data[$field]) || !$this->required($data[$field])) return true;
                }

            // Required(*) rule
            }else if($rule == $this->_config['symbol_required']) {
                if(!isset($data[$field]) || !$this->required($data[$field])) {
                    $result = false;
                    $error_type = 'required_field';
                    if(empty($msg)) {
                        $msg = $this->_get_error_template('required');
                    }
                }

            // Optional(O) rule
            }else if($rule == $this->_config['symbol_optional']) {
                if(!isset($data[$field]) || !$this->required($data[$field])) {
                    return true;
                }

            // Regular expression
            }else if(preg_match($this->_config['reg_preg'], $rule, $matches)) {
                if(!preg_match($rule, $data[$field], $matches)) {
                    $result = false;
                    if(empty($msg)) {
                        $msg = $this->_get_error_template('preg');
                    }
                    $msg = str_replace($this->_symbol_preg, $rule, $msg);
                }

            // Method
            }else {
                $method_rule = $this->_parse_method($rule, $data, $field);
                $params = $method_rule['params'];
                $result = $this->_execute_method($method_rule, $field_path);

                if($result === "Undefined") return false;

                // If method validation is success. should return true.
                // If retrun anything others which is not equal to true, then means method validation error.
                // If retrun not a boolean value, will use the result as error message.
                if($result !== true) {
                    if(is_array($result)) {
                        $error_type = isset($result['error_type'])? $result['error_type'] : $error_type;
                        if(empty($msg)) $msg = isset($result['message'])? $result['message'] : $msg;
                    }else {
                        $msg = empty($msg)? $result : $msg;
                    }
                    
                    // $result = false;
                    
                    if(empty($msg)) {
                        $msg = $this->_get_error_template($method_rule['symbol'])? $this->_get_error_template($method_rule['symbol']) : $this->_get_error_template('default');
                    }
                }
            }

            // 1. if it's a 'if' rule -> result is not true, should set error
            // 2. if it's a 'if' rule -> means this field is optional; If result is not true, don't set error
            if($result !== true && !$if_flag) {
                // Replace symbol to field name and parameter value
                $msg = str_replace($this->_symbol_me, $field_path, $msg);
                foreach($params as $key => $value) {
                    if($key == 0) continue;
                    if(is_array($value)) {
                        if($this->_is_in_method($method_rule['symbol'])) {
                            $value = implode(',', $value);
                        }else {
                            continue;
                        }
                    }

                    $msg = str_replace('@p'.$key, $value, $msg);
                }

                $message = array(
                    "error_type" => $error_type,
                    "message" => $msg,
                );

                // Default fields: error_type, message
                // Allow user to add extra field in error message.
                if(is_array($result)) {
                    $message = array_merge($result, $message);
                }

                $this->_set_error($field_path, $message, $is_or_rule);
                return false;
            }

            
        }

        return true;
    }

    /**
     * Parse method and its parameters
     * @Author   Devin
     * @param    string                     $rule  One separation of rule
     * @param    array                      $data  The parent data of the field which is related to the rule
     * @param    string                     $field The field which is related to the rule
     * @return   array                             method detail
     */
    private function _parse_method($rule, $data, $field)
    {
        // If force parameter, will not add the field value as the first parameter even though no the field parameter
        if(strpos($rule, $this->_config['symbol_param_force']) !== false) {
            $pos = strpos($rule, $this->_config['symbol_param_force']);
            $offset = strlen($this->_config['symbol_param_force']);
            
            $method = substr($rule, 0, $pos);
            $params = substr($rule, $pos+$offset);
            $params = explode($this->_config['symbol_param_separator'], $params);
        // If classic parameter, will add the field value as the first parameter if no the field parameter
        }else if(strpos($rule, $this->_config['symbol_param_classic']) !== false) {
            $pos = strpos($rule, $this->_config['symbol_param_classic']);
            $offset = strlen($this->_config['symbol_param_classic']);
            
            $method = substr($rule, 0, $pos);
            $params = substr($rule, $pos+$offset);
            $params = explode($this->_config['symbol_param_separator'], $params);
            if(!in_array($this->_symbol_me, $params)) {
                array_unshift($params, $this->_symbol_me);
            }
        // If no parameter, will add the field value as the first parameter
        }else {
            $method = $rule;
            $params = array($this->_symbol_me);
        }

        $symbol = $method;
        $method = isset($this->_method_template[$method])? $this->_method_template[$method] : $method;

        foreach($params as &$param) {
            if(is_array($param)) continue;

            if(strpos($param, '@') !== false) {
                switch($param) {
                    case $this->_symbol_me:
                    $param = isset($data[$field])? $data[$field] : null;
                    break;
                    case $this->_symbol_parent:
                    $param = $data;
                    break;
                    case $this->_symbol_root:
                    $param = $this->_data;
                    break;
                    default: 
                    $param_field = substr($param, 1);
                    $param = $this->_get_field($data, $param_field);
                    if($param === null) $param = $this->_get_field($this->_data, $param_field);
                    break;
                }
            }
        }

        // "in" method, all the parameters are treated as the second parameter.
        if($this->_is_in_method($symbol)) {
            $field_name = $params[0];
            $in_array = array_shift($params);
            $params = array(
                $field_name,
                $params
            );
        }

        $method_rule = array(
            'method' => $method,
            'symbol' => $symbol,
            'params' => $params
        );

        return $method_rule;
    }

    /**
     * Execute method
     * @Author   Devin
     * @param    array                      $method_rule method detail
     * @param    string                     $field_path  The field which is related to the rule
     * @return   mixed                                  call result
     */
    private function _execute_method($method_rule, $field_path) {
        $params = $method_rule['params'];
        if(isset($this->_methods[$method_rule['method']])) {
            $result = call_user_func_array($this->_methods[$method_rule['method']], $params);
        }else if(method_exists(__CLASS__, $method_rule['method'])) {
            $result = call_user_func_array([__CLASS__, $method_rule['method']], $params);
        }else if (function_exists($method_rule['method'])) {
            $result = call_user_func_array($method_rule['method'], $params);
        }else {
            $msg = str_replace('@method', $method_rule['symbol'], $this->_error_template['call_method']);
            $message = array(
                "error_type" => 'internal_server_error',
                "message" => $msg,
            );
            $this->_set_error($field_path, $message);
            return "Undefined";
        }

        return $result;
    }

    /**
     * Get field pointer
     * When force is true, will create the field if field is not existed
     * @Author   Devin
     * @param    pointer                    &$data data pointer
     * @param    string                     $field field path
     * @param    boolean                    $force When force is true, will create the field if field is not existed
     * @return   pointer                    
     */
    private function &_get_field(&$data, $field, $force=false) {
        $point = &$data;

        $fields = explode($this->_config['symbol_field_name_separator'], $field);
        $len = count($fields);

        foreach($fields as $key => $value) {
            if(!isset($point[$value])){
                if(!$force) {
                    $point = null;
                    return $point;
                }

                if($key !== ($len - 1)) {
                    $point[$value] = array();
                    $point = &$point[$value];
                }else {
                    $point[$value] = 'Extra field';
                    $point = &$point[$value];

                    return $point;
                }
            }else {
                if($key !== ($len - 1)) {
                    $point = &$point[$value];
                }else {
                    $point = &$point[$value];
                    return $point;
                }
            }
        }
    }

    /**
     * Recursively delete fields
     * After unset a field, check the parent field, if empty, then unset the parent field
     * @Author   Devin
     * @param    pointer                    &$data data
     * @param    string                     $field field path, suce as fruit.apple
     * @param    boolean                    $force If false, don't unset a field when it's not empty
     * @return   boolean                     
     */
    private function _unset(&$data, $field, $force=true) {
        $point = &$data;

        $fields = explode($this->_config['symbol_field_name_separator'], $field);
        $len = count($fields);
        $parent_field = '';

        foreach($fields as $key => $value) {
            if(isset($point[$value])){
                if($key === ($len - 1)) {
                    if($force) {
                        unset($point[$value]);
                        if($key > 0) $this->_unset($data, $parent_field, false);
                    }else if(empty($point[$value])) {
                        unset($point[$value]);
                        if($key > 0) $this->_unset($data, $parent_field, false);
                    }
                    return true;
                }else {
                    $parent_field = $parent_field? $parent_field.'.'.$value : $value;
                    $point = &$point[$value];
                }
            }else {
                return true;
            }
        }
    }

    /**
     * error template message
     * @Author   Devin
     * @param    string                   $tag 
     * @return   [type]                        
     */
    private function _get_error_template($tag = '')
    {
        return ($tag == '' || !isset($this->_error_template[$tag])) ? false : $this->_error_template[$tag];
    }

    /**
     * Set error message
     * If one of "or" rule is invalid, don't set _validation_status to false
     * @Author   Devin
     * @param    string                   $field      field path
     * @param    string                   $message    error message
     * @param    boolean                  $is_or_rule Flag of or rule
     */
    private function _set_error_0($field = '', $message = '', $is_or_rule=false)
    {
        if(!$is_or_rule) $this->_validation_status = false;

        if(!isset($this->_classic_errors[$field])) {
            $this->_classic_errors[$field] = $message;
        }else {
            // $this->_classic_errors[$field] .= " or " . $message;
            if(is_array($message)) {
                $this->_classic_errors[$field]['message'] .= " or " . $message['message'];
            }else {
                $this->_classic_errors[$field] .= " or " . $message;
            }
        }

        $p_standard_error = & $this->_get_field($this->_standard_errors, $field, true);
        $p_standard_error = $this->_classic_errors[$field];

        return $this;
    }

    /**
     * Set error message
     * If one of "or" rule is invalid, don't set _validation_status to false
     * If all of "or" rule is invalid, will set _validation_status to false in other method
     * @Author   Devin
     * @param    string                   $field      field path
     * @param    string                   $message    error message
     * @param    boolean                  $is_or_rule Flag of or rule
     */
    private function _set_error($field = '', $message = '', $is_or_rule=false)
    {
        if(!$is_or_rule) $this->_validation_status = false;

        if(is_array($message)) {
            if(!isset($this->_classic_errors['simple'][$field])) {
                $this->_classic_errors['complex'][$field] = $message;
                $this->_classic_errors['simple'][$field] = isset($message['message'])? $message['message'] : 'Unknown error';
            }else {
                $msg = isset($message['message'])? $message['message'] : 'Unknown error';
                if($this->_classic_errors['complex'][$field]['message'] !== $msg) {
                    $this->_classic_errors['complex'][$field]['message'] .= " or " . $msg;
                    $this->_classic_errors['simple'][$field] .= " or " . $msg;
                }
            }
        }else {
            if(!isset($this->_classic_errors['simple'][$field])) {
                $this->_classic_errors['complex'][$field] = $message;
                $this->_classic_errors['simple'][$field] = $message;
            }else {
                if($this->_classic_errors['complex'][$field] !== $msg) {
                    $this->_classic_errors['complex'][$field] .= " or " . $message;
                    $this->_classic_errors['simple'][$field] .= " or " . $message;
                }
            }
        }

        $p_standard_error_simple = & $this->_get_field($this->_standard_errors['complex'], $field, true);
        $p_standard_error_simple = $this->_classic_errors['complex'][$field];

        $p_standard_error_complex = & $this->_get_field($this->_standard_errors['simple'], $field, true);
        $p_standard_error_complex = $this->_classic_errors['simple'][$field];

        return $this;
    }

    /**
     * Get error message
     * @Author   Devin
     * @param    boolean                  $fromat [description]
     * @return   [type]                           [description]
     */
    public function get_error($standard=true, $simple=true)
    {
        if(!$this->_validation_global) {
            return current($this->_classic_errors['simple']);
        }

        if($standard) {
            if($simple) return $this->_standard_errors['simple'];
            else return $this->_standard_errors['complex'];
        }else {
            if($simple) return $this->_classic_errors['simple'];
            else return $this->_classic_errors['complex'];
        }
    }

    /**
     * Set result classic
     * If set to true, replace field value to "true" if it's valid, replace field value to error_message if it's invalid.
     * If set to false, don't replace field value if it's valid, replace field value to error_message if it's invalid.
     * @Author   Devin
     * @param    [type]                   $bool [description]
     */
    public function set_result_classic($bool)
    {
        $this->_result_classic = $bool;
    }

    /**
     * Set result
     * If set to true, replace field value to "true" if it's valid, replace field value to error_message if it's invalid.
     * If set to false, don't replace field value if it's valid, replace field value to error_message if it's invalid.
     * @Author   Devin
     * @param    [type]                   $field  [description]
     * @param    [type]                   $result [description]
     */
    private function _set_result($field, $result)
    {   
        if($this->_result_classic != true && $result == true) return;

        $p_result = & $this->_get_field($this->_result, $field, true);
        if($result == true) {
            if($p_result !== 'Extra field') $p_result = true;
        }else {
            $p_result = $this->_classic_errors['complex'][$field];
        }
    }

    public function get_result()
    {
        return $this->_result;
    }

    public function get_data()
    {
        return $this->_data;
    }

    private function _string_length($string) {
        return mb_strlen($string);
    }

    private function _is_numeric_array($array) {
        return array_keys($array) === range(0, count($array) - 1);
    }

    private function _is_in_method($method) {
        return preg_match('/\(.*\)/', $method, $matches);
    }

    protected function required($data)
    {
        return !(empty($data) && !is_numeric($data)) || is_bool($data);
    }

    protected function equal($data, $param)
    {
        return $data == $param;
    }

    protected function not_equal($data, $param)
    {
        return $data != $param;
    }

    protected function identically_equal($data, $param)
    {
        return $data === $param;
    }

    protected function not_identically_equal($data, $param)
    {
        return $data !== $param;
    }

    protected function greater_than($data, $param)
    {
        return $data > $param;
    }

    protected function less_than($data, $param)
    {
        return $data < $param;
    }

    protected function greater_than_equal($data, $param)
    {
        return $data >= $param;
    }

    protected function less_than_equal($data, $param)
    {
        return $data <= $param;
    }

    protected function interval($data, $param1, $param2)
    {
        return $data > $param1 && $data < $param2;
    }

    protected function greater_lessequal($data, $param1, $param2)
    {
        return $data > $param1 && $data <= $param2;
    }

    protected function greaterequal_less($data, $param1, $param2)
    {
        return $data >= $param1 && $data < $param2;
    }

    protected function greaterequal_lessequal($data, $param1, $param2)
    {
        return $data >= $param1 && $data <= $param2;
    }

    protected function in_number($data, $param)
    {
        return is_numeric($data) && in_array($data, $param);
    }

    protected function not_in_number($data, $param)
    {
        return is_numeric($data) && !in_array($data, $param);
    }

    protected function in_string($data, $param)
    {
        return is_string($data) && in_array($data, $param);
    }

    protected function not_in_string($data, $param)
    {
        return is_string($data) && !in_array($data, $param);
    }

    protected function length_equal($data, $param)
    {   
        $data_len = $this->_string_length($data);
        return $data_len == $param;
    }

    protected function length_not_equal($data, $param)
    {
        $data_len = $this->_string_length($data);
        return $data_len != $param;
    }

    protected function length_greater_than($data, $param)
    {   
        $data_len = $this->_string_length($data);
        return $data_len > $param;
    }

    protected function length_less_than($data, $param)
    {
        $data_len = $this->_string_length($data);
        return $data_len < $param;
    }

    protected function length_greater_than_equal($data, $param)
    {
        $data_len = $this->_string_length($data);
        return $data_len >= $param;
    }

    protected function length_less_than_equal($data, $param)
    {
        $data_len = $this->_string_length($data);
        return $data_len <= $param;
    }

    protected function length_interval($data, $param1, $param2)
    {
        $data_len = $this->_string_length($data);
        return $data_len > $param1 && $data_len < $param2;
    }

    protected function length_greater_lessequal($data, $param1, $param2)
    {
        $data_len = $this->_string_length($data);
        return $data_len > $param1 && $data_len <= $param2;
    }

    protected function length_greaterequal_less($data, $param1, $param2)
    {
        $data_len = $this->_string_length($data);
        return $data_len >= $param1 && $data_len < $param2;
    }

    protected function length_greaterequal_lessequal($data, $param1, $param2)
    {
        $data_len = $this->_string_length($data);
        return $data_len >= $param1 && $data_len <= $param2;
    }

    protected function integer($data)
    {
        return is_int($data);
    }

    protected function float($data)
    {
        return is_float($data);
    }

    protected function string($data)
    {
        return is_string($data);
    }

    protected function bool($data, $bool=''){
        $bool = strtolower($bool);
        if($data === true || $data === false){
            if($bool === '') return TRUE;
            if($data === true && $bool === 'true'){
                return TRUE;
            }else if($data === false && $bool === 'false'){
                return TRUE;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    protected function bool_str($data, $bool=''){
        $data = strtolower($data);
        if($data === "true" || $data === "false"){
            if($bool === '') return TRUE;
            if($data === $bool){
                return TRUE;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    protected function email($data) {
        if(!empty($data) && !preg_match('/^\w+([-+.]\w*)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/', $data)){
            return FALSE;
        }else{
            return TRUE;
        }
    }

    protected function url($data) {
        if(!empty($data) && !preg_match('/^http(s?):\/\/(?:[A-za-z0-9-]+\.)+[A-za-z]{2,4}(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?$/', $data)){
            return FALSE;
        }else{
            return TRUE;
        }
    }

    protected function ip($data) {
        if(!empty($data) && !preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $data)){
            return FALSE;
        }else{
            return TRUE;
        }
    }

    protected function mac($data) {
        if(!empty($data) && !preg_match('/^((([a-f0-9]{2}:){5})|(([a-f0-9]{2}-){5})|(([a-f0-9]{2} ){5}))[a-f0-9]{2}$/i', $data)){
            return FALSE;
        }else{
            return TRUE;
        }
    }

    // date of birth
    protected function dob($date){
        if(preg_match('/^(\d{4})-(\d{2})-(\d{2})$/',$date,$arr)){
            $obj = new DateTime($date);
            $dob_time = $obj->format("U");
            $now = time();
            if(checkdate($arr[2],$arr[3],$arr[1]) && $dob_time < $now){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    private function file_base64_size($file_base64) {
        $file_base64 = preg_replace('/^(data:\s*(\w+\/\w+);base64,)/', '', $file_base64);
        $file_base64 = str_replace('=', '',$file_base64);
        $file_len = strlen($file_base64);
        $file_size = $file_len - ($file_len/8)*2;

        $file_size = number_format(($file_size/1024),2);

        return $file_size;
    }

    protected function file_base64($file_base64, $mime=false, $max_size=false) {
        if(preg_match('/^(data:\s*(\w+\/\w+);base64,)/', $file_base64, $matches)){
            $file_mime = $matches[2];
            if($mime !== false && $mime != $file_mime) {
                return false;
            }

            if($max_size !== false){
                $file_base64 = str_replace($matches[1], '', $file_base64);
                $file_size = $this->file_base64_size($file_base64);
                if($file_size > $max_size) {
                    return false;
                }
            }
        }else{
            return false;
        }

        return true;
    }
}
