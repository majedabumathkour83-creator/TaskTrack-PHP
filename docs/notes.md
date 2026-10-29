# Technical Notes

## Docker problem and solution

The first build attempt failed with a connection error mentioning `dockerDesktopLinuxEngine`. The Docker command was installed, but the Linux engine was unavailable.

Opening Docker Desktop and waiting for its engine to start allowed the build to proceed. The lesson is that having the Docker CLI installed is different from having a running Docker Engine.

The first successful build took about 18.5 minutes, mostly downloading and preparing the PHP base image. Although the progress appeared stuck, the log eventually reported `FINISHED` and exported `tasktrack-php:latest`.

## Git/GitHub lesson

Review staged files before committing. Running `git add .` initially included `instructions.txt`, which was the assignment brief rather than application code. We removed it from staging with `git rm --cached instructions.txt` and added `/instructions.txt` to `.gitignore`.

Adding a file to `.gitignore` does not remove it from staging if it was already added. The same ignore file also keeps local SQLite data out of the repository.

The application and Docker setup were saved as separate commits with descriptive messages. Docker work uses the `feature/docker-setup` branch so it can be reviewed through a pull request before merging into `main`.

## Verification completed

- Built the `tasktrack-php` image successfully.
- Confirmed the `tasktrack` container had an `Up` status using `docker ps`.
- Opened the application at `http://127.0.0.1:8080` and added a task.
- Restarted the container with `docker restart tasktrack`, refreshed the browser, and confirmed the task remained saved.
- Saved screenshots of the build, running container, and working application in `docs/screenshots/`.

The run command mounts the named volume `tasktrack-data` at `/var/www/storage`, outside Apache's public directory, to keep the SQLite database independent of the container.
