<?php
require "vendor/autoload.php";


$db = new \App\Core\DBConnection();
$pdo = $db->getConnection();
session_start();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile shop</title>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
    <link rel='stylesheet' href='main.css'>

</head>

<body>
    <div class="modal" id="product-details">
        <div class="modal-content">
            <div class="modal-header">

            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">

            </div>
        </div>
    </div>
    <div class="wrap header-wrap">
        <nav>
            <?php
            if (isset($_SESSION['login'])) {
                $query = "SELECT u.firstname, u.lastname, e.email_address FROM user u
                JOIN email e ON u.email_id= e.email_id WHERE user_id = ?";

                $stmt = $pdo->prepare($query);

                $stmt->execute([$_SESSION['user_id']]);
                if ($stmt->rowCount() == 1) {
                    $user = $stmt->fetch(PDO::FETCH_OBJ);
            ?>
                    <user-details id='user_session' style='display:none;'>
                        <?php
                        foreach ($user as $key => $value) {
                        ?>
                            <span data-values="<?php echo $value ?>"></span>
                        <?php } ?>
                    </user-details>
                <?php } ?>
                <span class="main_nav_link" id="my_profile"><span id="my_profile_avatar" style="padding:0.3rem;text-align:center;border-radius:50%;background-color:white;">
                        <?php echo substr($user->firstname, 0, 1); ?></span><?php echo $user->firstname; ?></span>
                <span class="main_nav_link" id='logout'>Logout</span>
            <?php
            } else {
            ?>
                <span class="main_nav_link" id="login">Login</span>
                <span class="main_nav_link" id='registration'>Registration</span>
            <?php
            }
            ?>

            <span id='basket' style="position: relative;"><img style="width:3rem;" src="assets/img/shoping_cart.png" alt="">
                <span id='noItems' style="display:block;position:absolute;bottom:0;border-radius:30%;padding: 3px;background-color:black;color:white;left:-10px"></span></span>
        </nav>
        <header>
            <h1>MOBILE SHOP</h1>
        </header>
    </div>
    <div class="wrap">
        <aside class="aside">
            <div>
                <h2>Price</h2>
                <input type="hidden" id="hidden_minimum_price" value="1000">
                <input type="hidden" id="hidden_maximum_price" value="65000">
                <p id='price_show'>1000-65000</p>
                <div id="price_range"></div>
            </div>
            <div class="list">
                <h2>Brand</h2>
                <ul>
                    <?php
                    // $query = "select distinct(product_brand) from product where product_status = '1' order by product_id desc";
                    // $stmt = $pdo->prepare($query);
                    // $stmt->execute();
                    // $result = $stmt->fetchAll();

                    foreach ($result as $row) {

                    ?>
                        <li><input class="common_filter brand" value="<?php echo  $row['product_brand'] ?>" id="<?php echo  $row['product_brand'] ?>" type="checkbox"><label for="<?php echo  $row['product_brand'] ?>"><?php echo  $row['product_brand'] ?></label></li>
                    <?php
                    }
                    ?>


                </ul>
            </div>

            <div class="list">
                <h2>RAM</h2>
                <ul>
                    <?php
                    $query = "select distinct(product_ram) from product where product_status = '1' order by product_ram desc";
                    $stmt = $pdo->prepare($query);
                    $stmt->execute();
                    $result = $stmt->fetchAll();
                    foreach ($result as $row) {

                    ?>
                        <li><input class="common_filter ram" value="<?php echo  $row['product_ram'] ?>" id="<?php echo  $row['product_ram'] ?>" type="checkbox"><label for="<?php echo  $row['product_ram'] ?>"><?php echo  $row['product_ram'] ?> GB</label></li>
                    <?php
                    }
                    ?>
                </ul>
            </div>

            <div class="list">
                <h2>Internal Storage</h2>
                <ul>
                    <?php
                    $query = "select distinct(product_storage) from product where product_status = '1' order by product_storage desc";
                    $stmt = $pdo->prepare($query);
                    $stmt->execute();
                    $result = $stmt->fetchAll();
                    foreach ($result as $row) {

                    ?>
                        <li><input class="common_filter storage" value="<?php echo  $row['product_storage'] ?>" id="<?php echo  $row['product_storage'] ?>" type="checkbox"><label for="<?php echo  $row['product_storage'] ?>"><?php echo  $row['product_storage'] ?> GB</label></li>
                    <?php
                    }
                    ?>
                </ul>
            </div>
        </aside>
        <main class="main">
            <div class="filter-data"></div>


            <!-- <div class="card">
                    <div class="card-img">
                        <img src="assets/img/honor.jpg" alt="">
                    </div>
                    <div class="card-text">
                        <h3>Honor 7</h3>
                        <p>honor 7</p>
                        <p class="price">5000
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-img">
                        <img src="assets/img/honor.jpg" alt="">
                    </div>
                    <div class="card-text">
                        <h3>Honor 7</h3>
                        <p>honor 7</p>
                        <p>5000 EUR</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-img">
                        <img src="assets/img/honor.jpg" alt="">
                    </div>
                    <div class="card-text">
                        <h3>Honor 7</h3>
                        <p>honor 7</p>
                        <p>5000 EUR</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-img">
                        <img src="assets/img/honor.jpg" alt="">
                    </div>
                    <div class="card-text">
                        <h3>Honor 7</h3>
                        <p>honor 7</p>
                        <p>5000 EUR</p>
                    </div>
                </div>
                <div class="card">
                    <input class='itemId' type="hidden" value='7'>
                    <div class="card-img">
                        <img src="assets/img/honor.jpg" alt="">
                    </div>
                    <div class="card-text">

                        <h3>Honor 7</h3>
                        <p>honor 7</p>
                        <p>5000</p>

                    </div>
                    <div class="show-details">
                        <button class="add">ADD</button>
                        <button class='show_more'>SHOW MORE</button>
                    </div>
                </div>
                <div class="card">
                    <input class='itemId' type="hidden" value='5'>
                    <div class="card-img">
                        <img src="assets/img/honor.jpg" alt="">
                    </div>
                    <div class="card-text">
                        <h3>Nokia 10</h3>
                        <p>Nokia 10</p>
                        <p>3000</p>
                    </div>
                    <div class="show-details">
                        <button class="add">ADD</button>
                        <button class='show_more'>SHOW MORE</button>
                    </div>
                </div>
                <div class="card">
                    <input class='itemId' type="hidden" value='1'>
                    <div class="card-img">
                        <img src="assets/img/honor.jpg" alt="">
                    </div>
                    <div class="card-text">
                        <h3>Samsung Galaxy</h3>
                        <p>samsung</p>
                        <p>5000</p>
                    </div>
                    <div class="show-details">
                        <button class="add">ADD</button>
                        <button class='show_more'>SHOW MORE</button>
                    </div>
                </div> -->


        </main>
    </div>
    <script>
        fetch("methods.php?brand")
            .then(res => res.json())
            .then(data => {
                console.log(data)
            })
    </script>
    <script src="assets/js/main.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="//code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src='assets/js/jqajax.js'></script>
</body>

</html>