<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/invoices', 'InvoiceController@index');
Route::post('/invoice', 'InvoiceController@store');
Route::get('/invoice/{id}', 'InvoiceController@show');
Route::delete('/invoice/{id}', 'InvoiceController@destroy');