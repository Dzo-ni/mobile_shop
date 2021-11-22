<?php
namespace App\Controllers\Payment;
class PaymentCalculator{
    public static function calculateOrderAmount( $items): int {
// Replace this constant with a calculation of the order's amount
    // Calculate the order total on the server to prevent
    // customers from directly manipulating the amount on the client
    $total=0.0;
    foreach($items as $item){
        $total += floatval($item->product_price) * floatval($item->quantity);
    }
    return floatval($total * 100 + ($total * 100 /20));
    }
}