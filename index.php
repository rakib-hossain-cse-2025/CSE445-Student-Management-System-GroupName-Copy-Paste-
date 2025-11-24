<?php
require_once "config.php";
require_once "auth_check.php";

// Handle search
$search = $_GET['search'] ?? '';

if (!empty($search)) {
    $searchTerm = "%$search%";
    $stmt = $conn->prepare("
        SELECT * FROM students
        WHERE student_name LIKE ?
           OR student_id LIKE ?
           OR email LIKE ?
           OR department LIKE ?
        ORDER BY id DESC
    ");
    $stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM students ORDER BY id DESC");
}

// Handle messages (from redirects)
$msg  = $_GET['msg']  ?? '';
$type = $_GET['type'] ?? 'success';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Management System - CSE445</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>Student Management System</h1>

        <div>
            <a href="add_student.php">+ Add New Student</a>
            <a href="logout.php" style="margin-left:8px;background:#6c757d;">Logout</a>
        </div>
    </div>

    <?php if (!empty($msg)): ?>
        <div class="msg <?php echo htmlspecialchars($type); ?>">
            <?php echo htmlspecialchars($msg); ?>
        </div>
    <?php endif; ?>

    <!-- Search Form -->
    <form method="get" class="form-group" style="margin-bottom:15px;">
        <input
            type="text"
            name="search"
            placeholder="Search by name, ID, email, department..."
            style="width:60%;padding:8px;border-radius:4px;border:1px solid #aaa;"
            value="<?php echo htmlspecialchars($search); ?>"
        >
        <button type="submit" class="btn btn-primary">Search</button>
        <a href="index.php" class="btn btn-secondary">Reset</a>
    </form>

    <div class="table-container">
        <table>
            <thead>
            <tr>
                <th>#</th>
                <th>Student Name</th>
                <th>Student ID</th>
                <th>Email</th>
                <th>Department</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php $sl = 1; ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $sl++; ?></td>
                        <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['department']); ?></td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        <td class="actions">
                            <a class="edit" href="edit_student.php?id=<?php echo $row['id']; ?>">Edit</a>
                            <a class="delete"
                               href="delete_student.php?id=<?php echo $row['id']; ?>"
                               onclick="return confirm('Are you sure you want to delete this student?');">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">
                        <?php if (!empty($search)): ?>
                            No students found matching your search.
                        <?php else: ?>
                            No student records found. Click "Add New Student" to create one.
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
