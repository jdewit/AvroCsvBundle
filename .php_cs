<?php

return PhpCsFixer\Config::create()
    ->setRules(
        [
            '@Symfony' => true,
            'ordered_imports' => true,
        ]
    )
    ->setUsingCache(true)
    ->setCacheFile(__DIR__.'/.php_cs.cache');
