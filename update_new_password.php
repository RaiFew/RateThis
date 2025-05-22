<?php
require_once('connection.php');

if (isset($_POST['new_password']) && isset($_POST['confirm_password'])&& isset($_POST['token'])) {
    $token = $_POST['token'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    if (empty($token) || empty($new_password) || empty($confirm_password)) {
        header("Location: newpass.php?token=" . urlencode($token) . "&status=pwdempty"); 
        exit;
    }
    $query_check_token = "SELECT email_reset FROM password_resets WHERE token_reset = ? and expires_at_reset > UTC_TIMESTAMP()";
    $stmt = $conn->prepare($query_check_token);
    if ($stmt === false) {
        // กรณี prepare statement ไม่สำเร็จ (อาจมีปัญหาที่ SQL query หรือการเชื่อมต่อ)
        error_log("MySQLi prepare failed: " . $conn->error); // เก็บ log ไว้ดูข้อผิดพลาด
        header("Location: newpass.php?token=" . urlencode($token) . "&status=dberror"); // แสดงข้อผิดพลาดทั่วไปให้ผู้ใช้
        exit;
    }
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {// ถ้าไม่มี token
        $stmt->close();
        $conn->close();
        header("Location: newpass.php?token=" . urlencode($token) . "&status=invalidtokenupdate");
        exit;
    }else{
        $row = $result->fetch_assoc(); 
        $email_to_update = $row['email_reset'];
        $stmt->close(); // <--- เพิ่มการปิด statement ที่นี่
        if ($new_password !== $confirm_password) {
            header("Location: newpass.php?token=" . urlencode($token) . "&status=pwdnotmatch");
            exit; 
        }

        $options = [
            'cost' => PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
            'time_cost' => PASSWORD_ARGON2_DEFAULT_TIME_COST,
            'thread' => PASSWORD_ARGON2_DEFAULT_THREADS
        ];
        $passHash = password_hash($new_password, PASSWORD_ARGON2ID,$options);//นำรหัสมาเข้ารหัสอีกที
        $query_update_password = 'UPDATE account SET password_acc = ? WHERE email_acc = ?';
        $stmt_update_pass = $conn->prepare($query_update_password);

        if ($stmt_update_pass === false) {
            error_log("MySQLi prepare failed (update password): " . $conn->error);
            if(isset($conn) && $conn) $conn->close();
            header("Location: newpass.php?token=" . urlencode($token) . "&status=dberror");
            exit;
        }
        $stmt_update_pass->bind_param("ss", $passHash, $email_to_update);
        if ($stmt_update_pass->execute()) {
            $stmt_update_pass->close(); // ปิด statement นี้

            // 7. ลบ Token ที่ใช้งานแล้วออกจากตาราง password_resets
            $query_delete_token = "DELETE FROM password_resets WHERE token_reset = ?";
            $stmt_delete_token = $conn->prepare($query_delete_token);

            if ($stmt_delete_token) {
                $stmt_delete_token->bind_param("s", $token);
                $stmt_delete_token->execute();
                $stmt_delete_token->close();
            } else {
                error_log("MySQLi prepare failed (delete used token): " . $conn->error);
                // การทำงานหลักสำเร็จแล้ว อาจจะไม่ต้องแจ้งผู้ใช้ แต่ควร log ไว้
            }
            if(isset($conn) && $conn) $conn->close();
                header("Location: login.php?status=password_reset_success");
                exit;
        }else { // กรณี execute อัปเดตรหัสผ่านไม่สำเร็จ
            error_log("Execute failed (update password): " . $stmt_update_pass->error);
            $stmt_update_pass->close();
            if(isset($conn) && $conn) $conn->close();
            header("Location: newpass.php?token=" . urlencode($token) . "&status=update_failed");
            exit;
        }
    }
    
}else{ // else ของ isset
    header('Location: login.php');
    exit;
}
?>