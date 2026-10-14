<?php
include '../db.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Check admin role (case-insensitive)
if (strtolower($_SESSION['role']) != 'admin') {
    die("Access denied. Only admins can view this page.");
}

// Fetch all users
$users = $conn->query("SELECT * FROM users ORDER BY id ASC");
if (!$users) die("SQL Error: " . $conn->error);

$c_total = $users->num_rows;

// Helper: get badge color + display name
function roleBadge($role) {
    $r = strtolower(trim($role));

    $map = [
        'admin'   => ['Admin',   '#7c3aed'],
        'doctor'  => ['Doctor',  '#0ea5e9'],
        'nurse'   => ['Nurse',   '#f59e0b'],
        'patient' => ['Patient', '#10b981'],
    ];

    if (isset($map[$r])) {
        return '<span style="background:' . $map[$r][1] . '; color:#fff; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:600;">'
               . $map[$r][0] . '</span>';
    }

    // Fallback for unknown roles
    return '<span style="background:#6b7280; color:#fff; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:600;">'
           . htmlspecialchars($role ?: 'Unknown') . '</span>';
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin Dashboard - All Users</title>
  <link rel="stylesheet" href="../styles.css">
</head>
<body>
<div class="layout">

  <div class="sidebar">
    <h3>ETEI Admin</h3>
    <a class="active" href="dashboard.php">🏠 Dashboard</a>
    <a href="users.php">👥 Users</a>
    <a href="medicines.php">💊 Medicines</a>
    <a href="../logout.php">🚪 Logout</a>
  </div>

  <div class="main">
    <div class="topbar">
      <h2>All Users (<?= $c_total ?>)</h2>
      <span class="user">Welcome, <?= htmlspecialchars($_SESSION['name']) ?></span>
    </div>

    <table>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Registered</th>
      </tr>

      <?php if ($c_total == 0): ?>
        <tr><td colspan="5" style="text-align:center;padding:20px;">No users found.</td></tr>
      <?php else: ?>
        <?php while($u = $users->fetch_assoc()): ?>
        <tr>
          <td><?= $u['id'] ?></td>
          <td><?= htmlspecialchars($u['name']) ?></td>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td><?= roleBadge($u['role']) ?></td>
          <td><?= $u['created_at'] ?? '—' ?></td>
        </tr>
        <?php endwhile; ?>
      <?php endif; ?>

    </table>
  </div>
</div>
</body>
</html>