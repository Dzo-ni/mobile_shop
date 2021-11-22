<?php

namespace App\Controllers\Order;

use Mpdf\Mpdf;
use App\Core\Controller;
use App\Models\EventModel;
use PHPMailer\PHPMailer\PHPMailer;
use App\EventHandlers\EmailEventHandler;

class OrderEmailController extends Controller
{
   public function create($firstname, $lastname, $po_number , $subtotal_price, $total_price, $email_address , $order_detail_array){
     
      $mail = new PHPMailer(true);
               $event = new EmailEventHandler($mail);


               $html = "<!doctype html><html><meta charset='utf-8'></head><body>";
               $html .= "<h1>Receipt</h1>"; 
               $html .= "<p>Thank you  ". $firstname . " ". $lastname." for buy products</p>";
               $html .= "</body></html>";

            $pdf_html = "<!doctype html><html><head><meta charset='utf-8'></head><body>
            <header>
            <div  class='row'>
               <div class='col left_col' >
               <p> MOBILE SHOP </p>
               <p>Beogradska 100, Beograd 1100<p>
               <p>mobile_shop@mobile_shop.com</p>
               </div>
               <div class=' col right_col'>
               <p> ". $firstname . " ". $lastname."</p>
               <p>Beogradska 100, Beograd 11000<p>
               <p>". $email_address . "</p>
               </div>
            </div>
            <h1>Receipt PO NUMBER: #".$po_number."</h1></header> <table id='items_list' cellspacing='0'>
               <thead>
                  <tr>
                     <th>ID</th>
                     <th>PRODUCT NAME</th>
                     <th>QUANTITY</th>
                  </tr>
               </thead>
               <tbody>";
               $item_key=1;
               foreach ($order_detail_array as $item) {
                  $pdf_html .= " <tr>
                  <td> $item_key</td>
                  <td> $item->product_name</td>
                  <td> $item->quantity</td>
                     </tr>";
                     $item_key++;
               }

               $pdf_html .=  "</tbody> </table><table id='price_table' cellspacing='0'>
               <thead>
                  <tr>
                     <th>SUBTOTAL PRICE</th>
                     <th>TAXES PRICE</th>
                     <th>TOTAL PRICE</th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr>
                     <th>". $subtotal_price."$</th>
                     <th>20.00$</th>
                     <th> ". $total_price."$</th>
                  </tr>
                  </tbody></table>";

            
               $pdf_html .= "<div  class='row'>
               <div class='col left_col' >
               <p> Mobile Shop AD </p>
               <p>Beogradska 100<p>
               <p>mobile_shop@mobile_shop.com</p>
               </div>
               <div  class='col right_col'>
               <p>Online payment</p>
               <p> Date And Time:  </p>
               <p>".date("H:i:sa d-m-Y",time()+3600)."</p>
               </div>
            </div>";
            $pdf_html .= '<body><html>';
               $mpdf = new Mpdf(); 
               $stylesheet = file_get_contents('__DIR__. "/../../runtime/pdf/pdf.css"'); // external css
               $mpdf->WriteHTML($stylesheet,1);
               $mpdf->WriteHTML($pdf_html,2);
               // $mpdf->WriteHTML($pdf_html);
               
               //call watermark content aand image
               $mpdf->SetWatermarkText('Mobile Shop');
               $mpdf->showWatermarkText = true;
               $mpdf->watermarkTextAlpha = 0.1;
               
               
               //save the file put which location you need folder/filname
               $mpdf->Output(__DIR__. "/../../../runtime/pdf/receipt_".$po_number.".pdf", 'F');
               
               
               // //out put in browser below output 
               // $mpdf->Output();
               $event->setSubject("Receipt #" . $po_number);
               $event->setBody($html);
               $event->addAddress($email_address);
               $event->addAttachment(__DIR__. "/../../../runtime/pdf/receipt_".$po_number.".pdf");
               $eventModel= new EventModel($this->pdo);
               $event_id =$eventModel->add(
                  [
                     "type"=>"email",
                     "data"=> $event->getData(),
                     "status"=> "pending"
                  ]
               );
               return $event_id ?? null;
   }
}