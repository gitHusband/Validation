<?php

namespace githusband;

use githusband\Rule\RuleDefault;
use githusband\Exception\ghException;

class Validation
{
    use RuleDefault;

    const ERROR_FORMAT_NESTED_GENERAL = 'NESTED_GENERAL';
    const ERROR_FORMAT_NESTED_DETAILED = 'NESTED_DETAILED';
    const ERROR_FORMAT_DOTTED_GENERAL = 'DOTTED_GENERAL';
    const ERROR_FORMAT_DOTTED_DETAILED = 'DOTTED_DETAILED';

    /**
     * Validation rules. Default empty. Should be set before validation.
     * @var array
     */
    protected $rules = [];

    /**
     * Add by users using $this->add_method
     * @var array
     */
    protected $methods = [];

    /**
     * Back up the original data
     * @var array
     */
    protected $data = [];

    /**
     * Validation result of rules.
     * format: rule field => true | error message
     * @var array
     */
    protected $result = [];

    /**
     * Define $result format
     * If set to true, replace field value to "true" if it's valid, replace field value to error_message if it's invalid.
     * If set to false, don't replace field value if it's valid, replace field value to error_message if it's invalid.
     * @var array
     */
    protected $result_classic = true;

    /**
     * Contains all error messages.
     * One-dimensional array
     * Format:
     * {
     *      "A.1": "error_msg_A1",
     *      "A.2.a": "error_msg_A2a",
     * }
     * 
     * @var array
     */
    protected $dotted_errors = [
        'general' => [],    // only message string
        'detailed' => []    // message array, contains error type and error message
    ];

    /**
     * Contains all error messages.
     * Multidimensional array
     * Format: 
     * {
     *      "A": {
     *          "1": "error_msg_A1",
     *          "2": {
     *              "a": "error_msg_A2a"
     *          }
     *      }
     * }
     * @var array
     */
    protected $nested_errors = [
        'general' => [],    // only message string
        'detailed' => []    // message array, contains error type and error message
    ];

    /**
     * Current info of the recurrence validation: field path or its rule, etc.
     * If something get wrong, we can easily know which field or rule get wrong.
     * @var array
     */
    protected $recurrence_current = [
        'field_path' => '',
        'rule' => '',
    ];

    protected $symbol_this = '@this';
    protected $symbol_root = '@root';
    protected $symbol_parent = '@parent';
    protected $symbol_preg = '@preg';

    protected $default_config_backup;
    protected $config = [
        'language' => 'en-us',                                  // Language, default is en-us
        'lang_path' => '',                                      // Customer Language file path
        'validation_global' => true,                            // If true, validate all rules; If false, stop validating when one rule was invalid
        'auto_field' => "data",                                 // If root data is string or numberic array, add the auto_field to the root data, can validate these kind of data type.
        'reg_msg' => '/ >> (.*)$/',                             // Set special error msg by user 
        'reg_preg' => '/^(\/.+\/.*)$/',                         // If match this, using regular expression instead of method
        'reg_preg_strict' => '/^(\/.+\/[imsxADSUXJun]*)$/',     // Verify if the regular expression is valid
        'reg_if' => '/^!?if\((.*)\)/',                          // If match this, validate this condition first
        'reg_if_true' => '/^if\((.*)\)/',                       // If match this, validate this condition first, if true, then validate the field
        'reg_if_false' => '/^!if\((.*)\)/',                     // If match this, validate this condition first, if false, then validate the field
        'symbol_rule_separator' => '|',                         // Rule reqarator for one field
        'symbol_param_this_omitted' => '/^(.*)\\[(.*)\\]$/',    // If set function by this symbol, will add a @this parameter at first 
        'symbol_param_standard' => '/^(.*)\\((.*)\\)$/',        // If set function by this symbol, will not add a @this parameter at first 
        'symbol_param_separator' => ',',                        // Parameters separator, such as @this,@field1,@field2
        'symbol_field_name_separator' => '.',                   // Field name separator, suce as "fruit.apple"
        'symbol_required' => '*',                               // Symbol of required field, Same as "required"
        'symbol_optional' => 'O',                               // Symbol of optional field, can be unset or empty, Same as "optional"
        'symbol_optional_unset' => 'O!',                        // Symbol of optional field, can only be unset or not empty, Same as "optional_unset"
        'symbol_or' => '[||]',                                  // Symbol of or rule, Same as "[or]"
        'symbol_array_optional' => '[O]',                       // Symbol of array optional rule, Same as "[optional]"
        'symbol_index_array' => '.*',                           // Symbol of index array rule
    ];

    /**
     * See $config array, there are several symbol that are not semantically explicit.
     * So I set up the related full name for them
     *
     * The $symbol_full_name can not be customized and they are always meaningful
     * @var array
     */
    protected $symbol_full_name = [
        'symbol_required' => 'required',                // Symbol Full Name of required field
        'symbol_optional' => 'optional',                // Symbol Full Name of optional field
        'symbol_optional_unset' => 'optional_unset',    // Symbol Full Name of optional field
        'symbol_or' => '[or]',                          // Symbol Full Name of or rule
        'symbol_array_optional' => '[optional]',        // Symbol Full Name of array optional rule
    ];

    /**
     * While validating, if one field was invalid, set this to false;
     * @var boolean
     */
    protected $validation_status = true;

    /**
     * While validating, if one field was invalid, set this to false;
     * @var boolean
     */
    // protected $is_or_rule = true;
    // protected $or_status = true;

    /**
     * If true, validate all rules;
     * If false, stop validating when one rule was invalid.
     * @var boolean
     */
    protected $validation_global = true;

    /**
     * The method symbol
     * Using symbol mapped to real method. e.g. '=' => 'equal'
     * @see githusband\Rule\RuleDefault::$method_symbol_of_rule_default
     * @var array
     */
    protected $method_symbol = [];

    /**
     * Language file path
     * @var string
     */
    protected $lang_path = __DIR__ . '/Language/';

    /**
     * Languaue
     * @var array
     */
    protected $language = [];

    /**
     * If user don't set a error messgae, use this.
     * @see ./Language/EnUs.php
     * @see githusband\Rule\RuleDefault::$method_symbol_of_rule_default
     * @var array
     */
    protected $error_template = [];

    public function __construct($config = [])
    {
        $this->default_config_backup = $this->config;
        $this->initialzation($config);
    }

    protected function initialzation($config = [])
    {
        $this->config = array_merge($this->config, $config);

        $this->set_language($this->config['language']);

        $this->load_method_symbol();

        $this->set_validation_global($this->config['validation_global']);
    }

    /**
     * Set Config
     * @var array
     */
    public function set_config($config = [])
    {
        $this->config = array_merge($this->config, $config);

        if (isset($config['language'])) $this->set_language($this->config['language']);

        $this->set_validation_global($this->config['validation_global']);

        return $this;
    }

    /**
     * Get Config
     * @var array
     */
    public function get_config()
    {
        return $this->config;
    }

    /**
     * Reset Config
     */
    public function reset_config()
    {
        return $this->set_config($this->default_config_backup);
    }

    /**
     * Get all the traits from a class and its ancestors.
     * @return array
     */
    protected function get_all_traits()
    {
        $class = static::class;
        $traits = [];
        do {
            $class_traits = class_uses($class);
            // $reflector = new \ReflectionClass($class);
            // $class_traits = $reflector->getTraitNames();
            $traits = array_merge($class_traits, $traits);
        } while (($class = get_parent_class($class)) !== false);

        return array_unique($traits);
    }

    /**
     * Auto load the method symbol from rule traits
     * @return void
     */
    protected function load_method_symbol()
    {
        $used_traits = $this->get_all_traits();

        foreach ($used_traits as $key => $trait) {
            $slash_pos = strrpos($trait, '\\');
            if ($slash_pos !== false) $trait_name = substr($trait, strrpos($trait, '\\') + 1);
            else $trait_name = $trait;
            $trait_name_uncamelized = $this->uncamelize($trait_name);
            $trait_method_symbol = "method_symbol_of_{$trait_name_uncamelized}";
            if (property_exists($this, $trait_method_symbol)) {
                $this->method_symbol = array_merge($this->method_symbol, $this->{$trait_method_symbol});
            }
        }
    }

    /**
     * If user don't set a error messgae, use $lang.
     * The language file must exist.
     *
     * @param string $lang Languagu, suce as 'en-us'
     * @return static
     */
    public function set_language($lang)
    {
        $lang = $this->big_camelize($lang, '-');

        if (!empty($this->language[$lang])) {
            $this->error_template = $this->language[$lang];
        } else {
            $is_laod_file = false;
            // Customer language
            if (!empty($this->config['lang_path'])) {
                $lang_conf_file = $this->config['lang_path'] . $lang . '.php';
                if (file_exists($lang_conf_file)) {
                    require_once($lang_conf_file);
                    $is_laod_file = true;
                }
            }

            // If can not get customer language, get default language file 
            if ($is_laod_file == false) {
                $lang_conf_file = $this->lang_path . $lang . '.php';
                if (file_exists($lang_conf_file)) {
                    require_once($lang_conf_file);
                    $is_laod_file = true;
                }
            }

            if ($is_laod_file == true) {
                $lang_conf = new $lang();
                $this->custom_language($lang_conf, $lang);
            }
        }

        return $this;
    }

    /**
     * If user don't set a error messgae, use $lang_conf.
     * It allows user to custom their own language
     *
     * @param object $lang_conf Object instance
     * @param string $lang_name Languagu name
     * @return static
     */
    public function custom_language($lang_conf, $lang_name = '')
    {
        if (is_object($lang_conf)) {
            if (isset($lang_conf->error_template)) $this->error_template = array_merge($this->error_template, $lang_conf->error_template);
            if (!empty($lang_name)) $this->language[$lang_name] = $this->error_template;
        }

        return $this;
    }

    /**
     * Set validation rules
     *
     * @param array $rules
     * @return static
     */
    public function set_rules($rules = [])
    {
        if (empty($rules)) {
            return $this;
        }

        $this->rules = $rules;
        return $this;
    }

    /**
     * If false, stop validating data immediately when a field was invalid.
     *
     * @param [type] $bool
     * @return static
     */
    public function set_validation_global($bool)
    {
        $this->validation_global = $bool;
        return $this;
    }

    /**
     * Get validation_global flag
     *
     * @return bool
     */
    public function get_validation_global()
    {
        return $this->validation_global;
    }

    /**
     * Get Method Symbol
     *
     * @return array
     */
    public function get_method_symbol()
    {
        return $this->method_symbol;
    }

    /**
     * Get error message template
     *
     * @param string|null $tag
     * @return string|array
     */
    public function get_error_template($tag = null)
    {
        if ($tag === null) return $this->error_template;
        return ($tag == '' || !isset($this->error_template[$tag])) ? '' : $this->error_template[$tag];
    }

    /**
     * Allow user to add special methods 
     *
     * @param string $tag tag name
     * @param string $method function definition
     * @return static
     */
    public function add_method($tag, $method)
    {
        $this->methods[$tag] = $method;
        return $this;
    }

    /**
     * Start validating
     *
     * @param array $data The data you want to validate
     * @return bool validation result
     */
    public function validate($data = [])
    {
        $this->data = $data;
        $this->result = $data;
        $this->dotted_errors = [];
        $this->nested_errors = [];

        $this->validation_status = true;

        try {
            $this->execute($data, $this->rules);
        } catch (\Throwable $t) {
            $this->throw_gh_exception($t);
        }
        // For the PHP version < 7
        catch (\Exception $t) {
            $this->throw_gh_exception($t);
        }

        return $this->validation_status;
    }

    protected function throw_gh_exception($t)
    {
        if ($t instanceof ghException) {
            throw $t;
        } else {
            $current_field_path = $this->get_current_field_path();
            $current_field_path = empty($current_field_path) ? $this->config['auto_field'] : $current_field_path;
            $current_rule = $this->get_current_rule();
            $current_rule = empty($current_rule) ? 'NotSet' : $current_rule;
            throw (new ghException("@field:{$current_field_path}, @rule:{$current_rule} - " . $t->getMessage(), $t->getCode(), $t))
                ->set_recurrence_current($this->get_recurrence_current());
        }
    }

    /**
     * Execute validation with all data and all rules
     *
     * @param array $data The data you want to validate
     * @param array $rules The rules you set
     * @param bool $field_path The current field path, suce as fruit.apple
     * @param bool $is_array_loop If the execute method is called in array loop, $is_array_loop should be true
     * @return bool
     */
    protected function execute($data, $rules, $field_path = false, $is_array_loop = false)
    {
        $rules_system_symbol = $this->get_rule_system_symbol($rules);
        // If The root rules has rule_system_symbol
        // Or The root rules is String, means root data is not an array
        // Set root data as an array to help validate the data
        if (!empty($rules_system_symbol) || is_string($rules)) {
            $auto_field = $this->config['auto_field'];
            $data = [$auto_field => $data];
            $rules = [$auto_field => $rules];
            $this->data = $data;
            $this->result = $this->data;
        }

        foreach ($rules as $field => $rule) {
            $field_path_tmp = '';
            if ($field_path === false) $field_path_tmp = $field;
            else $field_path_tmp = $field_path . $this->config['symbol_field_name_separator'] . $field;
            $this->set_current_field_path($field_path_tmp)
                ->set_current_rule($rule);

            // if ($field == 'fruit_id') $a = UNDEFINED_VAR; // @see Unit::test_exception -> Case:Exception_lib_1

            $rule_system_symbol = $this->get_rule_system_symbol($rule);
            if (!empty($rule_system_symbol)) {
                // Allow array or object to be optional
                if ($this->has_system_symbol($rule_system_symbol, 'symbol_array_optional')) {
                    if (!$this->required(isset($data[$field]) ? $data[$field] : null)) {
                        $this->set_result($field_path_tmp, true);
                        continue;
                    }
                }

                // Validate "or" rules.
                // If one of "or" rules is valid, then the field is valid.
                if ($this->has_system_symbol($rule_system_symbol, 'symbol_or')) {
                    $result = $this->execute_or_rules($data, $field, $field_path_tmp, $rule[$rule_system_symbol]);
                }
                // Validate index array
                else if ($this->has_system_symbol($rule_system_symbol, 'symbol_index_array', true)) {
                    $result = $this->execute_index_array_rules($data, $field, $field_path_tmp, $rule[$rule_system_symbol], $is_array_loop);
                }
                // Validate association array
                else {
                    // Validate association array
                    if ($this->is_association_array_rule($rule[$rule_system_symbol])) {
                        $result = $this->execute(isset($data[$field]) ? $data[$field] : null, $rule[$rule_system_symbol], $field_path_tmp, $is_array_loop);
                    } else {
                        $this->set_current_field_path($field_path_tmp)
                            ->set_current_rule($rule);
                        $rule = $this->parse_one_rule($rule[$rule_system_symbol]);
                        $result = $this->execute_one_rule($data, $field, $rule, $field_path_tmp);
                        $this->set_result($field_path_tmp, $result);
                    }
                }

                // If _validation_global is set to false, stop validating when one rule was invalid.
                if (!$result && !$this->validation_global) return false;
            } else {
                // Allow array or object to be optional
                if ($this->has_system_symbol($field, 'symbol_array_optional')) {
                    $field = $this->delete_system_symbol($field, 'symbol_array_optional');
                    $field_path_tmp = $this->delete_system_symbol($field_path_tmp, 'symbol_array_optional');

                    // Delete all other array symbols
                    $field_tmp = $this->delete_system_symbol($field, 'symbol_or');
                    $field_tmp = $this->delete_system_symbol($field_tmp, 'symbol_index_array');

                    if (!$this->required(isset($data[$field_tmp]) ? $data[$field_tmp] : null)) {
                        $this->set_result($field_path_tmp, true);
                        continue;
                    }
                }

                // Validate "or" rules.
                // If one of "or" rules is valid, then the field is valid.
                if ($this->has_system_symbol($field, 'symbol_or')) {
                    $field = $this->delete_system_symbol($field, 'symbol_or');
                    $field_path_tmp = $this->delete_system_symbol($field_path_tmp, 'symbol_or');

                    $result = $this->execute_or_rules($data, $field, $field_path_tmp, $rule);
                }
                // Validate index array
                else if ($this->has_system_symbol($field, 'symbol_index_array')) {
                    $field = $this->delete_system_symbol($field, 'symbol_index_array');
                    $field_path_tmp = $this->delete_system_symbol($field_path_tmp, 'symbol_index_array');

                    $result = $this->execute_index_array_rules($data, $field, $field_path_tmp, $rule);
                }
                // Validate association array
                else if ($this->is_association_array_rule($rule)) {
                    $result = $this->execute(isset($data[$field]) ? $data[$field] : null, $rule, $field_path_tmp, $is_array_loop);
                } else {
                    $this->set_current_field_path($field_path_tmp)
                        ->set_current_rule($rule);
                    $rule = $this->parse_one_rule($rule);
                    $result = $this->execute_one_rule($data, $field, $rule, $field_path_tmp);

                    $this->set_result($field_path_tmp, $result);
                }

                // If _validation_global is set to false, stop validating when one rule was invalid.
                if (!$result && !$this->validation_global) return false;
            }
        }

        return true;
    }

    /**
     * There are some special rule system symbols
     * It's allowed to have multiple system symbols in one field name
     * 
     * [or] - It's default symbol is [||] which can be customized
     * [optional] - It's default symbol is [O] which can be customized
     * .* - This is a symbol which has not a full name and can be customized
     *
     * @param array $rule
     * @return array|string|bool
     */
    protected function get_rule_system_symbol($rule)
    {
        if (!is_array($rule)) return false;

        $keys = array_keys($rule);

        if (count($keys) != 1) return false;

        $rule_system_symbol_string = $keys[0];
        $rule_system_symbol_string_tmp = $rule_system_symbol_string;

        if ($this->has_system_symbol($rule_system_symbol_string, 'symbol_array_optional')) {
            $rule_system_symbol_string_tmp = $this->delete_system_symbol($rule_system_symbol_string_tmp, 'symbol_array_optional');
        }

        if ($this->has_system_symbol($rule_system_symbol_string, 'symbol_or')) {
            $rule_system_symbol_string_tmp = $this->delete_system_symbol($rule_system_symbol_string_tmp, 'symbol_or');
        }

        if ($this->has_system_symbol($rule_system_symbol_string, 'symbol_index_array')) {
            $rule_system_symbol_string_tmp = $this->delete_system_symbol($rule_system_symbol_string_tmp, 'symbol_index_array');
        } else if (strpos($this->config['symbol_index_array'], '.') === 0) {
            $symbol_index_array_tmp = ltrim($this->config['symbol_index_array'], '.');
            if ($this->has_system_symbol($rule_system_symbol_string, 'symbol_index_array', true)) {
                $rule_system_symbol_string_tmp = $this->delete_system_symbol($rule_system_symbol_string_tmp, 'symbol_index_array', true);
            }
        }

        if (!empty($rule_system_symbol_string_tmp)) return false;
        else return $rule_system_symbol_string;
    }

    /**
     * Check a field name contains a specific system symbol or not. 
     * Should check the system symbol and it's symbol full name at the meantime
     *
     * @param string $rule_system_symbol_string
     * @param string $symbol_name
     * @param bool $ingore_left_dot Only for symbol_index_array because symbol_index_array can ingore the left dot if it's not at the end of the field name
     * @return bool
     */
    protected function has_system_symbol($rule_system_symbol_string, $symbol_name, $ingore_left_dot = false)
    {
        switch ($symbol_name) {
            case 'symbol_array_optional':
                if (
                    strpos($rule_system_symbol_string, $this->config['symbol_array_optional']) !== false
                    || strpos($rule_system_symbol_string, $this->symbol_full_name['symbol_array_optional']) !== false
                ) {
                    return true;
                }
                break;
            case 'symbol_or':
                if (
                    strpos($rule_system_symbol_string, $this->config['symbol_or']) !== false
                    || strpos($rule_system_symbol_string, $this->symbol_full_name['symbol_or']) !== false
                ) {
                    return true;
                }
                break;
            case 'symbol_index_array':
                if (strpos($rule_system_symbol_string, $this->config['symbol_index_array']) !== false) {
                    return true;
                }

                if ($ingore_left_dot) {
                    if (strpos($rule_system_symbol_string, ltrim($this->config['symbol_index_array'], '.')) !== false) {
                        return true;
                    }
                }
                break;
            default:
                return false;
        }

        return false;
    }

    /**
     * Delete a specific system symbol from a field name. 
     * Should delete the system symbol and it's symbol full name at the meantime
     *
     * @param string $rule_system_symbol_string
     * @param string $symbol_name
     * @param bool $ingore_left_dot Only for symbol_index_array because symbol_index_array can ingore the left dot if it's not at the end of the field name
     * @param string $replace_str Replace the symbol to this string
     * @return string
     */
    protected function delete_system_symbol($rule_system_symbol_string, $symbol_name, $ingore_left_dot = false, $replace_str = '')
    {
        switch ($symbol_name) {
            case 'symbol_array_optional':
                $rule_system_symbol_string = str_replace($this->config['symbol_array_optional'], '', $rule_system_symbol_string);
                $rule_system_symbol_string = str_replace($this->symbol_full_name['symbol_array_optional'], '', $rule_system_symbol_string);
                return $rule_system_symbol_string;
                break;
            case 'symbol_or':
                $rule_system_symbol_string = str_replace($this->config['symbol_or'], '', $rule_system_symbol_string);
                $rule_system_symbol_string = str_replace($this->symbol_full_name['symbol_or'], '', $rule_system_symbol_string);
                return $rule_system_symbol_string;
                break;
            case 'symbol_index_array':
                $rule_system_symbol_string = str_replace($this->config['symbol_index_array'], '', $rule_system_symbol_string);
                if ($ingore_left_dot) $rule_system_symbol_string = str_replace(ltrim($this->config['symbol_index_array'], '.'), '', $rule_system_symbol_string);
                return $rule_system_symbol_string;
                break;
            default:
                return $rule_system_symbol_string;
        }

        return $rule_system_symbol_string;
    }

    /**
     * Execute validation of "or" rules.
     * There has two ways to add "or" rules:
     * 1. Add symbol_or in the end of the field. Such as $rule = [ "name[or]" => [ "*|string", "*|int" ] ];
     * 2. Add symbol_or as the only one child of the field. Such as $rule = [ "name" => [ "[or]" => [ "*|string", "*|int" ] ] ];
     * If one of "or" rules is valid, then the field is valid.
     *
     * @param array $data The parent data of the field which is related to the rules
     * @param string $field The field which is related to the rules
     * @param string $field_path Field path, suce as fruit.apple
     * @param array $rules The or rules
     * @return bool The result of validation
     */
    protected function execute_or_rules($data, $field, $field_path, $rules)
    {
        $or_len = count($rules);
        foreach ($rules as $key => $rule_or) {
            $this->set_current_rule($rule_or);

            // if ($key == 1) $a = UNDEFINED_VAR; // @see Unit::test_exception -> Case:Exception_lib_2

            $rule_or = $this->parse_one_rule($rule_or);
            $result = $this->execute_one_rule($data, $field, $rule_or, $field_path, true);
            $this->set_result($field_path, $result);
            if ($result) {
                $this->r_unset($this->nested_errors['general'], $field_path);
                $this->r_unset($this->nested_errors['detailed'], $field_path);
                unset($this->dotted_errors['general'][$field_path]);
                unset($this->dotted_errors['detailed'][$field_path]);
                return true;
            }
            if ($key == $or_len - 1) {
                // If one of "or" rule is invalid, don't set _validation_status to false
                // If all of "or" rule is invalid, then set _validation_status to false
                $this->validation_status = false;
                return false;
            }
        }
    }

    /**
     * Execute validation of index array rules.
     * There has two ways to add index array rules:
     * 1. Add symbol_index_array in the end of the field. Such as $rule = [ "name.*" => [ "*|string" ] ];
     * 2. Add symbol_index_array as the only one child of the field. Such as $rule = [ "name" => [ "*" => [ "*|string" ] ];
     *
     * @param array $data The parent data of the field which is related to the rules
     * @param string $field The field which is related to the rules
     * @param string $field_path Field path, suce as fruit.apple
     * @param array $rules The index array rules
     * @param bool $is_array_loop If the execute method is called in array loop, $is_array_loop should be true
     * @return bool The result of validation
     */
    protected function execute_index_array_rules($data, $field, $field_path, $rules, $is_array_loop = false)
    {
        if (!isset($data[$field]) || !$this->is_index_array($data[$field])) {
            $error_msg = $this->get_error_template('index_array');
            $error_msg = str_replace($this->symbol_this, $field_path, $error_msg);
            $message = [
                "error_type" => 'validation',
                "message" => $error_msg,
            ];
            $this->set_error($field_path, $message);
            return false;
        } else {
            $is_all_valid = true;
            foreach ($data[$field] as $key => $value) {
                $field_path_tmp = $field_path .  $this->config['symbol_field_name_separator'] . $key;

                $rule_system_symbol = $this->get_rule_system_symbol($rules);
                if (!empty($rule_system_symbol)) {
                    // $is_array_loop is true, means parent data is numberic arrya, too
                    $cur_field_path = $is_array_loop ? $field_path : $field_path_tmp;
                    $result = $this->execute($data[$field], [$key => $rules], $cur_field_path, true);
                } else if ($this->is_association_array_rule($rules)) {
                    $result = $this->execute($data[$field][$key], $rules, $field_path_tmp, true);
                }
                // Validate numberic array, all the rule are the same, only use $rules[0]
                else {
                    $this->set_current_field_path($field_path_tmp)
                        ->set_current_rule($rules);
                    $parsed_rules = $this->parse_one_rule($rules);
                    $result = $this->execute_one_rule($data[$field], $key, $parsed_rules, $field_path_tmp);
                    $this->set_result($field_path_tmp, $result);
                }

                $is_all_valid = $is_all_valid && $result;

                // If _validation_global is set to false, stop validating when one rule was invalid.
                if (!$result && !$this->validation_global) return false;
            }

            return $is_all_valid;
        }
    }

    /**
     * One rule object allows user to set error message in an object.
     * You don't have to set the rule and error message in one string.
     *
     * @param array $rule
     * @return bool
     */
    protected function is_one_rule_object($rule)
    {
        // Here is an one_rule_object example
        $one_rule_object_template = [
            'required|int',             // rule
            'error_message' => [        // error message
                'required' => 'It is request field',
                'int' => 'Must be integer',
            ]
        ];

        // $diff1 = array_diff_key($one_rule_object_template, $rule);
        // $diff2 = array_diff_key($rule, $one_rule_object_template);
        if (!array_diff_key($one_rule_object_template, $rule) && !array_diff_key($rule, $one_rule_object_template)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if it's a association array, except one_rule_object
     *
     * @param mixed $rule
     * @return bool
     */
    protected function is_association_array_rule($rule)
    {
        return is_array($rule) && !$this->is_one_rule_object($rule);
    }

    /**
     * Parse rule. Rule contains: 
     * 1. method and parameters
     * 2. regular expression
     * 3. error message
     *
     * @param string $rule
     * @return array
     */
    protected function parse_one_rule($rule)
    {
        if (is_array($rule)
            // && $this->is_one_rule_object($rule)
        ) {
            $error_msg = $rule['error_message'];
            $rule = $rule[0];
        } else {
            $error_msg = '';

            if (preg_match($this->config['reg_msg'], $rule, $matches)) {
                $error_msg = $matches[1];
                $rule = preg_replace($this->config['reg_msg'], '', $rule);
            }
        }

        $rules = $this->split_serial_rule_strict($rule);

        $parse_rule = [
            'rules' => $rules,
            'error_msg' => $this->parse_error_message($error_msg)
        ];

        return $parse_rule;
    }

    /**
     * Split a serial rule into multiple methods or regular expression by using the separator |
     * NOTE: 
     * - The regular expression may cantain the character(|) which is the same as rule separator(|)
     * - Only one regular expression is allowed in one serial rule
     *
     * @param string $rule
     * @return array
     */
    protected function split_serial_rule($rule)
    {
        // In consideration of the case that the regular expression cantains the character(|) which is the same as rule separator(|)
        // Using the way to handle regular expression
        // Only one regular expression is allowed in one serial rule
        $reg_flag = false;
        $reg_mark = "@reg_mark";
        // $reg_preg = '/\/.+?(?<!\\\)\//';
        $reg_preg = '/\/.+\|+.+?(?<!\\\)\//';
        if (preg_match_all($reg_preg, $rule, $matches)) {
            $rule = preg_replace($reg_preg, $reg_mark, $rule);
            $reg_flag = true;
        }

        $rules = empty($rule) ? [] : explode($this->config['symbol_rule_separator'], trim($rule));

        if ($reg_flag == true) {
            $i = 0;
            foreach ($rules as &$value) {
                if (strpos($value, $reg_mark) !== false) {
                    $value = str_replace($reg_mark, $matches[0][$i], $value);
                    $i++;
                }
            }
        }

        return $rules;
    }

    /**
     * Split a serial rule into multiple methods or regular expression by using the separator |
     * NOTE: 
     * - The regular expression may cantain the character(|) which is the same as rule separator(|)
     * - Multiple regular expressions are allowed in one serial rule
     *
     * @param string $rule
     * @return array
     */
    protected function split_serial_rule_strict($rule)
    {
        $symbol_rule_separator = $this->config['symbol_rule_separator'];
        $symbol_rule_separator_length = strlen($symbol_rule_separator);

        $rules = [];
        $current_method = '';
        $is_next_method_flag = 0;
        $is_reg_flag = 0;

        $rule_length = strlen($rule);
        for ($i = 0; $i < $rule_length; $i++) {
            $char = $rule[$i];

            // 支持自定义配置 symbol_rule_separator 为多个字符
            if ($symbol_rule_separator_length > 1 && $char == $symbol_rule_separator[0]) {
                $ii = $i + 1;
                $is_symbol_rule_separator = true;
                for ($j = 1; $j < $symbol_rule_separator_length; $j++) {
                    if ($symbol_rule_separator[$j] != $rule[$ii]) {
                        $is_symbol_rule_separator = false;
                        break;
                    }
                }
                if ($is_symbol_rule_separator) {
                    $i = $i + $symbol_rule_separator_length - 1;
                    $char = $symbol_rule_separator;
                }
            }

            // \ 是转义字符，在它之后的任意一个字符，都是正则表达式的一部分。
            // 例如：\/, \| 
            if ($char === '\\') {
                $current_method .= $char;
                $current_method .= $rule[$i + 1];
                $i++;
                continue;
            }

            // 首次正则表达式开头 /，表明接下来是正则表达式。此为 正则阶段 1
            // 直到匹配到下一个 /，表明正则表达式即将结束。此为 正则阶段 2
            // 在此之后，匹配的字符都当作是正则表达式的模式修饰符。直到匹配到 |，表示正则表达式完全结束
            if ($char === '/') {
                if ($is_reg_flag == 0) {
                    $is_reg_flag = 1;
                } else if ($is_reg_flag == 1) {
                    $is_reg_flag = 2;
                }
            }
            // 一般非正则表达式的方法中，不会包含 |，所以匹配到它则表明接下来是下一个方法
            // 在正则表达式的方法中，可能包含 |，所以必须在正则阶段 2 后，匹配到它才表明接下来是下一个方法
            else if ($char === $symbol_rule_separator) {
                if ($is_reg_flag == 0) {
                    $is_next_method_flag = 1;
                } else if ($is_reg_flag == 2) {
                    $is_reg_flag = 0;
                    $is_next_method_flag = 1;
                }
            }

            if ($is_next_method_flag == 0) {
                $current_method .= $char;
            } else {
                $is_next_method_flag = 0;
                if (!empty($current_method)) $rules[] = $current_method;
                $current_method = '';
                $is_reg_flag = 0;
            }
        }

        if (!empty($current_method)) $rules[] = $current_method;
        return $rules;
    }

    /**
     * Parse rule error message. 
     * 1. Simple string - show this error message if anything is invalid
     * 2. Json string - show one of the error message which is related to the invalid method
     * 3. Special string - Same functions as Json string. Such as " [ *] => It\'s required! [ preg  ] => It\'s invalid [no]=> [say yes] => yes"
     * 4. Array
     *
     * @param string $error_msg Simple string or Json string
     * @return array
     */
    protected function parse_error_message($error_msg)
    {
        // $error_msg = " [ *] => It\'s required! [ preg  ] => It\'s invalid [no]=> [say yes] => yes";
        // $parse_arr = [];
        // $this->parse_gh_string_to_array($parse_arr, $error_msg);
        // print_r($parse_arr);die;

        if (is_array($error_msg)) return $error_msg;

        // '{"*":"Users define - @this is required","preg":"Users define - @this should not be matched /^\\\d+$/"}'
        $json_arr = json_decode($error_msg, true);
        if ($json_arr) return $json_arr;

        $gh_arr = [];
        // " [ *] => It\'s required! [ preg  ] => It\'s invalid [no]=> [say yes] => yes"
        $this->parse_gh_string_to_array($gh_arr, $error_msg);
        if (!empty($gh_arr)) return $gh_arr;

        return ['gb_err_msg_key' => $error_msg];
    }

    /**
     * Parse a string to Array in a special format
     * 
     * Example: " [*] => It\'s required! [ preg  ] => It\'s invalid [no]=> [yes] => yes"
     * Array
     * (
     *       [*] => It\'s required!
     *       [preg] => It\'s invalid
     *       [no] => 
     *       [yes] => yes
     * )
     *
     * @param array $parse_arr
     * @param string $string
     * @param string $type
     * @return bool
     */
    protected function parse_gh_string_to_array(&$parse_arr, $string, $type = "key")
    {
        static $key = '';

        // echo "### Current String is $string\n";

        if ($type == 'key') {
            if (preg_match("/^\s*\[\s*(.*?)\s*\]\s*=>\s*(.*)$/", $string, $matches)) {
                $key = $matches[1];
                // echo "--- Current Key is $key\n";
                $parse_arr[$key] = '';

                return $this->parse_gh_string_to_array($parse_arr, $matches[2], 'value');
            } else {
                // echo "Can not get key\n";
                return false;
            }
        } else if ($type == 'value') {
            if (preg_match("/^(.*?)\s*(\[\s*.*?\s*\]\s*=>\s*.*)?$/", $string, $matches)) {
                $parse_arr[$key] = $matches[1];
                // echo "+++ Kye($key) Value is {$parse_arr[$key]}\n";
                return $this->parse_gh_string_to_array($parse_arr, (isset($matches[2]) ? $matches[2] : ''), 'key');
            } else {
                // echo "Can not get value for Key $key\n";
                return false;
            }
        }
    }

    /**
     * Match rule error message. 
     *
     * @param array $rule_error_msg Parsed error message array
     * @param string $tag
     * @param bool $only_user_err_msg If can not find error message from user error message, will try to find it from error template
     * @return string
     */
    protected function match_error_message($rule_error_msg, $tag, $only_user_err_msg = false)
    {
        // Only one error message was set
        if (!empty($rule_error_msg) && !empty($rule_error_msg['gb_err_msg_key'])) return $rule_error_msg['gb_err_msg_key'];

        // One rule one error message
        if (isset($rule_error_msg[$tag])) return $rule_error_msg[$tag];

        switch ($tag) {
            case 'required':
                if (isset($rule_error_msg[$this->config['symbol_required']])) return $rule_error_msg[$this->config['symbol_required']];
                break;
            case 'optional_unset':
                if (isset($rule_error_msg[$this->config['symbol_optional_unset']])) return $rule_error_msg[$this->config['symbol_optional_unset']];
                break;
            default:
                break;
        }

        // Can not match user error message and skip match error template
        if ($only_user_err_msg) return '';

        // Can not match user error message, return default error message
        $error_msg = $this->get_error_template($tag);
        if (empty($error_msg)) $error_msg = $this->get_error_template('default');
        return $error_msg;
    }

    /**
     * Execute validation with field and its rule. Contains cases:
     * 1. If rule
     * 2. required(*) rule
     * 3. regular expression
     * 4. method
     *
     * @param array $data The parent data of the field which is related to the rule
     * @param string $field The field which is related to the rule
     * @param array $rules The rule details
     * @param bool $field_path Field path, suce as fruit.apple
     * @param bool $is_or_rule Flag of or rule
     * @return bool The result of validation
     */
    protected function execute_one_rule($data, $field, $rules = [], $field_path = false, $is_or_rule = false)
    {
        if (empty($rules) || empty($rules['rules'])) {
            return true;
        }

        $rule_error_msg = $rules['error_msg'];

        foreach ($rules['rules'] as $rule) {
            if (empty($rule)) {
                continue;
            }

            $result = true;
            $error_type = 'validation';
            $if_flag = false;
            $params = [];

            // If rule
            if (preg_match($this->config['reg_if'], $rule, $matches)) {
                $if_flag = true;

                if (preg_match($this->config['reg_if_true'], $rule)) {
                    // 'if true' rule
                    $if_type = true;
                } else {
                    // 'if false' rule
                    $if_type = false;
                }

                $rule = $matches[1];

                $method_rule = $this->parse_method($rule, $data, $field);
                $params = $method_rule['params'];
                $result = $this->execute_method($method_rule, $field_path);

                if ($result === "Undefined") return false;
                if (($if_type && $result !== true) || (!$if_type && $result === true)) {
                    // If it's a 'if true' or 'if false' rule -> means this field is optional;
                    // If the 'if true' validation result is true, need to validate the other rule
                    // If the 'if true' validation result is false and this field is set and not empty, need to validate the other rule
                    // If the 'if true' validation result is false and this field is not set or empty, no need to validate the other rule
                    if (!$this->required(isset($data[$field]) ? $data[$field] : null)) return true;
                }
            }
            // Required(*) rule
            else if ($rule == $this->config['symbol_required'] || $rule == $this->symbol_full_name['symbol_required']) {
                if (!$this->required(isset($data[$field]) ? $data[$field] : null)) {
                    $result = false;
                    $error_type = 'required_field';
                    $error_msg = $this->match_error_message($rule_error_msg, $this->symbol_full_name['symbol_required']);
                }
            }
            // Optional(O) rule
            else if ($rule == $this->config['symbol_optional'] || $rule == $this->symbol_full_name['symbol_optional']) {
                if (!$this->required(isset($data[$field]) ? $data[$field] : null)) {
                    return true;
                }
            }
            // Unset(O!) rule
            else if ($rule == $this->config['symbol_optional_unset'] || $rule == $this->symbol_full_name['symbol_optional_unset']) {
                if (!isset($data[$field])) {
                    return true;
                } else if (!$this->required(isset($data[$field]) ? $data[$field] : null)) {
                    $result = false;
                    $error_type = 'optional_unset_field';
                    $error_msg = $this->match_error_message($rule_error_msg, $this->symbol_full_name['symbol_optional_unset']);
                }
            }
            // Regular expression
            else if (preg_match($this->config['reg_preg'], $rule, $matches)) {
                $preg = isset($matches[1]) ? $matches[1] : $matches[0];
                if (!preg_match($this->config['reg_preg_strict'], $preg, $matches)) {
                    $result = false;
                    $error_type = 'preg';
                    $error_msg = $this->match_error_message('', 'preg_format');
                    $error_msg = str_replace($this->symbol_preg, $preg, $error_msg);
                } else {
                    if (!preg_match($preg, $data[$field], $matches)) {
                        $result = false;
                        $error_msg = $this->match_error_message($rule_error_msg, 'preg');
                        $error_msg = str_replace($this->symbol_preg, $preg, $error_msg);
                    }
                }
            }
            // Method
            else {
                $method_rule = $this->parse_method($rule, $data, $field);
                $params = $method_rule['params'];
                $result = $this->execute_method($method_rule, $field_path);

                if ($result === "Undefined") return false;

                // If method validation is success. should return true.
                // If retrun anything others which is not equal to true, then means method validation error.
                // If retrun not a boolean value, will use the result as error message.
                if ($result !== true) {
                    $error_msg = $this->match_error_message($rule_error_msg, $method_rule['symbol'], true);

                    if (is_array($result)) {
                        $error_type = isset($result['error_type']) ? $result['error_type'] : $error_type;
                        if (empty($error_msg)) $error_msg = isset($result['message']) ? $result['message'] : $error_msg;
                    } else {
                        $error_msg = empty($error_msg) ? $result : $error_msg;
                    }

                    // $result = false;

                    if (empty($error_msg)) {
                        $error_msg = $this->match_error_message($rule_error_msg, $method_rule['symbol']);
                    }
                }
            }

            // 1. if it's a 'if' rule -> result is not true, should set error
            // 2. if it's a 'if' rule -> means this field is optional; If result is not true, don't set error
            if ($result !== true && !$if_flag) {
                // Replace symbol to field name and parameter value
                $error_msg = str_replace($this->symbol_this, $field_path, $error_msg);
                foreach ($params as $key => $value) {
                    // if ($key == 0) continue;
                    if (is_array($value)) {
                        if ($this->is_in_method($method_rule['symbol'])) {
                            $value = implode(',', $value);
                        } else {
                            continue;
                        }
                    }

                    $error_msg = str_replace('@p' . $key, (isset($value) ? $value : "NULL"), $error_msg);
                }

                $message = [
                    "error_type" => $error_type,
                    "message" => $error_msg,
                ];

                // Default fields: error_type, message
                // Allow user to add extra field in error message.
                if (is_array($result)) {
                    $message = array_merge($result, $message);
                }

                $this->set_error($field_path, $message, $is_or_rule);
                return false;
            }
        }

        return true;
    }

    /**
     * Parse method and its parameters
     *
     * @param string $rule One separation of rule
     * @param array $data The parent data of the field which is related to the rule
     * @param string $field The field which is related to the rule
     * @return array Method detail
     */
    protected function parse_method($rule, $data, $field)
    {
        // If force parameter, will not add the field value as the first parameter even though no the field parameter
        if (preg_match($this->config['symbol_param_standard'], $rule, $matches)) {
            $method = $matches[1];
            $params = $matches[2];
            $params = explode($this->config['symbol_param_separator'], $params);
            // If classic parameter, will add the field value as the first parameter if no the field parameter
        } else if (preg_match($this->config['symbol_param_this_omitted'], $rule, $matches)) {
            $method = $matches[1];
            $params = $matches[2];
            $params = explode($this->config['symbol_param_separator'], $params);
            if (!in_array($this->symbol_this, $params)) {
                array_unshift($params, $this->symbol_this);
            }
            // If no parameter, will add the field value as the first parameter
        } else {
            $method = $rule;
            $params = [$this->symbol_this];
        }

        $symbol = $method;
        $method = isset($this->method_symbol[$method]) ? $this->method_symbol[$method] : $method;

        foreach ($params as &$param) {
            if (is_array($param)) continue;

            if (strpos($param, '@') !== false) {
                switch ($param) {
                    case $this->symbol_this:
                        $param = isset($data[$field]) ? $data[$field] : null;
                        break;
                    case $this->symbol_parent:
                        $param = $data;
                        break;
                    case $this->symbol_root:
                        $param = $this->data;
                        break;
                    default:
                        $param_field = substr($param, 1);
                        $param = $this->get_field($data, $param_field);
                        if ($param === null) $param = $this->get_field($this->data, $param_field);
                        break;
                }
            }
        }

        // "in" method, all the parameters are treated as the second parameter.
        if ($this->is_in_method($symbol)) {
            $field_name = $params[0];
            array_shift($params);
            $params = [
                $field_name,
                $params
            ];
        }

        $method_rule = [
            'method' => $method,
            'symbol' => $symbol,
            'params' => $params
        ];

        return $method_rule;
    }

    /**
     * Execute method
     *
     * @param array $method_rule Method detail
     * @param string $field_path The field which is related to the rule
     * @return bool|string|array
     */
    protected function execute_method($method_rule, $field_path)
    {
        try {
            $params = $method_rule['params'];
            if (isset($this->methods[$method_rule['method']])) {
                $result = call_user_func_array($this->methods[$method_rule['method']], $params);
            } else if (method_exists($this, $method_rule['method'])) {
                $result = call_user_func_array([$this, $method_rule['method']], $params);
            } else if (function_exists($method_rule['method'])) {
                $result = call_user_func_array($method_rule['method'], $params);
            } else {
                $error_msg = str_replace('@method', $method_rule['symbol'], $this->error_template['call_method']);
                $message = [
                    "error_type" => 'internal_server_error',
                    "message" => $error_msg,
                ];
                $this->set_error($field_path, $message);
                return "Undefined";
            }
        } catch (\Throwable $t) {
            throw (new ghException("@field:{$field_path}, @method:{$method_rule['method']} - " . $t->getMessage(), $t->getCode(), $t))
                ->set_recurrence_current($this->get_recurrence_current());
        }
        // For the PHP version < 7
        catch (\Exception $t) {
            throw (new ghException("@field:{$field_path}, @method:{$method_rule['method']} - " . $t->getMessage(), $t->getCode(), $t))
                ->set_recurrence_current($this->get_recurrence_current());
        }

        return $result;
    }

    /**
     * Get the current info of the recurrence validation
     *
     * @return void
     */
    public function get_recurrence_current()
    {
        return $this->recurrence_current;
    }

    /**
     * Set current field path
     * 
     * @param string $field_path
     * @return static
     */
    protected function set_current_field_path($field_path)
    {
        $this->recurrence_current['field_path'] = $field_path;
        return $this;
    }

    /**
     * Get current field path
     * 
     * @return string
     */
    public function get_current_field_path()
    {
        return $this->recurrence_current['field_path'];
    }

    /**
     * Set the rule of current field path
     * 
     * @param string|array $rule
     * @return static
     */
    protected function set_current_rule($rule)
    {
        if (is_array($rule)) $rule = json_encode($rule, JSON_UNESCAPED_SLASHES);
        $this->recurrence_current['rule'] = $rule;
        return $this;
    }

    /**
     * Get the rule of current field path
     * 
     * @return string|array
     */
    public function get_current_rule()
    {
        return $this->recurrence_current['rule'];
    }

    /**
     * Get field pointer
     * When force is true, will create the field if field is not existed
     *
     * @param pointer $data data pointer
     * @param string $field field path
     * @param bool $force When force is true, will create the field if field is not existed
     * @return pointer
     */
    protected function &get_field(&$data, $field, $force = false)
    {
        $point = &$data;

        $fields = explode($this->config['symbol_field_name_separator'], $field);
        $len = count($fields);

        foreach ($fields as $key => $value) {
            if (!isset($point[$value])) {
                if (!$force) {
                    $tmpNull = null;
                    return $tmpNull;
                }

                if ($key !== ($len - 1)) {
                    $point[$value] = [];
                    $point = &$point[$value];
                } else {
                    $point[$value] = 'Extra field';
                    $point = &$point[$value];

                    return $point;
                }
            } else {
                if ($key !== ($len - 1)) {
                    if (empty($point[$value]) || !is_array($point[$value])) {
                        $point[$value] = [];
                    }
                    $point = &$point[$value];
                } else {
                    $point = &$point[$value];
                    return $point;
                }
            }
        }
    }

    /**
     * Recursively delete fields
     * After unset a field, check the parent field, if empty, then unset the parent field
     *
     * @param pointer $data data pointer
     * @param string $field field path, suce as fruit.apple
     * @param bool $force If false, don't unset a field when it's not empty
     * @return bool
     */
    protected function r_unset(&$data, $field, $force = true)
    {
        $point = &$data;

        $fields = explode($this->config['symbol_field_name_separator'], $field);
        $len = count($fields);
        $parent_field = '';

        foreach ($fields as $key => $value) {
            if (isset($point[$value])) {
                if ($key === ($len - 1)) {
                    if ($force) {
                        unset($point[$value]);
                        if ($key > 0) $this->r_unset($data, $parent_field, false);
                    } else if (empty($point[$value])) {
                        unset($point[$value]);
                        if ($key > 0) $this->r_unset($data, $parent_field, false);
                    }
                    return true;
                } else {
                    $parent_field = $parent_field ? $parent_field . '.' . $value : $value;
                    $point = &$point[$value];
                }
            } else {
                return true;
            }
        }
    }

    /**
     * Set error message
     * If one of "or" rule is invalid, don't set _validation_status to false
     * If all of "or" rule is invalid, will set _validation_status to false in other method
     *
     * @param string $field field path
     * @param string $message error message
     * @param bool $is_or_rule Flag of or rule
     * @return static
     */
    protected function set_error($field = '', $message = '', $is_or_rule = false)
    {
        if (!$is_or_rule) $this->validation_status = false;

        if (is_array($message)) {
            if (!isset($this->dotted_errors['general'][$field])) {
                $this->dotted_errors['detailed'][$field] = $message;
                $this->dotted_errors['general'][$field] = isset($message['message']) ? $message['message'] : 'Unknown error';
            } else {
                $error_msg = isset($message['message']) ? $message['message'] : 'Unknown error';
                if ($this->dotted_errors['detailed'][$field]['message'] !== $error_msg) {
                    $this->dotted_errors['detailed'][$field]['message'] .= " or " . $error_msg;
                    $this->dotted_errors['general'][$field] .= " or " . $error_msg;
                }
            }
        } else {
            if (!isset($this->dotted_errors['general'][$field])) {
                $this->dotted_errors['detailed'][$field] = $message;
                $this->dotted_errors['general'][$field] = $message;
            } else {
                if ($this->dotted_errors['detailed'][$field] !== $message) {
                    $this->dotted_errors['detailed'][$field] .= " or " . $message;
                    $this->dotted_errors['general'][$field] .= " or " . $message;
                }
            }
        }

        $p_nested_error_detailed = &$this->get_field($this->nested_errors['detailed'], $field, true);
        $p_nested_error_detailed = $this->dotted_errors['detailed'][$field];

        $p_nested_error_general = &$this->get_field($this->nested_errors['general'], $field, true);
        $p_nested_error_general = $this->dotted_errors['general'][$field];

        return $this;
    }

    /**
     * Get error message
     *
     * @param string|bool $error_format Recommend using format strings, not using bool
     * @param bool $is_general If true, return error message for general error. If false, return error message for detailed error.
     * @return array
     */
    public function get_error($error_format = self::ERROR_FORMAT_DOTTED_GENERAL, $is_general = true)
    {
        /**
         * $is_nested and $is_general are deprecated.
         * Please use $error_format instead.
         * @deprecated
         */
        if (is_bool($error_format)) {
            $is_nested = $error_format;
            if ($is_nested) {
                if ($is_general) return $this->nested_errors['general'];
                else return $this->nested_errors['detailed'];
            } else {
                if ($is_general) return $this->dotted_errors['general'];
                else return $this->dotted_errors['detailed'];
            }
        }

        switch ($error_format) {
            case self::ERROR_FORMAT_NESTED_GENERAL:
                return $this->nested_errors['general'];
                break;
            case self::ERROR_FORMAT_NESTED_DETAILED:
                return $this->nested_errors['detailed'];
                break;
            case self::ERROR_FORMAT_DOTTED_GENERAL:
                return $this->dotted_errors['general'];
                break;
            case self::ERROR_FORMAT_DOTTED_DETAILED:
                return $this->dotted_errors['detailed'];
                break;
            default:
                return $this->nested_errors['general'];
                break;
        }
    }

    /**
     * Set result classic
     * If set to true, replace field value to "true" if it's valid, replace field value to error_message if it's invalid.
     * If set to false, don't replace field value if it's valid, replace field value to error_message if it's invalid.
     *
     * @param bool $result_classic
     * @return void
     */
    public function set_result_classic($result_classic)
    {
        $this->result_classic = $result_classic;
    }

    /**
     * Set result
     * If set to true, replace field value to "true" if it's valid, replace field value to error_message if it's invalid.
     * If set to false, don't replace field value if it's valid, replace field value to error_message if it's invalid.
     *
     * @param string $field
     * @param bool $result
     * @return void
     */
    protected function set_result($field, $result)
    {
        if ($this->result_classic != true && $result == true) return;

        $p_result = &$this->get_field($this->result, $field, true);
        if ($result == true) {
            if ($p_result !== 'Extra field') $p_result = true;
        } else {
            $p_result = $this->dotted_errors['detailed'][$field];
        }
    }

    public function get_result()
    {
        return $this->result;
    }

    public function get_data()
    {
        return $this->data;
    }

    protected function uncamelize($camelcaps, $separator = '_')
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelcaps));
    }

    protected function big_camelize($uncamelcaps, $separator = '_')
    {
        $uncamelcaps = str_replace($separator, " ", lcfirst($uncamelcaps));
        return str_replace(" ", "", ucwords($uncamelcaps));
    }

    protected function camelize($uncamelcaps, $separator = '_')
    {
        return lcfirst($this->big_camelize($uncamelcaps, $separator));
    }

    protected function is_index_array($array)
    {
        if (!is_array($array)) return false;
        return array_keys($array) === range(0, count($array) - 1);
    }

    protected function is_in_method($method)
    {
        return preg_match('/\(.*\)/', $method, $matches);
    }
}
