<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in(['src/'])
;

return Symfony\CS\Config\Config::create()
    ->finder($finder)
    ->level(\Symfony\CS\FixerInterface::PSR2_LEVEL)
    ->fixers([
        'ordered_use',
        'short_array_syntax',
        'unused_use',
    ])
    ->setUsingCache(true)
;
