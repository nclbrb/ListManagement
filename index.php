<?php
session_start();

// Initialize tasks array in session if not set
if (!isset($_SESSION['tasks'])) {
    $_SESSION['tasks'] = [];
}

// Handle form submission to add a task
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_task'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $due_date = $_POST['due_date'];
    $priority = $_POST['priority'];

    $task = [
        'title' => $title,
        'description' => $description,
        'due_date' => $due_date,
        'priority' => $priority
    ];

    $_SESSION['tasks'][] = $task;
}

// Handle task update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_task'])) {
    $index = $_POST['task_index'];
    $_SESSION['tasks'][$index] = [
        'title' => trim($_POST['title']),
        'description' => trim($_POST['description']),
        'due_date' => $_POST['due_date'],
        'priority' => $_POST['priority']
    ];
    $message = "Task updated successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Task Manager (Session-Based)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { border-radius: 1rem; }
        .modal-content { border-radius: 1rem; }
        .btn { border-radius: 0.5rem; }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white text-center">
            <h3 class="mb-0">Create a New Task</h3>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Title:</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description:</label>
                    <textarea name="description" class="form-control" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Due Date:</label>
                    <input type="date" name="due_date" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Priority:</label>
                    <select name="priority" class="form-select">
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                    </select>
                </div>
                <button type="submit" name="add_task" class="btn btn-success w-100">Add Task</button>
            </form>
        </div>
    </div>

    <!-- Confirmation Message -->
    <?php if (isset($message)): ?>
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Task List -->
    <div class="card shadow-lg mt-4">
        <div class="card-header bg-dark text-white text-center">
            <h3 class="mb-0">Task List</h3>
        </div>
        <div class="card-body">
            <?php if (!empty($_SESSION['tasks'])): ?>
                <table class="table table-bordered table-hover">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Due Date</th>
                            <th>Priority</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_SESSION['tasks'] as $index => $task): ?>
                            <tr>
                                <td><?= htmlspecialchars($task['title']) ?></td>
                                <td><?= htmlspecialchars($task['description']) ?></td>
                                <td><?= htmlspecialchars($task['due_date']) ?></td>
                                <td><?= htmlspecialchars($task['priority']) ?></td>
                                <td class="text-center">
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $index ?>">Edit</button>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal<?= $index ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Task</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST">
                                                <input type="hidden" name="task_index" value="<?= $index ?>">
                                                <div class="mb-3">
                                                    <label class="form-label">Title:</label>
                                                    <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($task['title']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Description:</label>
                                                    <textarea name="description" class="form-control" rows="3" required><?= htmlspecialchars($task['description']) ?></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Due Date:</label>
                                                    <input type="date" name="due_date" class="form-control" value="<?= htmlspecialchars($task['due_date']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Priority:</label>
                                                    <select name="priority" class="form-select">
                                                        <option value="Low" <?= $task['priority'] == 'Low' ? 'selected' : '' ?>>Low</option>
                                                        <option value="Medium" <?= $task['priority'] == 'Medium' ? 'selected' : '' ?>>Medium</option>
                                                        <option value="High" <?= $task['priority'] == 'High' ? 'selected' : '' ?>>High</option>
                                                    </select>
                                                </div>
                                                <button type="submit" name="update_task" class="btn btn-primary w-100">Save Changes</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center">No tasks available.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>