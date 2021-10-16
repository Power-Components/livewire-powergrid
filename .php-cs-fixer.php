<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$rules = [
    '@PSR12'                  => true,
    'align_multiline_comment' => false,
    'array_indentation'       => true,
    "array_syntax"            => ['syntax' => 'short'],
    'binary_operator_spaces'  => [
        'operators' => [
            '='  => 'align_single_space',
            '=>' => 'align_single_space',
        ],
    ],
    'blank_line_after_namespace'   => true,
    'blank_line_after_opening_tag' => false,
    'blank_line_before_statement'  => ['statements' => ['break', 'continue', 'declare', 'return', 'throw', 'try']],
    'braces'                       => [
        'allow_single_line_closure'                   => false,
        'position_after_anonymous_constructs'         => 'same',
        'position_after_control_structures'           => 'same',
        'position_after_functions_and_oop_constructs' => 'next',
    ],
    'cast_spaces'                          => ['space' => 'none'],
    'class_attributes_separation'          => [
        'elements' => ['method' => 'one', 'property' => 'one'],
    ],
    'class_keyword_remove'                 => false,
    'combine_consecutive_issets'           => false,
    'combine_consecutive_unsets'           => false,
    'combine_nested_dirname'               => false,
    'comment_to_phpdoc'                    => false,
    'compact_nullable_typehint'            => false,
    'concat_space'                         => ['spacing' => 'one'],
    'constant_case'                        => [
        'case' => 'lower',
    ],
    'date_time_immutable'                  => false,
    'declare_equal_normalize'              => [
        'space' => 'single',
    ],
    'declare_strict_types'                 => false,
    'dir_constant'                         => false,
    'doctrine_annotation_array_assignment' => false,
    'doctrine_annotation_braces'           => false,
    'doctrine_annotation_indentation'      => [
        'ignored_tags'       => [],
        'indent_mixed_lines' => true,
    ],
    'doctrine_annotation_spaces'           => [
        'after_argument_assignments'     => false,
        'after_array_assignments_colon'  => false,
        'after_array_assignments_equals' => false,
    ],
    'elseif'                      => false,
    'encoding'                    => true,
    'indentation_type'            => true,
    'no_useless_else'             => false,
    'no_useless_return'           => true,
    'ordered_imports'             => true,
    'single_quote'                => false,
    'ternary_operator_spaces'     => true,
    'trailing_comma_in_multiline' => true,
];

$finder = Finder::create()
    ->notPath('bootstrap')
    ->notPath('storage')
    ->notPath('vendor')
    ->in(getcwd())
    ->name('*.php')
    ->notName('*.blade.php')
    ->notName('index.php')
    ->notName('server.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new Config())
    ->setFinder($finder)
    ->setRules($rules)
    ->setRiskyAllowed(false)
    ->setUsingCache(true);
