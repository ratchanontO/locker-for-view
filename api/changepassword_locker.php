<?php
    include '../connectdb.php';
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents("php://input"), true);

    $password = $data['password'];
    $key = $data['key'];
    $locker_id = $data['locker_id'];
    $userid = $data['userid'];


    if (!$locker_id || !$password || !$key) {
        echo json_encode(['status' => 'error', 'message' => 'ข้อมูลไม่ครบ']);
        exit;
    }
    $stmt = $conn->prepare("SELECT * FROM user_locker WHERE locker_id = ? AND user_id = ?");
    $stmt->bind_param("ii",  $locker_id, $userid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if($row["reset_key"] == $key){
            $hash_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE user_locker SET locker_password = ?, reset_key = NULL WHERE locker_id = ? AND user_id = ?");
            $stmt->bind_param("sii", $hash_password, $locker_id, $userid);
            if($stmt->execute()){
                echo json_encode(['status' => 'success']);
            }else{
                echo json_encode(['status' => 'error', 'message' => 'บันทึกไม่ได้']);
            }
        }else{
            echo json_encode(['status' => 'error', 'message' => 'คียไม่ตรง']);
        }
    }

?>