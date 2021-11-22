<?php
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");
include("config.php");

$uuid = guidv4();
$json = file_get_contents('php://input');
$data = json_decode($json);

define('uniqueID', uniqid('',true));

function calculateOrderAmount( $items): int {
  $total=0.0;
  foreach($items as $item){
      $total += floatval($item->product_price) * floatval($item->quantity);
  }
  return floatval($total * 100);
  }
  
  try {
    // retrieve JSON from POST body

    // Look up the available cards stored on your Customer
     
    // Charge the customer and card immediately
    $payment_intent = \Stripe\PaymentIntent::create([
      'amount' => calculateOrderAmount($data->items),
      'currency' => 'usd',
      'customer' => $data->customer_id,
      'payment_method' => $data->pm_method,
      'off_session' => true,
      'confirm' => true
    ], [
        'idempotency_key' => uniqueID
      ]);
    echo json_encode([
        'paymentIntent' => $payment_intent,
    ]);
  }
 catch(\Stripe\Exception\CardException $e) {
  // Since it's a decline, \Stripe\Exception\CardException will be caught
  echo 'Status is:' . $e->getHttpStatus() . '\n';
  echo 'Type is:' . $e->getError()->type . '\n';
  echo 'Code is:' . $e->getError()->code . '\n';
  // param is '' in this case
  echo 'Param is:' . $e->getError()->param . '\n';
  echo 'Message is:' . $e->getError()->message . '\n';
} catch (\Stripe\Exception\RateLimitException $e) {
  // Too many requests made to the API too quickly
} catch (\Stripe\Exception\InvalidRequestException $e) {
  // Invalid parameters were supplied to Stripe's API
} catch (\Stripe\Exception\AuthenticationException $e) {
  // Authentication with Stripe's API failed
  // (maybe you changed API keys recently)
} catch (\Stripe\Exception\ApiConnectionException $e) {
  // Network communication with Stripe failed
} catch (\Stripe\Exception\ApiErrorException $e) {
  // Display a very generic error to the user, and maybe send
  // yourself an email
} catch (Exception $e) {
  // Something else happened, completely unrelated to Stripe
}