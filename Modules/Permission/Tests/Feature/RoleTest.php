<?php

namespace Modules\Permission\Tests\Feature;

use Modules\Base\Tests\AdminTestCase;

class RoleTest extends AdminTestCase
{
    private static $fetchRoles = 'permission/roles';

    public function testFetchRoles()
    {
        $this->getJson(self::$fetchRoles)
            ->assertSuccessful()
            ->assertJsonStructure(['data' => ['roles', 'paginate']]);
    }
}
