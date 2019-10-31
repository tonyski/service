<?php

namespace Modules\Route\Tests\Feature;

use Modules\Admin\Entities\Admin;
use Tests\TestCase;

class RouteTest extends TestCase
{
    private static $fetchMenu = 'route/admin/fetchMenu';

    public function testFetchMenu()
    {
        $user = Admin::where('name', 'fly.fei')->first();

        $this->actingAs($user, 'admin')
            ->getJson(self::$fetchMenu)
            ->assertSuccessful()
            ->assertJsonStructure(['data' => ['index', 'menu']]);
    }
}
