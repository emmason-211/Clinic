<?php
include '../db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php"); exit;
}

// ---- ADD MEDICINE ----
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $n = $_POST['name'];
    $c = $_POST['category'];
    $q = (int)$_POST['quantity'];
    $u = $_POST['unit'];
    $e = $_POST['expiry'];
    $r = (int)$_POST['reorder'];

    $conn->query("INSERT INTO medicines (name,category,quantity,unit,expiry_date,reorder_level)
                  VALUES ('$n','$c',$q,'$u','$e',$r)");
    $msg = "Medicine added!";
}

// ---- UPDATE QUANTITY ----
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_qty'])) {
    $id = (int)$_POST['id'];
    $q  = (int)$_POST['quantity'];
    $conn->query("UPDATE medicines SET quantity=$q WHERE id=$id");
    $msg = "Quantity updated!";
}

// ---- DELETE MEDICINE ----
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM medicines WHERE id=$id");
    header("Location: medicines.php");
    exit;
}

$meds = $conn->query("SELECT * FROM medicines ORDER BY id DESC");
$total = $conn->query("SELECT COUNT(*) c FROM medicines")->fetch_assoc()['c'];
$low   = $conn->query("SELECT COUNT(*) c FROM medicines WHERE quantity <= reorder_level")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html>
<head>
  <title>Manage Medicines - Admin</title>
  <link rel="stylesheet" href="../styles.css">
</head>
<body>
<div class="layout">
  <div class="sidebar">
    <h3>ETEI Admin</h3>
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="users.php">👥 Users</a>
    <a class="active" href="medicines.php">💊 Medicines</a>
    <a href="../logout.php">🚪 Logout</a>
  </div>

  <div class="main">
    <div class="topbar">
      <h2>Medicine Inventory (Admin)</h2>
      <span class="user"><?= $_SESSION['name'] ?></span>
    </div>

    <div class="cards" style="margin-bottom:20px;">
      <div class="card"><h4>Total Medicines</h4><div class="num"><?= $total ?></div></div>
      <div class="card"><h4>Low Stock Items</h4><div class="num"><?= $low ?></div></div>
    </div>

    <?php if (!empty($msg)): ?>
      <p style="color:#0f766e; margin-bottom:12px;"><b><?= $msg ?></b></p>
    <?php endif; ?>

    <!-- ADD MEDICINE FORM -->
    <div class="form-box" style="margin-bottom:20px;">
      <h3>Add New Medicine</h3>
      <form method="POST">
        <input name="name" placeholder="Medicine Name" required>
        <input name="category" placeholder="Category (e.g. Antibiotic)">
        <input name="quantity" type="number" placeholder="Quantity" required>
        <input name="unit" placeholder="Unit (tablet, bottle, ml)">
        <input name="expiry" type="date" title="Expiry Date">
        <input name="reorder" type="number" placeholder="Reorder Level" value="10">
        <button class="btn" type="submit" name="add">Add Medicine</button>
      </form>
    </div>

    <!-- MEDICINES TABLE -->
    <table>
      <tr>
        <th>ID</th><th>Name</th><th>Category</th><th>Quantity</th>
        <th>Unit</th><th>Expiry</th><th>Reorder</th>
        <th>Status</th><th>Update Qty</th><th>Action</th>
      </tr>
      <?php while($m = $meds->fetch_assoc()): ?>
      <tr>
        <td><?= $m['id'] ?></td>
        <td><?= htmlspecialchars($m['name']) ?></td>
        <td><?= htmlspecialchars($m['category']) ?></td>
        <td><?= $m['quantity'] ?></td>
        <td><?= $m['unit'] ?></td>
        <td><?= $m['expiry_date'] ?></td>
        <td><?= $m['reorder_level'] ?></td>
        <td>
          <?php if ($m['quantity'] <= $m['reorder_level']): ?>
            <span class="badge cancelled">Low Stock</span>
          <?php elseif (!empty($m['expiry_date']) && strtotime($m['expiry_date']) < time()): ?>
            <span class="badge pending">Expired</span>
          <?php else: ?>
            <span class="badge completed">In Stock</span>
          <?php endif; ?>
        </td>
        <td>
          <form method="POST" style="display:flex; gap:5px;">
            <input type="hidden" name="id" value="<?= $m['id'] ?>">
            <input type="number" name="quantity" value="<?= $m['quantity'] ?>" style="width:70px; padding:4px;">
            <button class="btn" name="update_qty" type="submit" style="padding:5px 10px;">Save</button>
          </form>
        </td>
        <td>
          <a class="btn red" href="?delete=<?= $m['id'] ?>"
             onclick="return confirm('Delete this medicine?')">Delete</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </table>
  </div>
</div>
</body>
</html>