<?php
require_once 'connection.php';
if (isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["cpassword"]) && isset($_POST["email"])) {
    //ถ้ามีข้อมูลเข้ามา
    $username =     htmlspecialchars(mysqli_real_escape_string($conn, $_POST["username"])); //แปลงคำที่ผู้ใช้ใว่เข้ามาเป็นอักษรพิเศษ กันการใส่โค้ดเข้ามา
    $password =     mysqli_real_escape_string($conn, $_POST["password"]);
    $confirm_pass = mysqli_real_escape_string($conn, $_POST["cpassword"]);
    $email =        htmlspecialchars(mysqli_real_escape_string($conn, $_POST["email"]));  //แปลงคำที่ผู้ใช้ใว่เข้ามาเป็นอักษรพิเศษ กันการใส่โค้ดเข้ามา

    if (empty($username) || empty($password) || empty($confirm_pass) || empty($email)) {
        die(header("Location: register.php?status=emptyfields")); // ถ้ามีช่องว่างตอนกรอกเพื่อสมัคร
    } else if ($password !== $confirm_pass) { // เพิ่ม: ตรวจสอบว่ารหัสผ่านตรงกันหรือไม่
        die(header("Location: register.php?status=passwordmismatch"));
    }else{
        $query_check_email = "SELECT email_acc FROM account WHERE email_acc = '$email'";
        $query_check_username = "SELECT username_acc FROM account WHERE username_acc = '$username'";
        $callback_query_check_email = mysqli_query($conn, $query_check_email);
        $callback_query_check_username = mysqli_query($conn, $query_check_username);
        
        if (mysqli_num_rows($callback_query_check_email) > 0) {
            die(header("Location: register.php?status=emailtaken")); // มีคนใช้เมลนี้แล้ว
        }else if (mysqli_num_rows($callback_query_check_username)> 0){
            die(header("Location: register.php?status=usernametaken"));
        }
        else{
            // password_hash() จะสร้าง salt ที่ปลอดภัยให้เอง และรวมไว้ใน $passHash
            // ไม่จำเป็นต้องกำหนด $options หากใช้ค่าเริ่มต้นที่ปลอดภัยของ PHP เวอร์ชั่นใหม่ๆ
            // แต่ถ้าต้องการกำหนดเอง ก็ทำได้ดังนี้ (ตรวจสอบว่าค่า PASSWORD_ARGON2_DEFAULT_* เหมาะสมกับ PHP เวอร์ชั่นของคุณ)
            $options = [
                'cost' => PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
                'time_cost' => PASSWORD_ARGON2_DEFAULT_TIME_COST,
                'thread' => PASSWORD_ARGON2_DEFAULT_THREADS
            ];
            $passHash = password_hash($password, PASSWORD_ARGON2ID,$options);//นำรหัสที่รวมกับค่าเกลือมาเข้ารหัสอีกที
            // Insert ข้อมูล
            $query_create_account ="INSERT INTO account (username_acc,password_acc,email_acc) VALUE ('$username','$passHash','$email')";
            $callback_create_account = mysqli_query($conn, $query_create_account);
            
            if ($callback_create_account) {
                die(header("Location: login.php?status=registrationsuccess"));//สร้างสำเร็จจะไปหน้า login
            }else{
                die(header("Location: register.php?status=dberror"));//ถ้าสร้างไม่สำเร็จกลับหน้า register
            }
        }
    }
}else{
    die(header('Location: register.php?status=nodata'));// ไม่มีข้อมูล
}

?>