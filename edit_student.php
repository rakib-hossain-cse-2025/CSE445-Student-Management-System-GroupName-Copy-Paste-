<?php
require_once "config.php";
require_once "auth_check.php";

// Check if ID is valid
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $msg  = "Invalid student ID.";
    $type = "error";
    header("Location: index.php?msg=" . urlencode($msg) . "&type=" . $type);
    exit;
}

$id = (int) $_GET['id'];

// Fetch the existing student record
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result  = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

if (!$student) {
    $msg  = "Student not found.";
    $type = "error";
    header("Location: index.php?msg=" . urlencode($msg) . "&type=" . $type);
    exit;
}

$error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $student_name = isset($_POST['student_name']) ? trim($_POST['student_name']) : '';
    $student_id   = isset($_POST['student_id'])   ? trim($_POST['student_id'])   : '';
    $email        = isset($_POST['email'])        ? trim($_POST['email'])        : '';
    $department   = isset($_POST['department'])   ? trim($_POST['department'])   : '';

    if ($student_name === '' || $student_id === '' || $email === '' || $department === '') {
        $error = "All fields are required.";
    } else {
        $updateStmt = $conn->prepare("
            UPDATE students
            SET student_name = ?, student_id = ?, email = ?, department = ?
            WHERE id = ?
        ");

        if ($updateStmt) {
            $updateStmt->bind_param("ssssi", $student_name, $student_id, $email, $department, $id);

            if ($updateStmt->execute()) {
                $msg  = "Student updated successfully.";
                $type = "success";
                header("Location: index.php?msg=" . urlencode($msg) . "&type=" . $type);
                exit;
            } else {
                $error = "Failed to update student. Please try again.";
            }

            $updateStmt->close();
        } else {
            $error = "Database error: could not prepare update statement.";
        }
    }

    // If there was an error, keep the last submitted values in the form
    $student['student_name'] = $student_name;
    $student['student_id']   = $student_id;
    $student['email']        = $email;
    $student['department']   = $department;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Student - CSE445</title>
    <link rel="stylesheet" href="assets/style.css">
    <script src="assets/script.js"></script>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>Edit Student</h1>
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
                    value="<?php echo htmlspecialchars($student['student_name']); ?>"
                >
            </div>

            <div class="form-group">
                <label for="student_id">Student ID</label>
                <input
                    type="text"
                    id="student_id"
                    name="student_id"
                    value="<?php echo htmlspecialchars($student['student_id']); ?>"
                >
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input
                    type="text"
                    id="email"
                    name="email"
                    value="<?php echo htmlspecialchars($student['email']); ?>"
                >
            </div>

            <div class="form-group">
                <label for="department">Department</label>
                <input
                    type="text"
                    id="department"
                    name="department"
                    value="<?php echo htmlspecialchars($student['department']); ?>"
                >
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Student</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
