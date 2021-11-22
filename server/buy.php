<?php
session_start();


require_once "../database_connection.php";
function checkSession()
{

    $response['status'] = 1;
    $isLoged = false;
    if (isset($_SESSION['login'])) {
        $isLoged = true;
    }
    if (!$isLoged) {
        $response['status'] = 0;
        $response['msg'] = 'Morate se ulogovati';
    }
    echo json_encode($response);
    die;
}

if (isset($_GET['checksession'])) {
    checkSession();
}

if (isset($_POST['shoppingCart'])) {
    $shouldLogged = false;
    $response['status'] = 1;
    $products = $_POST['products'];
    $total = floatval($_POST['total']);
    $user_id = intval($_SESSION['user_id']);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->beginTransaction();
    try {
        $query = "INSERT INTO sales(user_id , total_price) VALUES(?,?)";
        $stmt = $pdo->prepare($query);
        $result = $stmt->execute([$user_id, $total]);
        $catch = false;

        $sales_id = $pdo->lastInsertId();
        $query = "INSERT INTO product_sales VALUES( ? , ? , ?)";

        $stmt = $pdo->prepare($query);
        $missing_item = "";
        foreach ($products as $product) {
            try {
                $stmt->execute([$product['id'], $sales_id, $product['quantity']]);
            } catch (\PDOException $e) {
                $missing_item .= " - item with id : " . $product['id'] . " with quantity of " . $product['quantity'];
                $catch = true;
            }
        }
        if ($catch) throw new PDOException();

        $pdo->commit();
    } catch (PDOException $e) {
        $message = "[" . date("H:i:sa d.M.Y") . "] " . $e->getMessage() . ' User with id ' . $user_id .  "trying to buy same missing items on stock: " .  $missing_item . PHP_EOL;

        error_log($message, 3, "./my_log.php");
        $response['msg'] = "Not enought quantities on stock";
        $response['status'] = 0;
        $pdo->rollBack();
    }

    //$pdo->commit();
    echo json_encode($response);
}
