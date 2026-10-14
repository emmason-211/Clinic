<?php
include 'db.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$role = $_SESSION['role'];
// Only clinic staff and admin can use this page
if ($role == 'patient') { header("Location: my_appointments.php"); exit; }

// ---- ADD APPOINTMENT ----
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $pid  = (int)$_POST['patient_id'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $reason = $_POST['reason'];

    $conn->query("INSERT INTO appointments (patient_id, appointment_date, appointment_time, reason)
                  VALUES ($pid,'$date','$time','$reason')");
    $msg = "Appointment created successfully!";
}

// ---- UPDATE STATUS ----
if (isset($_GET['status'], $_GET['id'])) {
    $id = (int)$_GET['id'];
    $st = $_GET['status'];
    if (in_array($st, ['approved','completed','cancelled','pending'])) {
        $conn->query("UPDATE appointments SET status='$st' WHERE id=$id");
    }
    header("Location: appointment.php");
    exit;
}

// Patients list for dropdown
$patients = $conn->query("SELECT p.id, u.name FROM patients p
                          JOIN users u ON p.user_id=u.id
                          ORDER BY u.name ASC");

// All appointments
$appts = $conn->query("SELECT a.*, u.name FROM appointments a
                       JOIN patients p ON a.patient_id = p.id
                       JOIN users u ON p.user_id = u.id
                       ORDER BY a.id DESC");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Appointments - ETEI Clinic</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="layout">
  <!-- SIDEBAR -->
  <div class="sidebar">
    <h3>ETEI Clinic</h3>
    <?php if ($role == 'admin'): ?>
      <a href="admin/dashboard.php">🏠 Dashboard</a>
      <a href="admin/users.php">👥 Users</a>
      <a href="admin/medicines.php">💊 Medicines</a>
    <?php else: ?>
      <a href="clinic/dashboard.php">🏠 Dashboard</a>
      <a href="clinic/patients.php">👥 Patients</a>
      <a href="clinic/appointments.php">📅 Appointments</a>
      <a href="clinic/visit.php">🩺 Visits</a>
      <a href="clinic/medicines.php">💊 Medicines</a>
      <a href="clinic/reports.php">📊 Reports</a>
    <?php endif; ?>
    <a class="active" href="appointment.php">➕ Book Appointment</a>
    <a href="logout.php">🚪 Logout</a>
  </div>

  <!-- MAIN -->
  <div class="main">
    <div class="topbar">
      <h2>Appointment Management</h2>
      <span class="user"><?= $_SESSION['name'] ?> (<?= ucfirst($role) ?>)</span>
    </div>

    <?php if (!empty($msg)): ?>
      <p style="color:#0f766e; margin-bottom:12px;"><b><?= $msg ?></b></p>
    <?php endif; ?>

    <!-- NEW APPOINTMENT FORM -->
    <div class="form-box" style="margin-bottom:20px;">
      <h3>Book New Appointment</h3>
      <form method="POST">
        <select name="patient_id" required>
          <option value="">-- Select Patient --</option>
          <?php while($p = $patients->fetch_assoc()): ?>
            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
          <?php endwhile; ?>
        </select>
        <input type="date" name="date" required>
        <input type="time" name="time" required>
        <textarea name="reason" placeholder="Reason for visit"></textarea>
        <button class="btn" type="submit" name="add">Book Appointment</button>
      </form>
    </div>

    <!-- APPOINTMENTS TABLE -->
    <table>
      <tr>
        <th>ID</th><th>Patient</th><th>Date</th><th>Time</th>
        <th>Reason</th><th>Status</th><th>Action</th>
      </tr>
      <?php while($a = $appts->fetch_assoc()): ?>
      <tr>
        <td><?= $a['id'] ?></td>
        <td><?= htmlspecialchars($a['name']) ?></td>
        <td><?= $a['appointment_date'] ?></td>
        <td><?= $a['appointment_time'] ?></td>
        <td><?= htmlspecialchars($a['reason']) ?></td>
        <td><span class="badge <?= $a['status'] ?>"><?= ucfirst($a['status']) ?></span></td>
        <td>
          <a class="btn" href="?id=<?= $a['id'] ?>&status=approved">Approve</a>
          <a class="btn yellow" href="?id=<?= $a['id'] ?>&status=completed">Complete</a>
          <a class="btn red" href="?id=<?= $a['id'] ?>&status=cancelled"
             onclick="return confirm('Cancel this appointment?')">Cancel</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </table>
  </div>
</div>
</body>
</html>