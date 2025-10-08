<style>
    /* :root {
        --ff-body: "Inter", system-ui, -apple-system, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        --ff-heading: "Playfair Display", Georgia, "Times New Roman", serif;
    } */

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

    h1, h2, h3, h4, h5, h6 {
        font-family: var(--ff-heading);
    }   
    
    .playpen-thai {
        font-family: "Playpen Sans Thai", cursive;
        font-weight: 400;
        font-style: normal;
    }

    .no-underline {
        text-decoration: none;
    }

    h1 {
        font-size: 24px;
        line-height: 30px;
        font-weight: 700;
    }

    .custom-narrow {
        max-width: 1123px;
        margin: auto;

    }   
</style>
<?php

if (isset($_POST['logout'])) {
    session_start();
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit();
}
?>
<!-- navbar -->
<div class="container custom-narrow">
    <div class="row align-items-center"style="background-color: #18243dc9; margin: 14px 12px; border-radius: 15px; padding: 1rem 0;">
        <div class="col-sm-6 col-7 d-flex flex-column align-items-start">
            <h1 class="text-white mx-2 mitr-extralight300">LOCKER FOR ECP</h1>
            <a class="text-white mx-2 mitr-extralight300 no-underline">บริการฝากของ</a>
        </div>
        <div class="col-sm-6 col-5 d-flex justify-content-end align-items-center mitr-extralight200">
            <a class="bi bi-person-fill " style="font-size:30px;"></a>
            <?php
            if (!empty($_SESSION["username"])) {
                echo '<a href="useredit.php" class="text-white mx-2 mitr-extralight" style="font-size:18px;" id="username"  >' . $_SESSION["username"] . '</a>';
                echo '<form method="post">';
                echo '<button  type="submit" class="btn btn-danger text-white mx-2" name="logout" style="border-radius: 20px;">logout</button>';
                echo '</form>';
            } else {
                echo '<a class="text-white mx-2" id="username">ยังไม่ได้LOGIN</a>';
                echo '<form method="post">';
                echo '<button  type="submit" class="btn btn-success text-white mx-2" name="logout" style="border-radius: 15px;">login</button>';
                echo '</form>';
            }
            ?>
        </div>
    </div>
</div>

<!-- end nav -->