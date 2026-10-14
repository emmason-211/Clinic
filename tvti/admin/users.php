<?php
include '../db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php"); exit;
}

// ---- ADD NEW USER ----
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    $name  = $_POST['name'];
    $email = $_POST['email'];
    $pass  = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role  = strtolower($_POST['role']);

    $check = $conn->query("SELECT id FROM users WHERE email='$email'");
    if ($check->num_rows > 0) {
        $msg = "Email already exists!";
    } else {
        $conn->query("INSERT INTO users (name,email,password,role)
                      VALUES ('$name','$email','$pass','$role')");
        $uid = $conn->insert_id;
        if ($role == 'patient') {
            $conn->query("INSERT INTO patients (user_id) VALUES ($uid)");
        }
        $msg = "User added successfully!";
    }
}

// ---- DELETE USER ----
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id != $_SESSION['user_id']) {
        $conn->query("DELETE FROM users WHERE id=$id");
    }
    header("Location: users.php");
    exit;
}

// ---- UPDATE ROLE ----
if (isset($_POST['update_role'])) {
    $id   = (int)$_POST['id'];
    $role = $_POST['role'];
    $conn->query("UPDATE users SET role='$role' WHERE id=$id");
    $msg = "Role updated!";
}

$users = $conn->query("SELECT * FROM users ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Manage Users - Admin</title>
  <link rel="stylesheet" href="../styles.css">
</head>
<body>
<div class="layout">
  <div class="sidebar">
    <h3>ETEI Admin</h3>
    <a href="dashboard.php">🏠 Dashboard</a>
    <a class="active" href="users.php">👥 Users</a>
    <a href="medicines.php">💊 Medicines</a>
    <a href="../logout.php">🚪 Logout</a>
  </div>

  <div class="main">
    <div class="topbar">
      <h2>User Management</h2>
      <span class="user"><?= $_SESSION['name'] ?></span>
    </div>

    <?php if (!empty($msg)): ?>
      <p style="color:#0f766e; margin-bottom:12px;"><b><?= $msg ?></b></p>
    <?php endif; ?>

    <!-- ADD USER FORM -->
    <div class="form-box" style="margin-bottom:20px;">
      <h3>Add New User</h3>
      <form method="POST">
        <input name="name" placeholder="Full Name" required>
        <input name="email" type="email" placeholder="Email" required>
        <input name="password" type="password" placeholder="Password" required>
        <select name="role" required>
          <option value="patient">Student / Staff (Patient)</option>
          <option value="nurse">Nurse / Clinic Staff</option>
          <option value="doctor">Doctor / Clinician</option>
          <option value="admin">Administrator</option>
        </select>
        <button class="btn" type="submit" name="add_user">Add User</button>
      </form>
    </div>

    <!-- USERS TABLE -->
    <table>
      <tr>
        <th>ID</th><th>Name</th><th>Email</th><th>Role</th>
        <th>Registered</th><th>Change Role</th><th>Action</th>
      </tr>
      <?php while($u = $users->fetch_assoc()): ?>
      <tr>
        <td><?= $u['id'] ?></td>
        <td><?= htmlspecialchars($u['name']) ?></td>
        <td><?= htmlspecialchars($u['email']) ?></td>
        <td>
          <span class="badge <?= $u['role'] == 'admin' ? 'cancelled' : ($u['role'] == 'doctor' ? 'approved' : ($u['role'] == 'nurse' ? 'pending' : 'completed')) ?>">
            <?= ucfirst($u['role']) ?>
          </span>
        </td>
        <td><?= $u['created_at'] ?></td>
        <td>
          <form method="POST" style="display:flex; gap:5px;">
            <input type="hidden" name="id" value="<?= $u['id'] ?>">
            <select name="role" style="padding:5px;">
              <option value="patient" <?= $u['role']=='patient'?'selected':'' ?>>Patient</option>
              <option value="nurse"   <?= $u['role']=='nurse'  ?'selected':'' ?>>Nurse</option>
              <option value="doctor"  <?= $u['role']=='doctor' ?'selected':'' ?>>Doctor</option>
              <option value="admin"   <?= $u['role']=='admin'  ?'selected':'' ?>>Admin</option>
            </select>
            <button class="btn" name="update_role" type="submit" style="padding:5px 10px;">Save</button>
          </form>
        </td>
        <td>
          <?php if ($u['id'] != $_SESSION['user_id']): ?>
            <a class="btn red" href="?delete=<?= $u['id'] ?>"
               onclick="return confirm('Delete this user?')">Delete</a>
          <?php else: ?>
            <small>(you)</small>
          <?php endif; ?>
        </td>
      </tr>
      <?php endwhile; ?>
    </table>
  </div>
</div>
</body>
</html>