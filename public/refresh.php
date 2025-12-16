<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/github.php';
require_once __DIR__ . '/../app/repository_store.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo 'Method not allowed';
    exit;
}

$cfg = app_config();
$perPage = (int)$cfg['github']['per_page'];

try {
    $items = github_search_top_php_repos($perPage);
    $toUpsert = [];
    foreach ($items as $item) {
        if (!is_array($item)) {
            continue;
        }
        $toUpsert[] = [
            ':repo_id' => (int)($item['id'] ?? 0),
            ':name' => (string)($item['full_name'] ?? $item['name'] ?? ''),
            ':url' => (string)($item['html_url'] ?? ''),
            ':created_at' => github_iso_to_mysql_datetime($item['created_at'] ?? null),
            ':pushed_at' => github_iso_to_mysql_datetime($item['pushed_at'] ?? null),
            ':description' => $item['description'] ?? null,
            ':stars' => (int)($item['stargazers_count'] ?? 0),
        ];
    }
    repo_upsert_many($toUpsert);
    header('Location: /');
    exit;
} catch (Throwable $e) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Refresh failed: " . $e->getMessage();
}
