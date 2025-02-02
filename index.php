<?php
require 'db.php'; // Database connection

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $priority = $_POST['priority'];

    $stmt = $conn->prepare("INSERT INTO tasks (title, description, due_date, priority) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $title, $description, $due_date, $priority);

    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>Task created successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error creating task.</div>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Task</title>
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
            <?php echo $message; ?>
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

                <button type="submit" class="btn btn-success w-100">Create Task</button>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
