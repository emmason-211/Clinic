<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../db.php';

if (!isset($_SESSION['user_id'])) { header("Location: ../login.php"); exit; }

// ── COUNTS ──
$totalPatients = $conn->query("SELECT COUNT(*) c FROM patients")->fetch_assoc()['c'];
$totalAppts    = $conn->query("SELECT COUNT(*) c FROM appointments")->fetch_assoc()['c'];
$pending       = $conn->query("SELECT COUNT(*) c FROM appointments WHERE status='pending'")->fetch_assoc()['c'];
$visits        = $conn->query("SELECT COUNT(*) c FROM visits")->fetch_assoc()['c'];
$lowStock      = $conn->query("SELECT COUNT(*) c FROM medicines WHERE quantity <= reorder_level")->fetch_assoc()['c'];

// ── RECENT APPOINTMENTS (LEFT JOIN = safer) ──
$recent = $conn->query("SELECT a.id, a.appointment_date, a.appointment_time,
                               a.reason, a.status, u.name
                        FROM appointments a
                        LEFT JOIN patients p ON a.patient_id = p.id
                        LEFT JOIN users u ON p.user_id = u.id
                        ORDER BY a.id DESC
                        LIMIT 5");

if (!$recent) {
    echo "SQL Error: " . $conn->error;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Dashboard</title>
  <link rel="stylesheet" href="../styles.css">
</head>
<body>
<div class="layout">

  <div class="sidebar">
    <h3>ETEI Clinic</h3>
    <a class="active" href="dashboard.php">🏠 Dashboard</a>
    <a href="patients.php">👥 Patients</a>
    <a href="appointments.php">📅 Appointments</a>
    <a href="visit.php">🩺 Visits</a>
    <a href="prescriptions.php">📝 Prescriptions</a>
    <a href="medicines.php">💊 Medicines</a>
    <a href="reports.php">📊 Reports</a>
    <a href="../logout.php">🚪 Logout</a>
  </div>

  <div class="main">
    <div class="topbar">
      <h2>Good day, <?= htmlspecialchars($_SESSION['name']) ?> 👋</h2>
      <span class="user"><?= ucfirst($_SESSION['role']) ?></span>
    </div>

    <!-- STAT CARDS -->
    <div class="cards">
      <div class="card"><h4>Total Patients</h4><div class="num"><?= $totalPatients ?></div></div>
      <div class="card"><h4>Total Appointments</h4><div class="num"><?= $totalAppts ?></div></div>
      <div class="card"><h4>Pending</h4><div class="num"><?= $pending ?></div></div>
      <div class="card"><h4>Visits</h4><div class="num"><?= $visits ?></div></div>
      <div class="card"><h4>Low Stock</h4><div class="num"><?= $lowStock ?></div></div>
    </div>

    <!-- RECENT APPOINTMENTS -->
    <h3 style="margin:25px 0 12px; color:#0f4c3a;">Recent Appointments</h3>

    <table>
      <tr>
        <th>ID</th>
        <th>Patient</th>
        <th>Date</th>
        <th>Time</th>
        <th>Reason</th>
        <th>Status</th>
      </tr>

      <?php if ($recent && $recent->num_rows > 0): ?>
        <?php while($r = $recent->fetch_assoc()): ?>
        <tr>
          <td><?= $r['id'] ?></td>
          <td><?= htmlspecialchars($r['name'] ?? 'Unknown') ?></td>
          <td><?= $r['appointment_date'] ?></td>
          <td><?= $r['appointment_time'] ?></td>
          <td><?= htmlspecialchars($r['reason'] ?? '') ?></td>
          <td>
            <span class="badge <?= $r['status'] ?>">
              <?= ucfirst($r['status']) ?>
            </span>
          </td>
        </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr>
          <td colspan="6" style="text-align:center; padding:20px; color:#999;">
            No recent appointments found.
          </td>
        </tr>
      <?php endif; ?>

    </table>
  </div>
</div>
</body>
</html>