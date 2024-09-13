<?php
session_start();
require 'db_connection.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$errors = [];

// Handle Add Task
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_task'])) {
    $task = $_POST['task'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $due_time = $_POST['due_time'];

    $sql = "INSERT INTO tasks (user_id, task, description, due_date, due_time, completed, created_at) 
            VALUES (?, ?, ?, ?, ?, 0, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('issss', $user_id, $task, $description, $due_date, $due_time);

    if ($stmt->execute()) {
        header('Location: tasks.php');
        exit;
    } else {
        $errors[] = "Error adding task: " . $conn->error;
    }

    $stmt->close();
}

// Handle Task Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_task'])) {
    $task_id = $_POST['task_id'];
    $task = $_POST['task'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $due_time = $_POST['due_time'];
    $completed = isset($_POST['completed']) ? 1 : 0;

    $sql = "UPDATE tasks SET task = ?, description = ?, due_date = ?, due_time = ?, completed = ? WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssiii', $task, $description, $due_date, $due_time, $completed, $task_id, $user_id);

    if ($stmt->execute()) {
        header('Location: tasks.php');
        exit;
    } else {
        $errors[] = "Error updating task: " . $conn->error;
    }

    $stmt->close();
}

// Handle Delete Task
if (isset($_GET['delete_task'])) {
    $task_id = $_GET['delete_task'];
    $sql = "DELETE FROM tasks WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $task_id, $user_id);

    if ($stmt->execute()) {
        header('Location: tasks.php');
        exit;
    }

    $stmt->close();
}

// Retrieve all incomplete tasks for the logged-in user
$sql = "SELECT * FROM tasks WHERE user_id = ? AND completed = 0 ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$tasks = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Retrieve all completed tasks for the logged-in user
$sql = "SELECT * FROM tasks WHERE user_id = ? AND completed = 1 ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$completed_tasks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasks</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function toggleForm() {
            var form = document.getElementById('task-form');
            var button = document.getElementById('toggle-form-btn');
            if (form.style.display === 'none' || form.style.display === '') {
                form.style.display = 'block';
                button.innerText = 'Cancel';
            } else {
                form.style.display = 'none';
                button.innerText = 'Add Task';
            }
        }

        function toggleEditForm(taskId) {
            const taskView = document.getElementById('task-view-' + taskId);
            const editForm = document.getElementById('edit-form-' + taskId);
            if (editForm.style.display === 'none' || editForm.style.display === '') {
                editForm.style.display = 'block';
                taskView.style.display = 'none';
            } else {
                editForm.style.display = 'none';
                taskView.style.display = 'block';
            }
        }
    </script>
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="container">
        <a href="tasks.php" class="navbar-brand">Task Manager</a>
        <ul class="navbar-nav">
            <li><a href="tasks.php">Home</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
</nav>

<!-- Main Content -->
<div class="container">
    <h2>Your Tasks</h2>

    <?php if (!empty($errors)): ?>
        <div class="error-message">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Add Task Button -->
    <button id="toggle-form-btn" class="btn" onclick="toggleForm()">Add Task</button>

    <!-- Add Task Form (Initially Hidden) -->
    <form id="task-form" class="task-form" action="tasks.php" method="POST" style="display: none;">
        <label for="task">Task:</label>
        <input type="text" id="task" name="task" placeholder="Task title" required>

        <label for="description">Description:</label>
        <textarea id="description" name="description" placeholder="Task description" required></textarea>

        <label for="due_date">Due Date:</label>
        <input type="date" id="due_date" name="due_date" required>

        <label for="due_time">Due Time:</label>
        <input type="time" id="due_time" name="due_time" required>

        <button class="btn add-btn" type="submit" name="add_task">Add Task</button>
        <button type="button" class="btn cancel-btn" onclick="toggleForm()">Cancel</button>
    </form>

    <hr>

    <!-- Task List -->
    <h3>Task List</h3>
    <?php if (count($tasks) > 0): ?>
        <ul class="task-list">
            <?php foreach ($tasks as $task): ?>
                <li class="task-item">
                    <div id="task-view-<?php echo $task['id']; ?>" class="task-view">
                        <div class="task-header">
                            <strong><?php echo htmlspecialchars($task['task']); ?></strong>
                        </div>
                        <div class="task-body">
                            <p><?php echo htmlspecialchars($task['description']); ?></p>
                            <p>Due: <?php echo $task['due_date']; ?> at <?php echo $task['due_time']; ?></p>
                        </div>
                        <div class="task-footer">
                            <input type="checkbox" <?php if ($task['completed']) echo 'checked'; ?> disabled> Completed
                            <button class="btn edit-btn" onclick="toggleEditForm(<?php echo $task['id']; ?>)">Edit</button>
                            <a class="btn delete-btn" href="tasks.php?delete_task=<?php echo $task['id']; ?>" onclick="return confirm('Are you sure you want to delete this task?');">Delete</a>
                        </div>
                    </div>

                    <!-- Edit Task Form (Initially Hidden) -->
                    <form id="edit-form-<?php echo $task['id']; ?>" class="edit-form" action="tasks.php" method="POST">
                        <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                        <label for="task">Task:</label>
                        <input type="text" name="task" value="<?php echo htmlspecialchars($task['task']); ?>" required>
                        <label for="description">Description:</label>
                        <textarea name="description" required><?php echo htmlspecialchars($task['description']); ?></textarea>
                        <label for="due_date">Due Date:</label>
                        <input type="date" name="due_date" value="<?php echo $task['due_date']; ?>" required>
                        <label for="due_time">Due Time:</label>
                        <input type="time" name="due_time" value="<?php echo $task['due_time']; ?>" required>
                        <label for="completed">Completed:</label>
                        <input type="checkbox" name="completed" <?php if ($task['completed']) echo 'checked'; ?>>
                        <button class="btn" type="submit" name="update_task">Update</button>
                        <button class="btn cancel-btn" type="button" onclick="toggleEditForm(<?php echo $task['id']; ?>)">Cancel</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No tasks found.</p>
    <?php endif; ?>

    <hr>

    <!-- Completed Task History -->
    <h3>Completed Task History</h3>
    <?php if (count($completed_tasks) > 0): ?>
        <ul class="task-list">
            <?php foreach ($completed_tasks as $task): ?>
                <li class="task-item">
                    <div class="task-view">
                        <div class="task-header">
                            <strong><?php echo htmlspecialchars($task['task']); ?></strong>
                        </div>
                        <div class="task-body">
                            <p><?php echo htmlspecialchars($task['description']); ?></p>
                            <p>Due: <?php echo $task['due_date']; ?> at <?php echo $task['due_time']; ?></p>
                        </div>
                        <div class="task-footer">
                            <input type="checkbox" checked disabled> Completed
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No completed tasks found.</p>
    <?php endif; ?>
</div>

</body>
</html>
