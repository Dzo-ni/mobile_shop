<?php
// session_start();
use App\Core\DBConnection;
use App\Models\StripeUserModel;

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");
include("config.php");

$uuid = guidv4();
$json = file_get_contents('php://input');
$data = json_decode($json);


function calculateOrderAmount( $items): int {
    // Replace this constant with a calculation of the order's amount
    // Calculate the order total on the server to prevent
    // customers from directly manipulating the amount on the client
    $total=0.0;
    foreach($items as $item){
        $total += floatval($item->product_price) * floatval($item->quantity);
    }
    return floatval($total * 100 + ($total * 100 /20));
  }

try {
  
    //for  signin users 
    if($data->phpsess){
        session_start();
       
        //retrieve customer_id from DB 
        $userId= $_SESSION['user_id'];
        $db= new DBConnection();
        $stripeUserModel = new StripeUserModel($db->getConnection());
      
        $cust_id=$stripeUserModel->getCustomerId($userId, 'user_id');
        // $cust_id = "cus_KNNYS1dy80EKby";
        //retrieve customer from stripe 
        $customer =\Stripe\Customer::retrieve($cust_id);
     
        //if exist payment intent then update it
        if($data->pi_id != ""){
            $paymentIntent = Stripe\PaymentIntent::update(
              $data->pi_id,['amount' => calculateOrderAmount($data->items),'customer'=>$customer->id]
            );
          
        }else{
            //if exist payment intent which not confirm create new payment intent
            $paymentIntent = Stripe\PaymentIntent::create([
                'amount' => calculateOrderAmount($data->items),
                'currency' => 'usd',
                'customer'  =>$customer->id,
                'setup_future_usage' => 'off_session',
                ]);
              
        }
       
        //for non signin users 
        //LATTER MUST CHANGED 
        //NOT CREATE USER ON STRIPE
    }else{
       //provide email value and based on that 
        $db= new DBConnection();
        
        $stripeUserModel = new StripeUserModel($db->getConnection());
        $customer_id=$stripeUserModel->getCustomerId("roro@yahoo.com",'email');



        // $response['customer_id'] = $customer_id;
        // $response['pi_id'] = $data->pi_id;
        // exit(json_encode($response));
        //user doesnt exist with that email
        if(!$customer_id){
           
          //create custom on stripe
            $customer = \Stripe\Customer::create([
            'description' =>"roro@yahoo.com" ,
        ]
        , [
            'idempotency_key' => $uuid
          ]);
        
          //get customer_id
          $customer_id = $customer->id;
        }
        //for not signin users
        

   
    if($data->pi_id != ""){
       
        $pid=explode('_',$data->pi_id);
        $pid = $pid[0]."_".$pid[1];
        $paymentIntent = Stripe\PaymentIntent::update(
            $pid,['amount' => calculateOrderAmount($data->items),"customer" =>$customer_id]
        );
      
    }else{
        $paymentIntent = Stripe\PaymentIntent::create([
            'amount' => calculateOrderAmount($data->items),
            'currency' => 'usd',
            // 'customer'  =>$customer->id,
            'setup_future_usage' => 'off_session',
            "customer" =>$customer_id
            
            ]);
    }
}

    $output = [
        'clientSecret' => $paymentIntent->client_secret,
        'payment_intent' => $paymentIntent->id,
        // 'customer_id'  =>$customer->id
      ];
      echo json_encode($output);
} catch(\Stripe\Exception\CardException $e) {
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


// if($data->token){
//     $token = $data->token;
// $customer = \Stripe\Customer::create([
//     'email' => $token->email,
//     "source"=> $token->id
// ],[
//     'idempotency_key' => $ikey
//   ]);
 
// if($customer){
//     $ikey=uniqid("customer_",true);
// $charge = \Stripe\Charge::create([
//     "amount"=> 10000,
//     "currency" => "usd", 
//     "customer" => $customer->id,
//     "description" => "Purcase of product"
// ],[
//     'idempotency_key' => $ikey
//   ]
// );
//     json_encode($charge);
// }

// }



  

