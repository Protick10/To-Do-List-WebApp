<?php
require 'db_connection.php';

$username = $email = $password = "";
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    }

    // Check if username already exists
    $sql = "SELECT id FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors['username'] = "Username is already taken.";
    }
    $stmt->close();

    // Check if email is already in use
    $sql = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors['email'] = "Email is already registered.";
    }
    $stmt->close();

    // Password strength validation
    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/', $password)) {
        $errors['password'] = "Password must be at least 8 characters long, contain at least one letter, one number, and one special character.";
    }

    // If there are no errors, proceed with registration
    if (empty($errors)) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert user into the database
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sss', $username, $email, $hashed_password);

        if ($stmt->execute()) {
            echo "<p style='color: green;'>Registration successful!</p>";
            // You can redirect to login or some other page here
            // header('Location: login.php');
        } else {
            echo "Error: " . $conn->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>

<h2>Register</h2>

<form action="register.php" method="POST">
    <label for="username">Username:</label><br>
    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
    <?php if (isset($errors['username'])): ?>
        <p style="color: red;"><?php echo $errors['username']; ?></p>
    <?php endif; ?>
    <br>

    <label for="email">Email:</label><br>
    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
    <?php if (isset($errors['email'])): ?>
        <p style="color: red;"><?php echo $errors['email']; ?></p>
    <?php endif; ?>
    <br>

    <label for="password">Password:</label><br>
    <input type="password" id="password" name="password" required>
    <?php if (isset($errors['password'])): ?>
        <p style="color: red;"><?php echo $errors['password']; ?></p>
    <?php endif; ?>
    <br>

    <button type="submit">Register</button>
</form>

<p>Already have an account? <a href="login.php">Login here</a></p>

</body>
</html>
