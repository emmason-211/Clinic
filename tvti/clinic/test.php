<?php
include '../db.php';
$res = $conn->query("SELECT * FROM appointments LIMIT 5");
echo "<pre>";
while($r = $res->fetch_assoc()) {
    print_r($r);
}
echo "</pre>";
?>