<?php


namespace App\Controllers;

use App\Core\Config;

use App\Core\Controller;
use App\Core\DBConnection;
use App\Models\EmailModel;
use App\Models\AddressModel;
use App\Models\CustomerModel;
use App\Controllers\Customer\StripeCustomer;
use App\Controllers\Payment\PaymentCalculator;
use App\Controllers\Payment\StripePaymentIntent;
use App\Core\RandomString;

// use App\Models\ProductSalesModel;


class PaymentController extends Controller { 
  
    use RandomString;
   
    private StripePaymentIntent $stripePaymentIntent;
    private StripeCustomer $stripeCustomer;
    private $paymentIntent;
    public function __construct(){
        $this->stripePaymentIntent = new StripePaymentIntent();
        
  
    }
   
public function payment(){
  
   
    require_once(dirname(__DIR__).'/../config.php');
//check session and check AUTHORISATION

$uuid = guidv4();
$json = file_get_contents('php://input');
$data = json_decode($json);

$db= new DBConnection();
$this->pdo=$db->getConnection();
$this->stripeCustomer = new StripeCustomer($this->pdo);
try {

    //for  signin users 
    //$data->appsession
    if($this->getSession()->has("user_id")){
      
      $userId=$this->getSession()->get("user_id");
     
        
        $customer_id= $this->stripeCustomer->getIdByUserId($userId);
       
        // $customer_id = "cus_KNNYS1dy80EKby";
        //retrieve customer from stripe 
       // $this->stripeCustomer =\Stripe\Customer::retrieve($customer_id);
     
        //if exist payment intent then update it
        
        if($data->pi_id != ""){
      
            //if already have payment_intent id which is not confirm, then get same stripe payment intent
            $this->paymentIntent = $this->stripePaymentIntent->update($data->pi_id, $customer_id,$data->items);

        
          
        }else{
            //if exist payment intent which not confirm create new payment intent
          
            $this->paymentIntent= $this->stripePaymentIntent->create($customer_id,$data->items);
          
        }
       
        //for non signin users 
        //LATTER MUST CHANGED 
        //NOT CREATE USER ON STRIPE
    }else{
       //provide email value and based on that 
       //stripe_customer table have email_id
       //select sc.customer_id from stripe_customer join email e on 
      
        $emailModel= new EmailModel($this->pdo);
        $email =   $emailModel->getByFieldName("email_address",$data->customer->email);
        $customer_id = $email ? $email->customer_id : null;
        if($customer_id){
          $stripe_customer_id=$this->stripeCustomer->getIdByUserEmail($data->customer->email);
         
        }
      
        // $response['customer_id'] = $customer_id;
        // $response['pi_id'] = $data->pi_id;
        // exit(json_encode($response));
        //user doesnt exist with that email
        if(!$customer_id){
          
          //create custom on stripe
          //get customer_id
         $customer=  $data->customer;
         $stripe_customer_id= $this->stripeCustomer->create($uuid, $customer->email);
       
        $customerModel = new CustomerModel($this->pdo);
        $customer_id =  $customerModel->add([
          "firstname"=>$customer->firstname,
          "lastname"=> $customer->lastname,
          "stripe_customer_id"=> $stripe_customer_id
          ]);
       //create email
     
        
       $emailModel->add([
           "email_address"=>$customer->email, 
           "activation_code"=>$this->generateRandomString(),
           "email_status_id"=>Config::EMAIL_NOT_VERIFIED,
           "on_mailing"=>1, 
           "is_customer_registered"=> 0,
           "customer_id"=>$customer_id]);
         
       //create address
       $addressModel= new AddressModel($this->pdo);
       $addressModel->add([
           "street"=>$customer->street ,
           "city"=>$customer->city, 
           "customer_id"=>$customer_id]);
     
       
        }
        //for not signin users
        
        
   
    if($data->pi_id != ""){
        $this->paymentIntent = $this->stripePaymentIntent->update($data->pi_id, $stripe_customer_id,$data->items);
    }else{
        $this->paymentIntent= $this->stripePaymentIntent->create($stripe_customer_id,$data->items);
    }
}

    $output = [
        'clientSecret' => $this->paymentIntent->client_secret,
        'payment_intent' => $this->paymentIntent->id,
         'customer_id'  => $customer_id
      ];
      echo json_encode($output);
      die;
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
  } catch (\Exception $e) {
    // Something else happened, completely unrelated to Stripe
  }



}



  // private function generateRandomString($length = 20) {
  //   $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  //   $charactersLength = strlen($characters);
  //   $randomString = '';
  //   for ($i = 0; $i < $length; $i++) {
  //       $randomString .= $characters[rand(0, $charactersLength - 1)];
  //   }
  //   return $randomString;
  // }



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



  

