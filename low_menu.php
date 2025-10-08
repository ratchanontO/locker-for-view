

<style>
  .mitr-extralight200 {
    font-family: "Mitr", sans-serif;
    font-weight: 200;
    font-style: normal;
  }

  .mitr-extralight300 {
    font-family: "Mitr", sans-serif;
    font-weight: 300;
    font-style: normal;
  }

  .bottom-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: #fff;
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
    border-radius: 20px 20px 20px 20px;
    padding: 5px 0;
  }

  .nav-link {
    color: #333;
    font-size: 13px;
  }

  .nav-link i {
    display: block;
    font-size: 20px;
  }

  .nav-link.active {
    color: #ffffff !important;
    background: #4a90e2;
    border-radius: 50%;
    width: 60px;
    height: 60px;
    margin-top: -25px;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
  }

  .nav-link.active span {
    font-size: 10px;
    margin-top: 2px;
  }

  .custom-narrow {
    max-width: 1123px;
    margin: auto;
  }
</style>
  <nav class="bottom-nav d-flex justify-content-around align-items-center mitr-extralight300">
    <a href="#" class="nav-link text-center">
      <i class="fa-brands fa-waze"></i>
      <span>ติดต่อฉัน</span>
    </a>
    <a href="my_locker.php" class="nav-link text-center">
      <i class="fa-solid fa-box-open"></i>
      <span>ตู้ของฉัน</span>
    </a>
    <a href="index.php" class="nav-link active text-center">
      <i class="fas fa-home"></i>
      <span>Home</span>
    </a>
    <a href="history.php" class="nav-link text-center">
      <i class="fas fa-receipt"></i>
      <span>ประวัติการใช้งาน</span>
    </a>
    <a href="useredit.php" class="nav-link text-center">
      <i class="bi bi-person-fill"></i>
      <span>ข้อมูลผู้ใช้</span>
    </a>
  </nav>