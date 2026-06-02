<?php

declare(strict_types=1);

$files = [
    __DIR__.'/../vendor/composer/autoload_psr4.php',
    __DIR__.'/../vendor/composer/autoload_static.php',
];

$replacements = [
    ", \$vendorDir . '/laravel/pint/app'" => '',
    ", \$vendorDir . '/laravel/pint/database/seeders'" => '',
    ", \$vendorDir . '/laravel/pint/database/factories'" => '',
    ", __DIR__ . '/..' . '/laravel/pint/app'" => '',
    ", __DIR__ . '/..' . '/laravel/pint/database/seeders'" => '',
    ", __DIR__ . '/..' . '/laravel/pint/database/factories'" => '',
];

foreach ($files as $file) {
    if (! is_file($file)) {
        continue;
    }

    $contents = file_get_contents($file);

    if ($contents === false) {
        fwrite(STDERR, "Unable to read {$file}".PHP_EOL);
        exit(1);
    }

    $updated = str_replace(array_keys($replacements), array_values($replacements), $contents);

    if ($updated !== $contents) {
        file_put_contents($file, $updated);
    }
}
