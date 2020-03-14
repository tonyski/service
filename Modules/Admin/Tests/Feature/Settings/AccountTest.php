<?php

namespace Modules\Admin\Tests\Feature\Settings;

use Modules\Base\Tests\AdminTestCase;

class AccountTest extends AdminTestCase
{
    private static $accountUri = 'admin/settings/account';

    public function testUpdateAccount()
    {
        $this->patchJson(self::$accountUri, [
            'name' => $this->faker()->unique()->regexify('^[a-z]+(\.[a-z]+){0,2}$'),
            'email' => $this->faker()->email
        ])
            ->assertSuccessful()
            ->assertJsonStructure(['data' => ['admin']]);

        $this->patchJson(self::$accountUri, [
            'name' => 'fly.fei',
            'email' => 'fly.fei@feisu.com'
        ])
            ->assertSuccessful()
            ->assertJsonStructure(['data' => ['admin']]);
    }
}