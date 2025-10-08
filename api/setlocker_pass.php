<?php
    header('Content-Type: application/json');
    include '../connectdb.php';
    
    if (isset($_POST['userid']) && isset($_POST['locker_id']) && isset($_POST['locker_number']) && isset($_POST['password'])) {
        $userid = $_POST['userid'];
        $lockerid = $_POST['locker_id'];
        $lockernumber = $_POST['locker_number'];
        $password = $_POST['password'];

        
        $stmt = $conn->prepare("SELECT 1 FROM user_locker WHERE user_id = ?");
        $stmt->bind_param("i",$userid);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $stmt = $conn->prepare("SELECT 1 FROM user_locker WHERE locker_id = ?");
        $stmt->bind_param("s", $lockerid);
        $stmt->execute();
        $result1 = $stmt->get_result();

        if ($result->num_rows > 0 || $result1->num_rows > 0 ) {

            if($result1->num_rows > 0){
                echo json_encode(['success' => false, 'message' => 'ตู้ล็อกเกอร์นี้ถูกใช้ไปแล้ว']);
            }else if($result->num_rows > 0){
                echo json_encode(['success' => false, 'message' => '1 User ใช้ได้เพียง 1 ตู้']);
            }
        } else {

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO user_locker (user_id, locker_id, locker_number, locker_password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiss", $userid, $lockerid, $lockernumber, $hashed_password);

            if ($stmt->execute()) {

                $stmt1 = $conn->prepare("UPDATE locker_status SET is_owned = 1 WHERE locker_id = ?");
                $stmt1->bind_param("i", $lockerid);

                if($stmt1->execute()){
                    echo json_encode(['success' => true, 'message' => 'ตั้งรหัสผ่านสำเร็จ']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'บันทึกข้อมูลไม่สำเร็จ']);
            }
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบ']);
    }

?>




