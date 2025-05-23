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
if ($_SESSION['role_acc'] !== 'admin') {
    header("Location: login.php"); // admin ไป admin.php
    exit;
}else{
    $user_acc = $_SESSION["username_acc"];
    $show_user = null;

    $query_show_user = "SELECT * FROM account WHERE username_acc = ?";
    $stmt_show_user = $conn->prepare($query_show_user);

    if ($stmt_show_user === false) {
        // กรณี prepare statement ไม่สำเร็จ (อาจมีปัญหาที่ SQL query หรือการเชื่อมต่อ)
        error_log("MySQLi prepare failed: " . $conn->error); // เก็บ log ไว้ดูข้อผิดพลาด
        if ($conn) $conn->close();
        header("Location: admin.php?status=dberror"); // แสดงข้อผิดพลาดทั่วไปให้ผู้ใช้
        exit;
    }//ของ stmt
    $stmt_show_user->bind_param("s", $user_acc); // "s" หมายถึง $username เป็น string
    if ($stmt_show_user->execute()) {
        $result = $stmt_show_user->get_result();
        if ($result->num_rows === 1) {
            $show_user = $result->fetch_assoc();
        } else {
            // กรณีที่ไม่ควรเกิดขึ้น: admin ที่ login อยู่แต่หาข้อมูลใน DB ไม่เจอ
            error_log("Admin user data not found in DB for username: " . $current_admin_username);
            // อาจจะ redirect หรือแสดงข้อความผิดพลาดที่เหมาะสม
        }
    } else {
        error_log("MySQLi execute failed (admin page): " . $stmt_show_user->error);
        if (isset($conn) && $conn) $conn->close();
        header("Location: admin.php?status=dberror_execute");
        exit;
    }
    $stmt_show_user->close(); // ปิด statement หลังใช้งานเสร็จ
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
        <div class="is-centered">
            <?php echo htmlspecialchars($show_user['username_acc']); ?>
        </div>
        <div class="is-centered register">
            <a href="index.php" class="button">Go to main page</a>
        </div>
        <div class="is-centered register">
            <a href="index.php?logout=1" class="button" >Logout</a>
        </div>

</body>
</html>
<?php
// ปิด connection ที่ท้ายไฟล์ ถ้ายังไม่ได้ปิด
if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>