<?php
define('ALLOW_CONNECTION_ACCESS', true);
require_once 'connection.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
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
</head>

<body>
    <div>
    <?php if (isset($_GET['status'])) {
    $status = $_GET['status'];
    $message_text = '';
    $message_class = '';

    switch ($status) {
        case 'fail':
            $message_text = "Username หรือ Password ไม่ถูกต้อง";
            $message_class = 'error';
            break;
        case 'noacc':
            $message_text = "เกิดข้อผิดพลาดในการลงทะเบียน กรุณาลองใหม่อีกครั้ง";
            $message_class = 'error';
            break;
        case 'emptyfields':
            $message_text = "กรุณากรอกข้อมูลให้ครบทุกช่อง";
            $message_class = 'error';
            break;
        // คุณสามารถเพิ่ม case อื่นๆ ได้ตามต้องการ
    }

    if (!empty($message_text)) {
        echo '<div class="message is-centered ' . $message_class . ' mb-4">' . htmlspecialchars($message_text) . '</div>';
    }
}
?>
    <form class="box" action="process_login.php" method="post">
        <label class="title">Login</label>
        <div class="flex-container">
                <strong>Username</strong>
            <div class="control">
                <input class="input" type="text" name="username" placeholder="Username">
            </div>
        </div>
        <div class="flex-container">
            <strong>Password</strong>
            <div class="control">
                <input class="input" type="password" name="password" placeholder="Password">
                <a href="forgotpass.php" style="text-align: right;">Forgot password?</a>
            </div>
        </div>
        <button class="button" type="submit" id="login" name="login">Login</button>
        <div class="is-centered register">
            <a href="register.php">If you don't have an account, register here</a>
        </div>
    </form>
    </div>
</body>

</html>