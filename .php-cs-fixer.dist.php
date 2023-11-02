<?php

$dirs = [
    'DependencyInjection',
    'Form',
    'tests',
];

foreach ($dirs as $dir) {
    if (!file_exists($dir)) {
        exit(0);
    }
}

$finder = (new \PhpCsFixer\Finder())
    ->in($dirs);

return (new \PhpCsFixer\Config())
    ->setRules(array(
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'phpdoc_to_comment' => false,
    ))
    ->setRiskyAllowed(true)
    ->setFinder($finder);
