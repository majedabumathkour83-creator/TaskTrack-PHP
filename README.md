# TaskTrack PHP

A small task manager built with plain PHP and SQLite for the OS Lab Docker & GitHub assignment. Add tasks, mark them complete or pending, and delete them. A Docker volume keeps tasks saved independently of the container.

This is a single-user learning project with no accounts or authentication. The Docker command below makes it accessible only from your own computer.

## Stack

- PHP 8.4 and Apache in the official `php:8.4-apache` Docker image
- SQLite (a local database file; no separate database server)
- HTML and CSS, with no framework or external frontend dependencies

## Run with Docker

You need Git and a running Docker Engine. On Windows, start Docker Desktop with the WSL 2 backend and wait for the engine to be ready. PHP and SQLite do not need to be installed on your computer for this method.

Clone the repository:

```text
git clone https://github.com/majedabumathkour83-creator/TaskTrack-PHP.git
cd TaskTrack-PHP
```

Build the image from the project root:

```text
docker build -t tasktrack-php .
```

The first build downloads the base image and may take several minutes. Later builds can reuse cached layers.

Create and start the container:

```text
docker run -d --name tasktrack -p 127.0.0.1:8080:80 -v tasktrack-data:/var/www/storage tasktrack-php
```

Open <http://127.0.0.1:8080>. Check the running container with:

```text
docker ps
```

The `tasktrack` container should have an `Up` status. To inspect its logs, use `docker logs tasktrack`.

## Try it

1. Add a task with a title of 1–120 characters.
2. Use the square button to mark it complete, then click again to mark it pending.
3. Refresh the browser and confirm the task is still there.
4. Run `docker restart tasktrack`, refresh the browser, and confirm the task remains saved.
5. Use **Delete** to remove the task.

## Stop, start, and clean up

Stop the container:

```text
docker stop tasktrack
```

Start the same container again:

```text
docker start tasktrack
```

To remove the container while keeping saved tasks:

```text
docker stop tasktrack
docker rm tasktrack
```

Repeat the `docker run` command above to create a new container. Reusing the `tasktrack-data` volume restores the saved tasks.

To permanently delete the saved tasks as well, remove the volume after removing the container:

```text
docker volume rm tasktrack-data
```

## Configuration and storage

| Setting | Default |
| --- | --- |
| Browser URL | `http://127.0.0.1:8080` |
| Apache port inside the container | `80` |
| Container name | `tasktrack` |
| Docker volume | `tasktrack-data` |
| Database inside the container | `/var/www/storage/tasks.sqlite` |

No environment values are required. The optional `TASKTRACK_DATA_DIR` variable changes the database directory. If you override it, mount the volume at that location and ensure Apache's `www-data` user can write there.

The database is created automatically on first use, outside Apache's public directory. The volume preserves data when the container is removed and recreated; removing the volume deletes that data. The `.dockerignore` file excludes local database files and Git history from the build context.

If port `8080` is already in use, change the host port in the run command, for example to `-p 127.0.0.1:8081:80`, and open <http://127.0.0.1:8081>.

## Run locally without Docker

This alternative requires PHP 8.2 or later with the PDO SQLite extension enabled. Stop the Docker container first if it is using port `8080`.

From the project root:

```text
php -S 127.0.0.1:8080 -t public
```

Open <http://127.0.0.1:8080>. Stop the server with `Ctrl+C`. This development server is for local testing only.

Local mode stores tasks in `storage/tasks.sqlite`, separately from the Docker volume. The storage directory must be writable by PHP; generated data is ignored by Git.

## Project structure

```text
public/             Web entry point and stylesheet
src/                Database setup and task queries
storage/            Local SQLite data (ignored by Git)
docs/screenshots/   Assignment evidence
Dockerfile          PHP and Apache image configuration
.dockerignore       Files included in the build context
.gitignore          Files excluded from Git
```

Screenshots are available in [docs/screenshots](docs/screenshots/). The application was created for this assignment rather than adapted from an existing application.
