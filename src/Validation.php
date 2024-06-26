<?php

namespace githusband;

use githusband\Rule\RuleClassDefault;
use githusband\Rule\RuleClassDatetime;
use githusband\Exception\GhException;
use githusband\Exception\RuleException;
use Exception;
use Throwable;

/**
 * The primary class to validate data.
 * 
 * It's just a framework which maintains the validation logic, including parsing rules, calling validation methods, setting error messages, etc.
 * But it almost does not contain any validation methods.
 */
class Validation
{
    // use Rule\RuleDefaultTrait, Rule\RuleDatetimeTrait;

    const ERROR_FORMAT_NESTED_GENERAL = 'NESTED_GENERAL';
    const ERROR_FORMAT_NESTED_DETAILED = 'NESTED_DETAILED';
    const ERROR_FORMAT_DOTTED_GENERAL = 'DOTTED_GENERAL';
    const ERROR_FORMAT_DOTTED_DETAILED = 'DOTTED_DETAILED';

    /**
     * Validation rules. Default empty. Should be set before validation.
     * 
     * @see static::set_rules()
     * @var array
     */
    protected $rules = [];

    /**
     * The methods of user customized
     * 
     * @see static::add_method()
     * @var array
     */
    protected $methods = [];

    /**
     * Back up the original data
     * 
     * @see static::validate()
     * @var array
     */
    protected $data = [];

    /**
     * Validation result of rules.
     * 
     * @example
     * ```
     * {
     *      "A.1": true,
     *      "A.2.a": "error_msg_of_A2a"
     * }
     * ```
     * @var array<string, bool|string>
     */
    protected $result = [];

    /**
     * Define $result format
     * If set to true, replace field value to "true" if it's valid, replace field value to error_message if it's invalid.
     * If set to false, don't replace field value if it's valid, replace field value to error_message if it's invalid.
     * 
     * @var array
     */
    protected $result_classic = true;

    /**
     * Contains all error messages.
     * One-dimensional array
     * 
     * @example
     * ```
     * {
     *      "general": {
     *          "A.1": "error_msg_of_A1",
     *          "A.2.a": "error_msg_of_A2a",
     *      },
     *      "detailed": {
     *          "A.1": {
     *              "error_type": "required_field",
     *              "message": "error_msg_of_A1"
     *          },
     *          "A.2.a": {
     *              "error_type": "validation",
     *              "message": "error_msg_of_A2a",
     *              "extra": "extra_msg_of_A2a"
     *          },
     *      }
     * }
     * ```
     * @var array{general: array<string, mixed>, detailed: array<string, mixed>}
     */
    protected $dotted_errors = [
        'general' => [],    // only message string
        'detailed' => []    // message array, contains error type and error message
    ];

    /**
     * Contains all error messages.
     * Multidimensional array
     * 
     * @example
     * ```
     * {
     *      "general": {
     *          "A": {
     *              "1": "error_msg_of_A1",
     *              "2": {
     *                  "a": "error_msg_of_A2a"
     *              }
     *          }
     *      },
     *      "detailed": {
     *          "A": {
     *              "1": {
     *                  "error_type": "required_field",
     *                  "message": "error_msg_of_A1"
     *              },
     *              "2": {
     *                  "a": {
     *                      "error_type": "validation",
     *                      "message": "error_msg_of_A2a",
     *                      "extra": "extra_msg_of_A2a"
     *                  }
     *              }
     *          }
     *      }
     * }
     * ```
     * @var array{general: array<string, mixed>, detailed: array<string, mixed>}
     */
    protected $nested_errors = [
        'general' => [],    // only message string
        'detailed' => []    // message array, contains error type and error message
    ];

    /**
     * Current info of the recurrence validation: field path or its rule, etc.
     * If something get wrong, we can easily know which field or rule get wrong.
     * 
     * @example
     * ```
     * {
     *      "field_path": "A.1",
     *      "field_ruleset": "required|>[100]",
     *      "rule": "",
     *      "method": {
     *          "method": "greater_than",
     *          "symbol": ">",
     *          "params": [
     *              200,    // Your data which needs to be validated 
     *              100     // The parameter of >
     *          ]
     *      },
     * }
     * ```
     * @var array{field_path: string, field_ruleset: string, rule: string, method: array{method: string, symbol: string, params: array<mixed>} }
     */
    protected $recurrence_current = [
        'field_path' => '',
        'field_ruleset' => '',
        'rule' => '',
        'method' => [],
    ];

    /**
     * System symbol for this
     * - Used in ruleset as a parameter of a method: Means the value of the field which needs to be validated
     * - Used in error message template: Means the field name
     *
     * @var string
     */
    protected $symbol_this = '@this';
    /**
     * System symbol for root
     * - Used in ruleset as a parameter of a method: Means the whole data which needs to be validated
     *
     * @var string
     */
    protected $symbol_root = '@root';
    /**
     * System symbol for parent of the current field
     * - Used in ruleset as a parameter of a method: Means the parent data of the current field
     *
     * @var string
     */
    protected $symbol_parent = '@parent';
    /**
     * System symbol for regular expression
     * - Used in error message template: Means the regular expression which you are validating
     *
     * @var string
     */
    protected $symbol_preg = '@preg';
    /**
     * System symbol for method
     * - Used in error message template: Means the mehthod name or its symbol which you are validating
     *
     * @var string
     */
    protected $symbol_method = '@method';

    /**
     * The configs of language, symbols or when rule etc. 
     *
     * @var array<string, string>
     */
    protected $config = [
        'language' => 'en-us',                                      // Language, Default en-us
        'lang_path' => '',                                          // Customer Language file path
        'validation_global' => true,                                // 1. true - validate all rules even though previous rule had been failed; 2. false - stop validating when any rule is failed
        'auto_field' => "data",                                     // If root data is string or numberic array, add the auto_field as the root data field name
        'reg_msg' => '/ >> (.*)$/',                                 // Set the error message format for all the methods after a rule string
        'reg_preg' => '/^(\/.+\/.*)$/',                             // If a rule match reg_preg, indicates it's a regular expression instead of method
        'reg_preg_strict' => '/^(\/.+\/[imsxADSUXJun]*)$/',         // Verify if a regular expression is valid
        'reg_ifs' => '/^!?if\((.*)\)/',                             // A regular expression to match both reg_if and reg_if_not
        'reg_if' => '/^if\((.*)\)/',                                // If match reg_if, validate this condition first, if true, then continue to validate the subsequnse rule
        'reg_if_not' => '/^!if\((.*)\)/',                           // If match reg_if_not, validate this condition first, if false, then continue to validate the subsequnse rule
        'symbol_rule_separator' => '|',                             // Serial rules seqarator to split a rule into multiple methods
        'symbol_parallel_rule' => '[||]',                           // Symbol of the parallel rule, Same as "[or]"
        'symbol_method_standard' => '/^([^\\(]*)\\((.*)\\)$/',      // Standard method format, e.g. equal(@this,1)
        'symbol_method_omit_this' => '/^([^\\[]*)\\[(.*)\\]$/',     // @this omitted method format, will add a @this parameter at first. e.g. equal[1]
        'symbol_parameter_separator' => ',',                        // Parameters separator to split the parameter string of a method into multiple parameters, e.g. equal(@this,1)
        'symbol_field_name_separator' => '.',                       // Field name separator of error message, e.g. "fruit.apple"
        'symbol_required' => '*',                                   // Symbol of required field, Same as the rule "required"
        'symbol_optional' => 'O',                                   // Symbol of optional field, can be not set or empty, Same as the rule "optional"
        'symbol_optional_unset' => 'O!',                            // Symbol of optional field, can be not set only, Same as the rule "optional_unset"
        'symbol_array_optional' => '[O]',                           // Symbol of array optional rule, Same as "[optional]"
        'symbol_index_array' => '.*',                               // Symbol of index array rule
        'reg_whens' => '/^(.+):(!)?\?\((.*)\)/',                    // A regular expression to match both reg_when and reg_when_not. Most of the methods are allowed to append a if rule, e.g. required:when, optional:when_not
        'reg_when' => '/^(.+):\?\((.*)\)/',                         // A regular expression to match a field which must be validated by method($1) only when the condition($3) is true
        'symbol_when' => ':?',                                      // We don't use the symbol to match a When Rule, it's used to generate the symbols in README
        'reg_when_not' => '/^(.+):!\?\((.*)\)/',                    // A regular expression to match a field which must be validated by method($1) only when the condition($3) is not true
        'symbol_when_not' => ':!?',                                 // We don't use the symbol to match a When Rule, it's used to generate the symbols in README
    ];
    protected $config_backup;

    /**
     * See $config array, there are several symbol that are not semantically explicit.
     * So I set up the related full name for them
     *
     * The $config_default can not be customized and they are always meaningful
     * @var array<string, string>
     */
    protected $config_default = [
        'symbol_required' => 'required',                            // Default symbol of required field
        'symbol_optional' => 'optional',                            // Default symbol of optional field, can be not set or empty
        'symbol_optional_unset' => 'optional_unset',                // Default symbol of optional field, can be not set only
        'symbol_parallel_rule' => '[or]',                           // Default symbol of parallel rule
        'symbol_array_optional' => '[optional]',                    // Default symbol of array optional rule
        'reg_whens' => '/^(.+):when(_not)?\((.*)\)/',               // A regular expression to match both reg_when and reg_when_not. Most of the methods are allowed to append a if rule, e.g. required:when, optional:when_not
        'reg_when' => '/^(.+):when\((.*)\)/',                       // A regular expression of When Rule to match a field which must be validated by method($1) only when the condition($3) is true
        'symbol_when' => ':when',                                   // We don't use the symbol to match a When Rule, it's used to generate the symbols in README
        'reg_when_not' => '/^(.+):when_not\((.*)\)/',               // A regular expression of When Rule to match a field which must be validated by method($1) only when the condition($3) is not true
        'symbol_when_not' => ':when_not',                           // We don't use the symbol to match a When Rule, it's used to generate the symbols in README
    ];

    /**
     * While validating, if one field was invalid, set this to false;
     * 
     * @var boolean
     */
    protected $validation_status = true;

    /**
     * If true, validate all rules;
     * If false, stop validating when one rule was invalid.
     * 
     * @var boolean
     */
    protected $validation_global = true;

    /**
     * The method symbol
     * Using symbol mapped to real method. e.g. '=' => 'equal'
     * 
     * @example {"=": "equal"}
     * @see githusband\Rule\RuleClassDefault::$method_symbols
     * @see githusband\Rule\RuleDefaultTrait::$method_symbols_of_rule_default_trait
     * @var array
     */
    protected $method_symbols = [];
    protected $deprecated_method_symbols = [];
    /**
     * Reverse the method symbol
     * 
     * @example {"equal": "="}
     * @see static::$method_symbols
     * @var array
     */
    protected $method_symbols_reversed = [];

    /**
     * The rule class with your validation method.
     * The rule class must be static class.
     *
     * @var array<int, class-string|object>
     */
    protected $rule_classes = [];
    protected $rule_class_method_symbols_reversed = [];

    /**
     * If you don't set the default rule classes into $rule_classes, they will be auto set into $rule_classes.
     *
     * @var array<int, class-string>
     */
    protected $rule_classes_default = [
        RuleClassDefault::class,
        RuleClassDatetime::class,
    ];

    /**
     * Language file path
     * 
     * @var string
     */
    protected $lang_path = __DIR__ . '/Language/';

    /**
     * Error message template of different Languaues
     * 
     * @var array<string, array>
     */
    protected $languages = [];

    /**
     * If user don't set a error messgae, use this.
     * 
     * @see ./Language/EnUs.php
     * @see githusband\Rule\RuleClassDefault::$method_symbols
     * @see githusband\Rule\RuleDefaultTrait::$method_symbols_of_rule_default_trait
     * @var array
     */
    protected $error_templates = [];

    public function __construct($config = [])
    {
        $this->initialzation($config);
    }

    protected function initialzation($config = [])
    {
        $this->config = array_merge($this->config, $config);
        $this->config_backup = $this->config;

        $this->set_validation_global($this->config['validation_global']);

        $this->set_language($this->config['language']);

        $this->load_trait_data();

        $this->init_rule_classes();
    }

    /**
     * Set config
     *
     * @param array $config
     * @return static
     * @api
     */
    public function set_config($config = [])
    {
        $this->config = array_merge($this->config, $config);

        if (isset($config['language'])) $this->set_language($this->config['language']);

        $this->set_validation_global($this->config['validation_global']);

        return $this;
    }

    /**
     * Get config
     *
     * @return array
     */
    public function get_config()
    {
        return $this->config;
    }

    /**
     * Reset Config
     *
     * @return static
     */
    public function reset_config()
    {
        return $this->set_config($this->config_backup);
    }

    /**
     * Get all the traits from a class and its ancestors.
     * 
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
     * Auto load traits data
     * - method_symbols: {@see static::$method_symbols}
     *
     * @return void
     */
    protected function load_trait_data()
    {
        $used_traits = $this->get_all_traits();

        foreach ($used_traits as $key => $trait) {
            $slash_pos = strrpos($trait, '\\');
            if ($slash_pos !== false) $trait_name = substr($trait, strrpos($trait, '\\') + 1);
            else $trait_name = $trait;
            $trait_name_uncamelized = $this->uncamelize($trait_name);

            $deprecated_trait_method_symbols = "deprecated_method_symbols_of_{$trait_name_uncamelized}";
            if (property_exists($this, $deprecated_trait_method_symbols)) {
                $this->method_symbols = array_merge($this->method_symbols, $this->{$deprecated_trait_method_symbols});
                $this->deprecated_method_symbols = array_merge($this->deprecated_method_symbols, $this->{$deprecated_trait_method_symbols});
            }

            $trait_method_symbols = "method_symbols_of_{$trait_name_uncamelized}";
            if (property_exists($this, $trait_method_symbols)) {
                $this->method_symbols = array_merge($this->method_symbols, $this->{$trait_method_symbols});
            }
        }

        $this->method_symbols_reversed = array_flip($this->method_symbols);
    }

    /**
     * Init all rule classes
     * Set default rule classes, so that you can extend {$this->rule_classes} without the default rule classes.
     *
     * @return static
     */
    protected function init_rule_classes()
    {
        $rule_classes_default = [];
        foreach ($this->rule_classes_default as $rule_class) {
            if (!in_array($rule_class, $this->rule_classes)) {
                $rule_classes_default[] = $rule_class;
            }
        }
        if (!empty($rule_classes_default)) {
            $this->rule_classes = array_merge($rule_classes_default, $this->rule_classes);
        }

        foreach ($this->rule_classes as $key => $rule_class) {
            $this->rule_classes[$key] = $this->init_rule_class($rule_class);
        }

        return $this;
    }

    /**
     * Init a rule class
     * - Set method symbols and any deprecated method symbols
     * - Reverse method symbols in order to easily indicate a rule is a method or its symbol
     * - Instantiate rule class if only it's a name of class 
     *
     * @param string|object $class Instance or name of a class
     * @return object
     */
    protected function init_rule_class($rule_class)
    {
        if (property_exists($rule_class, 'deprecated_method_symbols')) {
            $rule_class::$method_symbols = array_merge($rule_class::$deprecated_method_symbols, $rule_class::$method_symbols);
        }

        $this->reverse_method_symbols($rule_class);

        if (is_object($rule_class)) return $rule_class;
        return new $rule_class();
    }

    /**
     * Reverse the method_symbols array
     *
     * @param class $rule_class A class that contains multiple rule methods
     * @return void
     */
    protected function reverse_method_symbols($rule_class)
    {
        $this->rule_class_method_symbols_reversed[$rule_class] = array_flip($rule_class::$method_symbols);
    }

    /**
     * Add a new rule class that contains multiple rule methods
     *
     * @param class $rule_class Instance or name of a class
     * @return static
     * @api
     */
    public function add_rule_class($rule_class)
    {
        $this->rule_classes[] = $this->init_rule_class($rule_class);
        return $this;
    }

    /**
     * Check if it is a symbol of method
     *
     * @param string $symbol
     * @return bool
     */
    public function is_method_symbol($symbol)
    {
        if (isset($this->method_symbols[$symbol])) return true;

        for ($i = count($this->rule_classes) - 1; $i >= 0; $i--) {
            $rule_class = $this->rule_classes[$i];
            $method_symbols = $rule_class::$method_symbols;
            if (isset($method_symbols[$symbol])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Match the method(from symbol) or symbol(from method) and return them and the rule class
     *
     * @param string $in The method or its symbol
     * @return array
     */
    public function match_method_and_symbol($in)
    {
        if ($this->is_method_symbol($in)) {
            $by_symbol = true;
            $symbol = $in;

            if (isset($this->method_symbols[$symbol])) {
                $class = 'static';
                $method = $this->method_symbols[$in];
            } else {
                for ($i = count($this->rule_classes) - 1; $i >= 0; $i--) {
                    $rule_class = $this->rule_classes[$i];
                    $method_symbols = $rule_class::$method_symbols;
                    if (isset($method_symbols[$in])) {
                        $class = $rule_class;
                        $method = $method_symbols[$in];
                        break;
                    }
                }

                if (empty($class)) $class = 'static';
                if (empty($method)) $method = $symbol;
            }
        } else {
            $by_symbol = false;
            $method = $in;
            if (isset($this->method_symbols_reversed[$in])) {
                $class = 'static';
                $symbol = $this->method_symbols_reversed[$in];
            } else {
                for ($i = count($this->rule_classes) - 1; $i >= 0; $i--) {
                    $rule_class = $this->rule_classes[$i];
                    $rule_class_name = get_class($rule_class);
                    $method_symbols_reversed = $this->rule_class_method_symbols_reversed[$rule_class_name];
                    if (isset($method_symbols_reversed[$in])) {
                        $class = $rule_class_name;
                        $symbol = $method_symbols_reversed[$in];
                        break;
                    }
                }

                if (empty($class)) $class = 'static';
                if (empty($symbol)) $symbol = $method;
            }
        }

        return [
            $by_symbol,
            $symbol,
            $method,
            $class
        ];
    }

    /**
     * If user don't set a error messgae, use $lang.
     * The language file must exist.
     *
     * @param string $lang Languagu, suce as 'en-us'
     * @return static
     * @api
     */
    public function set_language($lang)
    {
        $lang = $this->capital_camelize($lang, '-');

        if (!empty($this->languages[$lang])) {
            $this->error_templates = $this->languages[$lang];
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
     * @api
     */
    public function custom_language($lang_conf, $lang_name = '')
    {
        if (is_object($lang_conf)) {
            if (isset($lang_conf->error_templates)) $this->error_templates = array_merge($this->error_templates, $lang_conf->error_templates);
            if (!empty($lang_name)) $this->languages[$lang_name] = $this->error_templates;
        }

        return $this;
    }

    /**
     * Set validation rules
     *
     * @param array $rules
     * @return static
     * @api
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
     * @param bool $bool
     * @return static
     * @api
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
     * - this method symbols
     * - rule classes method symbols
     *
     * @return array
     */
    public function get_method_symbols()
    {
        $method_symbols = $this->method_symbols;
        $rule_classes_length = count($this->rule_classes);
        for ($i = 0; $i < $rule_classes_length; $i++) {
            $rule_class = $this->rule_classes[$i];
            $method_symbols = array_merge($method_symbols, $rule_class::$method_symbols);
        }

        return $method_symbols;
    }

    /**
     * Get Method Symbol that are deprecated
     * - this deprecated method symbols
     * - rule deprecated classes method symbols
     *
     * @return array
     */
    public function get_deprecated_method_symbols()
    {
        $deprecated_method_symbols = $this->deprecated_method_symbols;
        $rule_classes_length = count($this->rule_classes);
        for ($i = 0; $i < $rule_classes_length; $i++) {
            $rule_class = $this->rule_classes[$i];
            if (property_exists($rule_class, 'deprecated_method_symbols')) {
                $deprecated_method_symbols = array_merge($deprecated_method_symbols, $rule_class::$deprecated_method_symbols);
            }
        }

        return $deprecated_method_symbols;
    }

    /**
     * Get default config
     *
     * @return array
     */
    public function get_config_default()
    {
        return $this->config_default;
    }

    /**
     * Get error message template
     *
     * @return array
     */
    public function get_error_templates()
    {
        return $this->error_templates;
    }

    /**
     * Get error message template of one tag/method
     *
     * @param string $tag
     * @return string
     */
    public function get_error_template($tag)
    {
        return (!isset($tag) || $tag == '' || !isset($this->error_templates[$tag])) ? '' : $this->error_templates[$tag];
    }

    /**
     * Add customized methods and their symbols
     *
     * @param string $method Method name
     * @param callable $callable Function definition
     * @param string $symbol Symbol of method
     * @return static
     * @api
     */
    public function add_method($method, $callable, $symbol = '')
    {
        $this->methods[$method] = $callable;
        if (!empty($symbol)) {
            $this->method_symbols[$symbol] = $method;
            $this->method_symbols_reversed[$method] = $symbol;
        }
        return $this;
    }

    /**
     * Start validating
     *
     * @param array $data The data you want to validate
     * @return bool Validation result
     * @throws GhException
     * @api
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
        } catch (Throwable $t) {
            $this->throw_gh_exception($t);
        }
        // For the PHP version < 7
        catch (Exception $t) {
            $this->throw_gh_exception($t);
        }

        return $this->validation_status;
    }

    /**
     * Encapsulate any errors or exceptions as GhException
     * 
     * @param Throwable $t
     * @return void
     * @throws GhException
     */
    protected function throw_gh_exception($t)
    {
        if ($t instanceof GhException) {
            throw $t;
        } else {
            throw GhException::extend_privious($t, $this->get_recurrence_current(), $this->config['auto_field']);
        }
    }

    /**
     * Execute validation with all data and all rules
     *
     * @param array $data The data you want to validate
     * @param array $rules The rules you set
     * @param null|string $field_path The current field path, suce as fruit.apple
     * @param bool $is_array_loop If the execute method is called in array loop, $is_array_loop should be true
     * @return bool
     */
    protected function execute($data, $rules, $field_path = null, $is_array_loop = false)
    {
        $rules_system_symbol = $this->get_ruleset_system_symbol($rules);
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

        foreach ($rules as $field => $ruleset) {
            $current_field_path = '';
            if ($field_path === null) $current_field_path = $field;
            else $current_field_path = $field_path . $this->config['symbol_field_name_separator'] . $field;
            $this->set_current_field_path($current_field_path);

            // if ($field == 'fruit_id') $a = UNDEFINED_VAR; // @see Unit::test_exception -> Case:Exception_lib_1

            $ruleset_system_symbol = $this->get_ruleset_system_symbol($ruleset);
            if (!empty($ruleset_system_symbol)) {
                // Allow array or object to be optional
                if ($this->has_system_symbol($ruleset_system_symbol, 'symbol_array_optional')) {
                    if (!static::required(isset($data[$field]) ? $data[$field] : null)) {
                        $this->set_result($current_field_path, true);
                        continue;
                    }
                }

                // Validate parallel rules.
                // If one of parallel rules is valid, then the field is valid.
                if ($this->has_system_symbol($ruleset_system_symbol, 'symbol_parallel_rule')) {
                    $result = $this->execute_parallel_rules($data, $field, $current_field_path, $ruleset[$ruleset_system_symbol]);
                }
                // Validate index array
                else if ($this->has_system_symbol($ruleset_system_symbol, 'symbol_index_array', true)) {
                    $result = $this->execute_index_array_rules($data, $field, $current_field_path, $ruleset[$ruleset_system_symbol], $is_array_loop);
                }
                // Validate association array
                else {
                    // Validate association array
                    if ($this->is_association_array_rule($ruleset[$ruleset_system_symbol])) {
                        $result = $this->execute(isset($data[$field]) ? $data[$field] : null, $ruleset[$ruleset_system_symbol], $current_field_path, $is_array_loop);
                    } else {
                        $result = $this->execute_ruleset($data, $field, $ruleset[$ruleset_system_symbol], $current_field_path);
                        $this->set_result($current_field_path, $result);
                    }
                }

                // If the config validation_global is set to false, stop validating when one rule was invalid.
                if (!$result && !$this->validation_global) return false;
            } else {
                // Allow array or object to be optional
                if ($this->has_system_symbol($field, 'symbol_array_optional')) {
                    $field = $this->delete_system_symbol($field, 'symbol_array_optional');
                    $current_field_path = $this->delete_system_symbol($current_field_path, 'symbol_array_optional');

                    // Delete all other array symbols
                    $field_tmp = $this->delete_system_symbol($field, 'symbol_parallel_rule');
                    $field_tmp = $this->delete_system_symbol($field_tmp, 'symbol_index_array');

                    if (!static::required(isset($data[$field_tmp]) ? $data[$field_tmp] : null)) {
                        $this->set_result($current_field_path, true);
                        continue;
                    }
                }

                // Validate parallel rules.
                // If one of parallel rules is valid, then the field is valid.
                if ($this->has_system_symbol($field, 'symbol_parallel_rule')) {
                    $field = $this->delete_system_symbol($field, 'symbol_parallel_rule');
                    $current_field_path = $this->delete_system_symbol($current_field_path, 'symbol_parallel_rule');

                    $result = $this->execute_parallel_rules($data, $field, $current_field_path, $ruleset);
                }
                // Validate index array
                else if ($this->has_system_symbol($field, 'symbol_index_array')) {
                    $field = $this->delete_system_symbol($field, 'symbol_index_array');
                    $current_field_path = $this->delete_system_symbol($current_field_path, 'symbol_index_array');

                    $result = $this->execute_index_array_rules($data, $field, $current_field_path, $ruleset);
                }
                // Validate association array
                else if ($this->is_association_array_rule($ruleset)) {
                    $result = $this->execute(isset($data[$field]) ? $data[$field] : null, $ruleset, $current_field_path, $is_array_loop);
                } else {
                    $result = $this->execute_ruleset($data, $field, $ruleset, $current_field_path);

                    $this->set_result($current_field_path, $result);
                }

                // If the config validation_global is set to false, stop validating when one rule was invalid.
                if (!$result && !$this->validation_global) return false;
            }
        }

        return true;
    }

    /**
     * There are some special rule defined through system symbols
     * It's allowed to have multiple system symbols for one field and they are allowed to be customized.
     * 
     * System symbols: 
     *   - symbol_parallel_rule
     *   - symbol_array_optional
     *   - symbol_index_array
     *
     * @see static::$config_default
     * @see static::$config
     * @param array $ruleset
     * @return array|string|bool
     */
    protected function get_ruleset_system_symbol($ruleset)
    {
        if (!is_array($ruleset)) return false;

        $keys = array_keys($ruleset);

        if (count($keys) != 1) return false;

        $ruleset_system_symbol_string = $keys[0];
        $ruleset_system_symbol_string_tmp = $ruleset_system_symbol_string;

        if ($this->has_system_symbol($ruleset_system_symbol_string, 'symbol_array_optional')) {
            $ruleset_system_symbol_string_tmp = $this->delete_system_symbol($ruleset_system_symbol_string_tmp, 'symbol_array_optional');
        }

        if ($this->has_system_symbol($ruleset_system_symbol_string, 'symbol_parallel_rule')) {
            $ruleset_system_symbol_string_tmp = $this->delete_system_symbol($ruleset_system_symbol_string_tmp, 'symbol_parallel_rule');
        }

        if ($this->has_system_symbol($ruleset_system_symbol_string, 'symbol_index_array')) {
            $ruleset_system_symbol_string_tmp = $this->delete_system_symbol($ruleset_system_symbol_string_tmp, 'symbol_index_array');
        } else if (strpos($this->config['symbol_index_array'], '.') === 0) {
            $symbol_index_array_tmp = ltrim($this->config['symbol_index_array'], '.');
            if ($this->has_system_symbol($ruleset_system_symbol_string, 'symbol_index_array', true)) {
                $ruleset_system_symbol_string_tmp = $this->delete_system_symbol($ruleset_system_symbol_string_tmp, 'symbol_index_array', true);
            }
        }

        if (!empty($ruleset_system_symbol_string_tmp)) return false;
        else return $ruleset_system_symbol_string;
    }

    /**
     * Check a field name contains a specific system symbol or not. 
     * Should check the customized system symbol and its default symbol
     *
     * @param string $ruleset_system_symbol_string
     * @param string $symbol_name
     * @param bool $ingore_left_dot Only for symbol_index_array because symbol_index_array can ingore the left dot if it's not at the end of the field name
     * @return bool
     */
    protected function has_system_symbol($ruleset_system_symbol_string, $symbol_name, $ingore_left_dot = false)
    {
        switch ($symbol_name) {
            case 'symbol_array_optional':
                if (
                    strpos($ruleset_system_symbol_string, $this->config['symbol_array_optional']) !== false
                    || strpos($ruleset_system_symbol_string, $this->config_default['symbol_array_optional']) !== false
                ) {
                    return true;
                }
                break;
            case 'symbol_parallel_rule':
                if (
                    strpos($ruleset_system_symbol_string, $this->config['symbol_parallel_rule']) !== false
                    || strpos($ruleset_system_symbol_string, $this->config_default['symbol_parallel_rule']) !== false
                ) {
                    return true;
                }
                break;
            case 'symbol_index_array':
                if (strpos($ruleset_system_symbol_string, $this->config['symbol_index_array']) !== false) {
                    return true;
                }

                if ($ingore_left_dot) {
                    if (strpos($ruleset_system_symbol_string, ltrim($this->config['symbol_index_array'], '.')) !== false) {
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
     * Delete a specific system symbol from a field. 
     * Should delete the customized system symbol and its default symbol
     *
     * @param string $ruleset_system_symbol_string
     * @param string $symbol_name
     * @param bool $ingore_left_dot Only for symbol_index_array because symbol_index_array can ingore the left dot if it's not at the end of the field name
     * @param string $replace_str Replace the symbol to this string
     * @return string
     */
    protected function delete_system_symbol($ruleset_system_symbol_string, $symbol_name, $ingore_left_dot = false, $replace_str = '')
    {
        switch ($symbol_name) {
            case 'symbol_array_optional':
                $ruleset_system_symbol_string = str_replace($this->config['symbol_array_optional'], '', $ruleset_system_symbol_string);
                $ruleset_system_symbol_string = str_replace($this->config_default['symbol_array_optional'], '', $ruleset_system_symbol_string);
                return $ruleset_system_symbol_string;
                break;
            case 'symbol_parallel_rule':
                $ruleset_system_symbol_string = str_replace($this->config['symbol_parallel_rule'], '', $ruleset_system_symbol_string);
                $ruleset_system_symbol_string = str_replace($this->config_default['symbol_parallel_rule'], '', $ruleset_system_symbol_string);
                return $ruleset_system_symbol_string;
                break;
            case 'symbol_index_array':
                $ruleset_system_symbol_string = str_replace($this->config['symbol_index_array'], '', $ruleset_system_symbol_string);
                if ($ingore_left_dot) $ruleset_system_symbol_string = str_replace(ltrim($this->config['symbol_index_array'], '.'), '', $ruleset_system_symbol_string);
                return $ruleset_system_symbol_string;
                break;
            default:
                return $ruleset_system_symbol_string;
        }

        return $ruleset_system_symbol_string;
    }

    /**
     * Execute validation of parallel rules.
     * There has two ways to add parallel rules:
     * 1. Add symbol_parallel_rule in the end of the field. Such as $rule = [ "name[or]" => [ "*|string", "*|int" ] ];
     * 2. Add symbol_parallel_rule as the only one child of the field. Such as $rule = [ "name" => [ "[or]" => [ "*|string", "*|int" ] ] ];
     * If one of parallel rules is valid, then the field is valid.
     *
     * @param array $data The parent data of the field which is related to the rules
     * @param string $field The field which is related to the rules
     * @param string $field_path Field path, suce as fruit.apple
     * @param array $ruleset The symbol value of the field ruleset
     * @return bool The result of validation
     */
    protected function execute_parallel_rules($data, $field, $field_path, $ruleset)
    {
        $or_len = count($ruleset);
        foreach ($ruleset as $key => $rule_or) {
            // if ($key == 0) $a = UNDEFINED_VAR; // @see Unit::test_exception -> Case:Exception_lib_2

            $result = $this->execute_ruleset($data, $field, $rule_or, $field_path, true);
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
     * @param array $ruleset The symbol value of the field ruleset
     * @param bool $is_array_loop If the execute method is called in array loop, $is_array_loop should be true
     * @return bool The result of validation
     */
    protected function execute_index_array_rules($data, $field, $field_path, $ruleset, $is_array_loop = false)
    {
        if (!isset($data[$field]) || !$this->is_index_array($data[$field])) {
            $error_template = $this->get_error_template('index_array');
            $error_msg = str_replace($this->symbol_this, $field_path, $error_template);
            $message = [
                "error_type" => 'validation',
                "message" => $error_msg,
            ];
            $this->set_error($field_path, $message);
            return false;
        } else {
            $is_all_valid = true;
            foreach ($data[$field] as $key => $value) {
                $current_field_path = $field_path .  $this->config['symbol_field_name_separator'] . $key;

                $rule_system_symbol = $this->get_ruleset_system_symbol($ruleset);
                if (!empty($rule_system_symbol)) {
                    // $is_array_loop is true, means parent data is numberic arrya, too
                    $current_field_path = $is_array_loop ? $field_path : $current_field_path;
                    $result = $this->execute($data[$field], [$key => $ruleset], $current_field_path, true);
                } else if ($this->is_association_array_rule($ruleset)) {
                    $result = $this->execute($data[$field][$key], $ruleset, $current_field_path, true);
                }
                // Validate numberic array, all the rule are the same, only use $ruleset[0]
                else {
                    $result = $this->execute_ruleset($data[$field], $key, $ruleset, $current_field_path);
                    $this->set_result($current_field_path, $result);
                }

                $is_all_valid = $is_all_valid && $result;

                // If the config validation_global is set to false, stop validating when one rule was invalid.
                if (!$result && !$this->validation_global) return false;
            }

            return $is_all_valid;
        }
    }

    /**
     * A ruleset of one field allows users to set error message template in an object.
     * So that users don't have to set the rule and error message in a string.
     * The object must in a special format which only contains two sub-fields: 0 and "error_message"
     * For example:
     * $rule_leaf_object_template = [
     *      'required|int',             // Ruleset
     *      'error_message' => [        // Error message template
     *          'required' => 'It is request field',
     *          'int' => 'Must be integer',
     *      ]
     * ];
     *
     * @param array $rule
     * @return bool
     */
    protected function is_rule_leaf_object($rule)
    {
        $key_count = 0;
        foreach ($rule as $key => $value) {
            if (
                $key !== 0
                && $key !== "error_message"
            ) {
                return false;
            }
            $key_count++;
        }
        if ($key_count !== 2) return false;

        return true;
    }

    /**
     * Check if it's a association array, except rule_leaf_object
     *
     * @param mixed $rule
     * @return bool
     */
    protected function is_association_array_rule($rule)
    {
        return is_array($rule) && !$this->is_rule_leaf_object($rule);
    }

    /**
     * Parse a ruleset which contains: 
     * 1. method and parameters
     * 2. regular expression
     * 3. error message template
     *
     * @param string $ruleset
     * @return array
     */
    protected function parse_ruleset($ruleset)
    {
        if (is_array($ruleset)) {
            $error_templates = $ruleset['error_message'];
            $ruleset = $ruleset[0];
        } else {
            $error_templates = '';

            if (preg_match($this->config['reg_msg'], $ruleset, $matches)) {
                $error_templates = $matches[1];
                $ruleset = preg_replace($this->config['reg_msg'], '', $ruleset);
            }
        }

        $rules = $this->parse_serial_ruleset($ruleset);

        return [
            'rules' => $rules,
            'error_templates' => $this->parse_error_templates($error_templates)
        ];
    }

    /**
     * Split a serial ruleset into multiple rules(methods or regular expression) by using the separator |
     * NOTE: 
     * - The regular expression may cantain the character(|) which is the same as rule separator(|)
     * - Multiple regular expressions are allowed in one serial rule
     *
     * @param string $ruleset
     * @return array
     */
    protected function parse_serial_ruleset($ruleset)
    {
        $symbol_rule_separator = $this->config['symbol_rule_separator'];
        $symbol_rule_separator_length = strlen($symbol_rule_separator);

        $rules = [];
        $current_rule = '';
        $is_next_method_flag = 0;
        $is_reg_flag = 0;

        $ruleset_length = strlen($ruleset);
        for ($i = 0; $i < $ruleset_length; $i++) {
            $char = $ruleset[$i];

            // 支持自定义配置 symbol_rule_separator 为多个字符
            if ($symbol_rule_separator_length > 1 && $char == $symbol_rule_separator[0]) {
                $ii = $i + 1;
                $is_symbol_rule_separator = true;
                for ($j = 1; $j < $symbol_rule_separator_length; $j++) {
                    if ($symbol_rule_separator[$j] != $ruleset[$ii]) {
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
                $current_rule .= $char;
                $current_rule .= $ruleset[$i + 1];
                $i++;
                continue;
            }

            // 首次正则表达式开头 /，表明接下来是正则表达式。此为 正则阶段 1
            // 直到匹配到下一个 /，表明正则表达式即将结束。此为 正则阶段 2
            // 在此之后，匹配的字符都当作是正则表达式的模式修饰符。直到匹配到 |，表示正则表达式完全结束
            if ($char === '/') {
                if ($is_reg_flag == 0) {
                    // 第一个字符不是 /，表明不是正则表达式
                    if ($current_rule == '') {
                        $is_reg_flag = 1;
                    }
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
                $current_rule .= $char;
            } else {
                $is_next_method_flag = 0;
                // Remove the whitespace on the left and right
                $current_rule = trim($current_rule, ' ');
                if ($current_rule === '') throw RuleException::ruleset('Contiguous separator', $this->get_recurrence_current(), $this->config['auto_field']);
                if (!empty($current_rule)) $rules[] = $current_rule;
                $current_rule = '';
                $is_reg_flag = 0;
            }
        }

        $current_rule = trim($current_rule, ' ');
        if ($current_rule === '') {
            if (empty($rules)) {
                throw RuleException::ruleset('Empty', $this->get_recurrence_current(), $this->config['auto_field']);
            } else {
                throw RuleException::ruleset('Endding separator', $this->get_recurrence_current(), $this->config['auto_field']);
            }
        }
        if (!empty($current_rule)) $rules[] = $current_rule;
        return $rules;
    }

    /**
     * Parse error message template of the ruleset
     * 1. Simple string - show this error message if anything is invalid
     * 2. Json string - show one of the error message which is related to the invalid method
     * 3. Special string - Same functions as Json string. Such as " [ *] => It\'s required! [ preg  ] => It\'s invalid [no]=> [say yes] => yes"
     * 4. Array
     *
     * @param string $error_templates Simple string or Json string
     * @return array
     */
    protected function parse_error_templates($error_templates)
    {
        if (is_array($error_templates)) return $error_templates;

        // '{"*":"Users define - @this is required","preg":"Users define - @this should not be matched /^\\\d+$/"}'
        $json_arr = json_decode($error_templates, true);
        if ($json_arr) return $json_arr;

        $gh_arr = [];
        // " [ *] => It\'s required! [ preg  ] => It\'s invalid [no]=> [say yes] => yes"
        $this->parse_gh_string_to_array($gh_arr, $error_templates);
        if (!empty($gh_arr)) return $gh_arr;

        return ['whole_ruleset' => $error_templates];
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
     * @deprecated 2.4.1
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
     * Match rule error message template. 
     * 
     * The error message template(EMT) priority from high to low:
     * 1. The temporary EMT(general string) defined in ruleset. No matter what rules in the ruleset are invalid, return this EMT
     * 2. The EMT returned from method directly
     *   2.1 The EMT(general string or array) returned from method
     *   2.2 The tag of EMT returned from method
     * 3. One rule, one EMT. Matching EMT by the tag of EMT
     *   3.1 The temporary EMT(JSON formatted) defined in ruleset
     *   3.2 The EMT defined via Internationalization, e.g. en-us
     *
     * @param array $ruleset_error_templates Parsed error message which is defined in ruleset
     * @param string|array $method_rule The rule or method rule
     * @param string $when_type The When rule of a rule or method
     * @param bool|string|array $method_result The result of running a method
     * @return string
     */
    protected function match_error_template($ruleset_error_templates, $method_rule, $when_type = '', $method_result = false)
    {
        // 1. The temporary EMT(general string formatted) defined in ruleset. No matter what rules in the ruleset are invalid, return this EMT
        if (!empty($ruleset_error_templates) && !empty($ruleset_error_templates['whole_ruleset'])) return $ruleset_error_templates['whole_ruleset'];

        // 2. The EMT returned from method directly
        // NOTE: In this case we cannot internationalize the error message template
        // 2.1 The EMT(general string or array) returned from method
        if (is_array($method_result) && !empty($method_result['message'])) $method_result = $method_result['message'];
        if ($method_result !== false && is_string($method_result) && !preg_match('/^TAG:(.*)/', $method_result, $matches)) {
            return $method_result;
        }

        if ($method_result !== false && !empty($matches)) {
            // 2.2 The tag of EMT returned from method
            $tags = [$matches[1]];
        } else if (is_string($method_rule)) {
            // symbol_required, symbol_optional, symbol_optional_unset
            if (isset($this->config[$method_rule]) && isset($this->config_default[$method_rule])) {
                $tags = [
                    $this->config[$method_rule],
                    $this->config_default[$method_rule]
                ];
            } else {
                $tags = [$method_rule];
            }
        } else {
            if (empty($method_rule['by_symbol'])) {
                $tags = [
                    $method_rule['method'],
                    $method_rule['symbol'],
                ];
            } else {
                $tags = [
                    $method_rule['symbol'],
                    $method_rule['method'],
                ];
            }
        }

        // 3. One rule, one EMT. Matching EMT by the tag of EMT
        // 3.1 The temporary EMT(JSON formatted) defined in ruleset
        // 3.2 The EMT defined via Internationalization, e.g. en-us
        $error_templates = array_merge($this->get_error_templates(), $ruleset_error_templates);
        $tags_max_key = count($tags) - 1;
        foreach ($tags as $key => $value) {
            $ignore_default_template = $tags_max_key > $key;
            $error_template = $this->match_error_template_by_tag($value, $error_templates, $when_type, $ignore_default_template);
            if (!empty($error_template)) break;
        }

        return $error_template;
    }

    /**
     * One rule, one error message template(EMT). Matching EMT by the tag of EMT
     *
     * @param string $tag
     * @param array $error_templates
     * @param string $when_type
     * @param bool $ignore_default_template Ignore the default EMT if it's not the last tag
     * @return string
     */
    protected function match_error_template_by_tag($tag, $error_templates, $when_type = '', $ignore_default_template = false)
    {
        // The tag of When rule
        $when_type_tag = empty($when_type) ? '' : $tag . ':' . $when_type;

        if (!empty($when_type_tag)) {
            // The EMT of the rule with When rule
            if (isset($error_templates[$when_type_tag])) {
                return $error_templates[$when_type_tag];
            } else {
                // The EMT of When rule only
                $when_type_msg = $error_templates[$when_type];
                // The EMT of rule only
                if (isset($error_templates[$tag])) {
                    $error_template = $error_templates[$tag];
                }
            }
        } else {
            // The EMT of rule only
            if (isset($error_templates[$tag])) {
                $error_template = $error_templates[$tag];
            }
        }

        if (empty($error_template) && $ignore_default_template == true) return '';

        if (empty($error_template)) {
            $error_template = $error_templates['default'];
        }

        // Auto inject the When rule EMT into the rule EMT
        if (!empty($when_type_msg)) $error_template = $when_type_msg . $error_template;

        return $error_template;
    }

    /**
     * Inject parameters into the error template
     *  
     * @param string $error_template
     * @param array $parameters
     * @param array $method_rule
     * @return string
     */
    protected function inject_parameters_to_error_template($error_template, $parameters, $method_rule)
    {
        foreach ($parameters as $key => $value) {
            $error_template = $this->inject_parameter_to_error_template($error_template, $key, $value, $method_rule);
        }
        return $error_template;
    }

    /**
     * Inject parameter into the error template
     * For example:
     * - @p1: The value of the second parameter
     * - @t1: The type of the second parameter
     *
     * @param string $error_template
     * @param int $key
     * @param mixed $value
     * @param array $method_rule
     * @return mixed
     */
    protected function inject_parameter_to_error_template($error_template, $key, $value, $method_rule)
    {
        if (is_array($value) || is_object($value)) {
            if ($this->is_in_method($method_rule['symbol'])) {
                $value = implode(',', $value);
            } else {
                return $error_template;
            }
        }

        $p = $value;
        if (!isset($value)) {
            $p = "NULL";
        } else if (is_bool($value)) {
            $p = $value ? 'true' : 'false';
        }
        $error_template = str_replace('@p' . $key, $p, $error_template);
        $error_template = str_replace('@t' . $key, $this->get_parameter_type($value), $error_template);
        return $error_template;
    }

    /**
     * Execute validation with the field and its ruleset. Contains cases:
     * 1. If rule
     * 2. Required(*) rule
     * 3. Optional(O) rule
     * 4. Optional Unset(O!) rule
     * 5. When rule
     * 6. Regular Expression
     * 7. Method
     *
     * @param array $data The parent data of the field which is related to the rule
     * @param string $field The field which is related to the rule
     * @param string $ruleset The ruleset of the field
     * @param bool $field_path Field path, suce as fruit.apple
     * @param bool $is_parallel_rule Flag of or rule
     * @return bool The result of validation
     */
    protected function execute_ruleset($data, $field, $ruleset, $field_path = false, $is_parallel_rule = false)
    {
        $this->set_current_field_path($field_path)
            ->set_current_field_ruleset($ruleset);

        $ruleset = $this->parse_ruleset($ruleset);

        if (empty($ruleset) || empty($ruleset['rules'])) {
            return true;
        }

        $ruleset_error_templates = $ruleset['error_templates'];

        foreach ($ruleset['rules'] as $rule) {
            if (empty($rule)) {
                continue;
            }

            $this->set_current_rule($rule);

            $result = true;
            $error_type = 'validation';
            $params = [];
            $method_rule = [];

            $has_when_rule = -1;
            $when_type = '';
            $is_met_when_rule = false;
            /**
             * Most of the system symbols or methods support to be append a When rule.
             * If the condition of When rule is not met, we don't need to check the methods.
             * 
             * The rule is {method} + ":" + "when|when_not"
             * For example:
             *  - required:when
             *  - optional:when_not
             *  - email:when
             */
            if (preg_match($this->config['reg_whens'], $rule, $matches) || preg_match($this->config_default['reg_whens'], $rule, $matches)) {
                $target_rule = $matches[1];
                $when_rule = $matches[3];

                if (preg_match($this->config['reg_when'], $rule) || preg_match($this->config_default['reg_when'], $rule)) {
                    $has_when_rule = 1;
                    $when_type = 'when';
                } else {
                    $has_when_rule = 0;
                    $when_type = 'when_not';
                }

                $method_rule = $this->parse_method($when_rule, $data, $field);
                $params = $method_rule['params'];
                $if_result = $this->execute_method($method_rule, $field_path);

                if (is_array($if_result) && !empty($if_result['error_type']) && $if_result['error_type'] == 'undefined_method') return false;

                if (
                    ($when_type === 'when' && $if_result === true)
                    || ($when_type === 'when_not' && $if_result !== true)
                ) {
                    $is_met_when_rule = true;
                } else {
                    $is_met_when_rule = false;
                }

                $rule = $target_rule;
            }

            /**
             * If rule
             * If it's a 'If rule' or 'If Not rule' -> Means this field is conditionally optional;
             * - If the 'If rule' validation result is true, continue to validate the subsequnse rule; Otherwise, skip validating the subsequnse rule
             * - If the 'If Not rule' validation result is not true, continue to validate the subsequnse rule; Otherwise, skip validating the subsequnse rule
             */
            if (preg_match($this->config['reg_ifs'], $rule, $matches)) {
                if (preg_match($this->config['reg_if'], $rule)) {
                    $if_type = 'if';
                } else {
                    $if_type = 'if_not';
                }

                $rule = $matches[1];

                $method_rule = $this->parse_method($rule, $data, $field);
                $params = $method_rule['params'];
                $result = $this->execute_method($method_rule, $field_path);

                if (is_array($result) && !empty($result['error_type']) && $result['error_type'] == 'undefined_method') return false;
                if (
                    ($if_type === 'if' && $result !== true)
                    || ($if_type === 'if_not' && $result === true)
                ) {
                    return true;
                }

                $result = true;
            }
            /**
             * - Required(*) rule
             * - Required When rule
             */
            else if ($rule == $this->config['symbol_required'] || $rule == $this->config_default['symbol_required']) {
                if (!static::required(isset($data[$field]) ? $data[$field] : null)) {
                    /**
                     * Required(*) rule
                     */
                    if ($has_when_rule === -1) {
                        $result = false;
                        $error_type = $this->config_default['symbol_required'];
                        $error_template = $this->match_error_template($ruleset_error_templates, 'symbol_required');
                    }
                    /**
                     * Required When rule
                     * If it's a 'Required When rule' or 'Required When Not rule' rule -> Means this field is conditionally required;
                     * - If the 'Required When rule' validation result is true and the field is not set or empty, then the field is invalid. Otherwise, continue to validate the subsequnse rule;
                     * - If the 'Required When Not rule' validation result is not true and the field is not set or empty, then the field is invalid. Otherwise, continue to validate the subsequnse rule;
                     */
                    else if ($has_when_rule !== -1 && $is_met_when_rule === true) {
                        $result = false;
                        $error_type = $this->config_default['symbol_required'] . ':' . $when_type;
                        $error_template = $this->match_error_template($ruleset_error_templates, 'symbol_required', $when_type);
                    }
                    /**
                     * Required When rule
                     * If don't met the When condition, then the field is optional
                     */
                    else if ($has_when_rule !== -1 && $is_met_when_rule !== true) {
                        return true;
                    }
                }
            }
            /**
             * - Optional(O) rule
             * - Optional When rule
             */
            else if ($rule == $this->config['symbol_optional'] || $rule == $this->config_default['symbol_optional']) {
                if (!static::required(isset($data[$field]) ? $data[$field] : null)) {
                    /**
                     * Optional(O) rule
                     */
                    if ($has_when_rule === -1) {
                        return true;
                    }
                    /**
                     * Optional When rule
                     * If met the When condition, then the field is optional
                     */
                    else if ($has_when_rule !== -1 && $is_met_when_rule === true) {
                        return true;
                    }
                    /**
                     * Optional When rule
                     * If don't met the When condition, then the field is required
                     */
                    else if ($has_when_rule !== -1 && $is_met_when_rule !== true) {
                        $result = false;
                        $error_type = $this->config_default['symbol_optional'] . ':' . $when_type;
                        $error_template = $this->match_error_template($ruleset_error_templates, 'symbol_optional', $when_type);
                    }
                }
            }
            /**
             * - Optional Unset(O!) rule
             * - Optional Unset When rule
             */
            else if ($rule == $this->config['symbol_optional_unset'] || $rule == $this->config_default['symbol_optional_unset']) {
                if (!isset($data[$field])) {
                    /**
                     * Optional Unset(O!) rule
                     */
                    if ($has_when_rule === -1) {
                        return true;
                    }
                    /**
                     * Optional Unset When rule
                     * If met the When condition, then the field is optional_unset
                     */
                    else if ($has_when_rule !== -1 && $is_met_when_rule === true) {
                        return true;
                    }
                    /**
                     * Optional Unset When rule
                     * If don't met the When condition, then the field is required
                     */
                    else if ($has_when_rule !== -1 && $is_met_when_rule !== true) {
                        $result = false;
                        $error_type = $this->config_default['symbol_optional_unset'] . ':' . $when_type;
                        $error_template = $this->match_error_template($ruleset_error_templates, 'symbol_optional_unset', $when_type);
                    }
                } else if (!static::required(isset($data[$field]) ? $data[$field] : null)) {
                    /**
                     * Optional Unset(O!) rule
                     */
                    if ($has_when_rule === -1) {
                        $result = false;
                        $error_type = $this->config_default['symbol_optional_unset'];
                        $error_template = $this->match_error_template($ruleset_error_templates, 'symbol_optional_unset');
                    }
                    /**
                     * Optional Unset When rule
                     * - If met the When condition, then the field can not be empty
                     * - If don't met the When condition, then the field is required
                     */
                    else {
                        $result = false;
                        $error_type = $this->config_default['symbol_optional_unset'] . ':' . $when_type;
                        $error_template = $this->match_error_template($ruleset_error_templates, 'symbol_optional_unset', $when_type);
                    }
                }
            }
            /**
             * Most of the system symbols or methods support to be append a When rule.
             * If the condition of When rule is not met, we don't need to check the Regular expression and Method below
             */
            else if ($has_when_rule !== -1 && $is_met_when_rule !== true) {
                $result = true;
            }
            // Regular expression
            else if (preg_match($this->config['reg_preg'], $rule, $matches)) {
                $preg = isset($matches[1]) ? $matches[1] : $matches[0];
                if (!preg_match($this->config['reg_preg_strict'], $preg, $matches)) {
                    $result = false;
                    $error_type = 'preg';
                    $error_template = $this->match_error_template([], 'preg_format');
                    $error_template = str_replace($this->symbol_preg, $preg, $error_template);
                } else {
                    if (!preg_match($preg, $data[$field], $matches)) {
                        $result = false;
                        $error_template = $this->match_error_template($ruleset_error_templates, 'preg', $when_type);
                        $error_template = str_replace($this->symbol_preg, $preg, $error_template);
                    }
                }
            }
            // Method
            else {
                $method_rule = $this->parse_method($rule, $data, $field);
                $params = $method_rule['params'];
                $result = $this->execute_method($method_rule, $field_path);

                /**
                 * If method validation is success. should return true.
                 * If retrun anything others which is not equal to true, then means method validation failed.
                 * If retrun not a boolean value, will use the result as the error message template.
                 */
                if ($result !== true) {
                    $error_template = $this->match_error_template($ruleset_error_templates, $method_rule, $when_type, $result);
                    if (empty($method_rule['by_symbol'])) $error_template = str_replace($this->symbol_method, $method_rule['method'], $error_template);
                    else $error_template = str_replace($this->symbol_method, $method_rule['symbol'], $error_template);

                    if (is_array($result)) {
                        $error_type = isset($result['error_type']) ? $result['error_type'] : $error_type;
                    }
                }
            }

            /**
             * Inject variables into error message template
             * For example:
             * - @this
             * - @p1
             */
            if ($result !== true) {
                // Replace symbol to field name and parameter value
                $error_template = str_replace($this->symbol_this, $field_path, $error_template);
                $error_msg = $this->inject_parameters_to_error_template($error_template, $params, $method_rule);

                $message = [
                    "error_type" => $error_type,
                    "message" => $error_msg,
                ];

                // Default fields: error_type, message
                // Allow user to add extra field in error message.
                if (is_array($result)) {
                    $message = array_merge($result, $message);
                }

                $this->set_error($field_path, $message, $is_parallel_rule);
                return false;
            }
        }

        return true;
    }

    /**
     * Parse method and its parameters
     *
     * @param string $rule One separation of ruleset
     * @param array $data The parent data of the field which is related to the rule
     * @param string $field The field which is related to the rule
     * @return array Method detail
     */
    protected function parse_method($rule, $data, $field)
    {
        // If force parameter, will not add the field value as the first parameter even though no the field parameter
        if (preg_match($this->config['symbol_method_standard'], $rule, $matches)) {
            $method = $matches[1];
            $params = $matches[2];
            $params = $this->parse_parameters_strict($params);
            // If classic parameter, will add the field value as the first parameter if no the field parameter
        } else if (preg_match($this->config['symbol_method_omit_this'], $rule, $matches)) {
            $method = $matches[1];
            $params = $matches[2];
            $params = $this->parse_parameters_strict($params);
            if (!in_array($this->symbol_this, $params)) {
                array_unshift($params, $this->symbol_this);
            }
            // If no parameter, will add the field value as the first parameter
        } else {
            $method = $rule;
            $params = [$this->symbol_this];
        }

        list($by_symbol, $symbol, $method, $class) = $this->match_method_and_symbol($method);

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
            } else {
                $param = static::parse_strict_data_type($param);
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
            'by_symbol' => $by_symbol,
            'params' => $params,
        ];

        $this->set_current_method($method_rule);

        return $method_rule;
    }

    /**
     * Parse parameters from string to array
     * - The symbol_parameter_separator(e.g. ",") is allowed in parameter
     * - Supports One-dimensional Array which look like this [1,2,3],a,b
     *
     * @param string $parameter
     * @return array
     */
    protected function parse_parameters_strict($parameter)
    {
        $symbol_parameter_separator = $this->config['symbol_parameter_separator'];
        $symbol_parameter_separator_length = strlen($symbol_parameter_separator);

        $parameters = [];
        $current_parameter = '';
        $is_next_parameter_flag = 0;
        $is_array_flag = 0;
        $array_flags = [];

        $parameter_length = strlen($parameter);
        for ($i = 0; $i < $parameter_length; $i++) {
            $char = $parameter[$i];

            // 支持自定义配置 symbol_parameter_separator 为多个字符
            if ($symbol_parameter_separator_length > 1 && $char == $symbol_parameter_separator[0]) {
                $ii = $i + 1;
                $is_symbol_parameter_separator = true;
                for ($j = 1; $j < $symbol_parameter_separator_length; $j++) {
                    if ($symbol_parameter_separator[$j] != $parameter[$ii]) {
                        $is_symbol_parameter_separator = false;
                        break;
                    }
                }
                if ($is_symbol_parameter_separator) {
                    $i = $i + $symbol_parameter_separator_length - 1;
                    $char = $symbol_parameter_separator;
                }
            }

            // \ 是转义字符，在它之后的任意一个字符，都不能被当做是参数分隔符
            // 例如：`\,` 表示字符串 `,`
            if ($char === '\\') {
                $current_parameter .= $parameter[$i + 1];
                $i++;
                continue;
            }

            /**
             * 首次数组开头 [ 或者 {，表明接下来是数组。
             */
            if ($char == '[' || $char == '{') {
                $is_array_flag++;
                $array_flags[$is_array_flag] = $char;
            }
            // 数组阶段中，任意字符都是当前参数的一部分
            // 直到匹配到下一个 ] 或者 }，表明数组结束。
            else if ($is_array_flag > 0) {
                if (
                    ($char == ']' && $array_flags[$is_array_flag] == '[')
                    || ($char == '}' && $array_flags[$is_array_flag] == '{')
                ) {
                    unset($array_flags[$is_array_flag]);
                    $is_array_flag--;
                }
            }
            // 一般非数组的参数中，不会包含 ","，所以匹配到它则表明接下来是下一个参数
            // 在数组中，可能包含 ","，所以必须在数组结束后，匹配到它才表明接下来是下一个参数
            else if ($char === $symbol_parameter_separator) {
                $is_next_parameter_flag = 1;
            }

            if ($is_next_parameter_flag == 0) {
                $current_parameter .= $char;
            } else {
                $is_next_parameter_flag = 0;
                // Remove the whitespace on the left and right
                $current_parameter = trim($current_parameter, ' ');
                if ($current_parameter === '') throw RuleException::parameter('Contiguous separator', $this->get_recurrence_current(), $this->config['auto_field']);
                $parameters[] = $current_parameter;
                $current_parameter = '';
                $is_array_flag = 0;
            }
        }

        if ($is_array_flag > 0) throw RuleException::parameter('Unclosed [ or {', $this->get_recurrence_current(), $this->config['auto_field']);
        // Remove the whitespace on the left and right
        $current_parameter = trim($current_parameter, ' ');
        if ($current_parameter === '') {
            if (empty($parameters)) {
                throw RuleException::parameter('Empty', $this->get_recurrence_current(), $this->config['auto_field']);
            } else {
                throw RuleException::parameter('Endding separator', $this->get_recurrence_current(), $this->config['auto_field']);
            }
        }
        if (!empty($current_parameter)) $parameters[] = $current_parameter;
        return $parameters;
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
            } else {
                $method_existed = false;
                for ($i = count($this->rule_classes) - 1; $i >= 0; $i--) {
                    $rule_class = $this->rule_classes[$i];
                    if (method_exists($rule_class, $method_rule['method'])) {
                        $method_existed = true;
                        $result = call_user_func_array([$rule_class, $method_rule['method']], $params);
                        break;
                    }
                }

                if (empty($method_existed)) {
                    if (method_exists($this, $method_rule['method'])) {
                        $result = call_user_func_array([$this, $method_rule['method']], $params);
                    } else if (function_exists($method_rule['method'])) {
                        $result = call_user_func_array($method_rule['method'], $params);
                    } else {
                        $result = [
                            "error_type" => 'undefined_method',
                            "message" => "TAG:call_method",
                        ];
                    }
                }
            }
        } catch (Throwable $t) {
            throw GhException::extend_privious($t, $this->get_recurrence_current(), $this->config['auto_field']);
        }
        // For the PHP version < 7
        catch (Exception $t) {
            throw GhException::extend_privious($t, $this->get_recurrence_current(), $this->config['auto_field']);
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
        $this->recurrence_current['field_ruleset'] = '';
        $this->recurrence_current['rule'] = '';
        $this->recurrence_current['method'] = [];
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
     * Set the ruleset of current field
     * 
     * @param string|array $rule
     * @return static
     */
    protected function set_current_field_ruleset($field_ruleset)
    {
        if (is_array($field_ruleset)) {
            /**
             * The 0 is the ruleset key.
             * @see static::is_rule_leaf_object()
             */
            $field_ruleset = isset($field_ruleset[0]) ? $field_ruleset[0] : '';
        }
        $this->recurrence_current['field_ruleset'] = $field_ruleset;
        $this->recurrence_current['rule'] = '';
        $this->recurrence_current['method'] = [];
        return $this;
    }

    /**
     * Get the ruleset of current field
     * 
     * @return string|array
     */
    public function get_current_field_ruleset()
    {
        return $this->recurrence_current['field_ruleset'];
    }

    /**
     * Set the current rule of the currect ruleset
     *
     * @param string $rule
     * @return static
     */
    protected function set_current_rule($rule)
    {
        $this->recurrence_current['rule'] = $rule;
        $this->recurrence_current['method'] = [];
        return $this;
    }

    /**
     * Get the current rule of the currect ruleset
     * 
     * @return string
     */
    public function get_current_rule()
    {
        return $this->recurrence_current['rule'];
    }

    /**
     * Set the current method of the currect rule
     * 
     * @param array $method
     * @return static
     */
    protected function set_current_method($method)
    {
        $this->recurrence_current['method'] = $method;
        return $this;
    }

    /**
     * Get the current method of the currect rule
     * 
     * @return string
     */
    public function get_current_method()
    {
        return $this->recurrence_current['method'];
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
     * @param bool $is_parallel_rule Flag of or rule
     * @return static
     */
    protected function set_error($field = '', $message = '', $is_parallel_rule = false)
    {
        if (!$is_parallel_rule) $this->validation_status = false;

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
     * @param bool {@deprecated} $is_general If true, return error message for general error. If false, return error message for detailed error.
     * @return array
     * @api
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

    /**
     * Get validation result of executing the rules.
     *
     * @return bool
     * @api
     */
    public function get_result()
    {
        return $this->result;
    }

    /**
     * Get the original data that need to be validated.
     *
     * @return array
     */
    public function get_data()
    {
        return $this->data;
    }

    /**
     * Detect the type of parameter and forcibly convert it to the corresponding type
     * For example:
     * - abc/"abc": string abc
     * - 123: int 123
     * - "123": string 123
     *
     * @param mixed $data
     * @param bool $is_trim_quotes_only If true, only trim quotes
     * @return mixed
     */
    public static function parse_strict_data_type($data, $is_trim_quotes_only = false)
    {
        if (!is_string($data)) return $data;

        if (preg_match('/^[\'"](.*)[\'"]$/', $data, $matches)) {
            $data = $matches[1];
        } else if (!$is_trim_quotes_only) {
            if (preg_match('/^-?\d+$/', $data)) {
                $data = (int) $data;
            } else if (preg_match('/^-?\d+\.\d+$/', $data)) {
                $data = (float) $data;
            } else if (static::bool_string($data)) {
                $data = in_array($data, ['true', 'TRUE']);
            } else if (static::null_string($data)) {
                $data = null;
            } else if (preg_match('/^[\[\{].*[\]\}]$/', $data)) {
                $data = json_decode($data);
                if ($data === null) throw new Exception(RuleException::make_parameter_type_message("JSON"), RuleException::CODE_PARAMETER_TYPE);
            }
        }

        return $data;
    }

    /**
     * Get the data type of parameter
     *
     * @param mixed $data
     * @return string
     */
    public function get_parameter_type($parameter)
    {
        $parameter_type = gettype($parameter);
        switch ($parameter_type) {
            case 'integer':
                $parameter_type = 'int';
                break;
            case 'double':
                $parameter_type = 'float';
                break;
            case 'boolean':
                $parameter_type = 'bool';
                break;
        }
        return $parameter_type;
    }

    /**
     * To convert a camelCase(camel case, camel caps or medial capitals) formatted string to a non-camelCase format.
     * For example: "RuleDefault" -> "rule_default"
     *
     * @param string $camelized_data
     * @param string $separator If it's "_", the non-camelCase format was called snakeCase
     * @return string
     */
    protected function uncamelize($camelized_data, $separator = '_')
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelized_data));
    }

    /**
     * To convert a non-camelCase formatted string to a camelCase(camel caps) format.
     *
     * @param string $uncamelized_data
     * @param string $separator
     * @return string
     */
    protected function capital_camelize($uncamelized_data, $separator = '_')
    {
        $uncamelized_data = str_replace($separator, " ", lcfirst($uncamelized_data));
        return str_replace(" ", "", ucwords($uncamelized_data));
    }

    /**
     * To convert a non-camelCase formatted string to a camelCase(medial capitals) format.
     *
     * @param string $uncamelized_data
     * @param string $separator
     * @return string
     */
    protected function medial_capital_camelize($uncamelized_data, $separator = '_')
    {
        return lcfirst($this->capital_camelize($uncamelized_data, $separator));
    }

    /**
     * Check if it's an index array or not.
     *
     * @param array $array
     * @return bool
     */
    protected function is_index_array($array)
    {
        if (!is_array($array)) return false;
        return array_keys($array) === range(0, count($array) - 1);
    }

    /**
     * Check if it's an "in" type method name or not.
     * For example, "in_array" method
     *
     * @param string $method
     * @return bool
     */
    protected function is_in_method($method)
    {
        return preg_match('/^!?[\<\(][a-zA-Z0-9_\*]+[\>\)]$/', $method, $matches);
    }

    /**
     * The field must be present and its data must not be empty string
     *
     * @param mixed $data
     * @return bool
     */
    public static function required($data)
    {
        return $data === 0 || $data === 0.0 || $data === 0.00 || $data === '0' || $data === '0.0' || $data === '0.00' || $data === false || !empty($data);
    }

    /**
     * The field data must be a boolean string
     *
     * @param mixed $data
     * @return bool
     */
    public static function bool_string($data)
    {
        if (!is_string($data)) return false;
        return in_array($data, ['true', 'TRUE', 'false', 'FALSE']);
    }

    /**
     * The field data must be a null string
     *
     * @param mixed $data
     * @return bool
     */
    public static function null_string($data)
    {
        if (!is_string($data)) return false;
        return in_array($data, ['null', 'NULL']);
    }
}
