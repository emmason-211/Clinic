<?php
include '../db.php';
if (!isset($_SESSION['user_id'])) { header("Location: ../login.php"); exit; }

if ($_SERVER['REQUEST_METHOD']=='POST') {
    $pid=$_POST['patient_id']; $sym=$_POST['symptoms'];
    $dia=$_POST['diagnosis']; $tre=$_POST['treatment'];
    $conn->query("INSERT INTO visits (patient_id,symptoms,diagnosis,treatment,visit_date)
                  VALUES ($pid,'$sym','$dia','$tre',CURDATE())");
}
$patients = $conn->query("SELECT p.id, u.name FROM patients p JOIN users u ON p.user_id=u.id");
$visits   = $conn->query("SELECT v.*, u.name FROM visits v 
                          JOIN patients p ON v.patient_id=p.id
                          JOIN users u ON p.user_id=u.id ORDER BY v.id DESC");
?>
<!DOCTYPE html>
<html>
<head><title>Visits</title><link rel="stylesheet" href="../styles.css"></head>
<body>
<div class="layout">
  <div class="sidebar">
    <h3>ETEI Clinic</h3>
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="patients.php">👥 Patients</a>
    <a href="appointments.php">📅 Appointments</a>
    <a class="active" href="visit.php">🩺 Visits</a>
    <a href="medicines.php">💊 Medicines</a>
    <a href="reports.php">📊 Reports</a>
    <a href="../logout.php">🚪 Logout</a>
  </div>
  <div class="main">
    <div class="topbar"><h2>Medical Visits</h2></div>

    <div class="form-box" style="margin-bottom:20px;">
      <h3>Record New Visit</h3>
      <form method="POST">
        <select name="patient_id" required>
          <option value="">-- Select Patient --</option>
          <?php while($p=$patients->fetch_assoc()): ?>
            <option value="<?= $p['id'] ?>"><?= $p['name'] ?></option>
          <?php endwhile; ?>
        </select>
        <textarea name="symptoms" placeholder="Symptoms"></textarea>
        <textarea name="diagnosis" placeholder="Diagnosis"></textarea>
        <textarea name="treatment" placeholder="Treatment"></textarea>
        <button class="btn" type="submit">Save Visit</button>
      </form>
    </div>

    <table>
      <tr><th>ID</th><th>Patient</th><th>Symptoms</th><th>Diagnosis</th><th>Treatment</th><th>Date</th></tr>
      <?php while($v=$visits->fetch_assoc()): ?>
      <tr>
        <td><?= $v['id'] ?></td>
        <td><?= $v['name'] ?></td>
        <td><?= $v['symptoms'] ?></td>
        <td><?= $v['diagnosis'] ?></td>
        <td><?= $v['treatment'] ?></td>
        <td><?= $v['visit_date'] ?></td>
      </tr>
      <?php endwhile; ?>
    </table>
  </div>
</div>
</body>
</html>