<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/repository_store.php';

$repos = repo_list();

?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Top Starred PHP Repos</title>
  <style>
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; margin: 24px; }
    table { border-collapse: collapse; width: 100%; max-width: 960px; }
    th, td { border: 1px solid #ddd; padding: 8px 10px; }
    th { background: #f6f6f6; text-align: left; }
    .row { display: flex; gap: 12px; align-items: center; margin-bottom: 16px; }
    .btn { padding: 8px 12px; border: 1px solid #333; background: #fff; cursor: pointer; }
    .muted { color: #666; font-size: 14px; }
  </style>
</head>
<body>
  <h1>Top Starred PHP Projects</h1>
  <div class="row">
    <form method="post" action="/refresh.php">
      <button class="btn" type="submit">Refresh from GitHub</button>
    </form>
    <div class="muted">
      Click a project for details. If the list is empty, refresh first.
    </div>
  </div>

  <table>
    <thead>
      <tr>
        <th>Project</th>
        <th>Stars</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!$repos): ?>
        <tr><td colspan="2">No data yet.</td></tr>
      <?php else: ?>
        <?php foreach ($repos as $r): ?>
          <tr>
            <td>
              <a href="/repo.php?repo_id=<?= htmlspecialchars((string)$r['repo_id'], ENT_QUOTES) ?>">
                <?= htmlspecialchars((string)$r['name'], ENT_QUOTES) ?>
              </a>
            </td>
            <td><?= htmlspecialchars((string)$r['stars'], ENT_QUOTES) ?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</body>
</html>
