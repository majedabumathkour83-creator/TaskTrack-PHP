<?php

declare(strict_types=1);

require dirname(__DIR__) . '/src/database.php';
require dirname(__DIR__) . '/src/tasks.php';

session_start();
$_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));

function escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$error = $_SESSION['error'] ?? null;
$oldTitle = $_SESSION['old_title'] ?? '';
unset($_SESSION['error'], $_SESSION['old_title']);

try {
    $database = database();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        $action = $_POST['action'] ?? '';

        if (!is_string($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
            $_SESSION['error'] = 'Your form has expired. Please try again.';
        } elseif ($action === 'add') {
            $title = is_string($_POST['title'] ?? null) ? trim($_POST['title']) : '';

            if ($title === '' || preg_match('/\A[^\r\n]{1,120}\z/u', $title) !== 1) {
                $_SESSION['error'] = 'Enter a task title between 1 and 120 characters on one line.';
                $_SESSION['old_title'] = $title;
            } else {
                addTask($database, $title);
            }
        } elseif ($action === 'toggle' || $action === 'delete') {
            $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

            if ($id === false) {
                $_SESSION['error'] = 'Invalid task.';
            } elseif ($action === 'toggle') {
                toggleTask($database, $id);
            } else {
                deleteTask($database, $id);
            }
        } else {
            $_SESSION['error'] = 'Unknown action.';
        }

        header('Location: /', true, 303);
        exit;
    }

    $tasks = allTasks($database);
} catch (Throwable $exception) {
    error_log((string) $exception);
    http_response_code(500);
    exit('TaskTrack could not access its data. Check the server log and storage permissions.');
}

$completed = count(array_filter($tasks, fn (array $task): bool => (bool) $task['completed']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TaskTrack — Simple task manager</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <main class="app">
        <header>
            <p class="eyebrow">A little focus, every day</p>
            <h1>TaskTrack<span>.</span></h1>
            <p class="intro">Write it down. Get it done.</p>
        </header>

        <section class="card" aria-labelledby="tasks-heading">
            <div class="section-heading">
                <h2 id="tasks-heading">Your tasks</h2>
                <span class="count"><?= $completed ?> / <?= count($tasks) ?> done</span>
            </div>

            <?php if ($error !== null): ?>
                <p class="error" role="alert"><?= escape($error) ?></p>
            <?php endif; ?>

            <form method="post" action="/" class="add-form">
                <input type="hidden" name="csrf_token" value="<?= escape($_SESSION['csrf_token']) ?>">
                <input type="hidden" name="action" value="add">
                <label for="title">What do you need to do?</label>
                <div class="add-controls">
                    <input id="title" name="title" type="text" maxlength="120" required
                           placeholder="e.g. Review the Docker assignment" value="<?= escape($oldTitle) ?>" dir="auto">
                    <button type="submit" class="primary">Add task</button>
                </div>
            </form>

            <?php if ($tasks === []): ?>
                <p class="empty">Your list is clear. Add your first task above.</p>
            <?php else: ?>
                <ul class="task-list">
                    <?php foreach ($tasks as $task): ?>
                        <li class="task <?= $task['completed'] ? 'completed' : '' ?>">
                            <form method="post" action="/" class="toggle-form">
                                <input type="hidden" name="csrf_token" value="<?= escape($_SESSION['csrf_token']) ?>">
                                <input type="hidden" name="action" value="toggle">
                                <input type="hidden" name="id" value="<?= (int) $task['id'] ?>">
                                <button type="submit" class="toggle"
                                        aria-label="<?= $task['completed'] ? 'Mark as pending: ' : 'Mark as completed: ' ?><?= escape($task['title']) ?>"
                                        aria-pressed="<?= $task['completed'] ? 'true' : 'false' ?>">
                                    <span aria-hidden="true"><?= $task['completed'] ? '&#10003;' : '' ?></span>
                                </button>
                            </form>
                            <span class="task-title" dir="auto"><?= escape($task['title']) ?></span>
                            <form method="post" action="/">
                                <input type="hidden" name="csrf_token" value="<?= escape($_SESSION['csrf_token']) ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int) $task['id'] ?>">
                                <button type="submit" class="delete" aria-label="Delete: <?= escape($task['title']) ?>">Delete</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
        <footer>A simple place to keep things moving.</footer>
    </main>
</body>
</html>
