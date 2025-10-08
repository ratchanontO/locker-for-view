<?php
include '../connectdb.php';
require_once '../config.php';

header("Content-Type: application/json");
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

$tokenapi = $api_keys['apikey'];

$data = json_decode(file_get_contents("php://input"), true);

$mode = $data["mode"];
$weight = $data["weight"];
$locker_number = $data["locker_number"];
$key = $data["key"];
$o = 0;
$one = 1;
$two =2;

$event_type1 = 1; //1ปกติ 2งัด
$event_type2 = 2;
if (!$mode || !$weight || !$key) {
    echo json_encode(["status" => "fail", "message" => "no paramiter"]);
    exit;
}

if ($key == $tokenapi) {
    $stmt = $conn->prepare("SELECT user_id, locker_id FROM user_locker WHERE locker_number = ?");
    $stmt->bind_param("i", $locker_number);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $user_id = $row['user_id'];
            $locker_id = $row['locker_id'];
        }
    }

    if ($mode == 1) { // kg_before
        $o = 0;
        // $stmt = $conn->prepare("UPDATE locker_status SET take_open = ? WHERE number_locker = ?");
        // $stmt->bind_param("ii", $o, $locker_number);
        // $stmt->execute();
        
        $stmt = $conn->prepare("UPDATE dataforlog SET kg_before = ?, user_id = ?, event_type = ? WHERE locker_number = ?");
        $stmt->bind_param("diii", $weight, $user_id, $event_type1, $locker_number);
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "ok kg_before "]);
        } else {
            echo json_encode(["status" => "fail", "message" => "kg_before no"]);
        }
    

    } elseif ($mode == 2) { // kg_after
        sleep(3);
        $stmt = $conn->prepare("UPDATE locker_status SET take_open = ? WHERE number_locker = ?");
        $stmt->bind_param("ii", $o, $locker_number);
        if($stmt->execute()){
            $stmt = $conn->prepare("UPDATE dataforlog SET kg_after = ? WHERE locker_number = ?");
            $stmt->bind_param("di", $weight, $locker_number);
            if ($stmt->execute()) {

                $stmt = $conn->prepare("SELECT * FROM dataforlog WHERE locker_number = ?");
                $stmt->bind_param("i", $locker_number);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                $stmt->close();

                if ($row) {
                    // เช็คว่าน้ำหนักในตู้เหลือน้อยกว่าที่กำหนด (< 10 กรัม)
                    if ($row["kg_after"] < 10) {
                        try {
                            // เปิด Transaction
                            $conn->begin_transaction();

                            // 1) บันทึกลง log_locker
                            $stmt = $conn->prepare("INSERT INTO log_locker (locker_id, locker_number, user_id, event_type, path_photo, kg_before, kg_after) VALUES (?, ?, ?, ?, ?, ?, ?)");
                            $stmt->bind_param(
                                "iiissdd", 
                                $row["locker_id"], 
                                $row["locker_number"], 
                                $row["user_id"], 
                                $row["event_type"], 
                                $row["path_photo"], 
                                $row["kg_before"], 
                                $row["kg_after"]
                            );
                            $stmt->execute();
                            $stmt->close();
                            $stmt = $conn->prepare("UPDATE locker_status SET loadcell_kg = ? WHERE number_locker = ?");
                            $stmt->bind_param("di", $row["kg_after"], $locker_number);

                            // 2) ลบข้อมูลใน users_locker (ยกเลิกการครอบครอง)
                            $stmt = $conn->prepare("DELETE FROM user_locker WHERE locker_number = ?");
                            $stmt->bind_param("i", $locker_number);
                            $stmt->execute();
                            $stmt->close();

                            // 3) รีเซ็ตค่าใน dataforlog
                            $stmt = $conn->prepare("UPDATE dataforlog d
                                JOIN locker_status l ON d.locker_number = l.number_locker
                                SET d.user_id = 0,
                                    d.kg_before = 0,
                                    d.kg_after = 0,
                                    d.path_photo = 0,
                                    d.event_type = 0,
                                    d.take_photo = 0,
                                    l.is_owned = 0
                                WHERE d.locker_number = ?");
                            $stmt->bind_param("i", $locker_number);
                            $stmt->execute();
                            $stmt->close();

                            // ถ้าทุกอย่างผ่าน → commit
                            $conn->commit();
                            echo json_encode(["status" => "success", "message" => "ok no item del locker"]);

                        } catch (Exception $e) {
                            // ถ้ามี error → rollback
                            $conn->rollback();
                            echo json_encode(["status" => "fail", "message" => "Transaction failed: " . $e->getMessage()]);
                        }
                    } else {
                        $stmt = $conn->prepare("INSERT INTO log_locker (locker_id, locker_number, user_id, event_type, path_photo, kg_before, kg_after) VALUES (?, ?, ?, ?, ?, ?, ?)");
                            $stmt->bind_param(
                                "iiissdd", 
                                $row["locker_id"], 
                                $row["locker_number"], 
                                $row["user_id"], 
                                $row["event_type"], 
                                $row["path_photo"], 
                                $row["kg_before"], 
                                $row["kg_after"]
                            );
                        if($stmt->execute()){
                            $stmt->close();
                            $stmt = $conn->prepare("UPDATE locker_status SET loadcell_kg = ? WHERE number_locker = ?");
                            $stmt->bind_param("di", $row["kg_after"], $locker_number);
                            $stmt->execute();
                            $stmt = $conn->prepare("UPDATE dataforlog 
                                    SET user_id = 0,
                                        kg_before = 0,
                                        kg_after = 0,
                                        path_photo = 0,
                                        event_type = 0,
                                        take_photo = 0
                                    WHERE locker_number = ?");
                            $stmt->bind_param("i", $locker_number);
                            if($stmt->execute()){
                                $stmt->close();
                                echo json_encode(["status" => "ok", "message" => "have item"]);
                                
                            }else{
                                echo json_encode(["status" => "fail", "message" => "can't db"]);
                            }
                        }
                    }
                }
            } else {
                echo json_encode(["status" => "fail", "message" => "kg_after No"]);
            }
        }else{
            echo json_encode(["status" => "fail", "message" => "kg_after No"]);
        }

    } elseif ($mode == 3) { //ถ่ายรูป
        $stmt = $conn->prepare("UPDATE dataforlog SET take_photo = ? WHERE locker_number = ?");
        $stmt->bind_param("ii",$one, $locker_number);
        if ($stmt->execute()) {   
            $stmt = $conn->prepare("UPDATE locker_status SET take_open = ? WHERE number_locker = ?");
            $stmt->bind_param("ii",$o, $locker_number);
            if($stmt->execute()){
                echo json_encode(["status" => "success", "message" => "insert log Done!!"]);
            }else {
                echo json_encode(["status" => "fail", "message" => "take_open cant"]);
            }
        }else {
            echo json_encode(["status" => "fail", "message" => "take_open cant"]);
        }
    } elseif ($mode == 4) { // โดนงัดตู้ + สั่งถ่ายรูป
        $stmt = $conn->prepare("UPDATE dataforlog SET take_photo = ?, event_type = ? WHERE locker_number = ?");
        $stmt->bind_param("iii",$one, $event_type2, $locker_number);
        if ($stmt->execute()){
            
            $stmt = $conn->prepare("SELECT * FROM user_locker WHERE locker_number = ?");
            $stmt->bind_param("i", $locker_number);
            $stmt->execute();
            $result = $stmt->get_result();
            if($result->num_rows > 0){
                $stmt = $conn->prepare("SELECT users.id, users.username, users.email
                                        FROM users
                                        INNER JOIN user_locker ON users.id = user_locker.user_id
                                        WHERE user_locker.locker_number = ?
                                        ");
                $stmt->bind_param("i", $locker_number);
                if ($stmt->execute()){
                    require '../mail_function.php';
                    $result = $stmt->get_result();
                    $row = $result->fetch_assoc();
                    $subject = "จดหมายจาก locker For ecp";
                    $link = $url;
                    $body = "
                        <h3>ตู้เก็บของหมายเลข $locker_number ถูกงัด</h3>
                        <p>ตู้เก็บของคุณ {$row["username"]} ถูกผู้ไม่หวังดีงัดตู้เก็บของที่คุณใช้บริการ</p>
                        <p>โปรดตรวจสอบตู้เก็บของเพื่อตรวจสอบสัมภาระของคุณ</p>
                        <p>สามารถตรวจสอบการถูกงัดได้ที่หน้าประวัติการใช้งานที่เว็บไซต์ <a href='$link'>เข้าสู่เว็บไซต์ </a></p>
                    ";
                    sendMail($row["email"], $subject, $body);
                    
                }
            }
        }else{
            echo json_encode(["status" => "fail", "message" => "can't take photo"]);
        } 
    } elseif ($mode == 5){ //รับน้ำหนักก่อนโดนงัดเอาของ
        $stmt = $conn->prepare("UPDATE dataforlog SET kg_before = ?, user_id = ? WHERE locker_number = ?");
        $stmt->bind_param("dii", $weight, $user_id, $locker_number);
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "ok kg_before "]);
        } else {
            echo json_encode(["status" => "fail", "message" => "kg_before no"]);
        }
    } 
}
?>
