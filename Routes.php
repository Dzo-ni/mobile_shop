<?php

return [
    App\Core\Route::get('|^api/filters/?$|','Filter','getAll'),
    App\Core\Route::post('|^api/products/?$|','Product','getAll'),

    App\Core\Route::get('|^api/product/([0-9]+)/?$|','Product','show'),
    App\Core\Route::get('|^api/product/([0-9]+)/relatedFour/?$|','Product','relatedFour'),
    App\Core\Route::get('|^api/products/latestFour/?$|','Product','latestFour'),

    App\Core\Route::get('|^api/user/?$|','User','getPersonalInformation'),
    App\Core\Route::post('|^api/user/([0-9]+)/?$|','User','getPersonalInformation'),
    App\Core\Route::post('|^api/user/edit/?$|','Address','setAddress'),
    App\Core\Route::get('|^api/email/verified/([a-zA-Z@_\.]+)/([\w]+)/?$|','Email','verified'),
    
    //ADMIN OPERATIONS
    //CREATE product
    App\Core\Route::post('|^admin/product/?$|','Product','create'),
    //get product 
    App\Core\Route::get('|^admin/dashboard/statistical/products/?$|','Dashboard','getStatisticalDataProducts'),

    App\Core\Route::get('|^admin/api/products/?$|','Product','getAllToDasboard'),
   
  
    //product detail dashobard
    App\Core\Route::get('|^admin/api/product/([0-9]+)/?$|','Product','showToDashboard'),

    //update product 
    App\Core\Route::post('|^admin/api/product/([0-9]+)/?$|','Product','update'),

    //delete product
    App\Core\Route::get('|^admin/api/product/delete/([0-9]+)/?$|','Product','delete'),

    //get users
    App\Core\Route::get('|^admin/api/users/?$|','User','getAllToDasboard'),
    App\Core\Route::get('|^admin/api/users/([0-9]+)/?$|','User','getToDasboard'),
    
    //get orders
    App\Core\Route::get('|^admin/api/orders/?$|','Order','getAllToDasboard'),
    App\Core\Route::get('|^admin/api/orders/([0-9]+)/?$|','Order','getToDasboard'),
    App\Core\Route::get('|^admin/api/orderdetail/([0-9]+)/?$|','OrderDetail','getToDasboard'),




    App\Core\Route::get('|^products/([0-9]+)/?$|','Main','delete'),   //delete







    //Order
    App\Core\Route::post('|^api/order/?$|','Order','order'),
    App\Core\Route::get('|^api/order/total_price/?$|','Order','getTotalSumOrder'),









    //Payment
    App\Core\Route::post('|^api/payment/?$|','Payment','payment'),

    //Auth
    App\Core\Route::post('|^auth/user/register/?$|','Auth','userRegister'),
    App\Core\Route::post('|^auth/user/login/?$|','Auth','userLogin'),
    App\Core\Route::get('|^auth/user/is_login/?$|','Auth','isLogin'),

    //Forgot Password
    App\Core\Route::get('|^forgot/password/([0-9a-z@\.]+)/?$|','ForgotPassword','handle'),
    //Reset Password
    App\Core\Route::post('|^reset/password/?$|','ResetPassword','handle'),

    // Show profile order history
    App\Core\Route::get('|^profile/orders/?$|','Profile','orders'),
    App\Core\Route::get('|^profile/orders/([0-9]+)/?$|','Profile','show'),
    App\Core\Route::get('|^profile/person/([0-9]+)/?$|','Profile','personalInformation'),

    //EVENT
    //EMAIL
    App\Core\Route::get('|^handle/([a-z]+)/?$|','EventHandler','handle'),
    App\Core\Route::get('|^handle/([a-z]+)/([0-9]+)/?$|','EventHandler','handleById'),

    //FILE
    App\Core\Route::get('|^remove/old/files/?$|','FileHandler','delete'),


    //Webhook
    App\Core\Route::post('|^webhook/stripe/?$|','Webhook','stripe'),


    App\Core\Route::any('|^.*$|','Main','home')
];