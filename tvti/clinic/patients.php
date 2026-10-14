<?php
include '../db.php';
if (!isset($_SESSION['user_id'])) { header("Location: ../login.php"); exit; }

// ── ADD NEW PATIENT ──
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Step 1: Create a user account
    $name  = $_POST['name'];
    $email = $_POST['email'];
    $pass  = password_hash('1234', PASSWORD_DEFAULT);   // default password
    $role  = 'patient';

    $conn->query("INSERT INTO users (name, email, password, role)
                  VALUES ('$name', '$email', '$pass', '$role')");
    $uid = $conn->insert_id;

    // Step 2: Create the patient profile
    $type  = $_POST['type'];
    $reg   = $_POST['reg'];
    $dept  = $_POST['dept'];
    $phone = $_POST['phone'];
    $gender= $_POST['gender'];
    $dob   = $_POST['dob'];

    $conn->query("INSERT INTO patients
                  (user_id, patient_type, registration_no, department, phone, gender, dob)
                  VALUES ($uid, '$type', '$reg', '$dept', '$phone', '$gender', '$dob')");

    $msg = "Patient registered successfully!";
}

// ── FETCH ALL PATIENTS ──
$patients = $conn->query("SELECT p.*, u.name, u.email
                          FROM patients p
                          JOIN users u ON p.user_id = u.id
                          ORDER BY p.id DESC");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Patients - FDRE TVTI Clinic</title>
  <link rel="stylesheet" href="../styles.css">
</head>
<body>
<div class="layout">
  <div class="sidebar">
    <h3>ETEI Clinic</h3>
    <a href="dashboard.php">🏠 Dashboard</a>
    <a class="active" href="patients.php">👥 Patients</a>
    <a href="appointments.php">📅 Appointments</a>
    <a href="visit.php">🩺 Visits</a>
    <a href="../logout.php">🚪 Logout</a>
  </div>

  <div class="main">
    <div class="topbar"><h2>Patient Management</h2></div>

    <?php if (!empty($msg)): ?>
      <p style="color:green;"><b><?= $msg ?></b></p>
    <?php endif; ?>

    <!-- ADD PATIENT FORM -->
    <div class="form-box" style="margin-bottom:20px;">
      <h3>Register New Patient</h3>
      <form method="POST">
        <input name="name"   placeholder="Full Name" required>
        <input name="email"  type="email" placeholder="Email" required>

        <select name="type">
          <option value="student">Student</option>
          <option value="staff">Staff</option>
        </select>

        <input name="reg"    placeholder="Registration No. (e.g. TTBR/186/16)">
        <input name="dept"   placeholder="Department (e.g. ICT)">
        <input name="phone"  placeholder="Phone (e.g. 0911223344)">

        <select name="gender">
          <option value="male">Male</option>
          <option value="female">Female</option>
        </select>

        <input name="dob" type="date">

        <button class="btn" type="submit">Save Patient</button>
      </form>
    </div>

    <!-- PATIENTS TABLE -->
    <table>
      <tr>
        <th>ID</th><th>Name</th><th>Email</th><th>Type</th>
        <th>Reg No</th><th>Department</th><th>Phone</th>
      </tr>
      <?php while($p = $patients->fetch_assoc()): ?>
      <tr>
        <td><?= $p['id'] ?></td>
        <td><?= htmlspecialchars($p['name']) ?></td>
        <td><?= htmlspecialchars($p['email']) ?></td>
        <td><?= ucfirst($p['patient_type']) ?></td>
        <td><?= $p['registration_no'] ?></td>
        <td><?= $p['department'] ?></td>
        <td><?= $p['phone'] ?></td>
      </tr>
      <?php endwhile; ?>
    </table>
  </div>
</div>
</body>
</html>