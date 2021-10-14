<?php

return (new PhpCsFixer\Config())
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->exclude([
                'docker',
                'node_modules',
                'public/bundles',
                'templates',
                'var',
                'vendor',
            ])
            ->in(__DIR__)
    )
    ->setRiskyAllowed(true)
    ->setRules([
        // https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/master/doc/rules/index.rst
        // Alias
        'array_push' => true,
        'backtick_to_shell_exec' => true,
        'ereg_to_preg' => true,
        'modernize_strpos' => true,
        'no_alias_functions' => ['sets' => ['@all']],
        'no_alias_language_construct_call' => true,
        'no_mixed_echo_print' => true,
        'set_type_to_cast' => true,
        // Array Notation
        'array_syntax' => true,
        'no_multiline_whitespace_around_double_arrow' => true,
        'no_trailing_comma_in_singleline_array' => true,
        'no_whitespace_before_comma_in_array' => true,
        'normalize_index_brace' => true,
        'trim_array_spaces' => true,
        'whitespace_after_comma_in_array' => true,
        // Basic
        'braces' => [
            'allow_single_line_anonymous_class_with_empty_body' => true,
            'allow_single_line_closure' => true,
            'position_after_functions_and_oop_constructs' => 'same',
            'position_after_control_structures' => 'same',
            'position_after_anonymous_constructs' => 'same',
        ],
        'encoding' => true,
        'non_printable_character' => true,
        'psr_autoloading' => true,
        // Casing
        'constant_case' => true,
        'lowercase_keywords' => true,
        'lowercase_static_reference' => true,
        'magic_constant_casing' => true,
        'magic_method_casing' => true,
        'native_function_casing' => true,
        'native_function_type_declaration_casing' => true,
        // Cast Notation
        'cast_spaces' => true,
        'lowercase_cast' => true,
        'modernize_types_casting' => true,
        'no_short_bool_cast' => true,
        'no_unset_cast' => true,
        'short_scalar_cast' => true,
        // Class Notation
        'class_attributes_separation' => [
            'elements' => [
                'const' => 'none',
                'method' => 'one',
                'property' => 'none',
                'trait_import' => 'none'
            ]
        ],
        'class_definition' => [
            'single_item_single_line' => true,
            'single_line' => true,
        ],
        'no_blank_lines_after_class_opening' => true,
        'no_null_property_initialization' => true,
        'no_php4_constructor' => true,
        'no_unneeded_final_method' => true,
        'ordered_class_elements' => [
            'order' => [
                'use_trait',
                'constant_public',
                'constant_protected',
                'constant_private',
                'property_public_static',
                'property_protected_static',
                'property_private_static',
                'property_public',
                'property_protected',
                'property_private',
                'construct',
                'destruct',
                'magic',
                'method_public_abstract_static',
                'method_protected_abstract_static',
                'method_public_static',
                'method_protected_static',
                'method_private_static',
                'method_public_abstract',
                'method_protected_abstract',
                'method_public',
                'method_protected',
                'method_private'
            ],
            'sort_algorithm' => 'alpha'
        ],
        'ordered_interfaces' => true,
        'ordered_traits' => true,
        'protected_to_private' => true,
        'self_accessor' => true,
        'self_static_accessor' => true,
        'single_class_element_per_statement' => true,
        'single_trait_insert_per_statement' => true,
        'visibility_required' => true,
        // Comment
        'comment_to_phpdoc' => true,
        'multiline_comment_opening_closing' => true,
        'no_empty_comment' => true,
        'no_trailing_whitespace_in_comment' => true,
        'single_line_comment_style' => true,
        // Control Structure
        'control_structure_continuation_position' => true,
        'elseif' => true,
        'empty_loop_body' => true,
        'empty_loop_condition' => true,
        'include' => true,
        'no_alternative_syntax' => true,
        'no_superfluous_elseif' => true,
        'no_trailing_comma_in_list_call' => true,
        'no_unneeded_control_parentheses' => true,
        'no_unneeded_curly_braces' => ['namespaces' => true],
        'no_useless_else' => true,
        'simplified_if_return' => true,
        'switch_case_semicolon_to_colon' => true,
        'switch_case_space' => true,
        'switch_continue_to_break' => true,
        // Function Notation
        'combine_nested_dirname' => true,
        'fopen_flag_order' => true,
        'fopen_flags' => true,
        'function_declaration' => true,
        'function_typehint_space' => true,
        'implode_call' => true,
        'lambda_not_used_import' => true,
        'method_argument_space' => true,
        'no_spaces_after_function_name' => true,
        'no_unreachable_default_argument_value' => true,
        'no_useless_sprintf' => true,
        'nullable_type_declaration_for_default_null_value' => true,
        'regular_callable_call' => true,
        'return_type_declaration' => true,
        'single_line_throw' => true,
        'static_lambda' => true,
        'use_arrow_functions' => true,
        'void_return' => true,
        // Import
        'fully_qualified_strict_types' => true,
        'global_namespace_import' => [
            'import_constants' => true,
            'import_functions' => true,
            'import_classes' => true,
        ],
        'no_leading_import_slash' => true,
        'no_unused_imports' => true,
        'ordered_imports' => true,
        'single_import_per_statement' => true,
        'single_line_after_imports' => true,
        // Language Construct
        'combine_consecutive_issets' => true,
        'combine_consecutive_unsets' => true,
        'declare_equal_normalize' => true,
        'declare_parentheses' => true,
        'dir_constant' => true,
        'explicit_indirect_variable' => true,
        'function_to_constant' => true,
        'is_null' => true,
        'no_unset_on_property' => true,
        'single_space_after_construct' => true,
        // List Notation
        'list_syntax' => true,
        // Namespace Notation
        'blank_line_after_namespace' => true,
        'clean_namespace' => true,
        'no_leading_namespace_whitespace' => true,
        'single_blank_line_before_namespace' => true,
        // Naming
        'no_homoglyph_names' => true,
        // Operator
        'assign_null_coalescing_to_coalesce_equal' => true,
        'binary_operator_spaces' => true,
        'concat_space' => true,
        'logical_operators' => true,
        'new_with_braces' => true,
        'no_space_around_double_colon' => true,
        'object_operator_without_whitespace' => true,
        'operator_linebreak' => true,
        'standardize_increment' => true,
        'standardize_not_equals' => true,
        'ternary_operator_spaces' => true,
        'ternary_to_elvis_operator' => true,
        'ternary_to_null_coalescing' => true,
        'unary_operator_spaces' => true,
        // PHP Tag
        'blank_line_after_opening_tag' => true,
        'full_opening_tag' => true,
        'linebreak_after_opening_tag' => true,
        'no_closing_tag' => true,
        // PHPDoc
        'align_multiline_comment' => true,
        'general_phpdoc_annotation_remove' => true,
        'general_phpdoc_tag_rename' => true,
        'no_blank_lines_after_phpdoc' => true,
        'no_empty_phpdoc' => true,
        'no_superfluous_phpdoc_tags' => ['allow_mixed' => true],
        'phpdoc_align' => true,
        'phpdoc_annotation_without_dot' => true,
        'phpdoc_indent' => true,
        'phpdoc_inline_tag_normalizer' => true,
        'phpdoc_no_access' => true,
        'phpdoc_no_alias_tag' => true,
        'phpdoc_no_empty_return' => true,
        'phpdoc_no_package' => true,
        'phpdoc_no_useless_inheritdoc' => true,
        'phpdoc_order_by_value' => true,
        'phpdoc_order' => true,
        'phpdoc_return_self_reference' => true,
        'phpdoc_scalar' => true,
        'phpdoc_separation' => true,
        'phpdoc_single_line_var_spacing' => true,
        'phpdoc_summary' => true,
        'phpdoc_tag_casing' => true,
        'phpdoc_tag_type' => true,
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        'phpdoc_trim' => true,
        'phpdoc_types' => true,
        'phpdoc_types_order' => ['sort_algorithm' => 'alpha', 'null_adjustment' => 'none'],
        'phpdoc_var_annotation_correct_order' => true,
        'phpdoc_var_without_name' => true,
        // Return Notation
        'no_useless_return' => true,
        'return_assignment' => true,
        'simplified_null_return' => true,
        // Semicolon
        'multiline_whitespace_before_semicolons' => true,
        'no_empty_statement' => true,
        'no_singleline_whitespace_before_semicolons' => true,
        'semicolon_after_instruction' => true,
        'space_after_semicolon' => true,
        // String Notation
        'escape_implicit_backslashes' => true,
        'heredoc_to_nowdoc' => true,
        'no_binary_string' => true,
        'no_trailing_whitespace_in_string' => true,
        'simple_to_complex_string_variable' => true,
        'single_quote' => ['strings_containing_single_quote_chars' => true],
        // Whitespace
        'array_indentation' => true,
        'compact_nullable_typehint' => true,
        'indentation_type' => true,
        'line_ending' => true,
        'method_chaining_indentation' => true,
        'no_extra_blank_lines' => true,
        'no_spaces_around_offset' => true,
        'no_spaces_inside_parenthesis' => true,
        'no_trailing_whitespace' => true,
        'no_whitespace_in_blank_line' => true,
        'single_blank_line_at_eof' => true,
        'types_spaces' => true,
    ])
    ->setUsingCache(false);
