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

        echo json_encode(["status" => "success", "message" => "ok done photo upload"]);
    }
} else {
    echo json_encode(["status" => "fail", "message" => "Invalid request"]);
}
?>
