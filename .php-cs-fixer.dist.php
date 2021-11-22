<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return (new PhpCsFixer\Config())
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
    ->setFinder(
        (new PhpCsFixer\Finder())
            ->in([
                __DIR__,
            ])
            ->notPath('#/Fixtures/#')
            ->append([__FILE__])
    )
    ->setCacheFile(__DIR__.'/.php-cs-fixer.cache');
