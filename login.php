<?php
session_start();
require_once "config.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = "Please enter both username and password.";
    } else {
        $sql = "SELECT * FROM users WHERE username = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user && $user['password'] === md5($password)) {
            $_SESSION['auth'] = true;
            $_SESSION['username'] = $username;

            header("Location: index.php");
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login | CSE445 CRUD</title>
    <link rel="stylesheet" href="assets/style.css">

    <style>
        /* Small style for password toggle icon */
        .password-wrapper {
            position: relative;
        }
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 9px;
            cursor: pointer;
            font-size: 13px;
            color: #555;
        }
        .toggle-password:hover {
            color: #000;
        }
    </style>
</head>

<body>
<div class="wrapper">
    <h1>Login</h1>

    <?php if (!empty($error)): ?>
        <div class="msg error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="post">
        
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username">
        </div>

        <div class="form-group password-wrapper">
            <label>Password</label>
            <input type="password" name="password" id="passwordField">
            <span class="toggle-password" onclick="togglePassword()">👁️</span>
        </div>

        <button type="submit" class="btn btn-primary">Login</button>
    </form>
</div>

<script>
function togglePassword() {
    const passField = document.getElementById("passwordField");
    if (passField.type === "password") {
        passField.type = "text";
    } else {
        passField.type = "password";
    }
}
</script>

</body>
</html>
