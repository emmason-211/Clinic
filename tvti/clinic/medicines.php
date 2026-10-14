<?php
include '../db.php';
if (!isset($_SESSION['user_id'])) { header("Location: ../login.php"); exit; }

if ($_SERVER['REQUEST_METHOD']=='POST') {
    $n=$_POST['name']; $c=$_POST['category']; $q=$_POST['quantity'];
    $u=$_POST['unit']; $e=$_POST['expiry']; $r=$_POST['reorder'];
    $conn->query("INSERT INTO medicines (name,category,quantity,unit,expiry_date,reorder_level)
                  VALUES ('$n','$c',$q,'$u','$e',$r)");
}
$res = $conn->query("SELECT * FROM medicines ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head><title>Medicines</title><link rel="stylesheet" href="../styles.css"></head>
<body>
<div class="layout">
  <div class="sidebar">
    <h3>ETEI Clinic</h3>
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="patients.php">👥 Patients</a>
    <a href="appointments.php">📅 Appointments</a>
    <a href="visit.php">🩺 Visits</a>
    <a class="active" href="medicines.php">💊 Medicines</a>
    <a href="reports.php">📊 Reports</a>
    <a href="../logout.php">🚪 Logout</a>
  </div>
  <div class="main">
    <div class="topbar"><h2>Medicine Inventory</h2></div>

    <div class="form-box" style="margin-bottom:20px;">
      <h3>Add Medicine</h3>
      <form method="POST">
        <input name="name" placeholder="Medicine Name" required>
        <input name="category" placeholder="Category">
        <input name="quantity" type="number" placeholder="Quantity" required>
        <input name="unit" placeholder="Unit (tablet, ml...)">
        <input name="expiry" type="date">
        <input name="reorder" type="number" placeholder="Reorder level" value="10">
        <button class="btn" type="submit">Save</button>
      </form>
    </div>

    <table>
      <tr><th>ID</th><th>Name</th><th>Category</th><th>Qty</th><th>Unit</th><th>Expiry</th><th>Reorder</th><th>Status</th></tr>
      <?php while($m=$res->fetch_assoc()): ?>
      <tr>
        <td><?= $m['id'] ?></td>
        <td><?= $m['name'] ?></td>
        <td><?= $m['category'] ?></td>
        <td><?= $m['quantity'] ?></td>
        <td><?= $m['unit'] ?></td>
        <td><?= $m['expiry_date'] ?></td>
        <td><?= $m['reorder_level'] ?></td>
        <td>
          <?php if ($m['quantity'] <= $m['reorder_level']): ?>
            <span class="badge cancelled">Low Stock</span>
          <?php else: ?>
            <span class="badge completed">In Stock</span>
          <?php endif; ?>
        </td>
      </tr>
      <?php endwhile; ?>
    </table>
  </div>
</div>
</body>
</html>