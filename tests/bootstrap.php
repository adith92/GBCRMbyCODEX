<?php

declare(strict_types=1);

// Guard for PHP 8.5 environments where max_execution_time=0 can break long-running CLI test bootstrap.
if ((int) ini_get('max_execution_time') <= 0) {
    ini_set('max_execution_time', '3600');
}

require __DIR__.'/../vendor/autoload.php';
