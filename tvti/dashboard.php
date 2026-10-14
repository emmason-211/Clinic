<?php
include 'db.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$role = $_SESSION['role'];
if ($role == 'admin')      header("Location: admin/dashboard.php");
elseif ($role == 'patient') header("Location: my_appointments.php");
else                        header("Location: clinic/dashboard.php");
?>