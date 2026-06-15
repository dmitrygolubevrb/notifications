<?php

namespace App\Support;

use RuntimeException;

class EnvFile
{
    public static function set(string $key, string $value): void
    {
        $envPath = base_path('.env');

        if (! is_file($envPath)) {
            throw new RuntimeException('.env file not found.');
        }

        $line = $key.'='.$value;
        $contents = file_get_contents($envPath);

        if (preg_match('/^'.preg_quote($key, '/').'=.*$/m', $contents)) {
            $contents = preg_replace('/^'.preg_quote($key, '/').'=.*$/m', $line, $contents);
        } else {
            $contents = rtrim($contents).PHP_EOL.$line.PHP_EOL;
        }

        file_put_contents($envPath, $contents);
    }
}
