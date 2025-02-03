<?php
session_start();

// Initialize tasks array in session if not set
if (!isset($_SESSION['tasks'])) {
    $_SESSION['tasks'] = [];
}

// Handle form submission to add a task
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_task'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Task Manager (Session-Based)</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
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

    <!-- Task List -->
    <div class="card shadow-lg mt-4">
        <div class="card-header bg-dark text-white">
            <h3 class="mb-0">Task List</h3>
        </div>
        <div class="card-body">
            <?php if (!empty($_SESSION['tasks'])): ?>
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Due Date</th>
                            <th>Priority</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_SESSION['tasks'] as $task): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($task['title']); ?></td>
                                <td><?php echo htmlspecialchars($task['description']); ?></td>
                                <td><?php echo htmlspecialchars($task['due_date']); ?></td>
                                <td><?php echo htmlspecialchars($task['priority']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center">No tasks available.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>