<?php

namespace githusband;

use githusband\Entity\RulesetEntity;
use githusband\Entity\RuleEntity;
use githusband\Rule\RuleClassDefault;
use githusband\Rule\RuleClassDatetime;
use githusband\Rule\RuleClassArray;
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
     * The lasted key of static::$rules
     *
     * @see static::set_rules()
     * @var string
     */
    protected $rule_key;

    /**
     * Validation rules. Default empty. Should be set before validation.
     * 
     * @see static::set_rules()
     * @var array
     */
    protected $rules = [];

    /**
     * Validation ruleset entities which are parsed from static::$rules
     * 
     * If enable_entity is true, we should parse the rules into ruleset entity, so we don't have to re-parse the rules every time when we try to validate the data by using the same rules.
     *
     * @see static::$config['enable_entity']
     * @var array
     */
    protected $ruleset_entities = [];

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
    protected $recurrence_current = [];

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
        'enable_entity' => false,                                   // Pre-parse ruleset into ruleset entity to reuse the ruleset without re-parse
        'validation_global' => true,                                // 1. true - validate all rules even though previous rule had been failed; 2. false - stop validating when any rule is failed
        'auto_field' => "data",                                     // If root data is string or numberic array, add the auto_field as the root data field name
        'reg_msg' => '/ >> (.*)$/sm',                               // Set the error message format for all the methods after a rule string
        'reg_preg' => '/^(\/.+\/.*)$/',                             // If a rule match reg_preg, indicates it's a regular expression instead of method
        'reg_preg_strict' => '/^(\/.+\/[imsxADSUXJun]*)$/',         // Verify if a regular expression is valid
        'symbol_if' => 'if',                                        // The start of IF construct. e.g. `if ( expr ) { statement }`
        'symbol_else' => 'else',                                    // The else part of IF construct. e.g. `else { statement }`. Then the elseif part is `else if ( expr ) { statement }`
        'symbol_logical_operator_not' => '!',                       // The logical operator not. e.g. `if ( !expr ) { statement }`
        'symbol_logical_operator_or' => '||',                       // The logical operator or. e.g. `if ( expr || expr ) { statement }`
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
        'self_ruleset_key' => '__self__',                           // If an array has such a subfield with the same name as {self_ruleset_key}, then the ruleset of this subfield is the ruleset of the array.
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
     * Using symbol mapped to real method. e.g. 'equal' => '='
     * 
     * @example `{"equal": "="}`
     * @example `{"equal": {"symbols": ["=", "EQUAL"]}}` Multiple symbols of one method.
     * @see githusband\Rule\RuleClassDefault::$method_symbols
     * @see githusband\Rule\RuleDefaultTrait::$method_symbols_of_rule_default_trait
     * @var array
     */
    protected $method_symbols = [];
    protected $deprecated_method_symbols = [];
    /**
     * Reversed method symbol.
     * 
     * Why reverse the method symbol? The purpose is that for easily to check if a rule is symbol or method.
     * 
     * @example {"=": "equal"}
     * @example `{"=": "equal", "EQUAL": "equal"}` Multiple symbols of one method.
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
        RuleClassArray::class,
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
                $this->method_symbols = $this->merge_method_symbols($this->method_symbols, $this->{$deprecated_trait_method_symbols});
                $this->deprecated_method_symbols = $this->merge_method_symbols($this->deprecated_method_symbols, $this->{$deprecated_trait_method_symbols});
            }

            $trait_method_symbols = "method_symbols_of_{$trait_name_uncamelized}";
            if (property_exists($this, $trait_method_symbols)) {
                $this->method_symbols = $this->merge_method_symbols($this->method_symbols, $this->{$trait_method_symbols});
            }
        }

        $this->method_symbols_reversed = $this->reverse_method_symbols($this->method_symbols);
    }

    /**
     * Merge two method symbols array
     * 
     * @param array $first_method_symbols
     * @param array $next_method_symbols
     * @return array
     */
    protected function merge_method_symbols($first_method_symbols, $next_method_symbols)
    {
        foreach ($next_method_symbols as $method => $method_data) {
            if (isset($first_method_symbols[$method])) {
                $first_method_symbols[$method] = $this->merge_symbol_of_method($first_method_symbols[$method], $method_data);
            } else {
                $first_method_symbols[$method] = $method_data;
            }
        }

        return $first_method_symbols;
    }

    /**
     * Merge two symbols of a same method
     * 
     * When you want to add new symbol for a same method to deprecate it old symbol, you 
     *
     * @example
     * ```
     * #1. First Method Symbol
     * `{"equal": "="}`
     * #2. Next Method Symbol
     * `{"equal": "EQUAL"}`
     * #3. Merged Method Symbol
     * {"equal": {"symbols": ["=", "EQUAL"]}}
     * ```
     * @param string|array $first
     * @param string|array $next
     * @return array
     */
    protected function merge_symbol_of_method($first, $next)
    {
        $method_data = [];
        if (is_string($first)) {
            $method_data['symbols'] = [$first];
        } else {
            $method_data = $first;
        }

        if (is_string($next)) {
            $method_data['symbols'][] = $next;
        } else {
            foreach ($next as $nk => $nv) {
                if ($nk == 'symbols') {
                    if (is_string($method_data[$nk])) $method_data[$nk] = [$method_data[$nk]];
                    if (is_string($next[$nk])) $next[$nk] = [$next[$nk]];
                    $method_data[$nk] = array_merge($method_data[$nk], $next[$nk]);
                } else {
                    $method_data[$nk] = $nv;
                }
            }
        }

        return $method_data;
    }

    /**
     * Reverse the method_symbols array
     *
     * @param array $method_symbols
     * @return array
     */
    protected function reverse_method_symbols($method_symbols)
    {
        $method_symbols_reversed = [];
        foreach ($method_symbols as $method => $method_data) {
            $this->reverse_one_method_symbol($method, $method_data, $method_symbols_reversed);
        }

        return $method_symbols_reversed;
    }

    /**
     * Reverse one of the method_symbols
     *
     * @example
     * ```
     * #1. Method Symbol
     * `{"equal": "="}`
     * #2. Reversed Method Symbol
     * `{"=": "equal"}`
     * ```
     * @example
     * ```
     * #1. Method Symbol
     * {"equal": {"symbols": ["=", "EQUAL"]}}
     * #2. Reversed Method Symbol
     * `{"=": "equal", "EQUAL": "equal"}`
     * ```
     * @see static::merge_symbol_of_method()
     * @param string $method
     * @param string|array $method_data
     * @param array $method_symbols_reversed The target you want to save the reversed data into.
     * @return void
     */
    protected function reverse_one_method_symbol($method, $method_data, &$method_symbols_reversed) {
        if (is_string($method_data)) {
            $method_symbols_reversed[$method_data] = $method;
        } else if (!empty($method_data['symbols'])) {
            $symbols = $method_data['symbols'];
            unset($method_data['symbols']);
            $method_tmp = array_merge([
                'method' => $method
            ], $method_data);
            if (is_array($symbols)) {
                foreach ($symbols as $symbol) {
                    $method_symbols_reversed[$symbol] = $method_tmp;
                }
            } else {
                $method_symbols_reversed[$symbols] = $method_tmp;
            }
        }
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
            $rule_class::$method_symbols = $this->merge_method_symbols($rule_class::$deprecated_method_symbols, $rule_class::$method_symbols);
        }

        $this->reverse_rule_class_method_symbols($rule_class);

        if (is_object($rule_class)) return $rule_class;
        return new $rule_class();
    }

    /**
     * Reverse the method_symbols array of a rule class
     *
     * @param class $rule_class A class that contains multiple rule methods
     * @return void
     */
    protected function reverse_rule_class_method_symbols($rule_class)
    {
        $this->rule_class_method_symbols_reversed[$rule_class] = $this->reverse_method_symbols($rule_class::$method_symbols);
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
    public function is_symbol_of_method($symbol)
    {
        if (isset($this->method_symbols_reversed[$symbol])) return true;

        for ($i = count($this->rule_classes) - 1; $i >= 0; $i--) {
            $rule_class = $this->rule_classes[$i];
            $rule_class_name = get_class($rule_class);
            $method_symbols_reversed = $this->rule_class_method_symbols_reversed[$rule_class_name];
            if (isset($method_symbols_reversed[$symbol])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Match the method(from symbol) or symbol(from method) and return them and the rule class
     *
     * @param string $in The method or its symbol
     * @param string $operator The logical operator. e.g. `!`
     * @return array
     */
    public function match_method_and_symbol($in, $operator)
    {
        $by_symbol = false;

        /**
         * Step 1. Check if the {$in} is a symbol.
         * If yes, then get its method.
         * If not, then treat it as a method and start step 2.
         */
        $in_with_operator = $operator . $in;
        if (!empty($operator) && isset($this->method_symbols_reversed[$in_with_operator])) {
            /**
             * Check if the {$operator + $in} symbol is existed
             * Because we support the operator to be a part of the symbol.
             * For example, `!=` is the symbol of method `not_equal`, so we don't treat the `!` as a operator.
             */
            $by_symbol = true;
            $in = $operator . $in;
            $operator = '';
            $class = 'static';
            $method_data = $this->method_symbols_reversed[$in_with_operator];
        } else if (isset($this->method_symbols_reversed[$in])) {
            $by_symbol = true;
            $class = 'static';
            $method_data = $this->method_symbols_reversed[$in];
        } else {
            /**
             * Check if it's the symbol which set in extended rule classes.
             * @see static::$rule_classes_default
             * @see static::init_rule_classes()
             */
            for ($i = count($this->rule_classes) - 1; $i >= 0; $i--) {
                $rule_class = $this->rule_classes[$i];
                $rule_class_name = get_class($rule_class);
                $method_symbols_reversed = $this->rule_class_method_symbols_reversed[$rule_class_name];
                if (!empty($operator) && isset($method_symbols_reversed[$in_with_operator])) {
                    $by_symbol = true;
                    $in = $operator . $in;
                    $operator = '';
                    $class = $rule_class;
                    $method_data = $method_symbols_reversed[$in_with_operator];
                    break;
                } else if (isset($method_symbols_reversed[$in])) {
                    $by_symbol = true;
                    $class = $rule_class;
                    $method_data = $method_symbols_reversed[$in];
                    break;
                }
            }
        }

        if ($by_symbol) {
            $symbol = $in;
        }
        /**
         * Step 2. Get the symbol for the method.
         * NOTE:
         * - One method may have not set a symbol.
         * - One method may have set multiple symbols.
         */
        else {
            $method = $in;
            if (isset($this->method_symbols[$in])) {
                $class = 'static';
                $symbol_data = $this->method_symbols[$in];
                if (is_array($symbol_data)) {
                    /** Why 'symbols'? @see static::merge_symbol_of_method() */
                    if (empty($symbol_data['symbols'])) {
                        // Only set the method data(e.g. is_variable_length_argument) without setting a symbol
                        $symbol = $method;
                        $method_data = $symbol_data;
                        $method_data['method'] = $method;
                    } else {
                        $symbol = $symbol_data['symbols'];
                        if (is_array($symbol)) $symbol = $symbol[0];
                        $method_data = $this->method_symbols_reversed[$symbol];
                    }
                } else {
                    $symbol = $symbol_data;
                    $method_data = $this->method_symbols_reversed[$symbol];
                }
            } else {
                /**
                 * Check if it's the method which set in extended rule classes.
                 * @see static::$rule_classes_default
                 * @see static::init_rule_classes()
                 */
                for ($i = count($this->rule_classes) - 1; $i >= 0; $i--) {
                    $rule_class = $this->rule_classes[$i];
                    $method_symbols = $rule_class::$method_symbols;
                    if (isset($method_symbols[$in])) {
                        $class = $rule_class;
                        $symbol_data = $method_symbols[$in];
                        if (is_array($symbol_data)) {
                            /** Why 'symbols'? @see static::merge_symbol_of_method() */
                            if (empty($symbol_data['symbols'])) {
                                // Only set the method data(e.g. is_variable_length_argument) without setting a symbol
                                $symbol = $method;
                                $method_data = $symbol_data;
                                $method_data['method'] = $method;
                            } else {
                                $symbol = $symbol_data['symbols'];
                                if (is_array($symbol)) $symbol = $symbol[0];
                                $rule_class_name = get_class($rule_class);
                                $method_symbols_reversed = $this->rule_class_method_symbols_reversed[$rule_class_name];
                                $method_data = $method_symbols_reversed[$symbol];
                            }
                        } else {
                            $symbol = $symbol_data;
                            $rule_class_name = get_class($rule_class);
                            $method_symbols_reversed = $this->rule_class_method_symbols_reversed[$rule_class_name];
                            $method_data = $method_symbols_reversed[$symbol];
                        }
                        break;
                    }
                }

                if (empty($class)) {
                    $class = 'global';
                    $symbol = $method;
                    /** Why 'method'? @see static::reverse_method_symbols() */
                    $method_data = [
                        'method' => $method
                    ];
                }
            }
        }

        return [
            $by_symbol,
            $symbol,
            $method_data,
            $operator,
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
     * @param string $rule_key
     * @param bool $skip_exception If true, then return static instead of throw an exception when exception accurs.
     * @param bool $force If false, the rule_key exists, then ingore to set it again. If true, even though the rule_key exists, update it.
     * @return static
     * @throws RuleException
     * @api
     */
    public function set_rules($rules = [], $rule_key = 'default', $skip_exception = false, $force = false)
    {
        if (empty($rules)) {
            return $this;
        }

        $this->rule_key = $rule_key;
        
        if (!$force && !empty($this->rules[$rule_key])) return $this;

        $this->rules[$rule_key] = $rules;

        if (!empty($this->config['enable_entity'])) {
            $this->init_recurrence_current();
            $ruleset_entity = $this->parse_to_ruleset_entity($rules, $rule_key, $skip_exception);
            $this->ruleset_entities[$rule_key] = $ruleset_entity;
        }

        return $this;
    }

    /**
     * Get validation rules
     *
     * @param ?string $rule_key
     * @return array
     */
    public function get_rules($rule_key = null)
    {
        if(!isset($rule_key)) $rule_key = $this->rule_key;

        return $this->rules[$rule_key];
    }

    /**
     * Get validation ruleset entity
     *
     * @param ?string $rule_key
     * @return RulesetEntity
     */
    public function get_ruleset_entity($rule_key = null)
    {
        if(!isset($rule_key)) $rule_key = $this->rule_key;

        return $this->ruleset_entities[$rule_key];
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
     * @param bool $is_reversed
     * @return array
     */
    public function get_method_symbols($is_reversed = false)
    {
        if (!$is_reversed) {
            $method_symbols = $this->method_symbols;
            $rule_classes_length = count($this->rule_classes);
            for ($i = 0; $i < $rule_classes_length; $i++) {
                $rule_class = $this->rule_classes[$i];
                $method_symbols = array_merge($method_symbols, $rule_class::$method_symbols);
            }

            return $method_symbols;
        } else {
            $method_symbols_reversed = $this->method_symbols_reversed;

            for ($i = count($this->rule_classes) - 1; $i >= 0; $i--) {
                $rule_class = $this->rule_classes[$i];
                $rule_class_name = get_class($rule_class);
                $method_symbols_reversed = array_merge($method_symbols_reversed, $this->rule_class_method_symbols_reversed[$rule_class_name]);
            }

            return $method_symbols_reversed;
        }
    }

    /**
     * Get Method Symbol that are deprecated
     * - this deprecated method symbols
     * - rule deprecated classes method symbols
     *
     * @param bool $is_reversed
     * @return array
     */
    public function get_deprecated_method_symbols($is_reversed = false)
    {
        $deprecated_method_symbols = $this->deprecated_method_symbols;
        $rule_classes_length = count($this->rule_classes);
        for ($i = 0; $i < $rule_classes_length; $i++) {
            $rule_class = $this->rule_classes[$i];
            if (property_exists($rule_class, 'deprecated_method_symbols')) {
                $deprecated_method_symbols = $this->merge_method_symbols($deprecated_method_symbols, $rule_class::$deprecated_method_symbols);
            }
        }

        if ($is_reversed) $deprecated_method_symbols = $this->reverse_method_symbols($deprecated_method_symbols);

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
     * @param string|array $symbol Symbol of method
     * @return static
     * @api
     */
    public function add_method($method, $callable, $symbol = '')
    {
        $this->methods[$method] = $callable;
        if (!empty($symbol)) {
            $this->method_symbols[$method] = $symbol;
            $this->reverse_one_method_symbol($method, $symbol, $this->method_symbols_reversed);
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
            if (empty($this->config['enable_entity'])) {
                $this->validate_by_ruleset_array($data);
            } else {
                $this->validate_by_ruleset_entity($data);
            }
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
     * Validating by ruleset array
     *
     * @param array $data
     * @return void
     */
    protected function validate_by_ruleset_array($data = [])
    {
        $rules = $this->get_rules();
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

        $this->init_recurrence_current();
        $this->execute($data, $rules);
    }

    /**
     * Validating by ruleset entity
     *
     * @param array $data
     * @return void
     */
    protected function validate_by_ruleset_entity($data = [])
    {
        $ruleset_entity = $this->get_ruleset_entity();
        $ruleset_entity->before_validate();
        $real_root_name = $ruleset_entity->get_real_root_name();
        // If The root rules has rule_system_symbol
        // Or The root rules is String, means root data is not an array
        // Set root data as an array to help validate the data
        if ($real_root_name !== null) {
            $auto_field = $this->config['auto_field'];
            $data = [$auto_field => $data];
            $this->data = $data;
            $this->result = $this->data;
        }

        $this->init_recurrence_current();
        $ruleset_entity->init_root_index_array_status();
        $this->execute_entity($data, $ruleset_entity);
        $ruleset_entity->init_root_index_array_status();
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
     * @return bool
     */
    protected function execute($data, $rules, $field_path = null)
    {
        $self_result = $this->execute_self_rules($data, $rules, $field_path);
        if ($self_result == 0) return false;
        else if ($self_result == 1 && empty($data)) return true;     // optional

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
                    $current_data = isset($data[$field]) ? $data[$field] : null;
                    $self_result = $this->execute_self_rules($current_data, $ruleset, $current_field_path);
                    if ($self_result == 0) $result = false;
                    else if ($self_result == 1 && empty($current_data)) $result = true;     // optional
                    else $result = $this->execute_index_array_rules($data, $field, $current_field_path, $ruleset[$ruleset_system_symbol]);
                }
                // Validate association array
                else {
                    // Validate association array
                    if ($this->is_association_array_rule($ruleset[$ruleset_system_symbol])) {
                        $result = $this->execute(isset($data[$field]) ? $data[$field] : null, $ruleset[$ruleset_system_symbol], $current_field_path);
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

                    $current_field_path = $this->delete_system_symbol($current_field_path, 'symbol_parallel_rule');
                    $current_field_path = $this->delete_system_symbol($current_field_path, 'symbol_index_array');
                    $this->set_current_field_path($current_field_path);

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
                    $this->set_current_field_path($current_field_path);

                    $result = $this->execute_parallel_rules($data, $field, $current_field_path, $ruleset);
                }
                // Validate index array
                else if ($this->has_system_symbol($field, 'symbol_index_array')) {
                    $field = $this->delete_system_symbol($field, 'symbol_index_array');
                    $current_field_path = $this->delete_system_symbol($current_field_path, 'symbol_index_array');
                    $this->set_current_field_path($current_field_path);

                    $result = $this->execute_index_array_rules($data, $field, $current_field_path, $ruleset);
                }
                // Validate association array
                else if ($this->is_association_array_rule($ruleset)) {
                    $result = $this->execute(isset($data[$field]) ? $data[$field] : null, $ruleset, $current_field_path);
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

        // self ruleset of index array
        if (isset($ruleset[$this->config['self_ruleset_key']])) unset($ruleset[$this->config['self_ruleset_key']]);

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
     * @return int 0 - No; 1 - By Default config; 2 - By User config;
     */
    protected function has_system_symbol($ruleset_system_symbol_string, $symbol_name, $ingore_left_dot = false)
    {
        switch ($symbol_name) {
            case 'symbol_array_optional':
                if (strpos($ruleset_system_symbol_string, $this->config['symbol_array_optional']) !== false) {
                    return 2;
                } else if (strpos($ruleset_system_symbol_string, $this->config_default['symbol_array_optional']) !== false) {
                    return 1;
                }
                break;
            case 'symbol_parallel_rule':
                if (strpos($ruleset_system_symbol_string, $this->config['symbol_parallel_rule']) !== false) {
                    return 2;
                } else if (strpos($ruleset_system_symbol_string, $this->config_default['symbol_parallel_rule']) !== false) {
                    return 1;
                }
                break;
            case 'symbol_index_array':
                if (strpos($ruleset_system_symbol_string, $this->config['symbol_index_array']) !== false) {
                    return 2;
                }

                if ($ingore_left_dot) {
                    if (strpos($ruleset_system_symbol_string, ltrim($this->config['symbol_index_array'], '.')) !== false) {
                        return 2;
                    }
                }
                break;
            default:
                return 0;
        }

        return 0;
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
    protected function delete_system_symbol($ruleset_system_symbol_string, $symbol_name, $ingore_left_dot = false)
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
     * Execute validation of self rules of a array node.
     * 
     * Result:
     *  - -1: No self ruleset
     *  -  0: Validate self ruleset but failed.
     *  -  1: Validate self ruleset and success.
     * 
     * @example
     * ```
     * $rule => [
     *      "person" => [
     *          "__self__" => "optional|<keys>[id, name]",
     *          "id" => "required|int|>=[1]|<=[100]",
     *          "name" => "required|string|/^[A-Z]+-\d+/"
     *      ]
     * ];
     * ```
     * @param array $data The parent data of the field which is related to the rules
     * @param string $field The field which is related to the rules
     * @param string $field_path Field path, suce as fruit.apple
     * @return int The result of validation
     */
    protected function execute_self_rules($data, &$rules, $field_path)
    {
        if (isset($rules[$this->config['self_ruleset_key']])) {
            $field = $this->get_path_final_field($field_path);
            $result = $this->execute_ruleset([
                $field => $data
            ], $field, $rules[$this->config['self_ruleset_key']], $field_path);
            unset($rules[$this->config['self_ruleset_key']]);
            $this->set_result($field_path, $result);
            return $result ? 1 : 0;
        }

        return -1;
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
     * @return bool The result of validation
     */
    protected function execute_index_array_rules($data, $field, $field_path, $ruleset)
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
                $result = $this->execute($data[$field], [$key => $ruleset], $field_path, true);

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
                $ruleset = str_replace($matches[0], '', $ruleset);
            }
        }

        $rules = $this->parse_if_ruleset($ruleset);

        return [
            'rules' => $rules,
            'error_templates' => $this->parse_error_templates($error_templates)
        ];
    }

    /**
     * It's allowed to set the if construct ruleset like PHP if ... else ...
     * 
     * A if construct contains:
     *  - condition
     *  - statement
     * 
     * @example 
     * ```
     * $rule = [
     *      "id" => "required|><[0,10]",
     *      "name_1_5" => "if (<(@id,5)) {
     *          if (<(@id,3)) {
     *              required|string|/^\d{1,3}\w+/
     *          } else {
     *              required|string|/^\d{4,5}\w+/
     *          }
     *      } else if (optional|=(@id,5)) {
     *          optional|string|/^\d{1,5}\w+/
     *      } else {
     *          optional|string
     *      }"
     * ]
     * ```
     * @param string $ruleset
     * @return array
     */
    protected function parse_if_ruleset($ruleset)
    {
        $symbol_if = $this->config['symbol_if'];
        $symbol_else = $this->config['symbol_else'];

        $symbol_if_length = strlen($symbol_if);
        $symbol_else_length = strlen($symbol_else);

        /**
         * Options:
         *  - ''
         *  - IF_CONDITION
         *  - IF_STATEMENT
         *  - IF_END
         *  - ELSE
         */
        $current_if_flag = '';

        $if_ruleset = '';
        $if_condition_ruleset = '';

        $left_bracket = '(';
        $right_bracket = ')';
        $left_bracket_count = 0;

        $left_curly_bracket = '{';
        $right_curly_bracket = '}';
        $left_curly_bracket_count = 0;

        /**
         * - 0: if;
         * - ...n: elseif or else;
         */
        $if_branch_index = 0;
        $if_branch_ruleset = '';
        $rules = [
            'text' => $ruleset,
            'type' => 'IF',
            'result' => [],
        ];

        $ruleset_length = strlen($ruleset);
        $is_start = true;

        /** @deprecated v2.6.0 */
        $deprecated_if_not_operator = '';

        for ($i = 0; $i < $ruleset_length; $i++) {
            $char = $ruleset[$i];

            /**
             * Ignore the spaces( \n\r\t):
             * - The spaces at the beginning
             * - The spaces of "if ( xxx ) { xxx } else if ( xxx ) { xxx } else { xxx }"
             */
            if ($is_start == true) {
                if (ctype_space($char)) {
                    continue;
                } else {
                    $is_start = false;

                    /**
                     * Supprot the old version of "!if" rule.
                     * Note that it would not work if you customed the "!if" rule config.
                     * 
                     * @deprecated v2.6.0
                     */
                    if (
                        $char == $this->config['symbol_logical_operator_not'] && $ruleset[$i+1] == $symbol_if[0] && $ruleset[$i+2] == $symbol_if[1]
                    ) {
                        $deprecated_if_not_operator = $char;
                        continue;
                    }
                }
            }
            $if_branch_ruleset .= $char;

            /**
             * Check if it's:
             *  1. "IF" rule
             *  2. "ELSEIF" rule after 1
             *  3. "ELSE" rule after 1 or 2
             */
            if ($current_if_flag === '') {
                /**
                 * 1. IF
                 * 2. ELSEIF: The ELSE part is checked before here, see IF_END block below.
                 */
                if (
                    ($symbol_if_length == 1 && $char == $symbol_if)
                    || ($symbol_if_length > 1 && $char == $symbol_if[0])
                ) {
                    $current_if_flag = 'IF_CONDITION';
    
                    $ij = $i;
                    if ($symbol_if_length > 1) {
                        $ij++;
                        for ($j = 1; $j < $symbol_if_length; $j++) {
                            if ($ij >= $ruleset_length || $symbol_if[$j] != $ruleset[$ij]) {
                                $current_if_flag = '';
                                break;
                            }
                            $if_branch_ruleset .= $ruleset[$ij];
                            $ij++;
                        }
                    }
    
                    $ik = $ij;
                    if ($current_if_flag === 'IF_CONDITION') {
                        for ($ik; $ik < $ruleset_length; $ik++) {
                            if (ctype_space($ruleset[$ik])) {
                                continue;
                            }
                            // If the first char after the symbol_if(and any space) is not left bracket("("), then it's not if rule.
                            else if ($ruleset[$ik] === $left_bracket) {
                                $if_branch_ruleset .= $ruleset[$ik];
                                $left_bracket_count++;
                                $ik++;
                                break;
                            } else {
                                $if_branch_ruleset .= $ruleset[$ik];
                                $current_if_flag = '';
                                $ik++;
                                break;
                            }
                        }
    
                        if ($current_if_flag === 'IF_CONDITION') {
                            if ($ik >= $ruleset_length) throw RuleException::ruleset('Invalid end of if ruleset', $this->get_recurrence_current(), $this->config['auto_field']);
                            $i = $ik;
                            $char = $ruleset[$i];
                            $if_branch_ruleset .= $char;
                        }
                    }
                    
                    if ($current_if_flag === '' && $if_branch_index > 0) {
                        throw RuleException::ruleset('Invalid ELSEIF ruleset', $this->get_recurrence_current(), $this->config['auto_field']);
                    }
                }
                /**
                 * 3. ELSE: The last part of the "if" rule.
                 */ 
                else if ($if_branch_index > 0) {
                    if ($char === $left_curly_bracket) {
                        $current_if_flag = 'ELSE';
                        $left_curly_bracket_count = 1;
                        continue;
                    } else {
                        throw RuleException::ruleset('Invalid ELSE ruleset', $this->get_recurrence_current(), $this->config['auto_field']);
                    }
                }
            }
            /**
             * After parsing a IF or ELSEIF is completed: That means after the "}"
             * For example:
             *  - After if (xxx) {xxx}
             *  - After else if (xxx) {xxx}
             */
            else if ($current_if_flag === 'IF_END') {
                if (
                    ($symbol_else_length == 1 && $char == $symbol_else)
                    || ($symbol_else_length > 1 && $char == $symbol_else[0])
                ) {
                    $ij = $i;
                    if ($symbol_else_length > 1) {
                        $ij++;
                        for ($j = 1; $j < $symbol_else_length; $j++) {
                            if ($ij >= $ruleset_length || $symbol_else[$j] != $ruleset[$ij]) {
                                throw RuleException::ruleset('Invalid ELSE symbol', $this->get_recurrence_current(), $this->config['auto_field']);
                                break;
                            }
                            $if_branch_ruleset .= $ruleset[$ij];
                            $ij++;
                        }
                    }
    
                    // Start a next IF or ELSE only
                    $current_if_flag = '';
                    $i = $ij - 1;
                    $is_start = true;
                    continue;
                } else {
                    throw RuleException::ruleset('Extra ending of IF ruleset', $this->get_recurrence_current(), $this->config['auto_field']);
                }
            }

            /**
             * It's not a if ruleset, stop and return the result of parsing serial ruleset
             */
            if ($current_if_flag === '') {
                $rules = $this->parse_serial_ruleset($ruleset);
                break;
            }
            /**
             * The condition ruleset between the parentheses ()
             * For example:
             * - The xxx of "if ( xxx ) { ... }"
             * - The xxx of "else if ( xxx ) { ... }"
             */
            else if ($current_if_flag === 'IF_CONDITION') {
                if ($char === $left_bracket) $left_bracket_count++;
                else if ($char === $right_bracket) $left_bracket_count--;

                if ($left_bracket_count > 0) {
                    $if_condition_ruleset .= $char;
                } else {
                    if (empty($if_condition_ruleset)) throw RuleException::ruleset('Empty if condition ruleset', $this->get_recurrence_current(), $this->config['auto_field']);
                    $rules['result'][$if_branch_index]['result']['if_condition_rules'] = $this->parse_if_condition_ruleset($if_condition_ruleset);

                    // Ignore the spaces after the right boundary(")") of IF_CONDITION and before the left boundary("{") of the IF
                    $is_start = true;
                    $current_if_flag = 'IF_STATEMENT';
                    $if_condition_ruleset = '';
                }
            }
            /**
             * The if ruleset between the curly braces {}
             */
            else if (
                $current_if_flag === 'IF_STATEMENT'
                || $current_if_flag === 'ELSE'
            ) {
                if ($left_curly_bracket_count === 0) {
                    if ($char !== "{") {
                        /**
                         * Supprot the old version of "if" rule.
                         * Note that it would not work if you customed the "if" rule config.
                         * 
                         * @deprecated v2.6.0
                         */
                        if ($char === $this->config['symbol_rule_separator'][0]) {
                            $symbol_rule_separator = $this->config['symbol_rule_separator'];
                            $symbol_rule_separator_length = strlen($symbol_rule_separator);
                            if ($symbol_rule_separator_length > 1 && $char == $symbol_rule_separator[0]) {
                                $ij = $i + 1;
                                $is_symbol_rule_separator = true;
                                for ($j = 1; $j < $symbol_rule_separator_length; $j++) {
                                    if ($symbol_rule_separator[$j] != $ruleset[$ij]) {
                                        $is_symbol_rule_separator = false;
                                        break;
                                    }
                                    $ij++;
                                }
                                if ($is_symbol_rule_separator) {
                                    $i = $i + $symbol_rule_separator_length - 1;
                                    $char = $symbol_rule_separator;
                                }
                            }
                            if ($char === $symbol_rule_separator) {
                                $if_ruleset = substr($ruleset, $i+1);
                                $rules['result'][$if_branch_index]['result']['if_statement_rules'] = $this->parse_serial_ruleset($if_ruleset);
                                if (!empty($deprecated_if_not_operator)) $rules['result'][$if_branch_index]['result']['if_condition_rules']['operator'] = $deprecated_if_not_operator;
                                // echo "{$ruleset} - {$if_ruleset}\n"; print_r($rules['result'][$if_branch_index]['result']); die;
                                return $rules;
                            }
                        }

                        throw RuleException::ruleset('Missing left curly bracket', $this->get_recurrence_current(), $this->config['auto_field']);
                    } else {
                        $left_curly_bracket_count++;
                        continue;
                    }
                }

                if ($char === $left_curly_bracket) $left_curly_bracket_count++;
                else if ($char === $right_curly_bracket) $left_curly_bracket_count--;

                if ($left_curly_bracket_count > 0) {
                    $if_ruleset .= $char;
                } else {
                    $current_if_flag = 'IF_END';
                    $if_ruleset = trim($if_ruleset);
                    $rules['result'][$if_branch_index]['result']['if_statement_rules'] = $this->parse_if_ruleset($if_ruleset);
                    $rules['result'][$if_branch_index] = [
                        'text' => $if_branch_ruleset,
                        'result' => $rules['result'][$if_branch_index]['result']
                    ];
                    $if_branch_ruleset = '';

                    // Ignore the spaces after the right boundary("}") of IF and before the left boundary(elseif or else) of the ELSE_CONDITION
                    $is_start = true;
                    $if_ruleset = '';
                    $if_branch_index++;
                }
            }
        }

        /** @example `if (( expr ) { statement }` */
        if ($left_bracket_count > 0) {
            throw RuleException::ruleset('Unclosed left bracket "("', $this->get_recurrence_current(), $this->config['auto_field']);
        }
        /** @example `if ( expr ) {{ statement }` */
        else if ($left_curly_bracket_count > 0) {
            throw RuleException::ruleset('Unclosed left curly bracket "{"', $this->get_recurrence_current(), $this->config['auto_field']);
        }
        /** @example `if ( expr ) { statement } else if ( expr )` */
        else if (($current_if_flag === 'IF_STATEMENT' || $current_if_flag === 'ELSE') && empty($if_ruleset)) {
            throw RuleException::ruleset('Missing statement ruleset of if construct', $this->get_recurrence_current(), $this->config['auto_field']);
        }
        /** @example `` Empty ruleset */
        else if (empty($rules['result'])) {
            throw RuleException::ruleset('Empty', $this->get_recurrence_current(), $this->config['auto_field']);
        }
        /** @example `if ( expr ) { statement } else` */
        else if ($rules['type'] === 'IF' && $current_if_flag === '' && empty($if_ruleset)) {
            throw RuleException::ruleset('Missing statement ruleset of else construct', $this->get_recurrence_current(), $this->config['auto_field']);
        }

        return $rules;
    }

    /**
     * Parse the if condition.
     * 
     * The condition looks like the serial ruleset. But it's allowed to add Not Operator("!")
     * @example
     * ```
     * - length=[@id, 36]|!uuid
     * - !(length=[@id, 36]|uuid)
     * - !(length=[@id, 36]|uuid) || !(length=[@id, 26]|ulid)
     * ```
     * @param string $ruleset
     * @return array
     */
    protected function parse_if_condition_ruleset($ruleset)
    {
        $logical_operator_not = $this->config['symbol_logical_operator_not'];   // Default to '!'
        $logical_operator_or = $this->config['symbol_logical_operator_or'];     // Default to '||'
        $logical_operator_or_length = strlen($logical_operator_or);

        $rules = [
            'text' => $ruleset,
            'type' => 'IF_CONDITION',
            /**
             * Logical Operators(e.g. !)
             * @todo To support Comparison Operators(e.g. ==, !=)
             * */ 
            'operator' => '',               
            // 'compared_value' => NULL,       /** @todo The compared value of the comparison operator. */
            'result' => [],
        ];

        $left_bracket = '(';
        $right_bracket = ')';

        $has_left_bracket = false;              // If a or branch doesn't have a prefix left bracket, that indicates we or branch doesn't have a sub-or branch 
        $left_bracket_count = 0;                // If matching a left bracket, then +1, if matching a right bracket, then -1.
        $or_branch_ruleset = '';                // A if condition may have `||` operators, the part divided by it is called "or branch ruleset".
        $or_branch_ruleset_index = 0;           // The index of "or branch ruleset".
        $can_ingore_space = true;               // If true, ignore the next spaces.
        $is_start_of_or_branch = true;          // Indicates if it's the start of "or branch ruleset".
        $current_logical_operator_not = '';     // The logical operator not(!) of the "or branch ruleset".

        $ruleset_length = strlen($ruleset);
        for ($i = 0; $i < $ruleset_length; $i++) {
            $char = $ruleset[$i];
            if ($can_ingore_space == true) {
                if (ctype_space($char)) {
                    continue;
                }
            }

            if ($is_start_of_or_branch == true) {
                if ($char == $logical_operator_not) {
                    $current_logical_operator_not .= $char;
                    /**
                     * Only one logical operator not(`!`) is supported currently.
                     * We may support multiple operators(`!!`) in the future.
                     */
                    continue;
                }
                /**
                 * Have left bracket at the start of the ruleset, then
                 * 1. If have logical operator not before the left bracket, the operator is for a ruleset, not only a single method.
                 * 2. If DON't have logical operator not before the left bracket, it's not necessary if no sub or branch ruleset.
                 */
                else if ($char == $left_bracket) {
                    /**
                     * Only one logical operator not(`!`) is supported currently.
                     * We may support multiple operators(`!!`) in the future.
                     */
                    if (strlen($current_logical_operator_not) > 1) {
                        throw RuleException::ruleset("Multiple operator not({$current_logical_operator_not})", $this->get_recurrence_current(), $this->config['auto_field']);
                    }
                    
                    $is_start_of_or_branch = false;
                    $has_left_bracket = true;
                    $left_bracket_count++;
                }
                /**
                 * No left bracket at the start of the ruleset, then
                 * 1. If have logical operator not, the operator is for one single method only, not for a ruleset.
                 */
                else {
                    $or_branch_ruleset .= $current_logical_operator_not;
                    $or_branch_ruleset .= $char;
                    $is_start_of_or_branch = false;
                    $can_ingore_space = false;
                }

                continue;
            }

            /**
             * `\` is an escape character, and any character after it is part of the regular expression.
             * For example: `\/`, `\|`
             */
            if ($char === '\\') {
                $i++;
                $or_branch_ruleset .= $ruleset[$i];
                continue;
            }

            if ($char == $left_bracket) {
                $left_bracket_count++;
            } else if ($char == $right_bracket) {
                $left_bracket_count--;
                if ($left_bracket_count == 0) {
                    $can_ingore_space = true;
                    // Ignore the last right bracket
                    if ($has_left_bracket == true) continue;
                }
            }
            /**
             * If left_bracket_count >= 0, that indicates the left bracket is not closed, we should find the corresponding right bracket first.
             */
            else if ($left_bracket_count == 0) {
                if (
                    ($logical_operator_or_length == 1 && $char == $logical_operator_or)
                    || ($logical_operator_or_length > 1 && $char == $logical_operator_or[0])
                ) {
                    $is_logical_operator_or = true;
                    $ij = $i;
                    if ($logical_operator_or_length > 1) {
                        $ij++;
                        for ($j = 1; $j < $logical_operator_or_length; $j++) {
                            if ($logical_operator_or[$j] != $ruleset[$ij]) {
                                $is_logical_operator_or = false;
                                break;
                            }
                            $ij++;
                        }
                    }

                    if ($is_logical_operator_or == true) {
                        /**
                         * Parse the current or branch ruleset
                         * If the current or branch ruleset is enclosed in parentheses, that means it may have sub or branches.
                         */
                        if ($has_left_bracket == true) {
                            $rules['result'][$or_branch_ruleset_index] = $this->parse_if_condition_ruleset($or_branch_ruleset);
                            $rules['result'][$or_branch_ruleset_index]['operator'] = $current_logical_operator_not;
                        } else {
                            $rules['result'][$or_branch_ruleset_index] = $this->parse_serial_ruleset($or_branch_ruleset, true);
                        }

                        /**
                         * Start a next or branch
                         */
                        $has_left_bracket = false;
                        $left_bracket_count = 0;
                        $or_branch_ruleset = '';
                        $or_branch_ruleset_index++;
                        $can_ingore_space = true;
                        $is_start_of_or_branch = true;
                        $current_logical_operator_not = '';
                        $i = $ij;

                        continue;
                    }
                } else if ($has_left_bracket == true) {
                    $has_left_bracket = false;
                    $or_branch_ruleset = $current_logical_operator_not . $or_branch_ruleset;
                    // We don't treat it as an error because it may be a serial ruleset. e.g. `if(!(>(@id,10))|int){ xxx }`
                    // throw RuleException::ruleset('Invalid logical operator OR', $this->get_recurrence_current(), $this->config['auto_field']);
                }
            }

            $or_branch_ruleset .= $char;
        }

        if ($left_bracket_count > 0) {
            throw RuleException::ruleset('Unclosed left bracket "("', $this->get_recurrence_current(), $this->config['auto_field']);
        }

        if ($has_left_bracket == true) {
            $rules['result'][$or_branch_ruleset_index] = $this->parse_if_condition_ruleset($or_branch_ruleset);
            $rules['result'][$or_branch_ruleset_index]['operator'] = $current_logical_operator_not;
        } else {
            $rules['result'][$or_branch_ruleset_index] = $this->parse_serial_ruleset($or_branch_ruleset, true);
        }

        return $rules;
    }

    /**
     * Split a serial ruleset into multiple rules(methods or regular expression) by using the separator |
     * 
     * NOTE: 
     * - The regular expression may cantain the character(|) which is the same as rule separator(|)
     * - Multiple regular expressions are allowed in one serial rule
     * @example `required|>[10]`
     * @param string $ruleset
     * @param bool $is_if_condition
     * @return array
     */
    protected function parse_serial_ruleset($ruleset, $is_if_condition = false)
    {
        $symbol_rule_separator = $this->config['symbol_rule_separator'];
        $symbol_rule_separator_length = strlen($symbol_rule_separator);
        $logical_operator_not = $this->config['symbol_logical_operator_not'];   // Default to '!'

        $rules = [];
        $current_rule = '';
        $operator = '';
        $is_rule_start = true;
        $is_next_method_flag = 0;
        $is_reg_flag = 0;

        $ruleset_length = strlen($ruleset);
        for ($i = 0; $i < $ruleset_length; $i++) {
            $char = $ruleset[$i];

            if ($is_rule_start == true) {
                if (ctype_space($char)) {
                    continue;
                } else if ($char == $logical_operator_not) {
                    /**
                     * Only one logical operator not(`!`) is supported currently.
                     * We may support multiple operators(`!!`) in the future.
                     */
                    if ($operator == $logical_operator_not) {
                        $is_rule_start = false;
                        $current_rule .= $char;
                        continue;
                    }

                    $operator .= $char;
                    continue;
                } else {
                    $is_rule_start = false;
                }
            }

            /**
             * `\` is an escape character, and any character after it is part of the regular expression.
             * For example: `\/`, `\|`
             */
            if ($char === '\\') {
                $current_rule .= $char;
                $current_rule .= $ruleset[$i + 1];
                $i++;
                continue;
            }

            // Support custom configuration symbol_rule_separator for multiple characters
            if ($symbol_rule_separator_length > 1 && $char == $symbol_rule_separator[0]) {
                $ij = $i + 1;
                $is_symbol_rule_separator = true;
                for ($j = 1; $j < $symbol_rule_separator_length; $j++) {
                    if ($symbol_rule_separator[$j] != $ruleset[$ij]) {
                        $is_symbol_rule_separator = false;
                        break;
                    }
                    $ij++;
                }
                if ($is_symbol_rule_separator) {
                    $i = $i + $symbol_rule_separator_length - 1;
                    $char = $symbol_rule_separator;
                }
            }

            /**
             * The first match of a regular expression starting with `/`, indicates that the following characters are part of the regular expression. This is the stage 1 of regular expression.
             * Until the next `/` is matched, indicating that the regular expression is about to end. This is the stage 2 of regular expression.
             * After stage 2, the matching characters are treated as pattern modifiers of the regular expression. Until `|` is matched, indicating that the regular expression has completely ended
             */
            if ($char === '/') {
                if ($is_reg_flag == 0) {
                    // The first character is not `/`, indicating that it is not a regular expression
                    if ($current_rule == '') {
                        $is_reg_flag = 1;
                    }
                } else if ($is_reg_flag == 1) {
                    $is_reg_flag = 2;
                }
            }
            /**
             * Generally, non-regular expression methods do not contain `|`, so matching it indicates that the next method is comming.
             * The regular expression method may contain `|`, so it must be matched after the stage 2 of regular expression to indicate that the next method is comming.
             */
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
                $current_rule = trim($current_rule);
                if ($current_rule === '') throw RuleException::ruleset('Contiguous separator', $this->get_recurrence_current(), $this->config['auto_field']);
                if (!empty($current_rule)) $rules[] = [
                    'rule' => $current_rule,
                    'operator' => $operator
                ];
                $current_rule = '';
                $operator = '';
                $is_rule_start = true;
                $is_reg_flag = 0;
            }
        }

        $current_rule = trim($current_rule);
        if ($current_rule === '') {
            if (empty($rules)) {
                throw RuleException::ruleset('Empty', $this->get_recurrence_current(), $this->config['auto_field']);
            } else {
                throw RuleException::ruleset('Endding separator', $this->get_recurrence_current(), $this->config['auto_field']);
            }
        }
        if (!empty($current_rule)) $rules[] = [
            'rule' => $current_rule,
            'operator' => $operator
        ];
        return [
            'text' => $ruleset,
            'type' => 'SERIAL',
            'is_if_condition' => $is_if_condition,
            'result' => $rules,
        ];
    }

    /**
     * Parse method and its parameters
     *
     * @param string $rule One separation of ruleset
     * @param string $operator Logical operator of the separation. e.g. `!`
     * @param ?array $data The parent data of the field which is related to the rule
     * @param ?string $field The field which is related to the rule
     * @return array Method detail
     */
    protected function parse_method($rule, $operator, $data, $field)
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

        list($by_symbol, $symbol, $method_data, $operator, $class) = $this->match_method_and_symbol($method, $operator);
        $is_variable_length_argument = false;
        if (is_array($method_data)) {
            $method = $method_data['method'];
            $is_variable_length_argument = !empty($method_data['is_variable_length_argument']);
        } else {
            $method = $method_data;
        }

        foreach ($params as &$param) {
            if (is_array($param)) continue;

            if (strpos($param, '@') !== false) {
                if (
                    $data === null
                    && $field === null
                ) {
                    /**
                     * When we parse the rule to rule entity, we don't need to parse the @ parameters to the real data.
                     * Because the real data is changing when we start to validate it.
                     */
                    continue;
                }

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

        /**
         * Check whether the second parameter of the current rule is variable length argument or not
         * If true, all the parameters are treated as the second parameter.
         */
        if ($is_variable_length_argument) {
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
            'operator' => $operator,
            'is_variable_length_argument' => $is_variable_length_argument,
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

        if (is_array($method_rule) && $method_rule['method'] == 'check_id') {
            $a = 1;
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
            if ($method_rule['is_variable_length_argument']) {
                $value_max_index = count($value) - 1;
                $index = 0;
                $serialized_value = '';
                foreach ($value as $k => $v) {
                    if (is_array($v)) {
                        $v = $k;
                    } else if (!is_string($v)) {
                        $v = $this->var_export($v, true);
                    }
                    $serialized_value .= "$v";
                    if ($index != $value_max_index) $serialized_value .= ",";
                    $index++;
                }
                $value = $serialized_value;
            } else {
                return $error_template;
            }
        }

        $p = $value;
        if (!is_string($value)) $p = $this->var_export($value, true);
        $error_template = str_replace('@p' . $key, $p, $error_template);
        $error_template = str_replace('@t' . $key, $this->get_parameter_type($value), $error_template);
        return $error_template;
    }

    /**
     * Execute validation with the field and its ruleset.
     * 
     * A ruleset may contains:
     * 1. Required(*) rule
     * 2. Optional(O) rule
     * 3. Optional Unset(O!) rule
     * 4. When rule
     * 5. Regular Expression
     * 6. Method
     *
     * @param array $data The parent data of the field which is related to the rule
     * @param string $field The field which is related to the rule
     * @param string $ruleset The ruleset of the field
     * @param string $field_path Field path, suce as fruit.apple
     * @param bool $is_parallel_rule Flag of or rule
     * @return bool The result of validation
     */
    protected function execute_ruleset($data, $field, $ruleset, $field_path = '', $is_parallel_rule = false)
    {
        $this->set_current_field_path($field_path)
            ->set_current_field_ruleset($ruleset);

        $ruleset = $this->parse_ruleset($ruleset);

        if (empty($ruleset) || empty($ruleset['rules']['result'])) {
            return true;
        }

        return $this->execute_if_ruleset($data, $field, $ruleset, $field_path, $is_parallel_rule);
    }

    /**
     * A ruleset may contains if construct(condition ruleset and statement ruleset).
     * 
     * 1. If not contain if construct, treat it as serial ruleset.
     * 2. If contains if construct, 
     *    - If condition ruleset evaluates to true, we will execute statement ruleset.
     *    - If condition ruleset evaluates to false, we will ignore statement ruleset.
     *    - If all condition rulesets evaluates to false, we will ignore all statement ruleset and treat the result as true.
     *
     * @see static::parse_if_ruleset()
     * @param array $data The parent data of the field which is related to the rule
     * @param string $field The field which is related to the rule
     * @param array $ruleset The ruleset of the field that has been parsed
     * @param string $field_path Field path, suce as fruit.apple
     * @param bool $is_parallel_rule Flag of or rule
     * @return bool The result of validation
     */
    protected function execute_if_ruleset($data, $field, $ruleset, $field_path = '', $is_parallel_rule = false)
    {
        $result = true;
        if ($ruleset['rules']['type'] == 'IF') {
            foreach ($ruleset['rules']['result'] as $if_ruleset) {
                if (
                    empty($if_ruleset['result']['if_condition_rules'])
                    || $this->execute_if_condition_ruleset($data, $field, [
                        'error_templates' => $ruleset['error_templates'],
                        'rules' => $if_ruleset['result']['if_condition_rules'],
                    ], $field_path, false, true)
                ) {
                    $next_if_ruleset = [
                        'error_templates' => $ruleset['error_templates'],
                        'rules' => $if_ruleset['result']['if_statement_rules'],
                    ];
                    $result = $this->execute_if_ruleset($data, $field, $next_if_ruleset, $field_path);

                    // One of the condition is met, we just execute its statement ruleset and ignore the others.
                    break;
                }
            }
        } else {
            $result = $this->execute_serial_ruleset($data, $field, $ruleset, $field_path, $is_parallel_rule);
        }

        return $result;
    }

    /**
     * A condition may contains multiple "or branch ruleset" and logical operator not(!)
     * 
     * - If one of the "or branch ruleset" evaluates to true, the condition result is true
     * - If all the "or branch ruleset" evaluates to false, the condition result is false
     * - If contains logical operator not(!), we will reverse the result.
     *
     * @see static::parse_if_condition_ruleset()
     * @param array $data The parent data of the field which is related to the rule
     * @param string $field The field which is related to the rule
     * @param array $ruleset The ruleset of the field that has been parsed
     * @param string $field_path Field path, suce as fruit.apple
     * @return bool
     */
    protected function execute_if_condition_ruleset($data, $field, $ruleset, $field_path = '')
    {
        $result = true;
        foreach ($ruleset['rules']['result'] as $if_condition_ruleset) {
            if ($if_condition_ruleset['type'] == 'IF_CONDITION') {
                $result = $this->execute_if_condition_ruleset($data, $field, [
                    'error_templates' => $ruleset['error_templates'],
                    'rules' => $if_condition_ruleset,
                ], $field_path);
            } else {
                $result = $this->execute_serial_ruleset($data, $field, [
                    'error_templates' => $ruleset['error_templates'],
                    'rules' => $if_condition_ruleset,
                ], $field_path, false, true);
            }

            /**
             * One of the or branch ruleset result is true, indicates the if condition result is true.
             * We don't have to validate other or branch.
             */
            if ($result === true) break;
        }

        return $this->is_met_condition($ruleset['rules'], $result);
    }

    /**
     * Check the condition result is met the expected result.
     *
     * @see RulesetEntity::is_met_condition()
     * @param array $ruleset_obj
     * @param mixed $executed_result
     * @return bool
     */
    protected function is_met_condition($ruleset_obj, $executed_result)
    {
        if (empty($ruleset_obj['operator'])) return $executed_result;

        return ($ruleset_obj['operator'] == '' && $executed_result === true)
            || ($ruleset_obj['operator'] == $this->config['symbol_logical_operator_not'] && $executed_result !== true);
    }

    /**
     * Execute validation with the field and its ruleset.
     * 
     * A ruleset may contains:
     * 1. Required(*) rule
     * 2. Optional(O) rule
     * 3. Optional Unset(O!) rule
     * 4. When rule
     * 5. Regular Expression
     * 6. Method
     *
     * @see static::parse_serial_ruleset()
     * @param array $data The parent data of the field which is related to the rule
     * @param string $field The field which is related to the rule
     * @param array $ruleset The ruleset of the field that has been parsed
     * @param string $field_path Field path, suce as fruit.apple
     * @param bool $is_parallel_rule Flag of or rule
     * @param bool $is_condition_rule
     * @return bool The result of validation
     */
    protected function execute_serial_ruleset($data, $field, $ruleset, $field_path = '', $is_parallel_rule = false, $is_condition_rule = false)
    {
        $ruleset_error_templates = $ruleset['error_templates'];

        foreach ($ruleset['rules']['result'] as $rule_obj) {
            $rule = $rule_obj['rule'];
            $operator = $rule_obj['operator'];

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

                $method_rule = $this->parse_method($when_rule, '', $data, $field);
                $params = $method_rule['params'];
                $when_result = $this->execute_method($method_rule);

                if (is_array($when_result) && !empty($when_result['error_type']) && $when_result['error_type'] == 'undefined_method') return false;

                if (
                    ($when_type === 'when' && $when_result === true)
                    || ($when_type === 'when_not' && $when_result !== true)
                ) {
                    $is_met_when_rule = true;
                } else {
                    $is_met_when_rule = false;
                }

                $rule = $target_rule;
            }

            /**
             * - Required(*) rule
             * - Required When rule
             */
            if ($rule == $this->config['symbol_required'] || $rule == $this->config_default['symbol_required']) {
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
                $method_rule = $this->parse_method($rule, $operator, $data, $field);
                $params = $method_rule['params'];
                $result = $this->execute_method($method_rule);

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
             * If it's the condition rules of a "if" ruleset,
             * We just try to match the condition without setting any error result.
             */
            if ($is_condition_rule) {
                if ($result !== true) {
                    return false;
                }
            }
            /**
             * Inject variables into error message template
             * For example:
             * - @this
             * - @p1
             */
            else if ($result !== true) {
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
     * Execute method
     *
     * @param array $method_rule Method detail
     * @return bool|string|array
     */
    protected function execute_method($method_rule)
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

        return $this->is_met_condition($method_rule, $result);
    }

    /**
     * Init the current info of the recurrence validation
     *
     * @return void
     */
    public function init_recurrence_current()
    {
        $this->recurrence_current = [
            'field_path' => '',
            'field_ruleset' => '',
            'rule' => '',
            'method' => [],
        ];
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
     * Return a parsable string representation of a variable
     * 
     * The built-in function `var_export` does not work for decimal in PHP 5.6.
     * So I create the function for the same functionality.
     *
     * @see https://www.php.net/manual/en/function.var-export.php
     * @param mixed $value
     * @param bool $return
     * @return void
     */
    public function var_export($value, $return = true)
    {
        if ($value === null) {
            $value = 'NULL';
        } else if (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        }

        return $value;
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

    /**
     * Parse the rulesets into RulesetEntity(s)
     *
     * @param string|array $rules
     * @param ?string $rule_key
     * @param bool $skip_exception If true, then return ruleset entity instead of throw an exception when exception accurs.
     * @return RulesetEntity
     * @throws RuleException
     */
    protected function parse_to_ruleset_entity($rules, $rule_key = null, $skip_exception = false)
    {
        $rules_system_symbol = $this->get_ruleset_system_symbol($rules);
        // If The root rules has rule_system_symbol
        // Or The root rules is String, means root data is not an array
        // Set root data as an array to help validate the data
        if (!empty($rules_system_symbol) || is_string($rules)) {
            $auto_field = $this->config['auto_field'];
            $rules = [$auto_field => $rules];
        }
        
        try {
            /**
             * Most of the time the $rules is an associate array, so the ruleset type is RULESET_TYPE_ASSOC_ARRAY
             * But sometimes it's for index array, we will change the ruleset type below.
             */
            $root_ruleset_entity = new RulesetEntity('root', $rules, null);
            if (!empty($auto_field)) $root_ruleset_entity->set_real_root_name($auto_field);
            $root_ruleset_entity->set_ruleset_type(RulesetEntity::RULESET_TYPE_ASSOC_ARRAY);
            $symbol_index_array_without_left_dot = ltrim($this->config['symbol_index_array'], '.');
            $root_ruleset_entity->set_symbol_index_array($symbol_index_array_without_left_dot);

            if ($rule_key !== null) $this->ruleset_entities[$rule_key] = $root_ruleset_entity;

            $this->parse_ruleset_entity($root_ruleset_entity);
        } catch (Throwable $t) {
            if (!empty($root_ruleset_entity)) {
                $root_ruleset_entity->set_exception($t);
                if (!$skip_exception) throw $t;
            } else {
                throw $t;
            }

        }
        // For the PHP version < 7
        catch (Exception $t) {
            if (!empty($root_ruleset_entity)) {
                $root_ruleset_entity->set_exception($t);
                if (!$skip_exception) throw $t;
            } else {
                throw $t;
            }
        }

        return $root_ruleset_entity;
    }

    /**
     * Parse the rulesets of the parent RulesetEntity
     *
     * @param ?RulesetEntity $parent_ruleset_entity
     * @return RulesetEntity
     */
    protected function parse_ruleset_entity(&$parent_ruleset_entity)
    {
        $this->parse_self_ruleset_entity($parent_ruleset_entity);
        
        $parent_ruleset = $parent_ruleset_entity->get_value();
        $field_path = $parent_ruleset_entity->get_path();

        $ruleset_system_symbol = $this->get_ruleset_system_symbol($parent_ruleset);
        if (!empty($ruleset_system_symbol)) {
            $parent_ruleset_entity->set_system_symbol($ruleset_system_symbol);

            // Allow array or object to be optional
            if ($has_symbol_array_optional = $this->has_system_symbol($ruleset_system_symbol, 'symbol_array_optional')) {
                $array_node_ruleset = [];
                if ($has_symbol_array_optional == 1) {
                    $array_node_ruleset[] = $this->config_default['symbol_optional'];
                } else {
                    $array_node_ruleset[] = $this->config['symbol_optional'];
                }
                $array_node_ruleset_tmp = implode($this->config['symbol_rule_separator'], $array_node_ruleset);
                $parent_ruleset_entity->set_ruleset($array_node_ruleset_tmp);
                $this->parse_rule_entity($parent_ruleset_entity);
            }

            // Validate parallel rules.
            // If one of parallel rules is valid, then the field is valid.
            if ($this->has_system_symbol($ruleset_system_symbol, 'symbol_parallel_rule')) {
                $this->parse_parallel_ruleset_entity($parent_ruleset_entity);
            }
            // Validate index array
            else if ($this->has_system_symbol($ruleset_system_symbol, 'symbol_index_array', true)) {
                $parent_ruleset_entity->set_ruleset_type(RulesetEntity::RULESET_TYPE_INDEX_ARRAY);
                $this->parse_index_array_ruleset_entity($parent_ruleset_entity);
            }
            // Validate association array
            else if ($this->is_association_array_rule($parent_ruleset[$ruleset_system_symbol])) {
                $parent_ruleset_entity->set_ruleset_type(RulesetEntity::RULESET_TYPE_ASSOC_ARRAY);
                $this->parse_ruleset_entity($parent_ruleset_entity);
            }
            else {
                if ($has_symbol_array_optional >= 1) {
                    $array_node_ruleset[] = $parent_ruleset[$ruleset_system_symbol];
                    $array_node_ruleset_tmp = implode($this->config['symbol_rule_separator'], $array_node_ruleset);
                    $parent_ruleset_entity
                        ->reset_rule_entities()
                        ->set_ruleset($array_node_ruleset_tmp);
                }
                $parent_ruleset_entity->set_ruleset_type(RulesetEntity::RULESET_TYPE_LEAF);
                $this->parse_rule_entity($parent_ruleset_entity);
            }

            return $parent_ruleset_entity;
        }

        foreach ($parent_ruleset as $field => $ruleset) {
            $current_field_path = '';
            if ($field_path === null) $current_field_path = $field;
            else $current_field_path = $field_path . $this->config['symbol_field_name_separator'] . $field;
            $this->set_current_field_path($current_field_path);

            $ruleset_entity = new RulesetEntity($field, $ruleset, $current_field_path);
            $ruleset_entity->set_parent($parent_ruleset_entity);

            $system_symbol = '';
            
            // Allow array or object to be optional
            if ($has_symbol_array_optional = $this->has_system_symbol($field, 'symbol_array_optional')) {
                $field = $this->delete_system_symbol($field, 'symbol_array_optional');
                $current_field_path = $this->delete_system_symbol($current_field_path, 'symbol_array_optional');
                $this->set_current_field_path($current_field_path);

                if ($has_symbol_array_optional == 1) {
                    $system_symbol = $this->config_default['symbol_array_optional'];
                } else {
                    $system_symbol = $this->config['symbol_array_optional'];
                }
            }

            // Validate parallel rules.
            // If one of parallel rules is valid, then the field is valid.
            if ($has_symbol_parallel_rule = $this->has_system_symbol($field, 'symbol_parallel_rule')) {
                $field = $this->delete_system_symbol($field, 'symbol_parallel_rule');
                $current_field_path = $this->delete_system_symbol($current_field_path, 'symbol_parallel_rule');
                $this->set_current_field_path($current_field_path);

                if ($has_symbol_parallel_rule == 1) {
                    $system_symbol .= $this->config_default['symbol_parallel_rule'];
                } else {
                    $system_symbol .= $this->config['symbol_parallel_rule'];
                }
            }
            // Validate index array
            else if ($has_symbol_index_array = $this->has_system_symbol($field, 'symbol_index_array')) {
                $field = $this->delete_system_symbol($field, 'symbol_index_array');
                $current_field_path = $this->delete_system_symbol($current_field_path, 'symbol_index_array');
                $this->set_current_field_path($current_field_path);
                

                if (empty($system_symbol)) {
                    $system_symbol = ltrim($this->config['symbol_index_array'], '.');
                } else {
                    $system_symbol .= $this->config['symbol_index_array'];
                }
            }

            if (!empty($system_symbol)) {
                $current_ruleset = [
                    $system_symbol => $ruleset
                ];

                $ruleset_entity
                    ->set_name($field)
                    ->set_value($current_ruleset)
                    ->set_path($current_field_path);

                $this->parse_ruleset_entity($ruleset_entity);
            } else {
                // Validate association array
                if ($this->is_association_array_rule($ruleset)) {
                    $ruleset_entity->set_ruleset_type(RulesetEntity::RULESET_TYPE_ASSOC_ARRAY);
                    $this->parse_ruleset_entity($ruleset_entity);
                } else {
                    $ruleset_entity
                        ->set_ruleset_type(RulesetEntity::RULESET_TYPE_LEAF)
                        ->set_ruleset($ruleset);
                    $this->parse_rule_entity($ruleset_entity);
                }
            }

            $parent_ruleset_entity->add_children($field, $ruleset_entity);
        }

        return $parent_ruleset_entity;
    }

    /**
     * Parse the rulesets of the parent RulesetEntity
     *
     * @param ?RulesetEntity $parent_ruleset_entity
     */
    protected function parse_self_ruleset_entity(&$parent_ruleset_entity)
    {
        $parent_ruleset = $parent_ruleset_entity->get_value();
        if (isset($parent_ruleset[$this->config['self_ruleset_key']])) {
            $parent_ruleset_entity
                ->reset_rule_entities()
                ->set_ruleset($parent_ruleset[$this->config['self_ruleset_key']]);
            $this->parse_rule_entity($parent_ruleset_entity);

            unset($parent_ruleset[$this->config['self_ruleset_key']]);
            $parent_ruleset_entity->set_value($parent_ruleset);
        }

        return true;
    }

    /**
     * Parse a parallel ruleset into RulesetEntity(s)
     *
     * @param RulesetEntity $ruleset_entity
     * @return void
     */
    protected function parse_parallel_ruleset_entity(&$ruleset_entity)
    {
        $ruleset = $ruleset_entity->get_value();
        $ruleset = $ruleset_entity->get_value_of_system_symbol();
        $ruleset_entity->set_ruleset_type(RulesetEntity::RULESET_TYPE_LEAF);
        foreach ($ruleset as $key => $parallel_ruleset) {
            $parallel_ruleset_entity = clone $ruleset_entity;
            $parallel_ruleset_entity
                ->set_ruleset_type(RulesetEntity::RULESET_TYPE_LEAF_PARALLEL)
                ->set_value($parallel_ruleset);

            $ruleset_entity->add_parallel_ruleset_entity($parallel_ruleset_entity);

            $this->parse_rule_entity($parallel_ruleset_entity);
        }
    }

    /**
     * Parse a index array ruleset into RulesetEntity(s)
     *
     * @param RulesetEntity $index_array_ruleset_entity
     * @return void
     */
    protected function parse_index_array_ruleset_entity(&$index_array_ruleset_entity)
    {
        $parent_path = $index_array_ruleset_entity->get_path();
        $ruleset = $index_array_ruleset_entity->get_value_of_system_symbol();

        $symbol_index_array_without_left_dot = ltrim($this->config['symbol_index_array'], '.');
        $this->config['symbol_field_name_separator'];
        $child_ruleset_entity = new RulesetEntity(
            $symbol_index_array_without_left_dot,
            $ruleset,
            $parent_path . $this->config['symbol_field_name_separator'] . $symbol_index_array_without_left_dot
        );
        $child_ruleset_entity->set_parent($index_array_ruleset_entity);
        $index_array_ruleset_entity->add_children($symbol_index_array_without_left_dot, $child_ruleset_entity);

        $rule_system_symbol = $this->get_ruleset_system_symbol($ruleset);
        if (!empty($rule_system_symbol)) {
            $this->parse_ruleset_entity($child_ruleset_entity);
        } else if ($this->is_association_array_rule($ruleset)) {
            $child_ruleset_entity->set_ruleset_type(RulesetEntity::RULESET_TYPE_ASSOC_ARRAY);
            $this->parse_ruleset_entity($child_ruleset_entity);
        } else {
            $child_ruleset_entity->set_ruleset_type(RulesetEntity::RULESET_TYPE_LEAF);
            $this->parse_rule_entity($child_ruleset_entity);
        }
    }

    /**
     * Parse a ruleset into RuleEntity(s)
     *
     * @param RulesetEntity $ruleset_entity
     * @return void
     */
    protected function parse_rule_entity(&$ruleset_entity)
    {
        $ruleset = $ruleset_entity->get_ruleset();
        $this->set_current_field_path($ruleset_entity->get_path())
            ->set_current_field_ruleset($ruleset);

        $ruleset = $this->parse_ruleset($ruleset_entity->get_ruleset());

        if (empty($ruleset) || empty($ruleset['rules']['result'])) {
            return true;
        }

        $ruleset_entity->set_error_templates($ruleset['error_templates']);

        $this->parse_if_rule_entity($ruleset_entity, $ruleset);
    }

    /**
     * Parse a ruleset into RuleEntity(s)
     *
     * @see static::parse_if_ruleset()
     * @param RulesetEntity $ruleset_entity
     * @param array $ruleset
     * @return void
     */
    protected function parse_if_rule_entity(&$ruleset_entity, $ruleset)
    {
        if ($ruleset['rules']['type'] == 'IF') {
            foreach ($ruleset['rules']['result'] as $if_ruleset) {
                $if_ruleset_entity = clone $ruleset_entity;
                $if_ruleset_entity->set_ruleset_type(RulesetEntity::RULESET_TYPE_LEAF_IF)
                    ->set_value($if_ruleset['result']['if_statement_rules']['text']);
                $ruleset_entity->add_if_ruleset_entity($if_ruleset_entity);
                $current_if_ruleset = [
                    'error_templates' => $ruleset['error_templates'],
                    'rules' => $if_ruleset['result']['if_statement_rules'],
                ];
                $this->parse_if_rule_entity($if_ruleset_entity, $current_if_ruleset);
                
                if (!empty($if_ruleset['result']['if_condition_rules'])) {
                    $this->parse_if_condition_rule_entity($if_ruleset_entity, [
                        'error_templates' => $ruleset['error_templates'],
                        'rules' => $if_ruleset['result']['if_condition_rules'],
                    ]);
                }
            }
        } else {
            $this->parse_serial_rule_entity($ruleset_entity, $ruleset);
        }
    }

    /**
     * Parse a if condition ruleset into RuleEntity(s)
     *
     * @see static::parse_if_condition_ruleset()
     * @param RulesetEntity $ruleset_entity
     * @param array $ruleset
     * @return void
     */
    protected function parse_if_condition_rule_entity(&$ruleset_entity, $ruleset)
    {
        if ($ruleset_entity->get_ruleset_type() === RulesetEntity::RULESET_TYPE_LEAF_IF) {
            $root_if_condition_ruleset_entity = clone $ruleset_entity;
            $root_if_condition_ruleset_entity->set_ruleset_type(RulesetEntity::RULESET_TYPE_LEAF_CONDITION)
                ->set_value($ruleset['rules']['text']);
            if (!empty($ruleset['rules']['operator'])) $root_if_condition_ruleset_entity->set_operator($ruleset['rules']['operator']);
            $ruleset_entity->add_condition_ruleset_entity($root_if_condition_ruleset_entity);
            $ruleset_entity = &$root_if_condition_ruleset_entity;
        }

        foreach ($ruleset['rules']['result'] as $if_condition_ruleset) {
            $condition_ruleset_entity = clone $ruleset_entity;
            $condition_ruleset_entity->set_ruleset($if_condition_ruleset['text']);
            if (!empty($if_condition_ruleset['operator'])) $root_if_condition_ruleset_entity->set_operator($if_condition_ruleset['operator']);
            $ruleset_entity->add_condition_ruleset_entity($condition_ruleset_entity);

            if ($if_condition_ruleset['type'] == 'IF_CONDITION') {
                $condition_ruleset_entity->set_ruleset_type(RulesetEntity::RULESET_TYPE_LEAF_CONDITION);
                $this->parse_if_condition_rule_entity($condition_ruleset_entity, [
                    'error_templates' => $ruleset['error_templates'],
                    'rules' => $if_condition_ruleset,
                ]);
            } else {
                $condition_ruleset_entity->set_ruleset_type(RulesetEntity::RULESET_TYPE_LEAF_CONDITION_SERIAL);
                $this->parse_serial_rule_entity($condition_ruleset_entity, [
                    'error_templates' => $ruleset['error_templates'],
                    'rules' => $if_condition_ruleset,
                ]);
            }
        }
    }

    /**
     * Parse a serial ruleset into RuleEntity(s)
     *
     * @see static::parse_serial_ruleset()
     * @param RulesetEntity $ruleset_entity
     * @param array $ruleset
     * @return void
     */
    protected function parse_serial_rule_entity(&$ruleset_entity, $ruleset)
    {
        $this->set_current_field_path($ruleset_entity->get_path())
            ->set_current_field_ruleset($ruleset);

        $ruleset_error_templates = $ruleset['error_templates'];
        $is_error_template_for_whole_ruleset = false;
        if (!empty($ruleset_error_templates) && !empty($ruleset_error_templates['whole_ruleset'])) {
            $is_error_template_for_whole_ruleset = true;
        }
        $ruleset_entity->set_error_templates($ruleset_error_templates);

        foreach ($ruleset['rules']['result'] as $rule_obj) {
            $rule = $rule_obj['rule'];
            $operator = $rule_obj['operator'];

            if (empty($rule)) {
                continue;
            }

            $this->set_current_rule($rule);

            $error_type = 'validation';
            $error_template = '';
            $method_rule = [];

            $has_when_rule = -1;
            $when_type = '';
            $when_rule_entity = null;
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
                    $when_type = RuleEntity::RULE_TYPE_WHEN;
                } else {
                    $has_when_rule = 0;
                    $when_type = RuleEntity::RULE_TYPE_WHEN_NOT;
                }

                $method_rule = $this->parse_method($when_rule, '', null, null);

                $rule = $target_rule;

                $when_rule_entity = new RuleEntity($when_type, $method_rule['method'], $when_rule, $method_rule['params'], $method_rule['symbol'], $method_rule['by_symbol']);
            }

            /**
             * - Required(*) rule
             * - Required When rule
             */
            if ($rule == $this->config['symbol_required'] || $rule == $this->config_default['symbol_required']) {
                /**
                 * Required(*) rule
                 */
                if ($has_when_rule === -1) {
                    $error_type = $this->config_default['symbol_required'];
                    $error_template = $this->match_error_template($ruleset_error_templates, 'symbol_required');
                }
                /**
                 * Required When rule
                 * If it's a 'Required When rule' or 'Required When Not rule' rule -> Means this field is conditionally required;
                 * - If the 'Required When rule' validation result is true and the field is not set or empty, then the field is invalid. Otherwise, continue to validate the subsequnse rule;
                 * - If the 'Required When Not rule' validation result is not true and the field is not set or empty, then the field is invalid. Otherwise, continue to validate the subsequnse rule;
                 */
                else if ($has_when_rule !== -1) {
                    $error_type = $this->config_default['symbol_required'] . ':' . $when_type;
                    $error_template = $this->match_error_template($ruleset_error_templates, 'symbol_required', $when_type);
                }

                $by_symbol = $rule == $this->config['symbol_required'];
                $rule_entity = new RuleEntity(RuleEntity::RULE_TYPE_METHOD, $this->config_default['symbol_required'], $rule, [$this->symbol_this], $this->config['symbol_required'], $by_symbol);
            }
            /**
             * - Optional(O) rule
             * - Optional When rule
             */
            else if ($rule == $this->config['symbol_optional'] || $rule == $this->config_default['symbol_optional']) {
                if ($has_when_rule !== -1) {
                    $error_type = $this->config_default['symbol_optional'] . ':' . $when_type;
                    $error_template = $this->match_error_template($ruleset_error_templates, 'symbol_optional', $when_type);
                }

                $by_symbol = $rule == $this->config['symbol_optional'];
                $rule_entity = new RuleEntity(RuleEntity::RULE_TYPE_METHOD, $this->config_default['symbol_optional'], $rule, [$this->symbol_this], $this->config['symbol_optional'], $by_symbol);
            }
            /**
             * - Optional Unset(O!) rule
             * - Optional Unset When rule
             */
            else if ($rule == $this->config['symbol_optional_unset'] || $rule == $this->config_default['symbol_optional_unset']) {
                /**
                 * Optional Unset(O!) rule
                 */
                if ($has_when_rule === -1) {
                    $error_type = $this->config_default['symbol_optional_unset'];
                    $error_template = $this->match_error_template($ruleset_error_templates, 'symbol_optional_unset');
                }
                /**
                 * Optional Unset When rule
                 * - If met the When condition, then the field can not be empty
                 * - If don't met the When condition, then the field is required
                 */
                else {
                    $error_type = $this->config_default['symbol_optional_unset'] . ':' . $when_type;
                    $error_template = $this->match_error_template($ruleset_error_templates, 'symbol_optional_unset', $when_type);
                }

                $by_symbol = $rule == $this->config['symbol_optional_unset'];
                $rule_entity = new RuleEntity(RuleEntity::RULE_TYPE_METHOD, $this->config_default['symbol_optional_unset'], $rule, [$this->symbol_this], $this->config['symbol_optional_unset'], $by_symbol);
            }
            // Regular expression
            else if (preg_match($this->config['reg_preg'], $rule, $matches)) {
                $preg = isset($matches[1]) ? $matches[1] : $matches[0];

                if (!preg_match($this->config['reg_preg_strict'], $preg, $matches)) {
                    $error_type = 'preg';
                    $error_template = $this->match_error_template([], 'preg_format');
                    $error_template = str_replace($this->symbol_preg, $preg, $error_template);
                } else {
                    $error_template = $this->match_error_template($ruleset_error_templates, 'preg', $when_type);
                    $error_template = str_replace($this->symbol_preg, $preg, $error_template);
                }

                $by_symbol = false;
                $rule_entity = new RuleEntity(RuleEntity::RULE_TYPE_PREG, $preg, $rule, [$this->symbol_this]);
            }
            // Method
            else {
                $method_rule = $this->parse_method($rule, $operator, null, null);

                $error_template = $this->match_error_template($ruleset_error_templates, $method_rule, $when_type);
                if (empty($method_rule['by_symbol'])) $error_template = str_replace($this->symbol_method, $method_rule['method'], $error_template);
                else $error_template = str_replace($this->symbol_method, $method_rule['symbol'], $error_template);
                $rule_entity = new RuleEntity(RuleEntity::RULE_TYPE_METHOD, $method_rule['method'], $rule, $method_rule['params'], $method_rule['symbol'], $method_rule['by_symbol']);
                $rule_entity->set_operator($method_rule['operator'])
                    ->set_is_variable_length_argument($method_rule['is_variable_length_argument']);
            }

            $rule_entity->set_error_type($error_type)
                    ->set_error_template($error_template)
                    ->set_is_error_template_for_whole_ruleset($is_error_template_for_whole_ruleset);
            if (!empty($when_rule_entity)) $rule_entity->add_when_rule_entity($when_rule_entity);
            $ruleset_entity->add_rule_entity($rule_entity);
        }
    }

    /**
     * Execute validation with all data and all rules
     *
     * @param array $data The data you want to validate
     * @param RulesetEntity $parent_ruleset_entity The ruleset entity
     * @return bool
     */
    protected function execute_entity($data, $parent_ruleset_entity)
    {
        $self_result = $this->execute_self_ruleset_entities($data, $parent_ruleset_entity);
        if ($self_result == 0) return false;
        else if ($self_result == 1 && empty($data)) return true;     // optional

        $current_index_array_key = null;
        $parent_ruleset_type = $parent_ruleset_entity->get_ruleset_type();
        if ($parent_ruleset_type == RulesetEntity::RULESET_TYPE_INDEX_ARRAY) {
            $current_index_array_key = $parent_ruleset_entity->get_root_index_array_key($parent_ruleset_entity->get_index_array_deep());
        }
        foreach ($parent_ruleset_entity->get_children() as $field => $ruleset_entity) {
            if ($current_index_array_key !== null) $field = $current_index_array_key;
            $current_field_path = $ruleset_entity->get_path();
            $this->set_current_field_path($current_field_path);

            $ruleset_type = $ruleset_entity->get_ruleset_type();
            switch ($ruleset_type) {
                case RulesetEntity::RULESET_TYPE_ASSOC_ARRAY:
                case RulesetEntity::RULESET_TYPE_INDEX_ARRAY:
                    if ($ruleset_type === RulesetEntity::RULESET_TYPE_ASSOC_ARRAY) {
                        $result = $this->execute_entity(isset($data[$field]) ? $data[$field] : null, $ruleset_entity, $current_field_path);
                    } else {
                        $result = $this->execute_index_array_ruleset_entities($data, $field, $ruleset_entity);
                    }
                    
                    break;
                case RulesetEntity::RULESET_TYPE_LEAF:
                    if ($ruleset_entity->has_if_ruleset_entities()) {
                        $result = $this->execute_if_ruleset_entities($data, $field, $ruleset_entity);
                    } else if ($ruleset_entity->has_parallel_ruleset_entities()) {
                        $result = $this->execute_parallel_ruleset_entities($data, $field, $ruleset_entity);
                    } else {
                        $result = $this->execute_ruleset_entity($data, $field, $ruleset_entity, $current_field_path);
                    }
                    $this->set_result($current_field_path, $result);
                    break;
            }

            // If the config validation_global is set to false, stop validating when one rule was invalid.
            if (!$result && !$this->validation_global) return false;
        }

        return true;
    }

    /**
     * Execute validation of self rules of a array node.
     * 
     * Result:
     *  - -1: No self ruleset
     *  -  0: Validate self ruleset but failed.
     *  -  1: Validate self ruleset and success.
     *
     * @param array $data The parent data of the field which is related to the rule
     * @param RulesetEntity $ruleset_entity The ruleset of the field
     * @return int The result of validation
     */
    protected function execute_self_ruleset_entities($data, $ruleset_entity)
    {
        if ($ruleset_entity->has_if_ruleset_entities()) {
            $field_path = $ruleset_entity->get_path();
            $field = $this->get_path_final_field($field_path);
            $result = $this->execute_if_ruleset_entities([
                $field => $data
            ], $field, $ruleset_entity);
            $this->set_result($field_path, $result);
            return $result ? 1 : 0;
        } else if ($ruleset_entity->has_parallel_ruleset_entities()) {
            $field_path = $ruleset_entity->get_path();
            $field = $this->get_path_final_field($field_path);
            $result = $this->execute_parallel_ruleset_entities([
                $field => $data
            ], $field, $ruleset_entity);
            $this->set_result($field_path, $result);
            return $result ? 1 : 0;
        } else if ($ruleset_entity->has_rule_entities()) {
            $field_path = $ruleset_entity->get_path();
            $field = $this->get_path_final_field($field_path);
            $result = $this->execute_ruleset_entity([
                $field => $data
            ], $field, $ruleset_entity, $field_path);
            $this->set_result($field_path, $result);
            return $result ? 1 : 0;
        } else {
            return -1;
        }
    }

    /**
     * Get the final field from the field path.
     * For example, The "C" is the final field of the path "A.B.C"
     * 
     * @param ?string $field_path
     * @return void
     */
    protected function get_path_final_field(&$field_path)
    {
        if ($field_path === null) $field_path = $this->config['auto_field'];
        $field_path_array = explode($this->config['symbol_field_name_separator'], $field_path);
        return $field_path_array[count($field_path_array)-1];
    }

    /**
     * Execute validation of index array rules.
     * There has two ways to add index array rules:
     * 1. Add symbol_index_array in the end of the field. Such as $rule = [ "name.*" => [ "*|string" ] ];
     * 2. Add symbol_index_array as the only one child of the field. Such as $rule = [ "name" => [ "*" => [ "*|string" ] ];
     *
     * @param array $data The parent data of the field which is related to the rule
     * @param string $field The field which is related to the rule
     * @param RulesetEntity $ruleset_entity The ruleset of the field
     * @return bool The result of validation
     */
    protected function execute_index_array_ruleset_entities($data, $field, $ruleset_entity)
    {
        $current_data = isset($data[$field]) ? $data[$field] : null;
        $self_result = $this->execute_self_ruleset_entities($current_data, $ruleset_entity);
        if ($self_result == 0) return false;
        else if ($self_result == 1 && empty($current_data)) return true;     // optional

        $field_path = $ruleset_entity->get_path();
        if (!isset($current_data) || !$this->is_index_array($current_data)) {
            $error_template = $this->get_error_template('index_array');
            $error_msg = str_replace($this->symbol_this, $field_path, $error_template);
            $message = [
                "error_type" => 'validation',
                "message" => $error_msg,
            ];
            $this->set_error($field_path, $message);
            return false;
        } else {
            $ruleset = $ruleset_entity->get_ruleset();
            $field_path = $ruleset_entity->get_path();
            $this->set_current_field_path($field_path)
                ->set_current_field_ruleset($ruleset);

            $index_array_deep = $ruleset_entity->generate_root_index_array_deep();
            $ruleset_entity->set_index_array_deep($index_array_deep);

            $is_all_valid = true;
            foreach ($current_data as $key => $value) {
                $ruleset_entity->set_root_index_array_key($index_array_deep, $key);
                $result = $this->execute_entity($current_data, $ruleset_entity);

                $is_all_valid = $is_all_valid && $result;

                // If the config validation_global is set to false, stop validating when one rule was invalid.
                if (!$result && !$this->validation_global) return false;
            }
            $ruleset_entity->unset_root_index_array_key($index_array_deep);

            return $is_all_valid;
        }
    }

    /**
     * Execute validation with all data and the "if ruleset entities"
     *
     * @param array $data The parent data of the field which is related to the rule
     * @param string $field The field which is related to the rule
     * @param RulesetEntity $ruleset_entity The ruleset of the field
     * @return bool The result of validation
     */
    protected function execute_if_ruleset_entities($data, $field, $ruleset_entity)
    {
        // If not condition is matched, default to true.
        $result = true;

        foreach ($ruleset_entity->get_if_ruleset_entities() as $if_ruleset_entity) {
            $current_field_path = $ruleset_entity->get_path();
            $this->set_current_field_path($current_field_path);

            $condition_ruleset_entities = $if_ruleset_entity->get_condition_ruleset_entities();
            if (!empty($condition_ruleset_entities)) {
                $condition_result = $this->execute_if_condition_ruleset_entities($data, $field, $condition_ruleset_entities[0]);
                if ($condition_result != true) continue;
            }
            
            if ($if_ruleset_entity->has_if_ruleset_entities()) {
                $result = $this->execute_if_ruleset_entities($data, $field, $if_ruleset_entity);
            } else {
                $result = $this->execute_ruleset_entity($data, $field, $if_ruleset_entity, $current_field_path);
                $this->set_result($current_field_path, $result);
            }
            break;
        }

        return $result;
    }

    /**
     * Execute validation with all data and the "if condition ruleset entities"
     *
     * @param array $data The parent data of the field which is related to the rule
     * @param string $field The field which is related to the rule
     * @param RulesetEntity $ruleset_entity The if condition ruleset of the field
     * @return bool The result of validation
     */
    protected function execute_if_condition_ruleset_entities($data, $field, $ruleset_entity)
    {
        // If no condition is matched, default to false.
        $result = false;

        foreach ($ruleset_entity->get_condition_ruleset_entities() as $condition_ruleset_entity) {
            if ($condition_ruleset_entity->get_ruleset_type() == RulesetEntity::RULESET_TYPE_LEAF_CONDITION) {
                $result = $this->execute_if_condition_ruleset_entities($data, $field, $condition_ruleset_entity);
            } else {
                $result = $this->execute_ruleset_entity($data, $field, $condition_ruleset_entity, $ruleset_entity->get_path(), true);
            }

            /**
             * One of the or branch ruleset result is true, indicates the if condition result is true.
             * We don't have to validate other or branch.
             */
            if ($result === true) break;
        }

        return $ruleset_entity->is_met_condition($result, $this->config['symbol_logical_operator_not']);
    }

    /**
     * Execute validation with all data and the "paramllel ruleset entities"
     *
     * @param array $data The parent data of the field which is related to the rule
     * @param string $field The field which is related to the rule
     * @param RulesetEntity $ruleset_entity The ruleset of the field
     * @return bool The result of validation
     */
    protected function execute_parallel_ruleset_entities($data, $field, $ruleset_entity)
    {
        $result = true;

        $parallel_ruleset_entities = $ruleset_entity->get_parallel_ruleset_entities();
        $last_prse_key = count($parallel_ruleset_entities) - 1;

        $current_field_path = $ruleset_entity->get_path();

        foreach ($parallel_ruleset_entities as $key => $parallel_ruleset_entity) {
            $result = $this->execute_ruleset_entity($data, $field, $parallel_ruleset_entity, $current_field_path);
            $this->set_result($current_field_path, $result);
            
            if ($result) {
                $this->r_unset($this->nested_errors['general'], $current_field_path);
                $this->r_unset($this->nested_errors['detailed'], $current_field_path);
                unset($this->dotted_errors['general'][$current_field_path]);
                unset($this->dotted_errors['detailed'][$current_field_path]);
                return true;
            }
            if ($key == $last_prse_key) {
                // If one of "or" rule is invalid, don't set _validation_status to false
                // If all of "or" rule is invalid, then set _validation_status to false
                $this->validation_status = false;
                return false;
            }
        }

        return $result;
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
     * @param RulesetEntity $ruleset_entity The ruleset of the field
     * @return bool The result of validation
     */
    protected function execute_ruleset_entity($data, $field, $ruleset_entity)
    {
        $ruleset = $ruleset_entity->get_ruleset();
        $field_path = $ruleset_entity->get_path();
        $this->set_current_field_path($field_path)
            ->set_current_field_ruleset($ruleset);

        $ruleset_error_templates = $ruleset_entity->get_error_templates();

        foreach ($ruleset_entity->get_rule_entities() as $rule_entity) {
            $rule = $rule_entity->get_value();
            $this->set_current_rule($rule);

            $result = true;
            $name = $rule_entity->get_name();
            $rule_type = $rule_entity->get_rule_type();
            $error_type = $rule_entity->get_error_type();
            $error_template = $rule_entity->get_error_template();
            $method_rule = $rule_entity->get_method_rule();
            $method_rule['params'] = $this->parse_rule_entity_params($method_rule['params'], $data, $field);
            $params = $method_rule['params'];
            $this->set_current_method($method_rule);

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
            $when_rule_entities = $rule_entity->get_when_rule_entities();
            if (!empty($when_rule_entities)) {
                foreach ($when_rule_entities as $when_rule_entity) {
                    $when_type = $when_rule_entity->get_rule_type();

                    $when_method_rule = $when_rule_entity->get_method_rule();
                    $when_method_rule['params'] = $this->parse_rule_entity_params($when_method_rule['params'], $data, $field);
                    $when_result = $this->execute_method($when_method_rule);

                    if (is_array($when_result) && !empty($when_result['error_type']) && $when_result['error_type'] == 'undefined_method') return false;

                    if (
                        ($when_type === 'when' && $when_result === true)
                        || ($when_type === 'when_not' && $when_result !== true)
                    ) {
                        $is_met_when_rule = true;
                    } else {
                        $is_met_when_rule = false;
                    }
                }
            }

            /**
             * - Required(*) rule
             * - Required When rule
             */
            if ($name == $this->config_default['symbol_required']) {
                if (!static::required(isset($data[$field]) ? $data[$field] : null)) {
                    if ($when_type !== '' && $is_met_when_rule !== true) {
                        return true;
                    } else {
                        $result = false;
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
                    if ($when_type === '') {
                        return true;
                    }
                    /**
                     * Optional When rule
                     * If met the When condition, then the field is optional
                     */
                    else if ($when_type !== '' && $is_met_when_rule === true) {
                        return true;
                    }
                    /**
                     * Optional When rule
                     * If don't met the When condition, then the field is required
                     */
                    else if ($when_type !== '' && $is_met_when_rule !== true) {
                        $result = false;
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
                    if ($when_type === '') {
                        return true;
                    }
                    /**
                     * Optional Unset When rule
                     * If met the When condition, then the field is optional_unset
                     */
                    else if ($when_type !== '' && $is_met_when_rule === true) {
                        return true;
                    }
                    /**
                     * Optional Unset When rule
                     * If don't met the When condition, then the field is required
                     */
                    else if ($when_type !== '' && $is_met_when_rule !== true) {
                        $result = false;
                    }
                } else if (!static::required(isset($data[$field]) ? $data[$field] : null)) {
                    /**
                     * Optional Unset(O!) rule
                     */
                    if ($when_type === '') {
                        $result = false;
                    }
                    /**
                     * Optional Unset When rule
                     * - If met the When condition, then the field can not be empty
                     * - If don't met the When condition, then the field is required
                     */
                    else {
                        $result = false;
                    }
                }
            }
            /**
             * Most of the system symbols or methods support to be append a When rule.
             * If the condition of When rule is not met, we don't need to check the Regular expression and Method below
             */
            else if ($when_type !== '' && $is_met_when_rule !== true) {
                $result = true;
            }
            // Regular expression
            else if ($rule_type === RuleEntity::RULE_TYPE_PREG) {
                $preg = $name;
                if ($error_type == 'preg') {
                    $result = false;
                } else {
                    if (!preg_match($preg, $data[$field], $matches)) {
                        $result = false;
                    }
                }
            }
            // Method
            else {
                $result = $this->execute_method($method_rule);

                /**
                 * If method validation is success. should return true.
                 * If retrun anything others which is not equal to true, then means method validation failed.
                 * If retrun not a boolean value, will use the result as the error message template.
                 */
                if ($result !== true) {
                    if (!$rule_entity->is_error_template_for_whole_ruleset()) {
                        $dynamic_error_template = $this->match_error_template_from_result($result, $ruleset_error_templates, $when_type);
                        if ($dynamic_error_template !== null) $error_template = $dynamic_error_template;
                        if (empty($method_rule['by_symbol'])) $error_template = str_replace($this->symbol_method, $method_rule['method'], $error_template);
                        else $error_template = str_replace($this->symbol_method, $method_rule['symbol'], $error_template);
                    }

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
                if ($ruleset_entity->is_condition_ruleset_entity()) return false; 

                // Replace symbol to field name and parameter value
                if ($field_path === null) $field_path = $this->config['auto_field'];  // root node
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

                $this->set_error($field_path, $message, $ruleset_entity->is_parallel_ruleset_entity());
                return false;
            }
        }

        return true;
    }

    /**
     * Parse parameters of the rule entity
     *
     * @see static::parse_method()
     * @param array $params The parameters of rule entity
     * @param ?array $data The parent data of the field which is related to the rule
     * @param ?string $field The field which is related to the rule
     * @return array The parameters
     */
    protected function parse_rule_entity_params($params, $data, $field)
    {
        foreach ($params as &$param) {
            if (!is_string($param)) continue;

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

        return $params;
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
     * @see static::match_error_template()
     * @param bool|string|array $method_result The result of running a method
     * @param array $ruleset_error_templates Parsed error message which is defined in ruleset
     * @param string $when_type The When rule of a rule or method
     * @return ?string
     */
    protected function match_error_template_from_result($method_result, $ruleset_error_templates, $when_type)
    {
        // 1. The temporary EMT(general string formatted) defined in ruleset. No matter what rules in the ruleset are invalid, return this EMT
        // if (!empty($ruleset_error_templates) && !empty($ruleset_error_templates['whole_ruleset'])) return $ruleset_error_templates['whole_ruleset'];

        if (empty($method_result)) return null;

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
}
