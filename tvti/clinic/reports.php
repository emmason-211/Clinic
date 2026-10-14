<?php
include '../db.php';
if (!isset($_SESSION['user_id'])) { header("Location: ../login.php"); exit; }

$patients = $conn->query("SELECT COUNT(*) c FROM patients")->fetch_assoc()['c'];
$appts    = $conn->query("SELECT COUNT(*) c FROM appointments")->fetch_assoc()['c'];
$visits   = $conn->query("SELECT COUNT(*) c FROM visits")->fetch_assoc()['c'];
$meds     = $conn->query("SELECT COUNT(*) c FROM medicines")->fetch_assoc()['c'];
$low      = $conn->query("SELECT COUNT(*) c FROM medicines WHERE quantity <= reorder_level")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html>
<head><title>Reports</title><link rel="stylesheet" href="../styles.css"></head>
<body>
<div class="layout">
  <div class="sidebar">
    <h3>ETEI Clinic</h3>
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="patients.php">👥 Patients</a>
    <a href="appointments.php">📅 Appointments</a>
    <a href="visit.php">🩺 Visits</a>
    <a href="medicines.php">💊 Medicines</a>
    <a class="active" href="reports.php">📊 Reports</a>
    <a href="../logout.php">🚪 Logout</a>
  </div>
  <div class="main">
    <div class="topbar"><h2>Clinic Reports</h2></div>
    <div class="cards">
      <div class="card"><h4>Registered Patients</h4><div class="num"><?= $patients ?></div></div>
      <div class="card"><h4>Total Appointments</h4><div class="num"><?= $appts ?></div></div>
      <div class="card"><h4>Total Visits</h4><div class="num"><?= $visits ?></div></div>
      <div class="card"><h4>Medicines</h4><div class="num"><?= $meds ?></div></div>
      <div class="card"><h4>Low Stock</h4><div class="num"><?= $low ?></div></div>
    </div>
  </div>
</div>
</body>
</html>