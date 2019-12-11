<?php

namespace Modules\Permission\Tests\Feature;

use Modules\Base\Tests\AdminTestCase;

class RoleTest extends AdminTestCase
{
    private static $roleUri = 'permission/roles';

    public function testFetchRoles()
    {
        $this->getJson(self::$roleUri)
            ->assertSuccessful()
            ->assertJsonStructure(['data' => ['roles', 'paginate']]);
    }

    public function testStoreRole()
    {
        $response = $this->postJson(self::$roleUri, [
            'locale' => config('app.locales'),
            'name' => $this->faker()->unique()->regexify('^[a-z]+(\.[a-z]+){0,2}$'),
            'guard_name' => config('auth.defaults.guard'),
            'comment' => $this->faker()->text(255),
        ])
            ->assertSuccessful()
            ->assertJsonStructure(['data' => ['role']]);

        return $response->original['data']['role']['uuid'];
    }

    /**
     * @depends testStoreRole
     */
    public function testUpdateRole($uuid)
    {
        $response = $this->patchJson(self::$roleUri . '/' . $uuid, [
            'locale' => config('app.locales'),
            'name' => $this->faker()->unique()->regexify('^[a-z]+(\.[a-z]+){0,2}$'),
            'guard_name' => config('auth.defaults.guard'),
            'comment' => $this->faker()->text(255),
        ])
            ->assertSuccessful()
            ->assertJsonStructure(['data' => ['role']]);
    }

    /**
     * @depends testStoreRole
     */
    public function testDestroyRole($uuid)
    {
        $response = $this->deleteJson(self::$roleUri . '/' . $uuid)
            ->assertSuccessful(['status' => 'success']);
    }
}
