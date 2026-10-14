<?php
include '../db.php';
if (!isset($_SESSION['user_id'])) { header("Location: ../login.php"); exit; }

$role = strtolower($_SESSION['role']);
if ($role == 'patient') { header("Location: ../my_appointments.php"); exit; }

// Status update
if (isset($_GET['status'], $_GET['id'])) {
    $id = (int)$_GET['id'];
    $st = strtolower($_GET['status']);
    if (in_array($st, ['pending','approved','completed','cancelled'])) {
        $conn->query("UPDATE appointments SET status='$st' WHERE id=$id");
    }
    header("Location: appointments.php");
    exit;
}

// Filter
$filter = $_GET['filter'] ?? 'all';
$where  = "";
if ($filter != 'all') {
    $safe  = $conn->real_escape_string($filter);
    $where = "WHERE a.status = '$safe'";
}

// Fetch
$res = $conn->query("SELECT a.*, u.name, u.email
                     FROM appointments a
                     LEFT JOIN patients p ON a.patient_id = p.id
                     LEFT JOIN users u ON p.user_id = u.id
                     $where
                     ORDER BY a.id DESC");

// Counts
$c_all       = $conn->query("SELECT COUNT(*) c FROM appointments")->fetch_assoc()['c'];
$c_pending   = $conn->query("SELECT COUNT(*) c FROM appointments WHERE status='pending'")->fetch_assoc()['c'];
$c_approved  = $conn->query("SELECT COUNT(*) c FROM appointments WHERE status='approved'")->fetch_assoc()['c'];
$c_completed = $conn->query("SELECT COUNT(*) c FROM appointments WHERE status='completed'")->fetch_assoc()['c'];
$c_cancelled = $conn->query("SELECT COUNT(*) c FROM appointments WHERE status='cancelled'")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html>
<head>
  <title>Appointments</title>
  <link rel="stylesheet" href="../styles.css">
</head>
<body>
<div class="layout">
  <div class="sidebar">
    <h3>FDRE TVTI Clinic</h3>
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="patients.php">👥 Patients</a>
    <a class="active" href="appointments.php">📅 Appointments</a>
    <a href="visit.php">🩺 Visits</a>
    <a href="prescriptions.php">📝 Prescriptions</a>
    <a href="medicines.php">💊 Medicines</a>
    <a href="reports.php">📊 Reports</a>
    <a href="../logout.php">🚪 Logout</a>
  </div>

  <div class="main">
    <div class="topbar">
      <h2>Appointments</h2>
      <span class="user"><?= htmlspecialchars($_SESSION['name']) ?></span>
    </div>

    <div class="cards" style="margin-bottom:20px;">
      <div class="card"><h4>Total</h4><div class="num"><?= $c_all ?></div></div>
      <div class="card"><h4>🟡 Pending</h4><div class="num"><?= $c_pending ?></div></div>
      <div class="card"><h4>🔵 Approved</h4><div class="num"><?= $c_approved ?></div></div>
      <div class="card"><h4>🟢 Completed</h4><div class="num"><?= $c_completed ?></div></div>
      <div class="card"><h4>🔴 Cancelled</h4><div class="num"><?= $c_cancelled ?></div></div>
    </div>

    <table>
      <tr>
        <th>ID</th>
        <th>Patient</th>
        <th>Date</th>
        <th>Time</th>
        <th>Reason</th>
        <th>Status</th>
        <th>Action</th>
      </tr>

      <?php if ($res->num_rows == 0): ?>
        <tr><td colspan="7" style="text-align:center; padding:20px;">No appointments found.</td></tr>
      <?php else: ?>
        <?php while($a = $res->fetch_assoc()): 
          $status = $a['status'] ?? 'pending';
        ?>
        <tr>
          <td><?= $a['id'] ?></td>
          <td><?= htmlspecialchars($a['name'] ?? '—') ?></td>
          <td><?= $a['appointment_date'] ?></td>
          <td><?= $a['appointment_time'] ?></td>
          <td><?= htmlspecialchars($a['reason']) ?></td>
          <td>
            <span style="background:
              <?= $status=='pending'   ? '#f59e0b' : '' ?>
              <?= $status=='approved'  ? '#0ea5e9' : '' ?>
              <?= $status=='completed' ? '#10b981' : '' ?>
              <?= $status=='cancelled' ? '#ef4444' : '' ?>;
              color:#fff; padding:5px 12px; border-radius:20px; font-size:12px; font-weight:600;">
              <?= ucfirst($status) ?>
            </span>
          </td>
          <td>
            <?php if ($status == 'pending'): ?>
              <a class="btn" href="?id=<?= $a['id'] ?>&status=approved">✅ Approve</a>
              <a class="btn red" href="?id=<?= $a['id'] ?>&status=cancelled"
                 onclick="return confirm('Cancel?')">❌ Cancel</a>
            <?php elseif ($status == 'approved'): ?>
              <a class="btn green" href="?id=<?= $a['id'] ?>&status=completed">🟢 Complete</a>
              <a class="btn red" href="?id=<?= $a['id'] ?>&status=cancelled"
                 onclick="return confirm('Cancel?')">❌ Cancel</a>
            <?php elseif ($status == 'completed'): ?>
              <small style="color:#10b981; font-weight:600;">✔ Done</small>
            <?php elseif ($status == 'cancelled'): ?>
              <a class="btn yellow" href="?id=<?= $a['id'] ?>&status=pending">↩ Reopen</a>
            <?php endif; ?>
          </td>
        </tr>
        <?php endwhile; ?>
      <?php endif; ?>
    </table>
  </div>
</div>
</body>
</html>