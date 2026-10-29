<?php

declare(strict_types=1);

function allTasks(PDO $database): array
{
    return $database->query('SELECT id, title, completed FROM tasks ORDER BY id DESC')->fetchAll();
}

function addTask(PDO $database, string $title): void
{
    $statement = $database->prepare('INSERT INTO tasks (title) VALUES (:title)');
    $statement->execute(['title' => $title]);
}

function toggleTask(PDO $database, int $id): void
{
    $statement = $database->prepare('UPDATE tasks SET completed = 1 - completed WHERE id = :id');
    $statement->execute(['id' => $id]);
}

function deleteTask(PDO $database, int $id): void
{
    $statement = $database->prepare('DELETE FROM tasks WHERE id = :id');
    $statement->execute(['id' => $id]);
}
