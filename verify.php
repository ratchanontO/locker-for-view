<?php
require "head.php";
include 'connectdb.php';
include 'config.php';

if (isset($_GET["token"]) && isset($_GET["email"])) {
    $token = $_GET['token'];
    $email = $_GET['email'];
    $status = "active";

    $stmt = $conn->prepare("SELECT id, status_email FROM users WHERE token = ? and email = ?");
    $stmt->bind_param("ss", $token, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        echo $user['status_email'];

        if ($user['status_email'] == "active" ) {
            $message = "บัญชีนี้ได้รับการยืนยันแล้ว";
        } else {
            // อัปเดตสถานะการยืนยัน
            $update = $conn->prepare("UPDATE users SET status_email = ?, token = NULL WHERE id = ?");
            $update->bind_param("si", $status, $user['id']);
            $update->execute();

            $message = "ยืนยันอีเมลสำเร็จ!";
        }
    } else {
        $message = "Token หรือ email ไม่ถูกต้อง";
    }
} else {
    $message = "ไม่มีข้อมูลแนบมา";
}
?>

<body class="bg-light ">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow p-4">
            <h3 class="text-center mb-3">ยืนยันอีเมล</h3>
            <p class="text-center"><?php echo $message; ?></p>
            <div class="text-center">
                <a href="<?= $url ?>login.php" class="btn btn-primary">เข้าสู่ระบบ</a>
            </div>
        </div>
    </div>
</body>
</html>