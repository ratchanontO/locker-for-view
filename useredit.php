<?php
    session_start();
    include 'connectdb.php';
    require "head.php";
    require "datefn.php";

    if(!isset($_SESSION["username"])){
        header("Location: login.php");
        exit;
    }
    

    
?>

<style>
    .mitr-extralight200 {
        font-family: "Mitr", sans-serif;
        font-weight: 200;
        font-style: normal;
    }

    .mitr-extralight300, .mitr-extralight300 th, .mitr-extralight300 td {
        font-family: 'Mitr', sans-serif;
        font-weight: 300;
    }
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
        margin: auto;
    }

    .status-circle {
        background-color: rgb(255, 136, 0);
        color: white;
        border-radius: 50%;
        padding: 8px 12px;
        font-size: 14px;
        font-weight: bold;
        text-align: center;
        min-width: 36px;
        min-height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>

    <body>
        <?php
            require "navbar.php";
            $stmt = $conn->prepare(" SELECT username, email, status_email, created_at
                                            FROM users
                                            WHERE id = ?
            ");
            $stmt->bind_param("i", $_SESSION['userid']);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            if ($row['status_email'] == 'active'){
                $status_email = false;
            }else{
                $status_email = true;
            }
            
            $thaiDateAndTime = convertDateToThaiFormat($row['created_at']);
        ?>
            <div class="container custom-narrow mt-4">
                <div class="row justify-content-center" >
                    <div class="col-md-11 col-11">
                        <div class="card shadow-sm" style="border-radius: 15px;">
                            <div class="card-body">
                                <h4 class=" mb-4 mitr-extralight300">
                                    <i class="fa-solid fa-user-circle me-2mitr-extralight300"></i> ข้อมูลผู้ใช้งาน
                                </h4>

                                <div class="mb-3 ">
                                    <h5 class="mitr-extralight300"><i class="fa-solid fa-user me-2"></i> ชื่อผู้ใช้: 
                                        <span class="text-primary fw-semibold mitr-extralight300"><?php echo $row['username'] ?></span>
                                    </h5>
                                </div>

                                <div class="mb-3">
                                    <h5 class="mitr-extralight300">
                                        <i class="fa-solid fa-envelope me-2"></i> อีเมล:
                                        <a><?php echo $row['email'] ?></a>
                                        <?php if ($status_email): ?>
                                            <span class="badge bg-warning text-dark ms-2">กรุณายืนยันอีเมล</span>
                                            <button class="btn btn-outline-primary">
                                                <i class="fa-solid fa-envelope"></i> ยืนยันอีเมล
                                            </button>
                                            <?php else: ?>
                                                <span class="badge bg-success ms-2">ยืนยันแล้ว</span>
                                                <?php endif; ?>
                                    </h5>
                                </div>

                                <div class="mb-4">
                                    <h5 class="mitr-extralight300">
                                        <i class="fa-solid fa-calendar-check me-2"></i> สมัครเมื่อ: 
                                        <a><?php echo $thaiDateAndTime ?></a>
                                    </h5>    
                                </div>

                                <button class="btn btn-outline-primary mitr-extralight200" onclick='changepassword_user(<?php echo $_SESSION["userid"]?>)'>
                                    <i class="fa-solid fa-key me-2 "></i> เปลี่ยนรหัสผ่าน
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
            require "low_menu.php";
        ?>

    </body>
    <script>
        function generateKey(length = 16) {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            let result = '';
            for (let i = 0; i < length; i++) {
                result += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            return result;
        }
       function changepassword_user(user_id) {
    const secureKey = generateKey(); // สร้าง key ก่อนเลย

    Swal.fire({
        title: "ยืนยันตัวตน",
        icon: "question",
        input: "password",
        inputLabel: "รหัสผ่านปัจจุบัน",
        inputPlaceholder: "รหัสผ่านปัจจุบัน",
        showCancelButton: true,
        confirmButtonText: "ตรวจสอบ",
        cancelButtonText: "ยกเลิก",
        preConfirm: (currentPassword) => {
            if (!currentPassword) {
                Swal.showValidationMessage("กรุณาใส่รหัสผ่าน");
            }

            return fetch("api/verify_password.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    user_id: user_id,
                    password: currentPassword,
                    key: secureKey
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status !== "success") {
                    throw new Error(data.message || "รหัสผ่านไม่ถูกต้อง");
                }
                return true;
            })
            .catch(err => {
                Swal.showValidationMessage(err.message);
            });
        }
    }).then((result) => {
        if (!result.isConfirmed) return;

        Swal.fire({
            title: "เปลี่ยนรหัสผ่านใหม่",
            icon: "warning",
            html: `
                <input id="new_password" type="password" class="swal2-input" placeholder="รหัสผ่านใหม่">
                <input id="confirm_password" type="password" class="swal2-input" placeholder="ยืนยันรหัสผ่าน">
            `,
            showCancelButton: true,
            confirmButtonText: "เปลี่ยนรหัสผ่าน",
            cancelButtonText: "ยกเลิก",
            focusConfirm: false,
            preConfirm: () => {
                const newPassword = document.getElementById("new_password").value;
                const confirmPassword = document.getElementById("confirm_password").value;

                if (!newPassword || !confirmPassword) {
                    Swal.showValidationMessage("กรุณากรอกรหัสผ่านให้ครบ");
                } else if (newPassword !== confirmPassword) {
                    Swal.showValidationMessage("รหัสผ่านไม่ตรงกัน");
                }

                return { password: newPassword, key: secureKey, userid: user_id };
            }
        }).then((final) => {
            if (!final.isConfirmed) return;

            fetch("api/changepassword_user.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(final.value)
            })
            .then(res => res.json())
            .then(response => {
                if (response.status === "success") {
                    Swal.fire("สำเร็จ", "เปลี่ยนรหัสผ่านเรียบร้อย", "success");
                } else {
                    Swal.fire("ล้มเหลว", response.message || "มีข้อผิดพลาด", "error");
                }
            });
        });
    });
}


    </script>                            


</html>