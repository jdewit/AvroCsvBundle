<?php
return PhpCsFixer\Config::create()
    ->setRules(
        array(
            '@Symfony' => true,
            'ordered_imports' => true,
        )
    )
    ->setUsingCache(true)
    ->setCacheFile(__DIR__.'/.php_cs.cache');
