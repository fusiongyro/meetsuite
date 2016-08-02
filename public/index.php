<?php

// set up the autoloader
set_include_path(get_include_path() . PATH_SEPARATOR . $_SERVER['DOCUMENT_ROOT'] . '/lib/');
spl_autoload_register();

echo "<p>${_SERVER['REQUEST_METHOD']} ${_SERVER['PATH_INFO']}</p>";

var_dump($_SERVER);

echo get_include_path();

$x = new Reservation();

var_dump($x);

?>