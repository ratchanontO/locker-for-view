<?php
require "../head.php";
include '../connectdb.php';

$status = false;

if (isset($_GET["Function"]) && isset($_GET["token"]) && isset($_GET["email"])) {
    $function = $_POST["Function"];
    $token = $_POST["token"];
    $email = $_POST["email"];

    $stmt = $conn->prepare("SELECT id, status_email FROM users WHERE token = ? and email = ?");
    $stmt->bind_param("ss", $token, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 1){
        $user = $result->fetch_assoc();
        $status = true;
    }
}

if (isset($_POST['password']) && isset($_POST['confirmpassword'])) {
    

}
?>
<style>
    .playpen-thai {
        font-family: "Playpen Sans Thai", cursive;
        font-weight: 400;
        font-style: normal;
    }

    .no-underline {
        text-decoration: none;
    }

    h1 {
        font-size: 24px;
        line-height: 30px;
        font-weight: 700;
    }

    .custom-narrow {
        max-width: 1000px;
        /* กำหนดขนาดเอง */
        margin: auto;
        /* จัดตรงกลาง */
    }
</style>

<body>
    <?php
    require "../navbar.php";
    ?>
    <div class="container custom-narrow mt-5">
        <?php
        if ($status) {
            echo '<div class="card shadow p-4" style="margin: 14px 12px; border-radius: 15px; padding: 1rem 0;">';
            echo '<h3 class="mb-4 text-center">เปลี่ยนรหัสผ่านผู้ใช้</h3>';
            echo '<form method="post" action="change_password.php">';

            echo '
        <div class="mb-3">
            <label for="password" class="form-label">รหัสผ่านปัจุบัน</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                <input type="password" class="form-control" id="password" name="oldpassword" placeholder="Password" required>
            </div>
        </div>';

            echo '
        <div class="mb-3">
            <label for="password" class="form-label">รหัสผ่านใหม่</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
            </div>
        </div>';

            echo '
        <div class="mb-3">
            <label for="confirmpassword" class="form-label">ยืนยันรหัสผ่าน</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                <input type="password" class="form-control" id="confirmpassword" name="confirmpassword" placeholder="Confirm Password" required>
            </div>
        </div>';

            echo '
        <div class="text-center">
            <button type="submit" class="btn btn-primary">ยืนยันการเปลี่ยนรหัสผ่าน</button>
        </div>';

            echo '</form>';
            echo '</div>';
        }else{
            echo 'ไม่พบผู้ใช้งาน';
        }
        ?>
    </div>


</body>