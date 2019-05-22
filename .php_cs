<?php
return PhpCsFixer\Config::create()
    ->setRules(
        [
            '@Symfony' => true,
            'array_syntax' => ['syntax' => 'short'],
            'ordered_imports' => [
                'imports_order' => [
                    'class',
                    'function',
                    'const',
                ],
                'sort_algorithm' => 'alpha',
            ],
            'ordered_class_elements' => true,
            'phpdoc_order' => true,
            'header_comment' => ['header' => "For the full copyright and license information, please view the LICENSE\nfile that was distributed with this source code."],
        ]
    )
    ->setCacheFile(__DIR__ . '/.php_cs.cache');
