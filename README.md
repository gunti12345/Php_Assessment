# PHP GitHub Stars (VUMC VICTR Challenge)

This app fetches the most-starred public **PHP** repositories from the GitHub API, stores them in **MariaDB**, and displays:

- A list table with repository name + stars
- A details page for a selected repository
- A refresh action to re-fetch and update the database

## Prereqs

- Docker Desktop (includes `docker compose`)

## Run locally (Docker Compose)

From this repo root:

```zsh
cp .env.example .env
docker compose up --build
```

Open:

- http://localhost:8080

Click **Refresh from GitHub** to load/update the database.

## Configuration

Edit `.env` (optional but recommended):

- `GITHUB_TOKEN` – avoids GitHub API rate limits. If blank, the app uses unauthenticated requests.
- `GITHUB_PER_PAGE` – number of repos fetched per refresh (1–100)

Database env vars in `.env` should match `docker-compose.yml`.

## What gets stored

MariaDB table `repositories` fields:

- Repository ID
- Name
- URL
- Created date
- Last push date
- Description
- Number of stars

## Notes

- GitHub API endpoint used: Search Repositories with `q=language:php&sort=stars&order=desc`.
