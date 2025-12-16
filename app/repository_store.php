<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';

function repo_upsert_many(array $repos): int {
    if (!$repos) {
        return 0;
    }

    $pdo = db();
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare(
            'INSERT INTO repositories (repo_id, name, url, created_at, pushed_at, description, stars) '
            . 'VALUES (:repo_id, :name, :url, :created_at, :pushed_at, :description, :stars) '
            . 'ON DUPLICATE KEY UPDATE '
            . 'name=VALUES(name), '
            . 'url=VALUES(url), '
            . 'created_at=VALUES(created_at), '
            . 'pushed_at=VALUES(pushed_at), '
            . 'description=VALUES(description), '
            . 'stars=VALUES(stars)'
        );

        $count = 0;
        foreach ($repos as $repo) {
            $ok = $stmt->execute($repo);
            if ($ok) {
                $count++;
            }
        }
        $pdo->commit();
        return $count;
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function repo_list(int $limit = 200): array {
    $pdo = db();
    $stmt = $pdo->prepare('SELECT repo_id, name, stars FROM repositories ORDER BY stars DESC, name ASC LIMIT :lim');
    $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function repo_find(int $repoId): ?array {
    $pdo = db();
    $stmt = $pdo->prepare('SELECT * FROM repositories WHERE repo_id = :id');
    $stmt->execute([':id' => $repoId]);
    $row = $stmt->fetch();
    return $row ?: null;
}
