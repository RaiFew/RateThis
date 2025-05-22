<?php 
    require_once('connection.php');
    $token_is_valid = false;         // ตัวแปรสถานะความถูกต้องของ Token
    $token_from_url = '';            // ตัวแปรเก็บ Token ที่ได้จาก URL
    $display_message = '';           // ตัวแปรสำหรับเก็บข้อความที่จะแสดงผล (Error หรือ Success)
    if (isset($_GET['token'])) {
        $token_from_url = trim($_GET['token']);
    
        if (empty($token_from_url)) {
            $display_message = "Token ไม่ถูกต้องหรือไม่พบ Token ในลิงก์";
        }else {
            // ตรวจสอบ Token กับฐานข้อมูล
            $sql_check_token = "SELECT email_reset FROM password_resets WHERE token_reset = ? AND expires_at_reset > NOW()";
            $stmt_check = $conn->prepare($sql_check_token);
    
            if ($stmt_check) {
                $stmt_check->bind_param("s", $token_from_url);
                $stmt_check->execute();
                $result_check = $stmt_check->get_result();
    
                if ($result_check->num_rows === 1) {
                    // Token ถูกต้อง และยังไม่หมดอายุ
                    $token_is_valid = true;
                    // $token_data = $result_check->fetch_assoc(); // อาจจะดึง email_reset มาเก็บไว้ถ้าต้องการ
                } else {
                    // Token ไม่พบในฐานข้อมูล หรือ หมดอายุแล้ว
                    $display_message = "ลิงก์สำหรับตั้งรหัสผ่านใหม่ไม่ถูกต้อง, หมดอายุ หรือถูกใช้งานไปแล้ว";
    
                    // (Optional) ลบ Token ที่ไม่ถูกต้องหรือหมดอายุออกจากฐานข้อมูล
                    $sql_delete_invalid = "DELETE FROM password_resets WHERE token_reset = ? OR expires_at_reset <= NOW()";
                    $stmt_delete = $conn->prepare($sql_delete_invalid);
                    if ($stmt_delete) {
                         $stmt_delete->bind_param("s", $token_from_url); // ใช้ token ที่รับมาเพื่อลบตัวมันเองถ้าไม่ valid หรือลบตัวอื่นๆ ที่หมดอายุ
                         $stmt_delete->execute();
                         $stmt_delete->close();
                    }
                }
                $stmt_check->close();
            }else {
                // กรณี prepare statement ไม่สำเร็จ
                error_log("MySQLi prepare failed (check token in newpass.php): " . $conn->error);
                $display_message = "เกิดข้อผิดพลาดในการตรวจสอบข้อมูล กรุณาลองใหม่อีกครั้งในภายหลัง";
            }
        }
    }else {
        // ไม่มีการส่ง Token มาใน URL
        $display_message = "ไม่พบ Token สำหรับการรีเซ็ตรหัสผ่าน";
    }
    if (isset($_GET['status'])) {
    // ควรจะแสดงข้อความจาก status ก่อน ถ้ามี
    $new_display_message = '';
    switch ($_GET['status']) {
        case 'pwdempty':
            $new_display_message = "กรุณากรอกรหัสผ่านใหม่ทั้งสองช่อง";
            break;
        case 'pwdnotmatch':
            $new_display_message = "รหัสผ่านใหม่และการยืนยันรหัสผ่านไม่ตรงกัน";
            break;
        case 'pwdshort': // ตัวอย่างเงื่อนไขความยาวรหัสผ่าน
            $new_display_message = "รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร";
            break;
        case 'update_failed':
            $new_display_message = "เกิดข้อผิดพลาดในการอัปเดตรหัสผ่าน กรุณาลองอีกครั้ง";
            break;
        case 'invalid_token_on_update':
            $new_display_message = "Token สำหรับรีเซ็ตไม่ถูกต้องหรือหมดอายุ (ขณะพยายามอัปเดต)";
            $token_is_valid = false; // ถ้า Token ไม่ valid ตอนอัปเดต ก็ไม่ควรแสดงฟอร์มอีก
            break;
        // สามารถเพิ่ม case อื่นๆ ตามที่ต้องการ
    }
    if (!empty($new_display_message)) {
        $display_message = $new_display_message; // ให้ข้อความจาก status ทับข้อความเดิม
    }

    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New pass</title>
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
        <?php if (!empty($display_message)): ?>
        <p class="message error-message"><?php echo htmlspecialchars($display_message); ?></p>
        <?php endif; ?>
        <?php if ($token_is_valid): // ถ้า Token ถูกต้อง ให้แสดงฟอร์ม ?>
        <form class="box" action="update_new_password.php" method="post">
            <label class="title">New Password</label>
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token_from_url); ?>">
            <div class="flex-container">
                <strong>Password</strong>
                <div class="control">
                    <input class="input" type="password" name="new_password" placeholder="Password">
                </div>
            </div>
            <div class="flex-container">
                <strong>Confirm Password</strong>
                <div class="control">
                    <input class="input" type="password" name="confirm_password" placeholder="Confirm Password">
                </div>
            </div>
            <button class="button" type="submit" value="new_pass">Submit</button>
            </form>
            <?php else: // ถ้า Token ไม่ valid หรือมี error message อื่นๆ ที่ทำให้ $token_is_valid เป็น false ?>
            <?php if(empty($display_message)) { // ถ้ายังไม่มี error message ใดๆ แต่ token ก็ไม่ valid (เช่น เข้ามาหน้าตรงๆ)
                echo '<p class="message error-message">ไม่สามารถดำเนินการได้ ลิงก์อาจไม่ถูกต้อง</p>';
            }?>
            <div class="link-forgotpass">
            <a href="forgotpass.php">กลับไปยังหน้าลืมรหัสผ่าน</a> </div>
        <?php endif; ?>
    </div>
</body>

</html>
<?php
if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>