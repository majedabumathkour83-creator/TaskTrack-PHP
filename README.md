# TaskTrack PHP

A small task manager built with plain PHP and SQLite for the OS Lab Docker & GitHub assignment. Add tasks, mark them complete or pending, and delete them. Tasks remain saved when the application stops.

## Stack

- PHP 8.2 or later with PDO SQLite enabled
- SQLite (a local database file; no separate database server)
- HTML and CSS, with no framework or external frontend dependencies

## Run locally

From the project root, run:

```sh
php -S 127.0.0.1:8080 -t public
```

Open <http://127.0.0.1:8080>. Stop the server with `Ctrl+C`.
The PHP development server is for local testing only.

## Try it

1. Add a task with a title of 1–120 characters.
2. Use the square button to mark it complete, then click again to mark it pending.
3. Refresh the page or restart the server to confirm tasks are saved.
4. Use **Delete** to remove the task.

## Project structure

```text
public/       Web entry point and stylesheet
src/          Database setup and task queries
storage/      Generated SQLite database (ignored by Git)
```

On first use, the app creates `storage/tasks.sqlite`. The storage directory must be writable by PHP and remains outside the public web directory. `TASKTRACK_DATA_DIR` can override the storage directory for a container volume later.

This is a single-user learning project. It has no accounts or authentication; run it locally.

Docker configuration and assignment evidence will be added in the next stages after local verification.
