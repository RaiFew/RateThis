<?php 
session_start();
$open_conn = 1;
require 'connection.php';

if (!isset($_SESSION['role_acc'])){
    header('Location: login.php');
}elseif (isset($_GET['logout'])){
    session_destroy();
    header('Location: login.php');
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Rate This</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="is-centered register">
        <a href="login.php">If you have an account, login here</a>
    </div>
    <div class="is-centered register">
            <a href="register.php">If you don't have an account, register here</a>
        </div>
        <div class="is-centered register">
            <a href="index.php?logout=1" class="button" >Logout</a>
            <?php if ($_SESSION['role_acc'] ==='admin'): // ถ้า เป็น admin ให้แสดงปุ่ม ?>
            <a href="admin.php" class="button" >Admin</a>
            <?php endif; ?>
        </div>
</body>
</html>