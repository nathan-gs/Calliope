<?php
error_reporting(-1);

require_once __DIR__.'/vendor/UniversalClassLoader/UniversalClassLoader.php';

$loader = new \UniversalClassLoader\UniversalClassLoader();

$loader->registerNamespaces(array(
    'Calliope\\Tests'   => __DIR__,
    'Calliope'   => __DIR__.'/../src',
));

$loader->register();