<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware(['auth:sanctum'])->group(function () {
    Route::group(['namespace' => 'App\Http\Controllers'], function () {
        Route::post('login', 'LoginController@login');
        Route::post('logout', 'LoginController@logout');
        Route::get('auth_user', 'UserController@getAuthUser');
        Route::get('all_users', 'UserController@getAllUsers');
        Route::post('add_new_user', 'UserController@addNewUser');
        Route::post('update_existing_user', 'UserController@updateExistingUser');
        Route::post('delete_existing_user/{id}', 'UserController@deleteExistingUser');
        Route::put('update_user/{id}', 'UserController@updateUser');
        Route::get('document_type_list', 'DocumentController@getDocumentTypes');
        Route::get('get_active_documents', 'DocumentController@getAllActiveDocuments');
        Route::get('get_document_track_record/{document}', 'DocumentController@getDocTrackRecord');
        Route::get('get_document_details/{id}', 'DocumentController@getSelectedDocument');
        Route::get('office_list', 'OfficeController@getOfficeList');
        Route::get('tracking_list', 'OfficeController@getTrackingList');
        Route::get('get_non_page_active_documents', 'DocumentController@getNonPaginatedActiveDocuments');
        Route::post('add_new_document', 'DocumentController@addNewDocument');
        Route::get('logs', 'LogController@index');
        Route::post('add_new_document/{document?}', 'DocumentController@addNewDocument');
        Route::post('receive_document_confirm/{id}', 'DocumentController@receiveDocument');
        Route::post('forward_document_confirm/{id}', 'DocumentController@forwardDocument');
        // Route::get('receive_document/{id}', 'DocumentController@ReceiveDocument');

        Route::post('add_new_office', 'OfficeController@addNewOffice');
        Route::post('update_existing_office', 'OfficeController@updateExistingOffice');
        Route::post('delete_office/{id}', 'OfficeController@deleteOffice');

    });

    Route::get('/authenticated', function () {
        return true;
    });
});
