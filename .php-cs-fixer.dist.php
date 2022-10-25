<?php

$finder = PhpCsFixer\Finder::create()
    ->ignoreDotFiles(false)
    ->ignoreVCS(true)
    ->exclude([
        'vendor',
    ])
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

$rules = [
    '@Symfony' => true,
    'align_multiline_comment' => [
        'comment_type' => 'phpdocs_like',
    ],
    'array_indentation' => true,
    'compact_nullable_typehint' => true,
    'multiline_comment_opening_closing' => true,
    'new_with_braces' => false,
    'phpdoc_to_comment' => false,
    'single_import_per_statement' => false,
    'concat_space' => ['spacing' => 'one'],
    'array_syntax' => ['syntax' => 'short'],
    'no_superfluous_phpdoc_tags' => true,
    'blank_line_after_opening_tag' => false,
    'linebreak_after_opening_tag' => false,
    'global_namespace_import' => [
        'import_classes' => false,
        'import_constants' => false,
        'import_functions' => false,
    ],
    'phpdoc_separation' => true,
    'yoda_style' => false,
    'native_function_invocation' => [
        'exclude' => [],
        'include' => ['@internal'],
        'scope' => 'all',
        'strict' => false,
    ],
    'types_spaces' => ['space' => 'single'],
];

$config = new PhpCsFixer\Config();

return $config
    ->setRiskyAllowed(true)
    ->setRules($rules)
    ->setFinder($finder)
;
