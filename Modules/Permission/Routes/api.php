<?php
//访问权限管理入口的权限，包括角色列表
Route::middleware(['permission:permission'])->group(function () {
    Route::get('roles', 'RoleController@index')->name('roles.index');
});

//操作权限管理
Route::middleware(['permission:permission.action'])->group(function () {
    Route::post('roles', 'RoleController@store')->name('roles.store');
    Route::patch('roles/{uuid}', 'RoleController@update')->name('roles.update');
    Route::delete('roles/{uuid}', 'RoleController@destroy')->name('roles.destroy');
});
