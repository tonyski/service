<?php

namespace Modules\Permission\Entities;

trait PermissionGroup
{
    /*
     * 权限分组
     *
     */
    public static $groups = [
        'admin'=>[
            'home',            //首页权限
            'permission',      //权限管理
            'route',           //入口管理
        ]
    ];
}
