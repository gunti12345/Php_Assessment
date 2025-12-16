<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

function github_search_top_php_repos(int $perPage): array {
    $cfg = app_config()['github'];
    $token = $cfg['token'];

    $url = 'https://api.github.com/search/repositories';
    $query = http_build_query([
        'q' => 'language:php',
        'sort' => 'stars',
        'order' => 'desc',
        'per_page' => (string)$perPage,
        'page' => '1',
    ]);

    $headers = [
        'User-Agent: victro-code-challenge',
        'Accept: application/vnd.github+json',
        'X-GitHub-Api-Version: 2022-11-28',
    ];
    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }

    $ch = curl_init($url . '?' . $query);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_HTTPHEADER => $headers,
    ]);
    $body = curl_exec($ch);
    if ($body === false) {
        $err = curl_error($ch);
        curl_close($ch);
        throw new RuntimeException('GitHub request failed: ' . $err);
    }

    $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $decoded = json_decode($body, true);
    if (!is_array($decoded)) {
        throw new RuntimeException('GitHub response was not valid JSON');
    }
    if ($status < 200 || $status >= 300) {
        $message = $decoded['message'] ?? ('HTTP ' . $status);
        throw new RuntimeException('GitHub API error: ' . $message);
    }

    return $decoded['items'] ?? [];
}

function github_iso_to_mysql_datetime(?string $iso): string {
    if (!$iso) {
        return gmdate('Y-m-d H:i:s');
    }
    $dt = new DateTimeImmutable($iso);
    return $dt->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s');
}
