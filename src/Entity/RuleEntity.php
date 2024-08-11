<?php

namespace githusband\Entity;

/**
 * To parse rule from string to entity.
 * 
 * Rule is defined by using simple string. We have to parse it every time when we start validating the rules.
 * We parse the rule into the rule entity(RE). For the same rule, there is no need to parse it again the next time it is validated.
 */
class RuleEntity
{
    /**
     * The rule types
     * 
     * @var string
     */
    const RULE_TYPE_METHOD = "method";
    const RULE_TYPE_PREG = "preg";
    const RULE_TYPE_WHEN = "when";
    const RULE_TYPE_WHEN_NOT = "when_not";

    /**
     * The ruleset type of the current node.
     *  - RULE_TYPE_METHOD
     *  - RULE_TYPE_PREG
     *  - RULE_TYPE_WHEN
     *  - RULE_TYPE_WHEN_NOT
     * 
     * @var string
     */
    protected $rule_type;

    /**
     * The name of the rule.
     *  - If the rule is an regular expression, the name is the regular expression
     *  - If not, the name is a method name
     *
     * @var string
     */
    protected $name;

    /**
     * The non-parsed rule of the current rule.
     *
     * @var string
     */
    protected $value;

    /**
     * The symbol of the current rule.
     *
     * @var string
     */
    protected $symbol;

    /**
     * The parameters of the current rule method.
     *
     * @var array
     */
    protected $parameters = [];

    /**
     * The logical operator of the current rule.
     * 
     * Some of the method symbol may start with the operator `!`. In this case, we treat the `!` as part of the symbol instead of a operator.
     * So if you change the method symbol map, you should re-parse the entities to ensure they are correct.
     * 
     * NOTE: Currently, only the logical operator not(`!`) is supported.
     * 
     * @example `!` Logical operator not
     * @var string
     * @todo Supports comparison operators, e.g. `==`, `!=`
     */
    protected $operator = '';

    /**
     * The compared value for the comparison operator of the current rule.
     * 
     * Due to we only support `!` currently, so the compared value is useless
     * 
     * @var string
     * @todo Supports compared values
     */
    // protected $compared_value = true;

    /**
     * The error type if the validation of the current rule is failed.
     *
     * @var string
     */
    protected $error_type = 'validation';

    /**
     * The error message template of the current rule.
     *
     * @var string
     */
    protected $error_template;

    /**
     * The temporary error message template(EMT)(general string formatted) defined in ruleset.
     * No matter what rules in the ruleset are invalid, return this EMT
     *
     * @var bool
     */
    protected $is_error_template_for_whole_ruleset = false;

    /**
     * Indicate the rule is set from method name or its symbol.
     * 
     * Default to false - from method name
     *
     * @var bool
     */
    protected $by_symbol = false;

    /**
     * The when rule entities of the current rule.
     * 
     * One rule can have only one type of when rule: when or when not
     * A when rule may have one or multiple rules
     *
     * @var RuleEntity[]
     */
    protected $when_rule_entities = [];

    /**
     * Constructor.
     *
     * @param string $rule_type
     * @param string $name
     * @param string $value
     * @param string $parameters
     * @param string $symbol
     * @param string $by_symbol
     */
    public function __construct($rule_type, $name, $value, $parameters, $symbol = null, $by_symbol = false)
    {
        $this->rule_type = $rule_type;
        $this->name = $name;
        $this->value = $value;
        $this->parameters = $parameters;
        $this->symbol = $symbol === null ? $name : $symbol;
        $this->by_symbol = $by_symbol;
    }

    /**
     * Get the rule_type
     *
     * @return string
     */
    public function get_rule_type()
    {
        return $this->rule_type;
    }

    /**
     * Get the name
     *
     * @return string
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     * Get the value
     *
     * @return string
     */
    public function get_value()
    {
        return $this->value;
    }

    /**
     * Get the symbol
     *
     * @return string
     */
    public function get_symbol()
    {
        return $this->symbol;
    }

    /**
     * Get the parameters
     *
     * @return array
     */
    public function get_parameters()
    {
        return $this->parameters;
    }

    /**
     * Set error_type
     *
     * @param string $error_type
     * @return self
     */
    public function set_error_type($error_type)
    {
        $this->error_type = $error_type;
        return $this;
    }

    /**
     * Get error_type
     *
     * @return string
     */
    public function get_error_type()
    {
        return $this->error_type;
    }

    /**
     * Set operator
     *
     * @param string $operator
     * @return self
     */
    public function set_operator($operator)
    {
        $this->operator = $operator;
        return $this;
    }

    /**
     * Get operator
     *
     * @return string
     */
    public function get_operator()
    {
        return $this->operator;
    }

    /**
     * Set error_template
     *
     * @param string $error_template
     * @return self
     */
    public function set_error_template($error_template)
    {
        $this->error_template = $error_template;
        return $this;
    }

    /**
     * Get error_template
     *
     * @return string
     */
    public function get_error_template()
    {
        return $this->error_template;
    }

    /**
     * Set is_error_template_for_whole_ruleset
     *
     * @param bool $is_error_template_for_whole_ruleset
     * @return self
     */
    public function set_is_error_template_for_whole_ruleset($is_error_template_for_whole_ruleset)
    {
        $this->is_error_template_for_whole_ruleset = $is_error_template_for_whole_ruleset;
        return $this;
    }

    /**
     * Get is_error_template_for_whole_ruleset
     *
     * @return bool
     */
    public function is_error_template_for_whole_ruleset()
    {
        return $this->is_error_template_for_whole_ruleset;
    }

    /**
     * Get by_symbol
     *
     * @return string
     */
    public function get_by_symbol()
    {
        return $this->by_symbol;
    }

    /**
     * Add a when RuleEntity
     *
     * @param RuleEntity $rule_entity
     * @return self
     */
    public function add_when_rule_entity($rule_entity)
    {
        $this->when_rule_entities[] = $rule_entity;
        return $this;
    }

    /**
     * Get all when RuleEntities
     *
     * @return RuleEntity[]
     */
    public function get_when_rule_entities()
    {
        return $this->when_rule_entities;
    }

    /**
     * Get method rule
     *
     * @see \githusband\Validation::parse_method()
     * @return void
     */
    public function get_method_rule()
    {
        return [
            'method' => $this->name,
            'symbol' => $this->symbol,
            'by_symbol' => $this->by_symbol,
            'operator' => $this->operator,
            'params' => $this->parameters,
        ];
    }
}
