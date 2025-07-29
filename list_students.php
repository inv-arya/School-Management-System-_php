<?php
include 'inc/auth.php';
include 'inc/db.php';

$result = $conn->query("SELECT * FROM students ORDER BY created_at DESC");
?>

<h2>Registered Students</h2>
<table border="1">
    <tr>
        <th>ID</th><th>Name</th><th>Reg No</th><th>Age</th><th>Email</th><th>Phone</th><th>Course</th><th>Created Date</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= $row['registration_no'] ?></td>
        <td><?= $row['age'] ?></td>
        <td><?= $row['email'] ?></td>
        <td><?= $row['phone'] ?></td>
        <td><?= $row['course'] ?></td>
        <td><?= $row['created_at'] ?></td>
    </tr>
    <?php endwhile; ?>
</table>
<br><br>
<a href="register_student.php" style="text-decoration:none;">
  <button type="button">Register New</button>
</a>

<a href="logout.php" style="text-decoration:none;">
  <button type="button">Logout</button>
</a>