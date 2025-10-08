<?php
    session_start();
    include 'connectdb.php';
    require "head.php";

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

    .mitr-extralight300 {
        font-family: "Mitr", sans-serif;
        font-weight: 300;
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
    ?>

        <div class="container custom-narrow">
            <div class="row align-items-center"style="background-color:rgba(255, 255, 255, 0.79); margin: 14px 12px; border-radius: 15px; padding: 1rem 0;">
                <h4 class="mb-4 mitr-extralight300">ตู้ของฉัน</h4>
                <?php 
                $stmt = $conn->prepare(" SELECT user_locker.locker_id, user_locker.locker_number, locker_status.loadcell_kg 
                                                FROM user_locker 
                                                INNER JOIN locker_status ON user_locker.locker_id = locker_status.locker_id
                                                WHERE user_locker.user_id = ?
                ");
                $stmt->bind_param("s", $_SESSION['userid']);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0){
                    while ($row = $result->fetch_assoc()){?>
                        <div class="col-sm-4 col-5 mitr-extralight300">
                            <div class="card text-center shadow-sm p-3" style="border-radius: 15px;">
                                <div class="position-absolute top-0 start-0 translate-middle  rounded-pill bg-warning text-dark">
                                    ตู้ของคุณ
                                </div>
                                <img src="img/logo.png" class="img-fluid mx-auto mb-2" style="max-height: 100px;" alt="logo">
                                <!-- <h5 class="text-muted mitr-extralight300">ตู้ฝากของ</h5> -->
                                <h5 class=" mitr-extralight300">ตู้ฝากของ</h5>
                                <h3 class=" mitr-extralight300"><?php echo $row["locker_number"]; ?></h3>

                            </div>
                        </div>
                            
                        <div class="col-md-8 col-7 mitr-extralight200">
                            <div class="border p-4  " style="border-radius: 15px;">
                                <h4><i class="bi bi-person-fill"></i> <span id="detail-username"></span> <?php echo $_SESSION["username"]?></h4>
                                <p>หมายเลขตู้: <strong id="detail-lockernumber"> <?php echo $row["locker_number"]?> </strong></p>
                                <p>น้ำหนักในตู้: <strong id="detail-weight"></strong><?php echo $row["loadcell_kg"]/1000?> กิโลกรัม</p>
                                <br>
                                <button class="btn btn-success" onclick='locker_password(<?php echo $row["locker_id"] ?>, <?php echo $_SESSION["userid"]?>)'>เปลี่ยนรหัสผ่านตู้</button>
                                <button class="btn btn-danger" onclick='cancelLocker(<?php echo $row["locker_id"] ?>, <?php echo $_SESSION["userid"]?>)'>ยกเลิกใช้ตู้</button>
                            </div>
                        </div>

                        
                    <?php 
                    }
                }else{
                    echo "
                        <script>
                            Swal.fire({
                                icon: 'info',
                                title: 'คุณยังไม่มีตู้',
                                text: 'สามารถเลือกใช้งานได้ที่หน้าหลัก',
                                confirmButtonText: 'ตกลง'
                            });
                        </script>
                    ";
                }
                ?>
            </div>
        </div>
        <?php
            require "low_menu.php";
        ?>

    <script>

        function generateKey(length = 16) {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            let result = '';
            for (let i = 0; i < length; i++) {
                result += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            return result;
        }

        function cancelLocker(locker_id, user_id) {
            Swal.fire({

                title: "คุณแน่ใจหรือไม่?",
                text: "คุณต้องการยกเลิกการใช้ตู้นี้?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "ใช่ยกเลิก",
                cancelButtonText: "ยกเลิก"

            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        
                        title: "ใส่รหัสผ่านเพื่อยกเลิกใช้งาน",
                        icon: "warning",
                        html: '<input id="password" type="password" class="swal2-input" placeholder="รหัสผ่าน">', 
                        showCancelButton: true,
                        confirmButtonText: "ยืนยัน",
                        cancelButtonText: "ยกเลิก",
                        preConfirm: () => {
                            const password = document.getElementById('password').value;
                            if (!password) {
                                Swal.showValidationMessage("กรุณากรอกรหัสผ่าน");
                                return false;
                            }
                            return password;
                        }
                    }).then((result2) => {
                        if (result2.isConfirmed) {
                            const password = result2.value;

                            Swal.showLoading();

                            fetch("api/delete_locker.php", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/x-www-form-urlencoded",
                                },
                                body: `userid=${encodeURIComponent(user_id)}&locker_id=${encodeURIComponent(locker_id)}&password=${encodeURIComponent(password)}`
                            })
                            .then(res => res.json())
                            .then(data => {
                                Swal.close();
                                if (data.success) {
                                    Swal.fire("สำเร็จ",data.message, "success").then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire("ผิดพลาด",data.message, "error");
                                }
                            })
                            .catch(err => {
                                Swal.close();
                                Swal.fire("เกิดข้อผิดพลาด",err.message, "error");
                            });
                        }
                    });
                }
            });
        }


        function locker_password(locker_id, user_id) {
            const secureKey = generateKey();

            fetch("api/save_key.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    locker_id: locker_id,
                    user_id: user_id,
                    key: secureKey
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    Swal.fire({
                        title: "เปลี่ยนรหัสผ่านตู้",
                        icon: "warning",
                        html: `
                            <input id="password" type="password" class="swal2-input" placeholder="รหัสผ่านใหม่">
                            <input id="confirm" type="password" class="swal2-input" placeholder="ยืนยันรหัสผ่าน">
                        `,
                        showCancelButton: true,
                        confirmButtonText: "เปลี่ยนรหัสผ่าน",
                        cancelButtonText: "ยกเลิก",
                        focusConfirm: false,
                        preConfirm: () => {
                            const password = document.getElementById('password').value;
                            const confirm = document.getElementById('confirm').value;

                            if (!password || !confirm) {
                                Swal.showValidationMessage('กรุณากรอกรหัสผ่านให้ครบ');
                            } else if (password !== confirm) {
                                Swal.showValidationMessage('รหัสผ่านไม่ตรงกัน');
                            } else if (!/^\d{5,}$/.test(password)) {
                                Swal.showValidationMessage('รหัสผ่านต้องเป็นตัวเลข และยาวอย่างน้อย 5 หลัก');
                            }

                            return { password: password, key: secureKey, locker_id: locker_id , userid: user_id};
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch("api/changepassword_locker.php", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json"
                                },
                                body: JSON.stringify(result.value)
                            })
                            .then(res => res.json())
                            .then(response => {
                                if (response.status === "success") {
                                    Swal.fire("สำเร็จ", "เปลี่ยนรหัสผ่านเรียบร้อย", "success");
                                } else {
                                    Swal.fire("ล้มเหลว", response.message || "มีข้อผิดพลาด", "error");
                                }
                            });
                        }
                    });
                } else {
                    Swal.fire("ล้มเหลว", "ไม่สามารถสร้าง key ได้", "error");
                }
            });
        }

    </script>

</body>

</html>