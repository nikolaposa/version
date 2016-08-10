<?php

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::PSR2_LEVEL)
    ->fixers([
        '-psr0',
        'join_function',
        'object_operator',
        'remove_lines_between_uses',
        'short_array_syntax',
        'standardize_not_equal',
        'unused_use',
        'whitespacy_lines',
    ])
    ->finder(
        Symfony\CS\Finder\DefaultFinder::create()
            ->in(__DIR__ . '/src')
            ->in(__DIR__ . '/tests')
    );
