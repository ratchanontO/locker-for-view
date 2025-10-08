<?php
include '../connectdb.php'; 

if (isset($_FILES['image']) && isset($_POST['locker_number'])) {
    $locker_number = intval($_POST['locker_number']); 
    $status = 0; // ค่า take_photo

    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $filename = date("Ymd_His") . "_" . basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $filename;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        // อัปเดตข้อมูลภาพใน dataforlog
        $stmt = $conn->prepare("UPDATE dataforlog SET path_photo = ?, take_photo = ? WHERE locker_number = ?");
        $stmt->bind_param("sii", $target_file, $status, $locker_number);
        $stmt->execute();
        $stmt->close();

        // ดึงข้อมูลล่าสุดของตู้
        $stmt = $conn->prepare("SELECT * FROM dataforlog WHERE locker_number = ?");
        $stmt->bind_param("i", $locker_number);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if ($row) {
            // ✅ เช็คว่าน้ำหนักในตู้เหลือน้อยกว่าที่กำหนด (สมมติ < 10 กรัม)
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

                    // 2) ลบข้อมูลใน users_locker (ยกเลิกการครอบครอง)
                    $stmt = $conn->prepare("DELETE FROM users_locker WHERE locker_number = ?");
                    $stmt->bind_param("i", $locker_number);
                    $stmt->execute();
                    $stmt->close();

                    // 3) รีเซ็ตค่าใน dataforlog
                    $stmt = $conn->prepare("UPDATE dataforlog SET user_id = 0, kg_before = 0, kg_after = 0, path_photo = '', event_type = 0, take_photo = 0 WHERE locker_number = ?");
                    $stmt->bind_param("i", $locker_number);
                    $stmt->execute();
                    $stmt->close();

                    // ถ้าทุกอย่างผ่าน → commit
                    $conn->commit();
                    echo json_encode(["status" => "success", "message" => "ตู้ถูกยกเลิก ของหมดแล้ว"]);

                } catch (Exception $e) {
                    // ถ้ามี error → rollback
                    $conn->rollback();
                    echo json_encode(["status" => "fail", "message" => "Transaction failed: " . $e->getMessage()]);
                }
            } else {
                echo json_encode(["status" => "ok", "message" => "ยังมีของในตู้"]);
            }
        }
    } else {
        echo json_encode(["status" => "fail", "message" => "Upload fail"]);
    }
} else {
    echo json_encode(["status" => "fail", "message" => "Invalid request"]);
}
?>
