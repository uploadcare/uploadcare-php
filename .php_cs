<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude([
        'vendor',
    ])
;

return PhpCsFixer\Config::create()
    ->setRules([
        '@PSR2' => true,

        'linebreak_after_opening_tag' => true,
        'ordered_imports' => true,
        'phpdoc_order' => true,
        'single_blank_line_before_namespace' => true,
        'trailing_comma_in_multiline_array' => true,
    ])
    ->setFinder($finder)
;
