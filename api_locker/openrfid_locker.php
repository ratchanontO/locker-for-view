<?php
    include '../connectdb.php';
    require_once '../config.php';
    $tokenapi = $api_keys['apikey'];

    $data = json_decode(file_get_contents("php://input"), true);

    $rfid_uid = $data["rfid_uid"];
    $key = $data["key"];

    if (!$rfid_uid || !$key){

        if($key == $tokenapi){
    
            $stmt = $conn->prepare("SELECT user_locker.locker_number 
                                            FROM user_locker 
                                            INNER JOIN users ON user_locker.user_id = users.id
                                            WHERE rfid_uid = ?");
            $stmt->bind_param("s", $rfid_uid);
            $stmt->execute();
            $result = $stmt->get_result();
    
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                echo json_encode(["status" => "success", "locker" => $row["locker_number"], "statuslocker" => "ON"]);
            }
    
        }else{
            echo json_encode(["status" => "fail", "message" => "รหัสผ่านไม่ถูกต้อง"]);
        }
    }else{
        echo json_encode(["status" => "fail", "message" => "ข้อมูลไม่ครบ"]);
    }

?>