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
            'params' => $this->parameters,
        ];
    }
}
