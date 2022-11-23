<?php

return [
    'app_path' => [
        __DIR__ . '/app/Command',
        '@minicli/command-help',
        '@minicli/command-stencil'
    ],
    'builders' => [
        'default' => "Builders\\DefaultBuilder",
        'test' => "Builders\\TestBuilder"
    ],
    'templatesDir' => __DIR__ . '/templates',
    'debug' => true
];
