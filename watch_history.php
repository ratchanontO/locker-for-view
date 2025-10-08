<?php
session_start();
include 'connectdb.php';
include 'config.php';

if (!isset($_SESSION["userid"])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ;
$locker_number = $_GET['locker_number'] ;

if (!$id || !$locker_number) {
    echo "<p class='text-danger'>พารามิเตอร์ไม่ครบหรือไม่ถูกต้อง</p>";
    exit;
}

$stmt = $conn->prepare("SELECT * FROM log_locker WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

$imgurl = $url . "api_cam/" . $data['path_photo'];

if (!$data) {
    echo "<p class='text-danger'>ไม่พบข้อมูล</p>";
    exit;
}
?>
<style>
    .mitr-extralight300, .mitr-extralight300 th, .mitr-extralight300 td {
        font-family: 'Mitr', sans-serif;
        font-weight: 300;
    }
</style>
<?php require "head.php"; ?>
<body>
<?php require "navbar.php"; ?>

<div class="container custom-narrow mt-4">
    <div class="card shadow-sm p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold m-0">รายละเอียดการใช้งานตู้</h4>
            <a href="history.php" class="btn btn-sm btn-primary">← กลับ</a>
        </div>
        <!-- <h4 class="fw-bold mb-4">รายละเอียดการใช้งานตู้</h4>
        <a href="history.php" class="btn btn-primary mt-3">← กลับ</a> -->
        <div class="text-center">
            <img src="<?= $imgurl ?>" class="img-fluid mb-2" style="max-height: 250px;">
        </div>
        
        <table class="table table-bordered mt-3">
            <tr class="mitr-extralight300">
                <th>ตู้หมายเลข</th>
                <td><?= htmlspecialchars($data['locker_number']) ?></td>
            </tr>
            <tr class="mitr-extralight300">
                <th>วันและเวลา</th>
                <td><?= htmlspecialchars($data['time']) ?></td>
            </tr>
            <tr class="mitr-extralight300">
                <th>น้ำหนักก่อน (kg)</th>
                <td><?= htmlspecialchars($data['kg_before']/1000) ?>  กิโลกรัม</td>
            </tr>
            <tr class="mitr-extralight300">
                <th class="mitr-extralight300">น้ำหนักหลัง (kg)</th>
                <td><?= htmlspecialchars($data['kg_after']/1000) ?>   กิโลกรัม</td>
            </tr>
        </table>
    </div>
</div>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>

<?php require "low_menu.php"; ?>
</body>
