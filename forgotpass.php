<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot pass</title>
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
</style>
<body>
    <div>
<?php if (isset($_GET['status'])) {
    $status = $_GET['status'];
    $message_text = '';
    $message_class = '';

    switch ($status) {
        case 'dberror':
            $message_text = "เกิดข้อผิดพลาดในการลงทะเบียน กรุณาลองใหม่อีกครั้ง";
            $message_class = 'error';
            break;
        case 'emailnotmatch':
            $message_text = "email ไม่ตรงกับ ข้อมูล";
            $message_class = 'error';
            break;
        case 'nouser':
            $message_text = "ไม่พบผู้ใช้";
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
    <form class="box" action="process_forgot.php" method="post">
    <label class="title">Forgot Password</label>
        <div class="flex-container">
                <strong>Username</strong>
            <div class="control">
                <input class="input" type="text" name="username" placeholder="Username">
            </div>
        </div>
        <div class="flex-container">
            <strong>Email</strong>
            <div class="control">
                <input class="input" type="email" name="email" placeholder="email" required>
            </div>
        </div>
        <button class="button" type="submit" id="Forgotpass" name="Forgotpass">Submit</button>
        <div class="is-centered register">
            <a href="login.php">If you have an account, login here</a>
        </div>
    </form>
    </div>
</body>
</html>