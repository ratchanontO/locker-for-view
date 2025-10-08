<?php
require "../head.php";
include '../connectdb.php';

$status = false;
$error_password = false;

if (isset($_GET["errorpass"])){
    $error_password = true;
}

if (isset($_GET["token"]) && isset($_GET["email"])) {
    $token = $_GET["token"];
    $email = $_GET["email"];

    echo "กูมาละ";
    $stmt = $conn->prepare("SELECT * FROM users WHERE token = ? and email = ?");
    $stmt->bind_param("ss", $token, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $status = true;
        $id = $user["id"];
        echo $user["username"];
        echo $user["id"];
    } else {
        $status = false;
    }


}

if (isset($_POST['password']) && isset($_POST['confirmpassword']) && isset($_POST['token']) && isset($_POST['email'])) {
    $_password = $_POST['password'];
    $_confirmpassword = $_POST['confirmpassword'];
    $_token = $_POST['token'];
    $_email = $_POST['email'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE token = ? and email = ?");
    $stmt->bind_param("ss", $_token, $_email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();


    if ($_password == $_confirmpassword) {
        echo "<script>console.log(" . json_encode($user['id']) . ");</script>";
        echo "<script>console.log(" . json_encode($_password) . ");</script>";

        $hash_password = password_hash($_password, PASSWORD_DEFAULT);

        echo "<script>console.log(" . json_encode($hash_password) . ");</script>";
        $update = $conn->prepare("UPDATE users SET password = ?, token = NULL WHERE id = ?");
        $update->bind_param("si", $hash_password, $user['id']);
        $update->execute();
    } else {
        $error_password = true;
        header("Location: user_forgetpassword.php?token=".$_token."&email=".$_email."&errorpass=".$error_password."");
    }
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

    .alert-sm {
        padding: 0.25rem 0.75rem;
        /* ลดช่องว่างด้านใน */
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
            echo '<form method="post" action="user_forgetpassword.php">';
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
            </div>';

            if ($error_password) {
                echo '<div class = "pt-2">';
                echo '<div class="alert alert-danger alert-sm" >รหัสผ่านไม่ตรงกัน!!</div>';
                echo '</div>';
            }

            echo '</div>';

            echo '
        <div class="text-center">
            <input type="hidden" name="token" value="' . htmlspecialchars($token) . '" required>
            <input type="hidden" name="email" value="' . htmlspecialchars($email) . '" required>
            <button type="submit" class="btn btn-primary">ยืนยันการเปลี่ยนรหัสผ่าน</button>
        </div>';

            echo '</form>';
            echo '</div>';
        } else {
            echo '
            <div class="text-center">
                <p>ไม่พบชื่อผู้ใช้งาน</p>
            </div>
            ';
        }

        ?>
    </div>
</body>