<?php
$finder = PhpCsFixer\Finder::create()
    ->exclude('bootstrap/cache')
    ->exclude('nova')
    ->exclude('resources/assets')
    ->exclude('resources/views')
    ->exclude('resources/lang')
    ->exclude('storage')
    ->exclude('node_modules')
    ->exclude('_ide_helper.php')
    ->exclude('.phpstorm.meta.php')
    ->name('*.php')
    ->notName('*.blade.php')
    ->ignoreDotFiles(true)
    ->in(__DIR__);
$rules = [
    '@Symfony' => true,
    'align_multiline_comment' => true,                     // PHPDoc的なコメント部分をいい感じに揃えてくれる
    'array_syntax' => ['syntax' => 'short'],               // 配列は[]に統一
    'blank_line_before_statement' => [
        'statements' => [
            'declare', 'do', 'for', 'foreach', 'if', 'switch', 'try'
        ]
    ],                                                     // 設定した項目の前に空白行を入れる
    'combine_consecutive_issets' => true,                  // 複数のissetを一つにまとめる
    'combine_consecutive_unsets' => true,                  // 複数のunsetを一つにまとめる
    'compact_nullable_typehint' => true,                   // nullableの書き方を統一
    'concat_space' => ['spacing' => 'one'],                // 文字列結合の際にスペースを1つあけるようにする
    'declare_equal_normalize' => true,                     // declereの書き方を統一
    'dir_constant' => true,                                // dirname(__FILE__)を__DIR__に変更
    'ereg_to_preg' => true,                                // ereg('[A-Z]')をpreg_match('/[A-Z]/D')に変更
    'escape_implicit_backslashes' => false,                // ダブルクオーテーションの文字列をシングルクオーテーションに変換
    'explicit_indirect_variable' => true,                  //
    'explicit_string_variable' => true,                    // 文字列の変数展開するところを上手いことやってくれる
    'final_internal_class' => true,                        // Classにfinalつける
    'function_to_constant' => true,                        // PHPの関数を定数に変更
    'function_typehint_space' => true,                     // functionの引数に型が設定されていた場合、スペースを追加して見やすくする
    'general_phpdoc_annotation_remove' => ['annotations' => ['class', 'package', 'author']],
    'single_line_comment_style' => true,                       // #でのコメントを//に変換
    'heredoc_to_nowdoc' => true,                           // ヒアドキュメントの文字列のところをシングルクオートに変更
    'include' => true,                                     // includeとrequireの書き方を統一
    'yoda_style' => true,                                  // isnull($b)をnull === $bに変更
    'linebreak_after_opening_tag' => true,                 // <?phpの行は処理を書けないように変更
    'list_syntax' => true,                                 //
    'lowercase_cast' => true,
    'magic_constant_casing' => true,
    'method_chaining_indentation' => true,
    'phpdoc_separation' => true,
    'modernize_types_casting' => true,
    'native_function_casing' => true,
    'no_alias_functions' => true,
    'no_blank_lines_after_class_opening' => true,
    'no_blank_lines_after_phpdoc' => true,
    'no_empty_comment' => true,
    'no_empty_phpdoc' => true,
    'no_empty_statement' => true,
    'no_extra_blank_lines' => ['tokens' => ['break', 'continue', 'extra', 'return', 'throw', 'use', 'parenthesis_brace_block', 'square_brace_block', 'curly_brace_block']],
    'no_homoglyph_names' => true,
    'no_leading_import_slash' => true,
    'no_leading_namespace_whitespace' => true,
    'no_mixed_echo_print' => true,
    'no_multiline_whitespace_around_double_arrow' => true,
    'multiline_whitespace_before_semicolons' => true,
    'no_null_property_initialization' => true,
    'no_php4_constructor' => true,
    'no_short_bool_cast' => true,
    'no_singleline_whitespace_before_semicolons' => true,
    'no_spaces_around_offset' => true,
    'no_trailing_comma_in_list_call' => true,
    'no_trailing_comma_in_singleline_array' => true,
    'no_unneeded_control_parentheses' => true,
    'no_unneeded_curly_braces' => true,
    'no_unneeded_final_method' => true,
    'no_unreachable_default_argument_value' => true,
    'no_unused_imports' => true,
    'no_useless_else' => true,
    'no_useless_return' => true,
    'no_whitespace_before_comma_in_array' => true,
    'no_whitespace_in_blank_line' => true,
    'normalize_index_brace' => true,
    'object_operator_without_whitespace' => true,
    'ordered_class_elements' => true,
    'ordered_imports' => true,
    'php_unit_construct' => true,
    'php_unit_dedicate_assert' => true,
    'php_unit_mock' => true,
    'php_unit_namespaced' => true,
    'phpdoc_add_missing_param_annotation' => ['only_untyped' => false],
    'phpdoc_align' => ['tags' => ['param']],
    'phpdoc_return_self_reference' => true,
    'phpdoc_scalar' => true,
    'phpdoc_single_line_var_spacing' => true,
    'phpdoc_summary' => true,
    'phpdoc_types_order' => true,
    'phpdoc_var_without_name' => true,
    'pow_to_exponentiation' => true,
    'protected_to_private' => true,
    'random_api_migration' => true,
    'return_type_declaration' => true,
    'self_accessor' => true,
    'semicolon_after_instruction' => true,
    'short_scalar_cast' => true,
    'simplified_null_return' => true,
    'single_blank_line_before_namespace' => true,
    'single_line_comment_style' => true,
    'space_after_semicolon' => ['remove_in_empty_for_expressions' => true],
    'standardize_not_equals' => true,
    'ternary_operator_spaces' => true,
    'ternary_to_null_coalescing' => true,
    'trailing_comma_in_multiline' => true,
    'trim_array_spaces' => true,
    'unary_operator_spaces' => true,
    'void_return' => true,
    'linebreak_after_opening_tag' => true,                 // 開始タグの後ろに改行を入れる
    'no_multiline_whitespace_around_double_arrow' => true, // 演算子 => は複数行の空白に囲まれない
    'multiline_whitespace_before_semicolons' => true,   // セミコロンを閉じる前の複数行の空白は禁止
    'no_unused_imports' => true,                           // 未使用のuse文は削除
    'ordered_imports' => ['sort_algorithm' => 'length'],    // use文の整列
    'simplified_null_return' => true,                      // return nullを簡略化する
    'strict_param' => true,
    'php_unit_method_casing' => false,
];
return (new PhpCsFixer\Config())
    ->setRules($rules)
    ->setFinder($finder)
    ->setUsingCache(false);
