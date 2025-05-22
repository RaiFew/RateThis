<?php
$hostname = "127.0.0.1"; // หรือ IP address ของ server
$username = "root";
$password = "";
$dbname = "ratethis";//ชื่อ database

// สร้างการเชื่อมต่อ
$conn = new mysqli($hostname, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// echo "Connected successfully"; // สามารถ uncomment เพื่อทดสอบการเชื่อมต่อ
?>