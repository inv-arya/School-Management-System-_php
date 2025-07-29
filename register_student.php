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

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Register Student - School Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
  <div class="container mt-5" style="max-width: 480px;">
    <div class="card shadow-sm">
      <div class="card-body">
        <h3 class="card-title mb-4 text-center">Register Student</h3>

        <?php if ($success): ?>
          <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if (isset($errors['db'])): ?>
          <div class="alert alert-danger"><?= htmlspecialchars($errors['db']) ?></div>
        <?php endif; ?>

        <form method="POST" novalidate>
          <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input
              type="text"
              name="name"
              id="name"
              class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
              value="<?= htmlspecialchars($name) ?>"
              required
            />
            <?php if (isset($errors['name'])): ?>
              <div class="invalid-feedback"><?= $errors['name'] ?></div>
            <?php endif; ?>
          </div>

          <div class="mb-3">
            <label for="registration_no" class="form-label">Registration No.</label>
            <input
              type="text"
              name="registration_no"
              id="registration_no"
              class="form-control <?= isset($errors['registration_no']) ? 'is-invalid' : '' ?>"
              value="<?= htmlspecialchars($reg) ?>"
              required
            />
            <?php if (isset($errors['registration_no'])): ?>
              <div class="invalid-feedback"><?= $errors['registration_no'] ?></div>
            <?php endif; ?>
          </div>

          <div class="mb-3">
            <label for="age" class="form-label">Age</label>
            <input
              type="number"
              name="age"
              id="age"
              class="form-control <?= isset($errors['age']) ? 'is-invalid' : '' ?>"
              value="<?= htmlspecialchars($age) ?>"
              min="18"
              max="25"
              required
            />
            <?php if (isset($errors['age'])): ?>
              <div class="invalid-feedback"><?= $errors['age'] ?></div>
            <?php endif; ?>
          </div>

          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input
              type="email"
              name="email"
              id="email"
              class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
              value="<?= htmlspecialchars($email) ?>"
              required
            />
            <?php if (isset($errors['email'])): ?>
              <div class="invalid-feedback"><?= $errors['email'] ?></div>
            <?php endif; ?>
          </div>

          <div class="mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input
              type="text"
              name="phone"
              id="phone"
              maxlength="10"
              class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>"
              value="<?= htmlspecialchars($phone) ?>"
              required
            />
            <?php if (isset($errors['phone'])): ?>
              <div class="invalid-feedback"><?= $errors['phone'] ?></div>
            <?php endif; ?>
          </div>

          <div class="mb-4">
            <label for="course" class="form-label">Course</label>
            <select
              name="course"
              id="course"
              class="form-select <?= isset($errors['course']) ? 'is-invalid' : '' ?>"
              required
            >
              <option value="" <?= $course === '' ? 'selected' : '' ?>>--Select--</option>
              <option value="BCA" <?= $course === 'BCA' ? 'selected' : '' ?>>BCA</option>
              <option value="BBA" <?= $course === 'BBA' ? 'selected' : '' ?>>BBA</option>
              <option value="B.Sc" <?= $course === 'B.Sc' ? 'selected' : '' ?>>B.Sc</option>
              <option value="MBA" <?= $course === 'MBA' ? 'selected' : '' ?>>MBA</option>
            </select>
            <?php if (isset($errors['course'])): ?>
              <div class="invalid-feedback"><?= $errors['course'] ?></div>
            <?php endif; ?>
          </div>

          <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary">Register</button>
          </div>
        </form>

        <div class="d-flex justify-content-between">
          <a href="list_students.php" class="btn btn-secondary">View Students</a>
          <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
