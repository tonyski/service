<?php


/**
 * 获取当前登录用户的信息
 */
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('fetchMenu', 'RouteController@fetchMenu')->name('fetchMenu');
});
