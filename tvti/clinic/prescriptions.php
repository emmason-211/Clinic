<?php
include '../db.php';

// ── CHECK LOGIN ──
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// ── CHECK ROLE ──
$role = strtolower($_SESSION['role']);
if ($role == 'patient') {
    header("Location: ../my_appointments.php");
    exit;
}

$msg = "";

// ── SAVE PRESCRIPTION ──
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {

    $visit_id = (int)$_POST['visit_id'];
    $doctor   = (int)$_SESSION['user_id'];

    if ($visit_id > 0) {

        // Insert prescription header
        $conn->query("INSERT INTO prescriptions (visit_id, prescribed_by)
                      VALUES ($visit_id, $doctor)");
        $pid = $conn->insert_id;

        // Insert each medicine item
        if (!empty($_POST['medicine_id'])) {
            foreach ($_POST['medicine_id'] as $i => $mid) {
                $mid = (int)$mid;
                if ($mid <= 0) continue;

                $dose = $conn->real_escape_string($_POST['dosage'][$i] ?? '');
                $freq = $conn->real_escape_string($_POST['frequency'][$i] ?? '');
                $dur  = $conn->real_escape_string($_POST['duration'][$i] ?? '');

                $conn->query("INSERT INTO prescription_items
                              (prescription_id, medicine_id, dosage, frequency, duration)
                              VALUES ($pid, $mid, '$dose', '$freq', '$dur')");
            }
        }

        $msg = "Prescription saved successfully!";
    } else {
        $msg = "Please select a visit.";
    }
}

// ── FETCH VISITS ──
$visits = $conn->query("SELECT v.id, u.name, v.visit_date
                        FROM visits v
                        JOIN patients p ON v.patient_id = p.id
                        JOIN users u ON p.user_id = u.id
                        ORDER BY v.id DESC");

// ── FETCH MEDICINES ──
$meds = $conn->query("SELECT id, name FROM medicines ORDER BY name");
$med_list = [];
while ($m = $meds->fetch_assoc()) {
    $med_list[] = $m;
}

// ── FETCH PRESCRIPTIONS ──
$list = $conn->query("SELECT pr.id, pr.created_at,
                             v.visit_date,
                             u.name AS patient,
                             us.name AS doctor_name
                      FROM prescriptions pr
                      JOIN visits v ON pr.visit_id = v.id
                      JOIN patients p ON v.patient_id = p.id
                      JOIN users u ON p.user_id = u.id
                      JOIN users us ON pr.prescribed_by = us.id
                      ORDER BY pr.id DESC");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Prescriptions - ETEI Clinic</title>
  <link rel="stylesheet" href="../styles.css">
</head>
<body>
<div class="layout">

  <!-- SIDEBAR -->
  <div class="sidebar">
    <h3>ETEI Clinic</h3>
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="patients.php">👥 Patients</a>
    <a href="appointments.php">📅 Appointments</a>
    <a href="visit.php">🩺 Visits</a>
    <a class="active" href="prescriptions.php">📝 Prescriptions</a>
    <a href="medicines.php">💊 Medicines</a>
    <a href="reports.php">📊 Reports</a>
    <a href="../logout.php">🚪 Logout</a>
  </div>

  <!-- MAIN -->
  <div class="main">
    <div class="topbar">
      <h2>Prescriptions</h2>
      <span class="user"><?= htmlspecialchars($_SESSION['name']) ?> (<?= ucfirst($role) ?>)</span>
    </div>

    <?php if ($msg != ""): ?>
      <p style="color:green;"><b><?= $msg ?></b></p>
    <?php endif; ?>

    <!-- FORM -->
    <div class="form-box" style="margin-bottom:20px;">
      <h3>New Prescription</h3>
      <form method="POST">

        <label>Select Visit:</label>
        <select name="visit_id" required>
          <option value="">-- Choose Visit --</option>
          <?php if ($visits && $visits->num_rows > 0): ?>
            <?php while($v = $visits->fetch_assoc()): ?>
              <option value="<?= $v['id'] ?>">
                #<?= $v['id'] ?> - <?= htmlspecialchars($v['name']) ?> (<?= $v['visit_date'] ?>)
              </option>
            <?php endwhile; ?>
          <?php endif; ?>
        </select>

        <h4 style="margin-top:15px;">Medicines</h4>

        <?php for ($i = 0; $i < 3; $i++): ?>
        <div style="display:flex; gap:8px; margin-bottom:8px; flex-wrap:wrap;">
          <select name="medicine_id[]" style="flex:2; padding:8px;">
            <option value="">-- Select Medicine --</option>
            <?php foreach ($med_list as $m): ?>
              <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['name']) ?></option>
            <?php endforeach; ?>
          </select>
          <input name="dosage[]"    placeholder="Dosage"    style="flex:1; padding:8px;">
          <input name="frequency[]" placeholder="Frequency" style="flex:1; padding:8px;">
          <input name="duration[]"  placeholder="Duration"  style="flex:1; padding:8px;">
        </div>
        <?php endfor; ?>

        <button class="btn" type="submit" name="add" style="margin-top:10px;">
          Save Prescription
        </button>
      </form>
    </div>

    <!-- TABLE -->
    <table>
      <tr>
        <th>ID</th>
        <th>Patient</th>
        <th>Doctor/Nurse</th>
        <th>Visit Date</th>
        <th>Medicines</th>
      </tr>

      <?php if (!$list || $list->num_rows == 0): ?>
        <tr><td colspan="5" style="text-align:center; padding:20px;">No prescriptions yet.</td></tr>
      <?php else: ?>
        <?php while($p = $list->fetch_assoc()):
          $pid = (int)$p['id'];
          $items = $conn->query("SELECT pi.*, m.name
                                 FROM prescription_items pi
                                 JOIN medicines m ON pi.medicine_id = m.id
                                 WHERE pi.prescription_id = $pid");
        ?>
        <tr>
          <td><?= $p['id'] ?></td>
          <td><?= htmlspecialchars($p['patient']) ?></td>
          <td><?= htmlspecialchars($p['doctor_name']) ?></td>
          <td><?= $p['visit_date'] ?></td>
          <td>
            <?php if (!$items || $items->num_rows == 0): ?>
              <em>No items</em>
            <?php else: ?>
              <?php while($it = $items->fetch_assoc()): ?>
                <div style="margin-bottom:4px;">
                  💊 <b><?= htmlspecialchars($it['name']) ?></b> —
                  <?= htmlspecialchars($it['dosage']) ?>,
                  <?= htmlspecialchars($it['frequency']) ?>,
                  <?= htmlspecialchars($it['duration']) ?>
                </div>
              <?php endwhile; ?>
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