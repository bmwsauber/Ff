<?php

define('PATH_ROOT', getcwd());
define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);
define('BASE_URL', $_SERVER['SERVER_NAME']);

require_once PATH_ROOT. DS .'lib'. DS .'autoload.php';

Lib_Autoload::register();


$fishing = Ff::run();
