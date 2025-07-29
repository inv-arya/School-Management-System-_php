<?php
session_start();
include 'inc/db.php';

$error = '';
$username = 'admin';
$password = 'admin';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input_user = $_POST['username'];
    $input_pass = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM login_attempts WHERE username = ?");
    $stmt->bind_param("s", $input_user);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $current_time = new DateTime();
    $lockout = false;

    if ($row) {
        $last_attempt = new DateTime($row['last_attempt']);
        $interval = $current_time->getTimestamp() - $last_attempt->getTimestamp();

        if ($row['attempts'] >= 3 && $interval < 300) {
            $remaining = 300 - $interval;
            $minutes = floor($remaining / 60);
            $seconds = $remaining % 60;
            $lockout = true;
            $error = "Too many attempts. Try again in {$minutes}m {$seconds}s.";
        } elseif ($row['attempts'] >= 3 && $interval >= 300) {
            // Reset lockout after 5 minutes
            $conn->query("DELETE FROM login_attempts WHERE username = '$input_user'");
        }
    }

    if (!$lockout) {
        if ($input_user === $username && $input_pass === $password) {
            $_SESSION['admin_logged_in'] = true;
            $conn->query("DELETE FROM login_attempts WHERE username = '$input_user'");
            header("Location: register_student.php");
            exit();
        } else {
            if ($row) {
                $attempts = $row['attempts'] + 1;
                $stmt = $conn->prepare("UPDATE login_attempts SET attempts = ?, last_attempt = NOW() WHERE username = ?");
                $stmt->bind_param("is", $attempts, $input_user);
            } else {
                $attempts = 1;
                $stmt = $conn->prepare("INSERT INTO login_attempts (username, attempts, last_attempt) VALUES (?, ?, NOW())");
                $stmt->bind_param("si", $input_user, $attempts);
            }
            $stmt->execute();
            $error = "Invalid credentials.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - School Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container mt-5" style="max-width: 400px;">
    <div class="card shadow-sm">
      <div class="card-body">
        <h3 class="card-title text-center mb-4">Admin Login</h3>
        
        <?php if ($error): ?>
          <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
          <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" name="username" id="username" class="form-control" required autofocus>
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" id="password" class="form-control" required>
          </div>

          <div class="d-grid">
            <button type="submit" class="btn btn-primary">Login</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
