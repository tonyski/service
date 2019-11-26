<?php

namespace Modules\Route\Tests\Feature;

use Modules\Base\Tests\AdminTestCase;

class RouteTest extends AdminTestCase
{
    private static $fetchMenu = 'route/admin/fetchMenu';

    public function testFetchMenu()
    {
        $this->getJson(self::$fetchMenu)
            ->assertSuccessful()
            ->assertJsonStructure(['data' => ['index', 'menu']]);
    }
}
