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

Route::get('tickets/search/{prioridade}','TicketController@search')->where('prioridade','([Aa][Ll][Tt][Aa])|([Nn][Oo][Rr][Mm][Aa][Ll])');
Route::get('tickets/search/{start}/{end}','TicketController@search')->where(['start' => '[0-9]{4}-(0[1-9]|1[0,1,2])-([1-9]|0[1-9]|[1,2][0-9]|3[0,1])', 'end' => '[0-9]{4}-(0[1-9]|1[0,1,2])-([1-9]|0[1-9]|[1,2][0-9]|3[0,1])']);
Route::get('tickets/search/{start}/{end}/{prioridade}','TicketController@search')->where(['start' => '[0-9]{4}-(0[1-9]|1[0,1,2])-([1-9]|0[1-9]|[1,2][0-9]|3[0,1])', 'end' => '[0-9]{4}-(0[1-9]|1[0,1,2])-([1-9]|0[1-9]|[1,2][0-9]|3[0,1])', 'prioridade' => '([Aa][Ll][Tt][Aa])|([Nn][Oo][Rr][Mm][Aa][Ll])']);
Route::get('tickets/search/{orderby}','TicketController@search')->where('orderby','[Dd][Aa][Tt][Aa]([Cc][Rr][Ii][Aa][Cc][Aa][Oo]|[Aa][Tt][Uu][Aa][Ll][Ii][Zz][Aa][Cc][Aa][Oo])');
Route::get('tickets/search/{prioridade}/{orderby}','TicketController@search')->where(['prioridade' => '([Aa][Ll][Tt][Aa])|([Nn][Oo][Rr][Mm][Aa][Ll])','orderby' => '[Dd][Aa][Tt][Aa]([Cc][Rr][Ii][Aa][Cc][Aa][Oo]|[Aa][Tt][Uu][Aa][Ll][Ii][Zz][Aa][Cc][Aa][Oo])']);
Route::get('tickets/search/{start}/{end}/{orderby}','TicketController@search')->where(['start' => '[0-9]{4}-(0[1-9]|1[0,1,2])-([1-9]|0[1-9]|[1,2][0-9]|3[0,1])', 'end' => '[0-9]{4}-(0[1-9]|1[0,1,2])-([1-9]|0[1-9]|[1,2][0-9]|3[0,1])', 'orderby' => '[Dd][Aa][Tt][Aa]([Cc][Rr][Ii][Aa][Cc][Aa][Oo]|[Aa][Tt][Uu][Aa][Ll][Ii][Zz][Aa][Cc][Aa][Oo])']);
Route::get('tickets/search/{start}/{end}/{prioridade}/{orderby}','TicketController@search')->where(['start' => '[0-9]{4}-(0[1-9]|1[0,1,2])-([1-9]|0[1-9]|[1,2][0-9]|3[0,1])', 'end' => '[0-9]{4}-(0[1-9]|1[0,1,2])-([1-9]|0[1-9]|[1,2][0-9]|3[0,1])','prioridade' => '([Aa][Ll][Tt][Aa])|([Nn][Oo][Rr][Mm][Aa][Ll])','orderby' => '[Dd][Aa][Tt][Aa]([Cc][Rr][Ii][Aa][Cc][Aa][Oo]|[Aa][Tt][Uu][Aa][Ll][Ii][Zz][Aa][Cc][Aa][Oo])']);