<?php
include 'connectdb.php';
include 'config.php';
require 'mail_function.php';
require "head.php";

function isValidUsername($username)
{
    return preg_match('/^[a-zA-Z0-9]+$/', $username);
}

$error_password = false;
$error_register = false;
$error_email = false;
$error_username = false;
$success = false;

if (!empty($_POST["username"])) {
    
    $token = bin2hex(random_bytes(16));
    $username = $_POST["username"];
    $password = $_POST["password"];
    $confirmpassword = $_POST["confirmpassword"];
    $email = $_POST["email"];
    $time = date("Y-m-d H:i:s");

    // ข้อมูลส่งเมล
    $subject = "ยืนยันการสมัครสมาชิก locker For ecp";
    $link = $url."verify.php?token=$token&email=$email";
    $body = "
        <h3>ยินดีต้อนรับสู่ Locker For Ecp</h3>
        <p>คุณได้สมัครสมาชิกเรียบร้อยแล้ว เพื่อใช้งานบริการของเราคุณต้องยืนยันอีเมลด้วยการกดลิ้งค์ข้างล่างก่อนใช้งาน</p>
        <p>คลิกเพื่อยืนยันอีเมล: <a href='$link'>ยืนยันอีเมล</a></p>
    ";

    if (isValidUsername($username)) {
        if ($password == $confirmpassword) {
            $hash_password = password_hash($password, PASSWORD_DEFAULT);
            // เริ่มลงdb
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? or email = ?");
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $error_register = true;
            } else {
                $insert = $conn->prepare("INSERT INTO users (username, password, email, token, created_at ) VALUES (?, ?, ?, ?, ?)");
                $insert->bind_param("sssss", $username, $hash_password, $email, $token, $time);
                $insert->execute();
                
                // ส่งลิ้งค์ยืนยันเมล
                if (sendMail($email, $subject, $body)) {
                    $success = true;
                }
            }
        } else {
            $error_password = true;
        }
    } else {
        $error_username = true;
    }
}
?>

<style>
    .mitr-extralight200 {
        font-family: "Mitr", sans-serif;
        font-weight: 200;
        font-style: normal;
    }

    .mitr-extralight300 {
        font-family: "Mitr", sans-serif;
        font-weight: 300;
        font-style: normal;
    }
    body {
        background-color:rgba(73, 70, 70, 0.12);
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .login-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        padding: 30px;
        background-color: #fff;
    }

    .form-control:focus {
        box-shadow: none;
        border-color: #6c63ff;
    }
</style>

<body>
    <div class="container">
        <div class="row justify-content-center mitr-extralight200">
            <div class="col-md-5">
                <div class="login-card p-4">
                    <h3 class="text-center mb-4 mitr-extralight200"> สมัครสมาชิก </h3>
                    <hr>
                    <form action="register.php" method="POST">
                        <label for="username"> ชื่อผู้ใช้ </label>
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                            <input type="text" class="form-control" placeholder="Username" id="username" name="username"
                                required>
                        </div>
                        <label for="password"> รหัสผ่าน </label>
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" class="form-control" placeholder="password" id="password"name="password" required>
                        </div>
                        <label for="password"> ยืนยันรหัสผ่าน </label>
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>  
                            <input type="password" class="form-control" placeholder="confirmpassword"
                                id="confirmpassword" name="confirmpassword" required>
                        </div>
                        <label for="email"> อีเมล </label>
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" placeholder="email" id="email" name="email"required>
                        </div>
                        <?php
                        if ($error_register) {
                            echo "ชื่อผู้ใช้หรืออีเมลมีคนใช้งานไปแล้ว";
                        }
                        if ($error_password) {
                            echo "รหัสผ่านไม่ตรงกัน";
                        }
                        if ($error_username) {
                            echo "ชื่อผู้ใช้งานมีอักษรพิเศษ";
                        }
                        ?>
                        <div class="mb-2">
                            <button type="submit" class="btn btn-success w-100">สมัครสมาชิก</button>
                        </div>
                    </form>
                    <p class="text-center mt-3">มีบัญชีแล้ว? <a href="login.php">เข้าสู่ระบบ</a></p>
                    <?php if ($success): ?>
                        <script>
                            Swal.fire({
                                icon: 'success',
                                title: 'สมัครสมาชิกสำเร็จ!',
                                text: 'กรุณายืนยันอีเมลที่กล่องข้อความอีเมลของท่าน',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                heightAuto: false
                            })
                        </script>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>