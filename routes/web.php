<?php
Route::get('/', 'IndexController@index');
Route::get('/list/{cateSlug}', 'IndexController@postList');

Route::get('pic/{img_id}_{size}_{suffix}', 'PicturesController@show')->name('image');

Route::group(['middleware' => 'auth'], function (){
   Route::post('ajax_upload_picture', 'PicturesController@upload');
});

