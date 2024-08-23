<?php

namespace githusband\Entity;

use githusband\Entity\RuleEntity;

/**
 * To parse ruleset from string to entity.
 * 
 * Ruleset is the set of rules(methods) of a data and its children.
 * Ruleset is finally defined by using simple string. We have to parse it every time when we start validating the rules.
 * We parse the ruleset into the ruleset entity. For the same ruleset, there is no need to parse it again the next time it is validated.
 */
class RulesetEntity
{
    /**
     * The ruleset types
     *
     * @var string
     */
    const RULESET_TYPE_INDEX_ARRAY = "rst_index_array";
    const RULESET_TYPE_ASSOC_ARRAY = "rst_assoc_array";
    const RULESET_TYPE_LEAF = "rst_leaf";
    const RULESET_TYPE_LEAF_PARALLEL = "rst_leaf_parallel";
    const RULESET_TYPE_LEAF_IF = "rst_leaf_if";
    const RULESET_TYPE_LEAF_CONDITION = "rst_leaf_condition";
    const RULESET_TYPE_LEAF_CONDITION_SERIAL = "rst_leaf_condition_serial";

    /**
     * The root node of the all entities.
     * 
     * If the root ruleset is string or index array etc, which has not a given field name of children, we will give it a field name, like "data".
     * So the self::$root is not the real root of the ruleset. The self::$root->children['data'] is the real root.
     * 
     * @example
     * ```
     * $rules_string = "required|int";
     *   ↓↓↓
     * $rules_string = [ "data" => "required|int" ];
     * 
     * $rules_index = [ "*" => "required|int" ];
     *   ↓↓↓
     * $rules_index = [ "data" => [ "*" => "required|int" ] ];
     * ```
     *
     * @var RulesetEntity|pointer
     */
    protected $root = null;

    /**
     * The real root name.
     *
     * @example data self::$root->children['data']
     * @var string
     */
    protected $real_root_name = null;

    /**
     * The parent node of the current entity.
     * If it's null, means it's the root node.
     * 
     * @var RulesetEntity|pointer
     */
    protected $parent = null;

    /**
     * The entity of its children.
     *
     * @var array<string, RulesetEntity>
     */
    protected $children = [];

    /**
     * The ruleset type of the current node.
     *  - RULESET_TYPE_INDEX_ARRAY
     *  - RULESET_TYPE_ASSOC_ARRAY
     *  - RULESET_TYPE_LEAF
     *  - RULESET_TYPE_LEAF_PARALLEL
     *  - RULESET_TYPE_LEAF_IF
     *  - RULESET_TYPE_LEAF_CONDITION
     * 
     * @var int
     */
    protected $ruleset_type;

    /**
     * The name of the current node.
     * 
     * @var string
     */
    protected $name;

    /**
     * The path of the current node.
     * - `null`: root node.
     * 
     * @var ?string
     */
    protected $path;

    /**
     * The value of the current node.
     * 
     * @var string|array
     */
    protected $value;

    /**
     * The non-parsed ruleset of the current node.
     * 
     * It will be parsed into:
     *  - {self::$rule_entities}
     *  - {self::$parallel_ruleset_entities}
     *  - {self::$if_ruleset_entities}
     *
     * @var string|array
     */
    protected $ruleset;

    /**
     * The system symbols set in the current node name or set as the onle child of the current node.
     * 
     * - [or] / [||]: Parallel rulesets
     * - [optional] / [O]: A array is optional
     * - .*: The ruleset is used for every children of a index array
     *
     * @see githusband\Validation::$config_default
     * @var string
     */
    protected $system_symbol;

    /**
     * The symbol_index_array (e.g. "*")
     * 
     * It can be customized by user.
     * The symbol_index_array is only set in the root ruleset entity.
     *
     * @see \githusband\Validation::$config['symbol_index_array']
     * @var string
     */
    protected $symbol_index_array;

    /**
     * The error templates of the current node.
     * 
     * It may contains:
     *  - "whole_ruleset": all the methods use the same error template
     *  - "xxx": Dynamic tag of the method error template. It can be the method name or any other tag name which return from a method.
     * @see \githusband\Validation::parse_error_templates()
     * @var array
     */
    protected $error_templates = [];

    /**
     * The rule entities(RE) of the current node.
     * 
     * Generally, a current node(final ruleset) has multiple rules(methods) and all of its rules will be parsed to rule entity.
     * But if in the two below, self::$rule_entities is empty:
     *  - The current node has "if ruleset entities"(IRSE). A IRSE has CRSE and REs.
     *  - The current node has "parallel ruleset entities"(PRSE). A PRSE has REs.
     *
     * @var RuleEntity[]
     */
    protected $rule_entities = [];

    /**
     * The "if ruleset entities"(IRSE) of the current node.
     * 
     * The ruleset_type of IRSE must be RULESET_TYPE_LEAF_IF/RULESET_TYPE_LEAF_IF_NOT.
     * 
     * We try to match a IRSE by its condition rules(CRSE).
     * If we match a IRSE, we should validate its rule entities and ignore other IRSE.
     * If we don't match the first IRSE, we should try to match the next IRSE.
     * If no IRSE is matched, means the validation result of the current node is success.
     * 
     * NOTE: Only one IRSE is supported currently.
     * 
     * Why add to self::$if_ruleset_entities instead of self::$children?
     * - Because a parent node may have IRSE.
     *
     * @var RulesetEntity[]
     */
    protected $if_ruleset_entities = [];

    /**
     * The "condition ruleset entities"(CRSE) of the IRSE.
     * 
     * The ruleset_type of CRSE must be RULESET_TYPE_LEAF_CONDITION.
     * They are the condition rules of the IRSE. So it must be empty if a RulesetEntity's ruleset_type is not RULESET_TYPE_LEAF_IF/RULESET_TYPE_LEAF_IF_NOT.
     * 
     * NOTE: Only one rule entity(RE) one CRSE is supported currently.
     *
     * @var RuleEntity[]
     */
    protected $condition_ruleset_entities = [];
    
    /**
     * The logical operator of a CRSE.
     * 
     * NOTE: Only the CRSE may have a logical operator and only the logical operator not(`!`) is supported.
     * 
     * @example `!` Logical operator not
     * @var string
     */
    protected $operator = '';

    /**
     * The "parallel ruleset entities"(PRSE) of the current node.
     * 
     * The ruleset_type of IRSE must be RULESET_TYPE_LEAF_PARALLEL.
     * 
     * One of them is validated, then the current node is validated.
     * If it's empty, check if the current node has "if ruleset entities"(IRSE).
     * 
     * Why add to self::$parallel_ruleset_entities instead of self::$children?
     * - Because a parent node may have PRSE.
     *
     * @var RulesetEntity[]
     */
    protected $parallel_ruleset_entities = [];

    /**
     * The current key of the index array.
     * 
     * If a ruleset is RULESET_TYPE_INDEX_ARRAY, we should record the current key of the index array.
     * Then we can use it to replace the "*" from path.
     * The index_array_keys is only set in the root ruleset entity.
     *
     * @var array<index_array_deep, index_array_key>
     */
    protected $index_array_keys = [];

    /**
     * The deep of index array.
     * 
     * The index_array_deep is set in the RULESET_TYPE_INDEX_ARRAY ruleset entity
     *
     * @var int
     */
    protected $index_array_deep = 0;
    
    /**
     * The thrown exception when parsing ruleset into RulesetEntity.
     * 
     * Sometimes the ruleset is invalid, then it will throw an exception and stop parsing.
     * The exception is only set in the root ruleset entity.
     *
     * @var \Throwable|\Exception
     */
    protected $exception = null;

    /**
     * Constructor.
     *
     * @param string $name
     * @param mixed $value
     * @param mixed $path
     */
    public function __construct($name, $value, $path = null)
    {
        $this->name = $name;
        $this->set_value($value);
        $this->path = $path;
    }

    /**
     * Clone the entity, we just need its:
     *  - root
     *  - name
     *  - path
     *
     * @return void
     */
    public function __clone() {
        $this->children = [];
        $this->ruleset_type = null;
        $this->value = null;
        $this->ruleset = null;
        $this->system_symbol = null;
        $this->symbol_index_array = null;
        $this->rule_entities = [];
        $this->if_ruleset_entities = [];
        $this->condition_ruleset_entities = [];
        $this->operator = '';
        $this->parallel_ruleset_entities = [];
        $this->index_array_keys = [];
        $this->index_array_deep = 0;
        $this->exception = null;
    }

    /**
     * Set the root RulesetEntity
     *
     * @param RulesetEntity|pointer $root The pointer of the root RulesetEntity
     * @return void
     */
    public function set_root(&$root)
    {
        $this->root = $root;
    }

    /**
     * Get the root RulesetEntity
     *
     * @return ?RulesetEntity|pointer
     */
    public function &get_root()
    {
        return $this->root;
    }

    /**
     * Set the real root name
     *
     * @param string $real_root_name
     * @return self
     */
    public function set_real_root_name($real_root_name)
    {
        $this->real_root_name = $real_root_name;
        return $this;
    }

    /**
     * Get the real root name
     *
     * @return ?string
     */
    public function get_real_root_name()
    {
        return $this->real_root_name;
    }

    /**
     * Set the parent RulesetEntity
     *
     * @param RulesetEntity|pointer $parent The pointer of the parent RulesetEntity
     * @return self
     */
    public function set_parent(&$parent)
    {
        $this->parent = $parent;

        $root =& $parent->get_root();
        if ($root === null) $root =& $parent;
        $this->set_root($root);

        return $this;
    }

    /**
     * Add a child node
     *
     * @param string $name
     * @param RulesetEntity $child
     * @return self
     */
    public function add_children($name, $child)
    {
        $this->children[$name] = $child;
        return $this;
    }

    /**
     * Get the children
     *
     * @return array<string,RulesetEntity>
     */
    public function get_children()
    {
        return $this->children;
    }

    /**
     * Set the ruleset_type
     *
     * @param string $ruleset_type
     * @return self
     */
    public function set_ruleset_type($ruleset_type)
    {
        $this->ruleset_type = $ruleset_type;
        return $this;
    }

    /**
     * Get the ruleset_type
     *
     * @return string
     */
    public function get_ruleset_type()
    {
        return $this->ruleset_type;
    }

    /**
     * Set the name
     *
     * @param string $name
     * @return self
     */
    public function set_name($name)
    {
        $this->name = $name;
        return $this;
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
     * Set the path
     *
     * @param ?string $path
     * @return self
     */
    public function set_path($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Get the path
     *
     * @return ?string
     */
    public function get_path()
    {
        if ($this->has_root_index_array_key()) {
            $path = $this->path;
            $symbol_index_array = preg_quote($this->get_symbol_index_array());
            foreach ($this->get_root_index_array_key() as $index_array_deep => $index_array_key) {
                $path = preg_replace("/{$symbol_index_array}/", $index_array_key, $path, 1);
            }
            return $path;
        } else {
            return $this->path;
        }
    }

    /**
     * Set the value
     *
     * @param string|array $value
     * @return self
     */
    public function set_value($value)
    {
        $this->value = $value;
        if (is_string($value)) $this->ruleset = $value;
        return $this;
    }

    /**
     * Get the value
     *
     * @param bool $is_pure
     * @return string|array
     */
    public function get_value($is_pure = false)
    {
        if ($is_pure) {
            return $this->value;
        } else {
            return $this->get_value_of_system_symbol();
        }
    }

    /**
     * Get the value of the system_symbol
     *
     * @return string|array
     */
    public function get_value_of_system_symbol()
    {
        if (empty($this->system_symbol))
            return $this->value;
        else if (isset($this->value[$this->system_symbol]))
            return $this->value[$this->system_symbol];
        else
            return $this->value;
    }

    /**
     * Set the ruleset
     *
     * @param string|array $ruleset
     * @return self
     */
    public function set_ruleset($ruleset)
    {
        $this->ruleset = $ruleset;
        return $this;
    }

    /**
     * Get the ruleset
     *
     * @return string|array
     */
    public function get_ruleset()
    {
        return $this->ruleset;
    }

    /**
     * Set the system_symbol
     *
     * @param string $system_symbol
     * @return self
     */
    public function set_system_symbol($system_symbol)
    {
        $this->system_symbol = $system_symbol;
        return $this;
    }

    /**
     * Get the system_symbol
     *
     * @return string
     */
    public function get_system_symbol()
    {
        return $this->system_symbol;
    }

    /**
     * Set the symbol_index_array
     *
     * @param string $symbol_index_array
     * @return self
     */
    public function set_symbol_index_array($symbol_index_array)
    {
        if ($this->root !== null) $this->root->set_symbol_index_array($symbol_index_array);
        else $this->symbol_index_array = $symbol_index_array;

        return $this;
    }

    /**
     * Get the symbol_index_array
     *
     * @return string
     */
    public function get_symbol_index_array()
    {
        if ($this->root !== null) return $this->root->get_symbol_index_array();
        else return $this->symbol_index_array;
    }

    /**
     * Set error_templates
     *
     * @param array $error_templates
     * @return self
     */
    public function set_error_templates($error_templates)
    {
        $this->error_templates = $error_templates;
        return $this;
    }

    /**
     * Get error_templates
     *
     * @return array
     */
    public function get_error_templates()
    {
        return $this->error_templates;
    }

    /**
     * Add a RuleEntity
     *
     * @param RuleEntity $rule_entity
     * @return self
     */
    public function add_rule_entity($rule_entity)
    {
        if (empty($this->if_ruleset_entities)) {
            $this->rule_entities[] = $rule_entity;
        } else {
            $last_if_ruleset_entity_key = count($this->if_ruleset_entities) - 1;
            $this->if_ruleset_entities[$last_if_ruleset_entity_key]->add_rule_entity($rule_entity);
        }
        return $this;
    }

    /**
     * Reset the rule_entities
     *
     * @return self
     */
    public function reset_rule_entities()
    {
        $this->rule_entities = [];
        return $this;
    }

    /**
     * Check if the RSE has IRSEs.
     *
     * @return RuleEntity[]
     */
    public function has_rule_entities()
    {
        return !empty($this->rule_entities);
    }

    /**
     * Get all the rule entities
     *
     * @return RuleEntity[]
     */
    public function get_rule_entities()
    {
        return $this->rule_entities;
    }

    /**
     * Add a "if ruleset entity"(IRSE)
     *
     * @param RulesetEntity $rule_entity
     * @return self
     */
    public function add_if_ruleset_entity($if_ruleset_entity)
    {
        $this->if_ruleset_entities[] = $if_ruleset_entity;
        return $this;
    }

    /**
     * Get all "if ruleset entity"(IRSE)
     *
     * @return RulesetEntity[]
     */
    public function get_if_ruleset_entities()
    {
        return $this->if_ruleset_entities;
    }

    /**
     * Check if the RSE has IRSEs.
     *
     * @return RulesetEntity[]
     */
    public function has_if_ruleset_entities()
    {
        return !empty($this->if_ruleset_entities);
    }

    /**
     * Add a "condition ruleset entity"(CRSE)
     *
     * @param RulesetEntity $condition_ruleset_entity
     * @return self
     */
    public function add_condition_ruleset_entity($condition_ruleset_entity)
    {
        $this->condition_ruleset_entities[] = $condition_ruleset_entity;
        return $this;
    }

    /**
     * Get all "condition ruleset entity"(CRSE)
     *
     * @return RulesetEntity[]
     */
    public function get_condition_ruleset_entities()
    {
        return $this->condition_ruleset_entities;
    }

    /**
     * Check if the IRSE has CRSE.
     *
     * @return RulesetEntity[]
     */
    public function has_condition_ruleset_entities()
    {
        return !empty($this->condition_ruleset_entities);
    }

    /**
     * Check if the RSE is a CRSE.
     *
     * @return bool
     */
    public function is_condition_ruleset_entity()
    {
        return $this->ruleset_type === self::RULESET_TYPE_LEAF_CONDITION || $this->ruleset_type === self::RULESET_TYPE_LEAF_CONDITION_SERIAL;
    }

    /**
     * Set operator of a CRSE
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
     * Get operator of a CRSE
     *
     * @return string
     */
    public function get_operator()
    {
        return $this->operator;
    }

    /**
     * Check the condition result is met the expected result.
     *
     * @param mixed $condition_result
     * @param string $logical_operator_not Default to '!'
     * @return bool
     */
    public function is_met_condition($condition_result, $logical_operator_not = '!')
    {
        return ($this->operator == '' && $condition_result === true)
            || ($this->operator == $logical_operator_not && $condition_result !== true);
    }

    /**
     * Add a "parallel ruleset entity"(PRSE)
     *
     * @param RulesetEntity $rule_entity
     * @return self
     */
    public function add_parallel_ruleset_entity($parallel_ruleset_entities)
    {
        $this->parallel_ruleset_entities[] = $parallel_ruleset_entities;
        return $this;
    }

    /**
     * Get all "parallel ruleset entity"(PRSE)
     *
     * @return RulesetEntity[]
     */
    public function get_parallel_ruleset_entities()
    {
        return $this->parallel_ruleset_entities;
    }

    /**
     * Check if the RSE has PRSEs.
     *
     * @return RulesetEntity[]
     */
    public function has_parallel_ruleset_entities()
    {
        return !empty($this->parallel_ruleset_entities);
    }

    /**
     * Check if the RSE is a PRSE.
     *
     * @return bool
     */
    public function is_parallel_ruleset_entity()
    {
        return $this->ruleset_type === self::RULESET_TYPE_LEAF_PARALLEL;
    }

    /**
     * Init index array status of the root node.
     * 
     * Make sure they don't impact the next validation of the other data.
     *
     * @return self
     */
    public function init_root_index_array_status()
    {
        if ($this->root !== null) {
            $this->root->init_index_array_status();
        } else {
            $this->index_array_keys = [];
            $this->index_array_deep = 0;
        }

        return $this;
    }

    /**
     * Init index array status of RULESET_TYPE_INDEX_ARRAY ruleset entity
     * 
     * Make sure they don't impact the next validation of the other data.
     *
     * @return self
     */
    public function init_index_array_status()
    {
        $this->index_array_deep = 0;

        return $this;
    }

    /**
     * Generate the deep of index array from the root node.
     *
     * @return int
     */
    public function generate_root_index_array_deep()
    {
        if ($this->root !== null) return $this->root->generate_root_index_array_deep();
        else return count($this->index_array_keys);
    }

    /**
     * Set the current key of the index array into the root node.
     *
     * @param int $index_array_deep
     * @param int $key
     * @return self
     */
    public function set_root_index_array_key($index_array_deep, $key)
    {
        if ($this->root !== null) $this->root->set_root_index_array_key($index_array_deep, $key);
        else $this->index_array_keys[$index_array_deep] = $key;

        return $this;
    }

    /**
     * Unset the current key of the index array into the root node.
     *
     * @param int $index_array_deep
     * @return self
     */
    public function unset_root_index_array_key($index_array_deep)
    {
        if ($this->root !== null) $this->root->unset_root_index_array_key($index_array_deep);
        else if (isset($this->index_array_keys[$index_array_deep])) unset($this->index_array_keys[$index_array_deep]);

        return $this;
    }

    /**
     * Get the key of the index array from the root node.
     *
     * @param ?int $index_array_deep
     * @return int|array
     */
    public function get_root_index_array_key($index_array_deep = null)
    {
        if ($this->root !== null) {
            return $this->root->get_root_index_array_key($index_array_deep);
        } else {
            if ($index_array_deep === null) {
                return $this->index_array_keys;
            } else {
                return $this->index_array_keys[$index_array_deep];
            }
        }
    }

    /**
     * Check if the root node has the current key of the index array.
     *
     * @return bool
     */
    public function has_root_index_array_key()
    {
        if ($this->root !== null) return $this->root->has_root_index_array_key();
        else return !empty($this->index_array_keys);
    }

    /**
     * Set the deep of the index array into the RULESET_TYPE_INDEX_ARRAY ruleset entity
     *
     * @param int $index_array_deep
     * @return self
     */
    public function set_index_array_deep($index_array_deep)
    {
        $this->index_array_deep = $index_array_deep;

        return $this;
    }

    /**
     * Set the deep of the index array of the RULESET_TYPE_INDEX_ARRAY ruleset entity
     *
     * @return ?int
     */
    public function get_index_array_deep()
    {
        return $this->index_array_deep;
    }

    /**
     * Set the thrown exception when parsing ruleset into RulesetEntity.
     *
     * @param \Throwable|\Exception $exception
     * @return self
     */
    public function set_exception($exception)
    {
        $this->exception = $exception;

        return $this;
    }

    /**
     * Get the thrown exception when parsing ruleset into RulesetEntity.
     *
     * @return \Throwable|\Exception
     */
    public function get_exception()
    {
        return $this->exception;
    }

    /**
     * Check the ruleset entity before validate data.
     * 
     * If the ruleset entity has an exception, the ruleset entity can not be used to validate data.
     * Then we throw this exception.
     *
     * @return void
     * @throws \Throwable|\Exception
     */
    public function before_validate()
    {
        if ($this->exception !== null) throw $this->exception;
    }
}
