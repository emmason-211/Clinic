<?php
include 'db.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$uid = $_SESSION['user_id'];
$p   = $conn->query("SELECT id FROM patients WHERE user_id=$uid")->fetch_assoc();
$pid = $p['id'] ?? 0;

// ── PATIENT BOOKS APPOINTMENT ──
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $d = $_POST['date'];
    $t = $_POST['time'];
    $r = $conn->real_escape_string($_POST['reason']);

    $conn->query("INSERT INTO appointments (patient_id, appointment_date, appointment_time, reason, status)
                  VALUES ($pid, '$d', '$t', '$r', 'pending')");
    $msg = "Appointment requested successfully! Waiting for staff approval.";
}

// ── FETCH PATIENT'S APPOINTMENTS ──
$res = $conn->query("SELECT * FROM appointments WHERE patient_id=$pid ORDER BY id DESC");

// ── COUNTS ──
$c_all       = $conn->query("SELECT COUNT(*) c FROM appointments WHERE patient_id=$pid")->fetch_assoc()['c'];
$c_pending   = $conn->query("SELECT COUNT(*) c FROM appointments WHERE patient_id=$pid AND status='pending'")->fetch_assoc()['c'];
$c_approved  = $conn->query("SELECT COUNT(*) c FROM appointments WHERE patient_id=$pid AND status='approved'")->fetch_assoc()['c'];
$c_completed = $conn->query("SELECT COUNT(*) c FROM appointments WHERE patient_id=$pid AND status='completed'")->fetch_assoc()['c'];
$c_cancelled = $conn->query("SELECT COUNT(*) c FROM appointments WHERE patient_id=$pid AND status='cancelled'")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html>
<head>
  <title>My Appointments - ETEI Clinic</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="layout">

  <!-- SIDEBAR -->
  <div class="sidebar">
    <h3>ETEI Clinic</h3>
    <a class="active" href="my_appointments.php">📅 My Appointments</a>
    <a href="logout.php">🚪 Logout</a>
  </div>

  <!-- MAIN -->
  <div class="main">
    <div class="topbar">
      <h2>Welcome, <?= htmlspecialchars($_SESSION['name']) ?></h2>
      <span class="user">Patient</span>
    </div>

    <?php if (!empty($msg)): ?>
      <p style="color:#0f766e; margin-bottom:15px;"><b><?= $msg ?></b></p>
    <?php endif; ?>

    <!-- BOOK APPOINTMENT FORM -->
    <div class="form-box" style="margin-bottom:20px;">
      <h3>Request New Appointment</h3>
      <form method="POST">
        <input type="date" name="date" required>
        <input type="time" name="time" required>
        <textarea name="reason" placeholder="Reason for visit" required></textarea>
        <button class="btn" type="submit">Submit Request</button>
      </form>
    </div>

    <!-- STATUS COUNT CARDS -->
    <div class="cards" style="margin-bottom:20px;">
      <div class="card"><h4>Total</h4><div class="num"><?= $c_all ?></div></div>
      <div class="card"><h4>🟡 Pending</h4><div class="num"><?= $c_pending ?></div></div>
      <div class="card"><h4>🔵 Approved</h4><div class="num"><?= $c_approved ?></div></div>
      <div class="card"><h4>🟢 Completed</h4><div class="num"><?= $c_completed ?></div></div>
      <div class="card"><h4>🔴 Cancelled</h4><div class="num"><?= $c_cancelled ?></div></div>
    </div>

    <!-- APPOINTMENTS TABLE -->
    <h3 style="margin:0 0 12px; color:#0f4c3a;">My Appointment History</h3>
    <table>
      <tr>
        <th>ID</th>
        <th>Date</th>
        <th>Time</th>
        <th>Reason</th>
        <th>Status</th>
      </tr>
      <?php if ($res->num_rows == 0): ?>
        <tr><td colspan="5" style="text-align:center; padding:20px;">No appointments yet. Book one above!</td></tr>
      <?php else: ?>
        <?php while($a = $res->fetch_assoc()): ?>
        <tr>
          <td><?= $a['id'] ?></td>
          <td><?= $a['appointment_date'] ?></td>
          <td><?= $a['appointment_time'] ?></td>
          <td><?= htmlspecialchars($a['reason']) ?></td>
          <td>
            <span class="badge <?= $a['status'] ?>">
              <?= ucfirst($a['status']) ?>
            </span>
          </td>
        </tr>
        <?php endwhile; ?>
      <?php endif; ?>
    </table>
  </div>
</div>
</body>
</html>