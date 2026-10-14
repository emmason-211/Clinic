<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $pass  = $_POST['password'];

    $res = $conn->query("SELECT * FROM users WHERE email='$email'");
    if ($res->num_rows == 1) {
        $u = $res->fetch_assoc();
        if (password_verify($pass, $u['password'])) {
            $_SESSION['user_id'] = $u['id'];
            $_SESSION['name']    = $u['name'];
            $_SESSION['role']    = $u['role'];
            header("Location: dashboard.php");
            exit;
        }
    }
    $error = "Invalid email or password";
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Login - ETEI Clinic</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="auth-wrapper">
  <form class="auth-box" method="POST">
    <img src="assets/logo.png" alt="FDRE TVTI">
    <h2>Clinic Login</h2>
    <?php if (!empty($error)) echo "<p style='color:red'>$error</p>"; ?>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
    <p style="margin-top:12px;">No account? <a href="register.php">Register</a></p>
  </form>
</div>
</body>
</html>