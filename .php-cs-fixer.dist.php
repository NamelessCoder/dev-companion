<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = (new Finder())
    ->in([__DIR__ . '/src', __DIR__ . '/tests'])
    // The entrypoints carry no extension, so the finder never sees them.
    ->append([__DIR__ . '/bin/cli', __DIR__ . '/bin/typo3-cms-mcp']);

return (new Config('typo3-cms-mcp'))
    ->setRiskyAllowed(true)
    ->setFinder($finder)
    ->setRules([
        '@PER-CS3x0' => true,
        'declare_strict_types' => true,
        'global_namespace_import' => ['import_classes' => false, 'import_constants' => false, 'import_functions' => false],
        'no_unused_imports' => true,
        'ordered_imports' => ['imports_order' => ['class', 'function', 'const'], 'sort_algorithm' => 'alpha'],
        'single_quote' => true,
        'trailing_comma_in_multiline' => ['elements' => ['arrays']],
        'no_empty_phpdoc' => true,
        'no_blank_lines_after_phpdoc' => true,
        'phpdoc_trim' => true,
    ]);
