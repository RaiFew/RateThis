<?php
session_start();
require_once('connection.php');
    if (isset($_POST["username"]) && isset($_POST["password"])){
        $username = $_POST["username"];
        $password = $_POST["password"];

        if (empty($username) || empty($password)) { // เช็คว่าข้อมูลที่ส่งมาว่างมั้ย
            header("Location: login.php?status=emptyfields");
            exit;
        }

        $query_check_account = "SELECT username_acc,password_acc,role_acc FROM account WHERE username_acc = ?";//เลือกข้อมูลใน database จาก $username
        $stmt = $conn->prepare($query_check_account); // เตรีบมดึงข้อมูลจากบรรทัดก่อนหน้า
        if ($stmt === false) {
            // กรณี prepare statement ไม่สำเร็จ (อาจมีปัญหาที่ SQL query หรือการเชื่อมต่อ)
            error_log("MySQLi prepare failed: " . $conn->error); // เก็บ log ไว้ดูข้อผิดพลาด
            header("Location: login.php?status=dberror"); // แสดงข้อผิดพลาดทั่วไปให้ผู้ใช้
            exit;
        }
        $stmt->bind_param("s", $username); // "s" หมายถึง $username เป็น string
        $stmt->execute();//สั่งทำงาน stmt
        $result = $stmt->get_result();//เอาผลลัพธ์จาก stmt เมื่อกี้ว่าเจอ username นี้เท่าไหร้
        if ($result->num_rows === 1) { //ถ้าเจอ
            $user_rows = $result->fetch_assoc(); //ดึงข้อมูลผู้ใช้ 
            if (password_verify($password, $user_rows['password_acc'])) {
                $_SESSION['username_acc'] = $user_rows['username_acc'];
                $_SESSION['role_acc'] = $user_rows['role_acc'];
                if ($user_rows['role_acc'] === 'admin') {
                    header("Location: admin.php"); // admin ไป admin.php
                    exit;
                } else {
                    header("Location: index.php"); // member ไป index.php
                    exit;
                }
                exit ;
            }else {
                header("Location: login.php?status=fail"); // รหัสผิด
                exit;
            }
        }else {
            header("Location: login.php?status=fail");//ไม่เจอบัญชี
            $stmt->close(); // ปิด statement
            if (isset($conn)) {
                $conn->close();
            }
            exit;
        }
        
        
    }else {
        // 7. จัดการเมื่อไม่มี POST data
        header("Location: login.php");
        exit;
    }
    
?>