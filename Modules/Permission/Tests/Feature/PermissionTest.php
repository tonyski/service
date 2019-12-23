<?php

namespace Modules\Permission\Tests\Feature;

use Modules\Base\Tests\AdminTestCase;

class PermissionTest extends AdminTestCase
{
    private static $adminPermissionUri = 'permission/admin/fetchPermission';
    private static $permissionUri = 'permission/permissions';
    private static $groupUri = 'permission/permissions/admin/groups';

    public function testFetchAdminPermissions()
    {
        $this->getJson(self::$adminPermissionUri)
            ->assertSuccessful()
            ->assertJsonStructure(['data' => ['permissions']]);
    }

    public function testFetchPermissions()
    {
        $this->getJson(self::$permissionUri)
            ->assertSuccessful()
            ->assertJsonStructure(['data' => ['permissions', 'paginate']]);
    }

    public function testFetchGroups()
    {
        $this->getJson(self::$groupUri)
            ->assertSuccessful()
            ->assertJsonStructure(['data' => ['groups']]);
    }
}
