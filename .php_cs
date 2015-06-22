<?php

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::SYMFONY_LEVEL)
    ->setUsingCache(true)
    ->finder(
        Symfony\CS\Finder\DefaultFinder::create()
            ->in(array('src/'))
    )
;
