<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__);

$config = new PhpCsFixer\Config();
return $config->setRules([
    '@PSR12' => true,
    'no_unused_imports' => true,
    'ordered_imports' => [
        'imports_order' => ['class', 'function', 'const'],
        'sort_algorithm' => 'alpha'
    ],
])
    ->setFinder($finder);
