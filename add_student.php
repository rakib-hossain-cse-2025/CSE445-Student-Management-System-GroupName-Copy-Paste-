<?php
require_once "config.php";
require_once "auth_check.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_name = trim($_POST['student_name'] ?? '');
    $student_id   = trim($_POST['student_id'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $department   = trim($_POST['department'] ?? '');

    if ($student_name === '' || $student_id === '' || $email === '' || $department === '') {
        $error = "All fields are required.";
    } else {
        $stmt = $conn->prepare("
            INSERT INTO students (student_name, student_id, email, department)
            VALUES (?, ?, ?, ?)
        ");

        if ($stmt) {
            $stmt->bind_param("ssss", $student_name, $student_id, $email, $department);

            if ($stmt->execute()) {
                header("Location: index.php?msg=" . urlencode("Student added successfully.") . "&type=success");
                exit;
            } else {
                $error = "Failed to add student. Please try again.";
            }

            $stmt->close();
        } else {
            $error = "Database error: could not prepare statement.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Student - CSE445</title>
    <link rel="stylesheet" href="assets/style.css">
    <script src="assets/script.js"></script>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>Add New Student</h1>
        <a href="index.php">← Back to List</a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="msg error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="form-container">
        <form method="post" onsubmit="return validateStudentForm();">
            <div class="form-group">
                <label for="student_name">Student Name</label>
                <input
                    type="text"
                    id="student_name"
                    name="student_name"
                    value="<?php echo htmlspecialchars($_POST['student_name'] ?? ''); ?>"
                >
            </div>

            <div class="form-group">
                <label for="student_id">Student ID</label>
                <input
                    type="text"
                    id="student_id"
                    name="student_id"
                    value="<?php echo htmlspecialchars($_POST['student_id'] ?? ''); ?>"
                >
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input
                    type="text"
                    id="email"
                    name="email"
                    value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                >
            </div>

            <div class="form-group">
                <label for="department">Department</label>
                <input
                    type="text"
                    id="department"
                    name="department"
                    value="<?php echo htmlspecialchars($_POST['department'] ?? ''); ?>"
                >
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Student</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
