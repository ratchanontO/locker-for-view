<?php
    include '../connectdb.php';
    require_once '../config.php';
    $tokenapi = $api_keys['apikey'];

    $data = json_decode(file_get_contents("php://input"), true);

    $locker_number = $data["locker_number"];
    $password = $data["password"];
    $key = $data["key"];

    if($key == $tokenapi){

        $stmt = $conn->prepare("SELECT * FROM user_locker WHERE locker_number = ?");
        $stmt->bind_param("i", $locker_number);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
    
        if (!$row || !password_verify($password, $row["locker_password"])) {
            echo json_encode(["status" => "fail", "message" => "รหัสผ่านไม่ถูกต้อง"]);
            exit;
        }else{
            echo json_encode(["status" => "success", "message" => $row["user_id"]]);
        }
    }

?>