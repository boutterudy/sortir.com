<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';
if($_SERVER['APP_ENV'] == 'prod') $trustedProxies[] = $_SERVER['REMOTE_ADDR'];

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
