<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/repository_store.php';

$repoId = filter_input(INPUT_GET, 'repo_id', FILTER_VALIDATE_INT);
if (!$repoId) {
    http_response_code(400);
    echo 'Missing or invalid repo_id';
    exit;
}

$repo = repo_find((int)$repoId);
if (!$repo) {
    http_response_code(404);
    echo 'Repository not found. Try refreshing.';
    exit;
}

?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars((string)$repo['name'], ENT_QUOTES) ?></title>
  <style>
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; margin: 24px; }
    .card { max-width: 960px; }
    dt { font-weight: 600; margin-top: 12px; }
    dd { margin: 4px 0 0 0; }
    a { color: #0645ad; }
  </style>
</head>
<body>
  <p><a href="/">← Back</a></p>
  <div class="card">
    <h1><?= htmlspecialchars((string)$repo['name'], ENT_QUOTES) ?></h1>
    <dl>
      <dt>Stars</dt>
      <dd><?= htmlspecialchars((string)$repo['stars'], ENT_QUOTES) ?></dd>

      <dt>URL</dt>
      <dd><a href="<?= htmlspecialchars((string)$repo['url'], ENT_QUOTES) ?>" target="_blank" rel="noreferrer">Open on GitHub</a></dd>

      <dt>Description</dt>
      <dd><?= nl2br(htmlspecialchars((string)($repo['description'] ?? ''), ENT_QUOTES)) ?></dd>

      <dt>Created</dt>
      <dd><?= htmlspecialchars((string)$repo['created_at'], ENT_QUOTES) ?> UTC</dd>

      <dt>Last Push</dt>
      <dd><?= htmlspecialchars((string)$repo['pushed_at'], ENT_QUOTES) ?> UTC</dd>

      <dt>Repository ID</dt>
      <dd><?= htmlspecialchars((string)$repo['repo_id'], ENT_QUOTES) ?></dd>
    </dl>
  </div>
</body>
</html>
