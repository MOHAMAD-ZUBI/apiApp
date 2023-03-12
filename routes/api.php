<?php

use Illuminate\Support\Facades\Route;
use App\http\Controllers\paypalController;
$rid = "App\Http\Controllers";

//authentication
Route::post('/register', "$rid\userController@register");
Route::post('/login', "$rid\userController@login");

//profile
Route::post('/update_username', "$rid\userController@update_username");
Route::post('/update_email', "$rid\userController@update_email");
Route::post('/update_password', "$rid\userController@update_password");

//products
Route::post('/products', "$rid\\userController@products");

//purchases
Route::post('/view_purchases', "$rid\\purchasesController@view_purchases");
Route::post('/change_ip', "$rid\\purchasesController@change_ip");
Route::get('/download', "$rid\\purchasesController@download");

//key
Route::post('/active_key', "$rid\\userController@active_key");

//ticket
Route::post('/make_ticket', "$rid\\ticketsController@make_ticket");
Route::post('/close_ticket', "$rid\\ticketsController@close_ticket");
Route::post('/reply_ticket', "$rid\\ticketsController@reply_ticket");
Route::post('/view_ticket', "$rid\\ticketsController@view_ticket");

//admin
Route::post('/add_product', "$rid\\adminController@add_product");
Route::post('/add_key', "$rid\\adminController@add_key");



// payments checkout

Route::post('/createOrderPaypal',[paypalController::class,'createOrder']); // api for paypal and coinbase
Route::get('/PaymentSucess',[paypalController::class,'success'])->name("paymentSucessPaypal");
Route::get('/PaymentDeny',[paypalController::class,'cancel'])->name("paymentCancellPaypal");

