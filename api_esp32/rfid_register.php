<?php
    include '../connectdb.php';
    require_once '../config.php';
    $tokenapi = $api_keys['apikey'];

    $data = json_decode(file_get_contents("php://input"), true);

    $userid = $data["userid"];
    $rfid_uid = $data["rfid_uid"];
    $key = $data["key"];
    if(!$userid || !$rfid_uid || !$key){
        echo json_encode(["status" => "fail", "message" => "ข้อมูลไม่ครบ"]);
        exit;
    }
    if($key == $tokenapi){

        $stmt = $conn->prepare("UPDATE users SET rfid_uid = ? WHERE id = ?");
        $stmt->bind_param("si", $rfid_uid, $userid);
        
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "ok"]);
        }else{
            echo json_encode(["status" => "fail", "message" => "บันทึกไม่ได้"]);
        }
    }
?>