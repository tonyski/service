<?php
//访问权限管理入口的权限，包括角色列表
Route::middleware(['permission:permission'])->group(function () {
    Route::get('roles', 'RoleController@index')->name('roles');
});
