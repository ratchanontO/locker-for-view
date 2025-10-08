<?php
    header('Content-Type: application/json');
    include '../connectdb.php';


    if(isset($_POST["userid"]) && isset($_POST["locker_id"]) && isset($_POST["password"])){
        $userid = $_POST["userid"];
        $password = $_POST["password"];
        $lockerid = $_POST["locker_id"];

        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $userid);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if(password_verify($password, $user['password'])){
            $stmt = $conn->prepare("SELECT loadcell_kg FROM locker_status WHERE locker_id = ?");
            $stmt->bind_param("i", $lockerid);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            if(1){
                $stmt = $conn->prepare("DELETE FROM user_locker WHERE user_id = ?");
                $stmt->bind_param("i", $userid);
                if($stmt->execute()){
                    $stmt1 = $conn->prepare("UPDATE locker_status SET is_owned = 0 WHERE locker_id = ?");
                    $stmt1->bind_param("i", $lockerid);
                    if($stmt1->execute()){
                        echo json_encode(["success" => true , 'message' => 'เรียบร้อย']);
                    }
                }
            }else{
                echo json_encode(["success" => false, 'message' => $userid]);
                echo json_encode(["success" => false, 'message' => 'คุณยังมีสัมภาระอยู่ภายในตู้อยู่']);
            }
        }else{
            echo json_encode(["success" => false, 'message' => 'รหัสผ่านไม่ถูกต้อง']);
        }
        
    }else{
        echo json_encode(["success" => false,"message" => "ข้อมูลไม่ครบ"]);
    }
?>