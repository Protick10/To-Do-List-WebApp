<?php
session_start();
require 'db_connection.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$search_query = isset($_GET['search_query']) ? $_GET['search_query'] : '';

// Search for tasks in the database based on the search query
$sql = "SELECT * FROM tasks WHERE user_id = ? AND (task LIKE ? OR description LIKE ?) ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$search_term = "%" . $search_query . "%";
$stmt->bind_param('iss', $user_id, $search_term, $search_term);
$stmt->execute();
$search_result = $stmt->get_result();
$tasks = $search_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="container">
        <a href="tasks.php" class="navbar-brand">Task Manager</a>

        <!-- Search Form -->
        <form class="navbar-search" action="search.php" method="GET">
            <input type="text" name="search_query" placeholder="Search tasks..." value="<?php echo htmlspecialchars($search_query); ?>">
            <button type="submit">Search</button>
        </form>

        <!-- Navbar Links -->
        <ul class="navbar-nav">
            <li><a href="tasks.php">Home</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
</nav>

<!-- Search Results -->
<div class="containerr">
    <h2>Search Results for "<?php echo htmlspecialchars($search_query); ?>"</h2>

    <?php if (count($tasks) > 0): ?>
        <ul class="task-list">
            <?php foreach ($tasks as $task): ?>
                <li class="task-item">
                    <div class="task-header">
                        <strong><?php echo htmlspecialchars($task['task']); ?></strong>
                    </div>
                    <div class="task-body">
                        <p><?php echo htmlspecialchars($task['description']); ?></p>
                        <p>Due: <?php echo $task['due_date']; ?> at <?php echo $task['due_time']; ?></p>
                    </div>
                    <div class="task-footer">
                        <input type="checkbox" <?php if ($task['completed']) echo 'checked'; ?> disabled> Completed
                        <a class="btn edit-btn" href="edit_task.php?task_id=<?php echo $task['id']; ?>">Edit</a>
                        <a class="btn delete-btn" href="tasks.php?delete_task=<?php echo $task['id']; ?>" onclick="return confirm('Are you sure you want to delete this task?');">Delete</a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No tasks found matching your search query.</p>
    <?php endif; ?>
</div>

</body>
</html>
