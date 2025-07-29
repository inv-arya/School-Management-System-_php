<?php
include 'inc/auth.php';
include 'inc/db.php';

$errors = [];
$success = '';

// Retain entered values
$name = $reg = $age = $email = $phone = $course = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $reg = trim($_POST['registration_no']);
    $age = $_POST['age'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $course = $_POST['course'];

    // Validations
    if (!preg_match("/^[a-zA-Z ]{2,}$/", $name)) {
        $errors['name'] = "Invalid name";
    }
    if (!preg_match("/^REG-\d{4}-\d{4}$/", $reg)) {
        $errors['registration_no'] = "Invalid registration number format (REG-YYYY-NNNN)";
    }
    if ($age < 18 || $age > 25) {
        $errors['age'] = "Age must be between 18 and 25";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    }
    if (!preg_match("/^\d{10}$/", $phone)) {
        $errors['phone'] = "Phone must be 10 digits";
    }
    if (empty($course)) {
        $errors['course'] = "Course is required";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO students (name, registration_no, age, email, phone, course) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisss", $name, $reg, $age, $email, $phone, $course);

        if ($stmt->execute()) {
            $success = "Student registered successfully!";
            // Reset form values after success
            $name = $reg = $age = $email = $phone = $course = '';
        } else {
            $errors['db'] = "Error: " . $conn->error;
        }
    }
}
?>

<h2>Register Student</h2>
<form method="POST">
    Name: <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" required><br>
    <?php if (isset($errors['name'])) echo "<span style='color:red'>{$errors['name']}</span><br>"; ?>
    <br>

    Reg. No: <input type="text" name="registration_no" value="<?= htmlspecialchars($reg) ?>" required><br>
    <?php if (isset($errors['registration_no'])) echo "<span style='color:red'>{$errors['registration_no']}</span><br>"; ?>
    <br>

    Age: <input type="number" name="age" min="18" max="25" value="<?= htmlspecialchars($age) ?>" required><br>
    <?php if (isset($errors['age'])) echo "<span style='color:red'>{$errors['age']}</span><br>"; ?>
    <br>

    Email: <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required><br>
    <?php if (isset($errors['email'])) echo "<span style='color:red'>{$errors['email']}</span><br>"; ?>
    <br>

    Phone: <input type="text" name="phone" maxlength="10" value="<?= htmlspecialchars($phone) ?>" required><br>
    <?php if (isset($errors['phone'])) echo "<span style='color:red'>{$errors['phone']}</span><br>"; ?>
    <br>

    Course:
    <select name="course" required>
        <option value="">--Select--</option>
        <option value="BCA" <?= $course === 'BCA' ? 'selected' : '' ?>>BCA</option><br>
        <option value="BBA" <?= $course === 'BBA' ? 'selected' : '' ?>>BBA</option><br>
        <option value="B.Sc" <?= $course === 'B.Sc' ? 'selected' : '' ?>>B.Sc</option><br>
        <option value="MBA" <?= $course === 'MBA' ? 'selected' : '' ?>>MBA</option><br>
    </select><br>
    <?php if (isset($errors['course'])) echo "<span style='color:red'>{$errors['course']}</span><br>"; ?>
    <br>

    <input type="submit" value="Register">
</form>

<?php
if ($success) echo "<p style='color:green;'>$success</p>";
if (isset($errors['db'])) echo "<p style='color:red;'>{$errors['db']}</p>";
?>

<!-- navbutton to logout and studenlist -->
<a href="list_students.php" style="text-decoration:none;">
  <button type="button">View Students</button>
</a>

<a href="logout.php" style="text-decoration:none;">
  <button type="button">Logout</button>
</a>
