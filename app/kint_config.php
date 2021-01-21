<?php

declare(strict_types=1);

if (class_exists('Kint')) {
    Kint\Renderer\RichRenderer::$folder = false;

    function ddd(...$v){
        d(...$v);
        exit;
    }
    \Kint::$aliases[] = 'ddd';
}
