<?php

declare(strict_types=1);

function database(): PDO
{
    $directory = getenv('TASKTRACK_DATA_DIR') ?: dirname(__DIR__) . '/storage';

    if (!is_dir($directory) && !mkdir($directory, 0770, true) && !is_dir($directory)) {
        throw new RuntimeException('Could not create the data directory.');
    }

    $database = new PDO('sqlite:' . $directory . '/tasks.sqlite');
    $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $database->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $database->exec('CREATE TABLE IF NOT EXISTS tasks (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        completed INTEGER NOT NULL DEFAULT 0
    )');

    return $database;
}
