<?php
require_once 'connection.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<style>
    body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            background-color: #1e1e1e;
        }
        .box {
            position: relative;
            min-width: 430px;
            max-width: 430px;
            padding: 2rem;
            margin: 1rem;
        }
        .flex-container {
            margin-bottom: 1rem;
        }
        .title {
            display: block;
            text-align: center;
            margin-bottom: 2rem;
            font-size: 2rem;
        }
        .button {
            width: 100%;
            margin-top: 1rem;
            background-color: #60a5fa;
        }
        .button:hover {
            background-color: #60a5fa;
            color: white;
        }
        .message { padding: 10px; margin-bottom: 15px; border-radius: 4px; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }

</style>
<body>
<div>
<?php
// ตรวจสอบและแสดงข้อความแจ้งเตือน
if (isset($_GET['status'])) {
    $status = $_GET['status'];
    $message_text = '';
    $message_class = '';

    switch ($status) {
        case 'emailtaken':
            $message_text = "อีเมลนี้ถูกใช้งานแล้ว กรุณาใช้อีเมลอื่น";
            $message_class = 'error';
            break;
        case 'dberror':
            $message_text = "เกิดข้อผิดพลาดในการลงทะเบียน กรุณาลองใหม่อีกครั้ง";
            $message_class = 'error';
            break;
        case 'emptyfields':
            $message_text = "กรุณากรอกข้อมูลให้ครบทุกช่อง";
            $message_class = 'error';
            break;
        case 'passwordmismatch':
            $message_text = "รหัสผ่านและการยืนยันรหัสผ่านไม่ตรงกัน";
            $message_class = 'error';
            break;
        case 'nodata':
            $message_text = "ไม่พบข้อมูลการส่ง";
            $message_class = 'error';
            break;
        case 'usernametaken':
            $message_text = 'ชื่อผู้ใช้นี้ถูกใช้ไปแล้ว กรุณาใช้ชื่อผู้ใช้อื่น';
            $message_class = 'error';
        // คุณสามารถเพิ่ม case อื่นๆ ได้ตามต้องการ
    }

    if (!empty($message_text)) {
        echo '<div class="message is-centered ' . $message_class . ' mb-4">' . htmlspecialchars($message_text) . '</div>';
    }
}
?>
    <form class="box" action="process_register.php" method="post">
        <label class="title">Register</label>
        <div class="flex-container">
            <strong>Username</strong>
            <div class="control">
                <input class="input" type="text" name="username"placeholder="Username" required>
            </div>
        </div>
        <div class="flex-container">
            <strong>Password</strong>
            <div class="control">
                <input class="input" type="password" name="password" placeholder="Password" required>
            </div>
        </div>
        <div class="flex-container">
            <strong>Confirm Password</strong>
            <div class="control">
                <input class="input" type="password" name="cpassword" placeholder="Confirm Password" required>
            </div>
        </div>
        <div class="flex-container">
            <strong>Email</strong>
            <div class="control">
                <input class="input" type="email" name="email" placeholder="email" required>
            </div>
        </div>
        <button class="button" type="submit" id="register" name="register">Register</button>
        <div class="is-centered register">
            <a href="login.php">If you have an account, login here</a>
        </div>
    </form>
    </div>
</body>
</html>