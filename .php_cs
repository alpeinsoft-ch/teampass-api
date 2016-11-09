<?php

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        'no_unreachable_default_argument_value' => false,
        'no_useless_else' => true,
        'ordered_class_elements' => true,
        'ordered_imports' => true,
        'psr4' => true,
        'short_array_syntax' => true,
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->notName('*.dist')
            ->notName('*.yml')
            ->notName('*.twig')
            ->in(['src/'])
    )
;
