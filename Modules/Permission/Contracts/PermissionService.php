<?php
/**
 * Created by PhpStorm.
 * User: fly
 * Date: 2020/3/27
 * Time: 13:41
 */

namespace Modules\Permission\Contracts;

interface PermissionService
{
    /**
     * @param $user  用户对象
     * @param array|string $roles 角色的uuid 数组
     * @param string $defaultRole 用户默认的角色的uuid
     * @return array
     */
    public function syncUserRoles($user, $roles, $defaultRole = '');

    /**
     * @param $user  用户对象
     * @param array|string $permissions 权限的uuid 数组
     * @return array
     */
    public function syncUserPermissions($user, $permissions);

    /**
     * @param $user 用户对象
     * @return \Modules\Permission\Contracts\Permission
     */
    public function getUserIndexPermission($user);

    /**
     * @param $user 用户对象
     * @return Collection  权限集合 \Modules\Permission\Contracts\Permission
     */
    public function getUserRoutePermission($user);
}