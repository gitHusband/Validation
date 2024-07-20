# Entity

> **如何预览结构？**
> - VSCODE 安装拓展 **Markdown Preview Mermaid Support**
> - [Mermaid](https://mermaid.live) 文档

## 概览

为了重用规则集，避免每次验证数据的时候，都必须重新解析规则集，我们先将规则集解析成 Entity，之后将使用 Entity 来验证数据。
Entity 包括：
- **Ruleset Entity(RSE)**: 与规则集数组一样，RSE 也是层层嵌套树状的结构，直到最后的 ruleset。
- **Rule Entity(RE)**: 一个 Ruleset 由一个或多个 rule（正则或者方法）组成。也就是说，一个 RSE 可能包含一个或多个 RE。RE 则是一个个解析过后的方法，包括方法名，参数以及错误模板等等。



## 结构

```mermaid
flowchart LR
    %% rse_ruleset_type --x rse_rst_index_array{{RULESET_TYPE_INDEX_ARRAY}}
    %% rse_ruleset_type --x rse_rst_assoc_array{{RULESET_TYPE_ASSOC_ARRAY}}
    %% rse_ruleset_type --x rse_rst_leaf{{RULESET_TYPE_LEAF}}
    %% rse_ruleset_type --x rse_rst_leaf_parallel{{RULESET_TYPE_LEAF_PARALLEL}}
    %% rse_ruleset_type --x rse_rst_leaf_if{{RULESET_TYPE_LEAF_IF}}
    %% rse_ruleset_type --x rse_rst_leaf_if_not{{RULESET_TYPE_LEAF_IF_NOT}}
    %% rse_ruleset_type --x rse_rst_leaf_condition{{RULESET_TYPE_LEAF_CONDITION}}

    %% re_rule_type --x re_rt_method{{RULE_TYPE_METHOD}}
    %% re_rule_type --x re_rt_preg{{RULE_TYPE_PREG}}
    %% re_rule_type --x re_rt_when{{RULE_TYPE_WHEN}}
    %% re_rule_type --x re_rt_when_not{{RULE_TYPE_WHEN_NOT}}

    RE["Rule Entity(RE)"] --> re_rule_type[Rule Type];
    RE --> re_name[Name];
    RE --> re_value[Value];
    RE --> re_symbol[Symbol];
    RE --> re_parameters[Parameters];
    RE --> re_error_type[Error Type];
    RE --> re_error_template[Error Template];
    RE --> re_is_error_template_for_whole_ruleset[Is Error template For Whole Ruleset];
    RE --> re_by_symbol[By symbol];
    RE --> re_when_rule_entities[When RE];

    re_when_rule_entities --> RE

    RSE["Ruleset Entity(RSE)"] --> rse_root[Root RSE];
    RSE --> rse_parent[Parent RSE];
    RSE --> rse_children[Children RSEs];
    RSE --> rse_ruleset_type[Ruleset Type];
    RSE --> rse_name[Name];
    RSE --> rse_path[Path];
    RSE --> rse_value[Value];
    RSE --> rse_ruleset[Ruleset];
    RSE --> rse_system_symbol[System Symbol];
    RSE --> rse_symbol_index_array["Symbol Of Index Array(Root Only)"];
    RSE --> rse_rule_entities[Rule Entities];
    RSE --> rse_if_ruleset_entities[If RSEs];
    RSE --> rse_condition_ruleset_entities["Condition RSEs(If RSE Only)"];
    RSE --> rse_parallel_ruleset_entities[Parallel RSEs];
    RSE --> rse_index_array_keys["Current Index Array Keys For All RSEs(Root Only)"];
    RSE --> rse_index_array_deep["Index Array Deep of the RSE(Index Array Only)"];
    RSE --> rse_exception["Parsing Exception(Root Only)"];

    rse_root --> RSE
    rse_parent --> RSE
    rse_children --> RSE
    rse_children --> RSE
    rse_if_ruleset_entities --> RSE
    rse_if_ruleset_entities --> RSE
    rse_parallel_ruleset_entities --> RSE
    rse_parallel_ruleset_entities --> RSE

    rse_rule_entities -- Parent or Leaf RSE has ruleset? --> RE
```

**Ruleset Type:**
 - `RULESET_TYPE_INDEX_ARRAY`: "rst_index_array"
 - `RULESET_TYPE_ASSOC_ARRAY`: "rst_assoc_array"
 - `RULESET_TYPE_LEAF`: "rst_leaf"
 - `RULESET_TYPE_LEAF_PARALLEL`: "rst_leaf_parallel"
 - `RULESET_TYPE_LEAF_IF`: "rst_leaf_if"
 - `RULESET_TYPE_LEAF_IF_NOT`: "rst_leaf_if_not"
 - `RULESET_TYPE_LEAF_CONDITION`: "rst_leaf_condition"

**Rule Type:**
 - `RULE_TYPE_METHOD`: "method"
 - `RULE_TYPE_PREG`: "preg"
 - `RULE_TYPE_WHEN`: "when"
 - `RULE_TYPE_WHEN_NOT`: "when_not"