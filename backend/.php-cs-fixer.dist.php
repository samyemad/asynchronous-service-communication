<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->name('*.php')
    ->exclude('var')
    ->exclude('vendor')
    ->exclude('node_modules');

$config = new PhpCsFixer\Config();
return $config->setRules([
    '@Symfony' => true,
    '@Symfony:risky' => true,
    '@PHP81Migration' => true,
    'array_syntax' => ['syntax' => 'short'],
    'ordered_imports' => ['sort_algorithm' => 'alpha'],
    'no_unused_imports' => true,
    'not_operator_with_successor_space' => true,
    'trailing_comma_in_multiline' => ['elements' => ['arrays']],
    'phpdoc_align' => ['align' => 'left'],
])
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setUsingCache(true);
