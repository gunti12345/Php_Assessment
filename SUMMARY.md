# End-to-End Summary (VUMC VICTR PHP Code Challenge)

## Source Requirements (from `VUMC VICTR PHP Code Challenge (1).docx`)

- Use the GitHub API to retrieve the most-starred public PHP projects.
- Load the data from GitHub into a database table in MySQL or MariaDB with fields:
  - Repository ID
  - Name
  - URL
  - Created date
  - Last push date
  - Description
  - Number of stars
- Web page:
  - Display a simple table showing project name and number of stars.
  - Allow clicking a project to see full details.
  - Provide a way to refresh the database with the latest GitHub data.
- Provide a `README.md` with local setup steps.
- Preferred: containerized solution (Docker Compose).

## What Was Built

A minimal PHP + MariaDB app (Docker Compose) in this repository that:

- Fetches GitHub “top starred PHP repos” using the Search API:
  - `q=language:php&sort=stars&order=desc&per_page=<N>`
- Stores/updates results into MariaDB table `repositories` (upsert by `repo_id`).
- UI:
  - `GET /` shows a table: project name + stars.
  - Clicking a project goes to `GET /repo.php?repo_id=<id>` for full details.
  - `POST /refresh.php` refreshes the DB from GitHub and redirects back to `/`.

## Key Files

- `docker-compose.yml` — runs `web` (PHP/Apache) + `db` (MariaDB).
- `docker/mariadb/init.sql` — creates the `repositories` table.
- `public/index.php` — list page (name + stars).
- `public/repo.php` — details page.
- `public/refresh.php` — refresh endpoint (POST-only).
- `app/github.php` — GitHub Search API call (supports optional token).
- `app/repository_store.php` — DB upsert/list/detail queries.
- `README.md` — how to run locally.
- `.env.example` — optional config (e.g., `GITHUB_TOKEN`, `GITHUB_PER_PAGE`).

## How To Run

```zsh
cd "/Users/nimishgoel/Desktop/test/php/VUMC VICTR PHP Code Challenge/Php_Assessment"
cp .env.example .env
docker compose up --build
```

Open:

- http://localhost:8080

Then click **Refresh from GitHub**.

## End-to-End Validation Performed

After starting containers:

- `GET http://localhost:8080/` returned `200 OK`.
- `POST http://localhost:8080/refresh.php` returned `302 Found` with `Location: /`.
- Database was populated successfully:
  - `SELECT COUNT(*) FROM repositories;` returned `50` (default `GITHUB_PER_PAGE=50`).
- Details page rendered for a stored repo:
  - `GET /repo.php?repo_id=3482588` returned `200 OK`.

## Bug Found & Fixed During Validation

- Symptom: `POST /refresh.php` returned `500` with a MariaDB SQL syntax error.
- Root cause: the UPSERT SQL string in `app/repository_store.php` contained literal `\n` sequences (single-quoted PHP strings don’t translate `\n` into newlines), which MariaDB treated as invalid syntax.
- Fix: removed the literal `\n` sequences from the prepared SQL statement.

## Git / GitHub Status

- A root commit was created locally: `Implement VUMC VICTR PHP Code Challenge app`.
- Push attempt failed with `403`:
  - `Permission to gunti12345/Php_Assessment.git denied to nimishgoel5600.`

This indicates the machine’s current Git credentials are authenticated as a different GitHub user than `gunti12345`.

### How to Push as the Correct User (recommended)

- Use GitHub’s recommended authentication method (PAT or GitHub CLI), but **do not paste passwords/tokens into chat**.
- Option A (recommended): install GitHub CLI and login:

```zsh
brew install gh
gh auth login
```

Then retry:

```zsh
git push -u origin main
```

- Option B: create a new GitHub Personal Access Token in the browser (fine-grained token with repo write access) and use it via Git credential manager/keychain.

## Notes / Security

- Any GitHub token previously shared in chat should be revoked immediately: https://github.com/settings/tokens
