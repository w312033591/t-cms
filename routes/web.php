<?php

Route::get('/', function (){
    return 'hello world!';
});
Route::get('pic/{img_id}_{size}_{suffix}', 'PicturesController@show')->name('image');
Route::post('ajax_upload_image', 'PicturesController@upload');