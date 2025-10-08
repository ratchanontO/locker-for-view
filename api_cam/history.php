<?php
include '../connectdb.php'; 
$status = 0;

if(isset($_FILES['image']) && isset($_POST['locker_number'])){
    $locker_number = intval($_POST['locker_number']); 

    $target_dir = "uploads/";
    if(!is_dir($target_dir)){
        mkdir($target_dir, 0777, true);
    }

    $filename = date("Ymd_His") . "_" . basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $filename;

    if(move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)){
        // echo "Upload success: " . $target_file;
        $stmt = $conn->prepare("UPDATE dataforlog SET path_photo = ? , take_photo = ? WHERE locker_number = ?");
        $stmt->bind_param("sii", $target_file, $status, $locker_number);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("SELECT * FROM dataforlog WHERE locker_number = ?");
        $stmt->bind_param("i", $locker_number);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt = $conn->prepare("INSERT INTO log_locker (locker_id,locker_number, user_id, event_type, path_photo, kg_before, kg_after) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iiissdd", $row["locker_id"], $row["locker_number"], $row["user_id"], $row["event_type"], $row["path_photo"], $row["kg_before"], $row["kg_after"]);
                        
            if ($stmt->execute()) {
                $stmt = $conn->prepare("UPDATE dataforlog SET user_id = 0, kg_before = 0, kg_after = 0, path_photo = 0, event_type = 0, take_photo = 0  WHERE locker_number = ?");
                $stmt->bind_param("i",$locker_number);
                $stmt->execute();
            }
        }




    } else {
        echo json_encode(["status" => "fail", "message" => "db is down"]);
    }
} else {
    echo json_encode(["status" => "fail", "message" => "db is down"]);
}
?>
