<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name  = $_POST['name'];
    $email = $_POST['email'];
    $pass  = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role  = strtolower($_POST['role']);

    $sql = "INSERT INTO users (name,email,password,role) VALUES ('$name','$email','$pass','$role')";
    if ($conn->query($sql)) {
        $uid = $conn->insert_id;
        if ($role == 'patient') {
            $conn->query("INSERT INTO patients (user_id) VALUES ($uid)");
        }
        header("Location: login.php");
        exit;
    }
    $error = "Email already exists";
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Register - ETEI Clinic</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="auth-wrapper">
  <form class="auth-box" method="POST">
    <img src="assets/logo.png" alt="ETEI">
    <h2>Create Account</h2>
    <?php if (!empty($error)) echo "<p style='color:red'>$error</p>"; ?>
    <input type="text" name="name" placeholder="Full Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <select name="role" required>
      <option value="patient">Student / Staff</option>
      <option value="nurse">Nurse / Clinic Staff</option>
      <option value="doctor">Doctor / Clinician</option>
      <option value="admin">Administrator</option>
    </select>
    <button type="submit">Register</button>
    <p style="margin-top:12px;">Already registered? <a href="login.php">Login</a></p>
  </form>
</div>
</body>
</html>