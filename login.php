<?php
include 'connectdb.php';
require "head.php";
session_start();
$login_error = 0;

if (isset($_POST['username']) && isset($_POST['password'])) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $_POST['username']);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($_POST['password'], $user['password'])) {
            if($user['status_email'] == 'active'){
                $_SESSION['username'] = $user['username'];
                $_SESSION['userid'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                header("Location: index.php");
                exit;
                
            }else{
                $login_error = 2;
            }
        } else {
            $login_error = 1;
        }
    } else {
        $login_error = 1;
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
        box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
        padding: 30px;
        background-color: #fff;
    }

    .form-control:focus {
        box-shadow: none;
        border-color: #6c63ff;
    }
    
    .table {
        font-family: "Mitr", sans-serif;  /* ฟอนต์ Mitr */
        font-size: 15px;                  /* ขนาดตัวอักษร */
        font-weight: 300;
    }

    .table th {
    font-weight: 600;                 /* หัวตารางหนากว่า */
    font-size: 16px;
    }

    .table td {
    font-weight: 300;                 /* เนื้อหาบางลง */
    }
</style>

<body>
    <div class="container ">
        <div class="row justify-content-center">
            <div class="col-md-5 mitr-extralight300">
                <div class="login-card p-4">
                    <h3 class="text-center mb-4 mitr-extralight200">เข้าสู่ระบบ</h3>
                    <hr>
                    <form action="login.php" method="POST">
                        <!-- <div class="mb-3">
                            <label for="username" class="form-label">ชื่อผู้ใช้</label>
                            <input type="text" class="form-control" placeholder="Username" id="username" name="username" required>
                        </div> -->
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                            <input type="text" class="form-control" placeholder="Username" name="username" required>
                        </div>
                        <!-- <div class="mb-3">
                            <label for="password" class="form-label">รหัสผ่าน</label>
                            <input type="password" class="form-control" placeholder="Password" id="password" name="password" required>
                        </div> -->
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" class="form-control" placeholder="Password" name="password" required>
                        </div>
                        <?php if ($login_error == 1): ?>
                            <!-- <a>ชื่อผู้ใช้งานหรือรหัสผ่านไม่ถูกต้อง</a> -->
                            <div class="alert alert-danger" role="alert">
                                    ชื่อผู้ใช้งานหรือรหัสผ่านไม่ถูกต้อง
                            </div>
                        <?php endif; ?>
                        <?php if ($login_error == 2): ?>
                            <!-- <a >คุณจำเป็นต้องยืนยันอีเมลที่อีเมลของท่านก่อน</a> -->
                            <div class="alert alert-danger" role="alert">
                                    คุณจำเป็นต้องยืนยันอีเมลที่อีเมลของท่านก่อน
                            </div>
                        <?php endif; ?>
                        <button type="submit" class="btn btn-success w-100 mitr-extralight300">เข้าสู่ระบบ</button>
                    </form>
                    <h5 class="text-center mt-3 mitr-extralight200">ยังไม่มีบัญชี? <a href="register.php">สมัครสมาชิก</a></h5>
                </div>
            </div>
        </div>
    </div>
</body>

</html>