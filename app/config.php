<?php

declare(strict_types=1);

function env(string $key, ?string $default = null): ?string {
    $value = getenv($key);
    if ($value === false) {
        return $default;
    }
    $value = trim((string)$value);
    return $value === '' ? $default : $value;
}

function int_env(string $key, int $default): int {
    $value = env($key);
    if ($value === null) {
        return $default;
    }
    $parsed = filter_var($value, FILTER_VALIDATE_INT);
    return $parsed === false ? $default : (int)$parsed;
}

function app_config(): array {
    return [
        'github' => [
            'token' => env('GITHUB_TOKEN'),
            'per_page' => max(1, min(100, int_env('GITHUB_PER_PAGE', 50))),
        ],
        'db' => [
            'host' => env('DB_HOST', 'db'),
            'port' => int_env('DB_PORT', 3306),
            'database' => env('DB_DATABASE', 'app'),
            'username' => env('DB_USERNAME', 'app'),
            'password' => env('DB_PASSWORD', 'app'),
        ],
    ];
}
