<?php
session_start();

if (!isset($_SESSION['tasks'])) {
    $_SESSION['tasks'] = [];
}

if (!isset($_SESSION['users'])) {
    $_SESSION['users'] = ['user1', 'user2', 'user3']; 
}

if (!isset($_SESSION['notifications'])) {
    $_SESSION['notifications'] = [];
}

// Handle form submission to add a task
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_task'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $due_date = $_POST['due_date'];
    $priority = $_POST['priority'];
    $assigned_user = $_POST['assigned_user'];

    // Add default status "To Do"
    $task = [
        'title' => $title,
        'description' => $description,
        'due_date' => $due_date,
        'priority' => $priority,
        'assigned_user' => $assigned_user,
        'status' => 'To Do',
        'comments' => []
    ];

    $_SESSION['tasks'][] = $task;

    // Sort the tasks by due date
    usort($_SESSION['tasks'], function($a, $b) {
        return strtotime($a['due_date']) - strtotime($b['due_date']);
    });

    $_SESSION['notifications'][$assigned_user][] = "You have been assigned a new task: $title";

    $_SESSION['new_notification_user'] = $assigned_user;
}

// Handle task update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_task'])) {
    $index = $_POST['task_index'];
    $status = $_POST['status'];  // Get status from the form
    $_SESSION['tasks'][$index] = [
        'title' => trim($_POST['title']),
        'description' => trim($_POST['description']),
        'due_date' => $_POST['due_date'],
        'priority' => $_POST['priority'],
        'assigned_user' => $_POST['assigned_user'],
        'status' => $status  // Update the status
    ];

    // Sort the tasks by due date after update
    usort($_SESSION['tasks'], function($a, $b) {
        return strtotime($a['due_date']) - strtotime($b['due_date']);
    });

    $message = "Task updated successfully!";
}

// Handle task deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_task'])) {
    $index = $_POST['task_index'];
    unset($_SESSION['tasks'][$index]);
    $_SESSION['tasks'] = array_values($_SESSION['tasks']); // Re-index array after deletion
    // Sort the tasks after deletion
    usort($_SESSION['tasks'], function($a, $b) {
        return strtotime($a['due_date']) - strtotime($b['due_date']);
    });
    $message = "Task deleted successfully!";
}

// Handle adding a comment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_comment'])) {
    $task_index = $_POST['task_index'];
    $comment = trim($_POST['comment']);
    
    if (!empty($comment)) {
        if (!in_array($comment, $_SESSION['tasks'][$task_index]['comments'])) {
            $_SESSION['tasks'][$task_index]['comments'][] = $comment;
        }
    }
}

// Filter tasks by status and priority
$status_filter = isset($_POST['status_filter']) ? $_POST['status_filter'] : '';
$priority_filter = isset($_POST['priority_filter']) ? $_POST['priority_filter'] : '';

$filtered_tasks = array_filter($_SESSION['tasks'], function($task) use ($status_filter, $priority_filter) {
    $status_match = ($status_filter == '' || $task['status'] == $status_filter);
    $priority_match = ($priority_filter == '' || $task['priority'] == $priority_filter);
    return $status_match && $priority_match;
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Task Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="index.css">

</head>
<body>
<div class="container mt-5">
    <?php if (isset($_SESSION['new_notification_user'])): ?>
        <div class="alert-container">
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <strong>Notification for <?= htmlspecialchars($_SESSION['new_notification_user']) ?>!</strong> You have been assigned a new task.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>

        <?php unset($_SESSION['new_notification_user']); ?>
    <?php endif; ?>

    <div class="task-form">
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
                    <div class="mb-3">
                        <label class="form-label">Assign to:</label>
                        <select name="assigned_user" class="form-select">
                            <?php foreach ($_SESSION['users'] as $user): ?>
                                <option value="<?= $user ?>"><?= $user ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" name="add_task" class="btn btn-success w-100">Add Task</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Task List Section -->
    <div class="task-list">
        <div class="card shadow-lg">
            <div class="card-header bg-dark text-white text-center">
                <h3 class="mb-0">Task List</h3>
            </div>
            <div class="card-body">
                <form method="POST" class="mb-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <select name="status_filter" class="form-select" style="width: 350px;">
                                <option value="">Filter by Status</option>
                                <option value="To Do" <?= $status_filter == 'To Do' ? 'selected' : '' ?>>To Do</option>
                                <option value="In Progress" <?= $status_filter == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                                <option value="Completed" <?= $status_filter == 'Completed' ? 'selected' : '' ?>>Completed</option>
                            </select>
                        </div>
                        <div>
                            <select name="priority_filter" class="form-select" style="width: 350px;">
                                <option value="">Filter by Priority</option>
                                <option value="Low" <?= $priority_filter == 'Low' ? 'selected' : '' ?>>Low</option>
                                <option value="Medium" <?= $priority_filter == 'Medium' ? 'selected' : '' ?>>Medium</option>
                                <option value="High" <?= $priority_filter == 'High' ? 'selected' : '' ?>>High</option>
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </div>
                </form>

                <?php if (!empty($filtered_tasks)): ?>
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Due Date</th>
                                <th>Priority</th>
                                <th>Assigned User</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($filtered_tasks as $index => $task): ?>
                                <tr>
                                    <td><?= htmlspecialchars($task['title']) ?></td>
                                    <td><?= htmlspecialchars($task['description']) ?></td>
                                    <td><?= htmlspecialchars($task['due_date']) ?></td>
                                    <td><?= htmlspecialchars($task['priority']) ?></td>
                                    <td><?= htmlspecialchars($task['assigned_user']) ?></td>
                                    <td><?= isset($task['status']) ? htmlspecialchars($task['status']) : 'Not Set' ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $index ?>">Edit</button>
                                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#commentModal<?= $index ?>">Comments</button>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="task_index" value="<?= $index ?>">
                                            <button type="submit" name="delete_task" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </td>
                                </tr>

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
                                                        <input type="text" name="title" value="<?= htmlspecialchars($task['title']) ?>" class="form-control" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Description:</label>
                                                        <textarea name="description" class="form-control" rows="3" required><?= htmlspecialchars($task['description']) ?></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Due Date:</label>
                                                        <input type="date" name="due_date" value="<?= $task['due_date'] ?>" class="form-control" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Priority:</label>
                                                        <select name="priority" class="form-select">
                                                            <option value="Low" <?= $task['priority'] == 'Low' ? 'selected' : '' ?>>Low</option>
                                                            <option value="Medium" <?= $task['priority'] == 'Medium' ? 'selected' : '' ?>>Medium</option>
                                                            <option value="High" <?= $task['priority'] == 'High' ? 'selected' : '' ?>>High</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Assigned User:</label>
                                                        <select name="assigned_user" class="form-select">
                                                            <?php foreach ($_SESSION['users'] as $user): ?>
                                                                <option value="<?= $user ?>" <?= $task['assigned_user'] == $user ? 'selected' : '' ?>><?= $user ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Status:</label>
                                                        <select name="status" class="form-select">
                                                            <option value="To Do" <?= $task['status'] == 'To Do' ? 'selected' : '' ?>>To Do</option>
                                                            <option value="In Progress" <?= $task['status'] == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                                                            <option value="Completed" <?= $task['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                                        </select>
                                                    </div>
                                                    <button type="submit" name="update_task" class="btn btn-success w-100">Update Task</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No tasks to display.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
