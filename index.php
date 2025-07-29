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
            $lockout = true;
            $error = "Too many attempts. Try again after 5 minutes.";
        }
    }

    if (!$lockout) {
        if ($input_user === $username && $input_pass === $password) {
            $_SESSION['admin_logged_in'] = true;

            // reset login attempts
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

<h2>Login</h2>
<form method="POST">
    Username: <input type="text" name="username" required><br><br>
    Password: <input type="password" name="password" required><br><br>
    <input type="submit" value="Login">
</form>

<?php if ($error): ?>
<p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>
