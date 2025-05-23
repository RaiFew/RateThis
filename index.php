<?php 
session_start();

// require 'connection.php';

$username_acc = null;
if (!isset($_SESSION['role_acc'])){
    //header('Location: login.php');
}elseif (isset($_GET['logout'])){
    session_destroy();
    header('Location: login.php');
}
/*if (isset($_SESSION['username_acc'])){
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
}*/
?>

<!DOCTYPE html>
<html>
<head>
    <title>Rate This</title>
    <link rel="stylesheet" href="style.css">
</head>
<style>
    .hero-head{
        background-color: #1e1e1e;
    }
    .hero-body{
        background-color: #e0e0e0;
    }
    .hero-foot{
        background-color: #1e1e1e;
    }
    .navbar-item:hover{
        background-color: #1e1e1e;
        color: #f5f5f5;
    }
    .navbar-item:{
        color: #1e1e1e;
    }
    .button:hover{
        background-color: #f5f5f5;
        color:#1e1e1e;
    }
    .btn_H_blue:hover{
        background-color: #60a5fa;!important
    }
</style>
<body>
<section class="hero is-info is-fullheight">
  <div class="hero-head">
    <nav class="navbar">
      <div class="container">
        <div class="navbar-brand">
          <a class="navbar-item">
            <img src="https://bulma.io/assets/images/bulma-type-white.png" alt="Logo" />
          </a>
          <span class="navbar-burger" data-target="navbarMenuHeroB">
            <span></span>
            <span></span>
            <span></span>
            <span></span>
          </span>
        </div>
        <div id="navbarMenuHeroB" class="navbar-menu">
          <div class="navbar-end">
            <?php if(!isset($_SESSION['role_acc'])): ?>
                <span class="navbar-item">
                <a href="login.php" class="button is-info is-inverted" >Login</a>
                </span>
            <?php endif; ?>
            <?php if (isset($_SESSION['username_acc'])):?>
            <span class="navbar-item">
              <a class="button is-info is-inverted">
                <span>

                <?php echo htmlspecialchars($_SESSION['username_acc']); ?>
                    
                </span>
              </a>
            </span>
            <span class="navbar-item">
                <a href="index.php?logout=1" class="button is-info is-inverted" >Logout</a>
            </span>
                <?php if ($_SESSION['role_acc'] ==='admin'): // ถ้า เป็น admin ให้แสดงปุ่ม ?>
                    <span class="navbar-item">
                <a href="admin.php" class="button is-info is-inverted" >Admin</a>
                    </span>
                <?php endif; ?>
                <?php endif; ?>

          </div>
        </div>
      </div>
    </nav>
  </div>

  <div class="hero-body">
    <div class="container has-text-centered">
      <p class="title">RateThis</p>
      <p class="subtitle">website for review and comment about<br> manga manhwa manhua or movie</p>
    </div>
  </div>

  <div class="hero-foot is-medium">
    <nav class="tabs is-boxed is-fullwidth ">
      <div class="container ">
        <ul class="">
          <li>
            <span >
            <a class="button is-inverted btn_H_blue "href="comics.php">Comics</a>
            </span>
          </li>
          <li>
          <span class="">
            <a class="button is-inverted btn_H_blue"href="movie.php">Movie</a>
            </span>
          </li>
          <li>
            <a class="button is-inverted btn_H_blue"href="series.php">Series</a>
          </li>
        </ul>
      </div>
    </nav>
  </div>
</section>
</body>
</html>