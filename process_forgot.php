<?php
require_once('connection.php');
if (isset($_POST['username']) && isset($_POST['email'])){
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);

    if (empty($username) || empty($email)) { // เช็คว่าข้อมูลที่ส่งมาว่างมั้ย
        if ($conn) $conn->close(); // <--- พิจารณาเพิ่มบรรทัดนี้
        header("Location: forgotpass.php?status=emptyfields");
        exit;
    }// ของ empty
    // ตรวจสอบ Username และ Email จากตาราง account
    $query_check_account = "SELECT email_acc FROM account WHERE username_acc = ?";//เลือกข้อมูลใน database จาก $username
    $stmt = $conn->prepare($query_check_account); // เตรีบมดึงข้อมูลจากบรรทัดก่อนหน้า

    if ($stmt === false) {
        // กรณี prepare statement ไม่สำเร็จ (อาจมีปัญหาที่ SQL query หรือการเชื่อมต่อ)
        error_log("MySQLi prepare failed: " . $conn->error); // เก็บ log ไว้ดูข้อผิดพลาด
        if ($conn) $conn->close();
        header("Location: forgotpass.php?status=dberror"); // แสดงข้อผิดพลาดทั่วไปให้ผู้ใช้
        exit;
    }//ของ stmt

    $stmt->bind_param("s", $username); // "s" หมายถึง $username เป็น string
    $stmt->execute();//สั่งทำงาน stmt
    $result = $stmt->get_result();//เอาผลลัพธ์จาก stmt เมื่อกี้ว่าเจอ username นี้เท่าไหร้

    if ($result->num_rows === 1) { //ถ้าเจอ
        $user_rows = $result->fetch_assoc();
        $db_email = $user_rows['email_acc'];

        // เปรียบเทียบ Email ที่กรอกกับ Email ในฐานข้อมูล (case-sensitive หรือ insensitive ขึ้นอยู่กับการออกแบบ)
        if (strtolower($email) === strtolower($db_email)) {
            // สร้าง Token
            $token = bin2hex(random_bytes(32)); // สร้าง Token แบบสุ่ม ปลอดภัย
            $expires_at = date("Y-m-d H:i:s", strtotime('+1 hour')); // Token หมดอายุใน 1 ชั่วโมง

            // เก็บ Token ลงฐานข้อมูล (ตาราง password_resets)
            // ก่อน insert ควรลบ token เก่าของ email นี้ (ถ้ามี) เพื่อให้มี active token เดียว
            $query_delete_old_token = "DELETE FROM password_resets WHERE email_reset = ?";
            $stmt_delete = $conn->prepare($query_delete_old_token);

            if($stmt_delete) {
                $stmt_delete->bind_param("s", $db_email); // ใช้ email จาก DB เพื่อความถูกต้อง
                $stmt_delete->execute();
                $stmt_delete->close();
            } // stmt_delete
            else {
                error_log("MySQLi prepare failed (delete old token): " . $conn->error);
                // ดำเนินการต่อได้ แต่ควร log ไว้
            }
            $query_insert_token = "INSERT INTO password_resets (email_reset, token_reset, expires_at_reset) VALUES (?, ?, ?)";
            $stmt_insert = $conn->prepare($query_insert_token);

                if ($stmt_insert === false) {
                    error_log("MySQLi prepare failed (insert token): " . $conn->error);
                    if ($stmt) $stmt->close();      // <--- เพิ่มการปิด statement หลัก
                    if ($conn) $conn->close();      // <--- เพิ่มการปิด connection
                    header("Location: forgotpass.php?status=dberror");
                    exit;
                }
            $stmt_insert->bind_param("sss", $db_email, $token, $expires_at);// ใช้ $db_email ที่ดึงจากฐานข้อมูล
            
            if ($stmt_insert->execute()) {
                // **จำลองการส่งอีเมล:** แสดงลิงก์ให้ผู้ใช้เห็น
                // ในระบบจริง ส่วนนี้จะเป็นการส่งอีเมลหา $db_email
                // $reset_link = "http://localhost/your_project_folder/reset_password_form.php?token=" . $token;
                // mail($db_email, "Reset Your Password", "Click here to reset: " . $reset_link);

                //ส่งลิงก์กลับไปแสดงผล
                $user_clickable_link = "newpass.php?token=" . $token;

                if ($stmt_insert) $stmt_insert->close(); // ปิด stmt_insert
                if ($stmt) $stmt->close();
                if ($conn) $conn->close();
                header("Location: forgotpass.php?status=linksent_simulation&reset_link=" . urlencode($user_clickable_link));
                exit;

            }//ของ stmt_insert
            else {
                error_log("Execute failed (insert token): " . $stmt_insert->error);
                if ($stmt_insert) $stmt_insert->close();
                if ($stmt) $stmt->close();
                if ($conn) $conn->close();
                header("Location: forgotpass.php?status=dberror");
                exit;
            }

        }//ของ email check
        else { 
            if ($stmt) $stmt->close();
            if ($conn) $conn->close();
            header('Location: forgotpass.php?status=emailnotmatch');//ถ้าเมลไม่ตรงกับที่ส่งมา
            exit;
        }
    }//ของ $result 
    else { 
        if ($stmt) $stmt->close();
        if ($conn) $conn->close();
        header('Location: forgotpass.php?status=nouser'); //ถ้าไม่เจอ account
        exit;
    }   
}//ของ isset

?>